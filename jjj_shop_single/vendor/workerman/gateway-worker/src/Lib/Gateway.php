<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace GatewayWorker\Lib;

use Exception;
use GatewayWorker\Protocols\GatewayProtocol;
use Workerman\Connection\TcpConnection;

/**
 * 資料傳送相關
 */
class Gateway
{
    /**
     * gateway 例項
     *
     * @var object
     */
    protected static $businessWorker = null;

    /**
     * 註冊中心地址
     *
     * @var string|array
     */
    public static $registerAddress = '127.0.0.1:1236';

    /**
     * 秘鑰
     * @var string
     */
    public static $secretKey = '';

    /**
     * 連結超時時間
     * @var int
     */
    public static $connectTimeout = 3;

    /**
     * 與Gateway是否是長連結
     * @var bool
     */
    public static $persistentConnection = false;
    
    /**
     * 向所有客戶端連線(或者 client_id_array 指定的客戶端連線)廣播訊息
     *
     * @param string $message           向客戶端傳送的訊息
     * @param array  $client_id_array   客戶端 id 陣列
     * @param array  $exclude_client_id 不給這些client_id發
     * @param bool   $raw               是否傳送原始資料（即不呼叫gateway的協議的encode方法）
     * @return void
     * @throws Exception
     */
    public static function sendToAll($message, $client_id_array = null, $exclude_client_id = null, $raw = false)
    {
        $gateway_data         = GatewayProtocol::$empty;
        $gateway_data['cmd']  = GatewayProtocol::CMD_SEND_TO_ALL;
        $gateway_data['body'] = $message;
        if ($raw) {
            $gateway_data['flag'] |= GatewayProtocol::FLAG_NOT_CALL_ENCODE;
        }

        if ($exclude_client_id) {
            if (!is_array($exclude_client_id)) {
                $exclude_client_id = array($exclude_client_id);
            }
            if ($client_id_array) {
                $exclude_client_id = array_flip($exclude_client_id);
            }
        }

        if ($client_id_array) {
            if (!is_array($client_id_array)) {
                echo new \Exception('bad $client_id_array:'.var_export($client_id_array, true));
                return;
            }
            $data_array = array();
            foreach ($client_id_array as $client_id) {
                if (isset($exclude_client_id[$client_id])) {
                    continue;
                }
                $address = Context::clientIdToAddress($client_id);
                if ($address) {
                    $key                                         = long2ip($address['local_ip']) . ":{$address['local_port']}";
                    $data_array[$key][$address['connection_id']] = $address['connection_id'];
                }
            }
            foreach ($data_array as $addr => $connection_id_list) {
                $the_gateway_data             = $gateway_data;
                $the_gateway_data['ext_data'] = json_encode(array('connections' => $connection_id_list));
                static::sendToGateway($addr, $the_gateway_data);
            }
            return;
        } elseif (empty($client_id_array) && is_array($client_id_array)) {
            return;
        }

        if (!$exclude_client_id) {
            return static::sendToAllGateway($gateway_data);
        }

        $address_connection_array = static::clientIdArrayToAddressArray($exclude_client_id);

        // 如果有businessWorker例項，說明執行在workerman環境中，透過businessWorker中的長連線傳送資料
        if (static::$businessWorker) {
            foreach (static::$businessWorker->gatewayConnections as $address => $gateway_connection) {
                $gateway_data['ext_data'] = isset($address_connection_array[$address]) ?
                    json_encode(array('exclude'=> $address_connection_array[$address])) : '';
                /** @var TcpConnection $gateway_connection */
                $gateway_connection->send($gateway_data);
            }
        } // 執行在其它環境中，透過註冊中心得到gateway地址
        else {
            $all_addresses = static::getAllGatewayAddressesFromRegister();
            foreach ($all_addresses as $address) {
                $gateway_data['ext_data'] = isset($address_connection_array[$address]) ?
                    json_encode(array('exclude'=> $address_connection_array[$address])) : '';
                static::sendToGateway($address, $gateway_data);
            }
        }

    }

    /**
     * 向某個client_id對應的連線發訊息
     *
     * @param string    $client_id
     * @param string $message
     * @param bool $raw
     * @return bool
     */
    public static function sendToClient($client_id, $message, $raw = false)
    {
        return static::sendCmdAndMessageToClient($client_id, GatewayProtocol::CMD_SEND_TO_ONE, $message, '', $raw);
    }

    /**
     * 向當前客戶端連線傳送訊息
     *
     * @param string $message
     * @param bool $raw
     * @return bool
     */
    public static function sendToCurrentClient($message, $raw = false)
    {
        return static::sendCmdAndMessageToClient(null, GatewayProtocol::CMD_SEND_TO_ONE, $message, '', $raw);
    }

    /**
     * 判斷某個uid是否線上
     *
     * @param string $uid
     * @return int 0|1
     */
    public static function isUidOnline($uid)
    {
        return (int)static::getClientIdByUid($uid);
    }
    
    /**
     * 判斷client_id對應的連線是否線上
     *
     * @param string $client_id
     * @return int 0|1
     */
    public static function isOnline($client_id)
    {
        $address_data = Context::clientIdToAddress($client_id);
        if (!$address_data) {
            return 0;
        }
        $address      = long2ip($address_data['local_ip']) . ":{$address_data['local_port']}";
        if (isset(static::$businessWorker)) {
            if (!isset(static::$businessWorker->gatewayConnections[$address])) {
                return 0;
            }
        }
        $gateway_data                  = GatewayProtocol::$empty;
        $gateway_data['cmd']           = GatewayProtocol::CMD_IS_ONLINE;
        $gateway_data['connection_id'] = $address_data['connection_id'];
        return (int)static::sendAndRecv($address, $gateway_data);
    }

    /**
     * 獲取所有線上使用者的session，client_id為 key(棄用，請用getAllClientSessions代替)
     *
     * @param string $group
     * @return array
     */
    public static function getAllClientInfo($group = '')
    {
        echo "Warning: Gateway::getAllClientInfo is deprecated and will be removed in a future, please use Gateway::getAllClientSessions instead.";
        return static::getAllClientSessions($group);
    }

    /**
     * 獲取所有線上client_id的session，client_id為 key
     *
     * @param string $group
     * @return array
     */
    public static function getAllClientSessions($group = '')
    {
        $gateway_data = GatewayProtocol::$empty;
        if (!$group) {
            $gateway_data['cmd']      = GatewayProtocol::CMD_GET_ALL_CLIENT_SESSIONS;
        } else {
            $gateway_data['cmd']      = GatewayProtocol::CMD_GET_CLIENT_SESSIONS_BY_GROUP;
            $gateway_data['ext_data'] = $group;
        }
        $status_data      = array();
        $all_buffer_array = static::getBufferFromAllGateway($gateway_data);
        foreach ($all_buffer_array as $local_ip => $buffer_array) {
            foreach ($buffer_array as $local_port => $data) {
                if ($data) {
                    foreach ($data as $connection_id => $session_buffer) {
                        $client_id = Context::addressToClientId($local_ip, $local_port, $connection_id);
                        if ($client_id === Context::$client_id) {
                            $status_data[$client_id] = (array)$_SESSION;
                        } else {
                            $status_data[$client_id] = $session_buffer ? Context::sessionDecode($session_buffer) : array();
                        }
                    }
                }
            }
        }
        return $status_data;
    }

    /**
     * 獲取某個組的連線資訊（棄用，請用getClientSessionsByGroup代替）
     *
     * @param string $group
     * @return array
     */
    public static function getClientInfoByGroup($group)
    {
        echo "Warning: Gateway::getClientInfoByGroup is deprecated and will be removed in a future, please use Gateway::getClientSessionsByGroup instead.";
        return static::getAllClientSessions($group);
    }

    /**
     * 獲取某個組的所有client_id的session資訊
     *
     * @param string $group
     *
     * @return array
     */
    public static function getClientSessionsByGroup($group)
    {
        if (static::isValidGroupId($group)) {
            return static::getAllClientSessions($group);
        }
        return array();
    }

    /**
     * 獲取所有線上client_id數
     *
     * @return int
     */
    public static function getAllClientIdCount()
    {
        return static::getClientCountByGroup();
    }

    /**
     * 獲取所有線上client_id數(getAllClientIdCount的別名)
     *
     * @return int
     */
    public static function getAllClientCount()
    {
        return static::getAllClientIdCount();
    }

    /**
     * 獲取某個組的線上client_id數
     *
     * @param string $group
     * @return int
     */
    public static function getClientIdCountByGroup($group = '')
    {
        $gateway_data             = GatewayProtocol::$empty;
        $gateway_data['cmd']      = GatewayProtocol::CMD_GET_CLIENT_COUNT_BY_GROUP;
        $gateway_data['ext_data'] = $group;
        $total_count              = 0;
        $all_buffer_array         = static::getBufferFromAllGateway($gateway_data);
        foreach ($all_buffer_array as $local_ip => $buffer_array) {
            foreach ($buffer_array as $local_port => $count) {
                if ($count) {
                    $total_count += $count;
                }
            }
        }
        return $total_count;
    }

    /**
     * getClientIdCountByGroup 函式的別名
     *
     * @param string $group
     * @return int
     */
    public static function getClientCountByGroup($group = '')
    {
        return static::getClientIdCountByGroup($group);
    }

    /**
     * 獲取某個群組線上client_id列表
     *
     * @param string $group
     * @return array
     */
    public static function getClientIdListByGroup($group)
    {
        if (!static::isValidGroupId($group)) {
            return array();
        }

        $data = static::select(array('uid'), array('groups' => is_array($group) ? $group : array($group)));
        $client_id_map = array();
        foreach ($data as $local_ip => $buffer_array) {
            foreach ($buffer_array as $local_port => $items) {
                //$items = ['connection_id'=>['uid'=>x, 'group'=>[x,x..], 'session'=>[..]], 'client_id'=>[..], ..];
                foreach ($items as $connection_id => $info) {
                    $client_id = Context::addressToClientId($local_ip, $local_port, $connection_id);
                    $client_id_map[$client_id] = $client_id;
                }
            }
        }
        return $client_id_map;
    }

    /**
     * 獲取叢集所有線上client_id列表
     *
     * @return array
     */
    public static function getAllClientIdList()
    {
        return static::formatClientIdFromGatewayBuffer(static::select(array('uid')));
    }

    /**
     * 格式化client_id
     *
     * @param $data
     * @return array
     */
    protected static function formatClientIdFromGatewayBuffer($data)
    {
        $client_id_list = array();
        foreach ($data as $local_ip => $buffer_array) {
            foreach ($buffer_array as $local_port => $items) {
                //$items = ['connection_id'=>['uid'=>x, 'group'=>[x,x..], 'session'=>[..]], 'client_id'=>[..], ..];
                foreach ($items as $connection_id => $info) {
                    $client_id = Context::addressToClientId($local_ip, $local_port, $connection_id);
                    $client_id_list[$client_id] = $client_id;
                }
            }
        }
        return $client_id_list;
    }


    /**
     * 獲取與 uid 繫結的 client_id 列表
     *
     * @param string $uid
     * @return array
     */
    public static function getClientIdByUid($uid)
    {
        $gateway_data             = GatewayProtocol::$empty;
        $gateway_data['cmd']      = GatewayProtocol::CMD_GET_CLIENT_ID_BY_UID;
        $gateway_data['ext_data'] = $uid;
        $client_list              = array();
        $all_buffer_array         = static::getBufferFromAllGateway($gateway_data);
        foreach ($all_buffer_array as $local_ip => $buffer_array) {
            foreach ($buffer_array as $local_port => $connection_id_array) {
                if ($connection_id_array) {
                    foreach ($connection_id_array as $connection_id) {
                        $client_list[] = Context::addressToClientId($local_ip, $local_port, $connection_id);
                    }
                }
            }
        }
        return $client_list;
    }

    /**
     * 獲取某個群組線上uid列表
     *
     * @param string $group
     * @return array
     */
    public static function getUidListByGroup($group)
    {
        if (!static::isValidGroupId($group)) {
            return array();
        }

        $group = is_array($group) ? $group : array($group);
        $data = static::select(array('uid'), array('groups' => $group));
        $uid_map = array();
        foreach ($data as $local_ip => $buffer_array) {
            foreach ($buffer_array as $local_port => $items) {
                //$items = ['connection_id'=>['uid'=>x, 'group'=>[x,x..], 'session'=>[..]], 'client_id'=>[..], ..];
                foreach ($items as $connection_id => $info) {
                    if (!empty($info['uid'])) {
                        $uid_map[$info['uid']] = $info['uid'];
                    }
                }
            }
        }
        return $uid_map;
    }

    /**
     * 獲取某個群組線上uid數
     *
     * @param string $group
     * @return int
     */
    public static function getUidCountByGroup($group)
    {
        if (static::isValidGroupId($group)) {
            return count(static::getUidListByGroup($group));
        }
        return 0;
    }

    /**
     * 獲取全域性線上uid列表
     *
     * @return array
     */
    public static function getAllUidList()
    {
        $data = static::select(array('uid'));
        $uid_map = array();
        foreach ($data as $local_ip => $buffer_array) {
            foreach ($buffer_array as $local_port => $items) {
                //$items = ['connection_id'=>['uid'=>x, 'group'=>[x,x..], 'session'=>[..]], 'client_id'=>[..], ..];
                foreach ($items as $connection_id => $info) {
                    if (!empty($info['uid'])) {
                        $uid_map[$info['uid']] = $info['uid'];
                    }
                }
            }
        }
        return $uid_map;
    }

    /**
     * 獲取全域性線上uid數
     * @return int
     */
    public static function getAllUidCount()
    {
        return count(static::getAllUidList());
    }

    /**
     * 透過client_id獲取uid
     *
     * @param $client_id
     * @return mixed
     */
    public static function getUidByClientId($client_id)
    {
        $data = static::select(array('uid'), array('client_id'=>array($client_id)));
        foreach ($data as $local_ip => $buffer_array) {
            foreach ($buffer_array as $local_port => $items) {
                //$items = ['connection_id'=>['uid'=>x, 'group'=>[x,x..], 'session'=>[..]], 'client_id'=>[..], ..];
                foreach ($items as $info) {
                    return $info['uid'];
                }
            }
        }
    }

    /**
     * 獲取所有線上的群組id
     *
     * @return array
     */
    public static function getAllGroupIdList()
    {
        $gateway_data             = GatewayProtocol::$empty;
        $gateway_data['cmd']      = GatewayProtocol::CMD_GET_GROUP_ID_LIST;
        $group_id_list            = array();
        $all_buffer_array         = static::getBufferFromAllGateway($gateway_data);
        foreach ($all_buffer_array as $local_ip => $buffer_array) {
            foreach ($buffer_array as $local_port => $group_id_array) {
                if (is_array($group_id_array)) {
                    foreach ($group_id_array as $group_id) {
                        if (!isset($group_id_list[$group_id])) {
                            $group_id_list[$group_id] = $group_id;
                        }
                    }
                }
            }
        }
        return $group_id_list;
    }


    /**
     * 獲取所有線上分組的uid數量，也就是每個分組的線上使用者數
     *
     * @return array
     */
    public static function getAllGroupUidCount()
    {
        $group_uid_map = static::getAllGroupUidList();
        $group_uid_count_map = array();
        foreach ($group_uid_map as $group_id => $uid_list) {
            $group_uid_count_map[$group_id] = count($uid_list);
        }
        return $group_uid_count_map;
    }



    /**
     * 獲取所有分組uid線上列表
     *
     * @return array
     */
    public static function getAllGroupUidList()
    {
        $data = static::select(array('uid','groups'));
        $group_uid_map = array();
        foreach ($data as $local_ip => $buffer_array) {
            foreach ($buffer_array as $local_port => $items) {
                //$items = ['connection_id'=>['uid'=>x, 'group'=>[x,x..], 'session'=>[..]], 'client_id'=>[..], ..];
                foreach ($items as $connection_id => $info) {
                    if (empty($info['uid']) || empty($info['groups'])) {
                        break;
                    }
                    $uid = $info['uid'];
                    foreach ($info['groups'] as $group_id) {
                        if(!isset($group_uid_map[$group_id])) {
                            $group_uid_map[$group_id] = array();
                        }
                        $group_uid_map[$group_id][$uid] = $uid;
                    }
                }
            }
        }
        return $group_uid_map;
    }

    /**
     * 獲取所有群組線上client_id列表
     *
     * @return array
     */
    public static function getAllGroupClientIdList()
    {
        $data = static::select(array('groups'));
        $group_client_id_map = array();
        foreach ($data as $local_ip => $buffer_array) {
            foreach ($buffer_array as $local_port => $items) {
                //$items = ['connection_id'=>['uid'=>x, 'group'=>[x,x..], 'session'=>[..]], 'client_id'=>[..], ..];
                foreach ($items as $connection_id => $info) {
                    if (empty($info['groups'])) {
                        break;
                    }
                    $client_id = Context::addressToClientId($local_ip, $local_port, $connection_id);
                    foreach ($info['groups'] as $group_id) {
                        if(!isset($group_client_id_map[$group_id])) {
                            $group_client_id_map[$group_id] = array();
                        }
                        $group_client_id_map[$group_id][$client_id] = $client_id;
                    }
                }
            }
        }
        return $group_client_id_map;
    }

    /**
     * 獲取所有群組線上client_id數量，也就是獲取每個群組線上連線數
     *
     * @return array
     */
    public static function getAllGroupClientIdCount()
    {
        $group_client_map = static::getAllGroupClientIdList();
        $group_client_count_map = array();
        foreach ($group_client_map as $group_id => $client_id_list) {
            $group_client_count_map[$group_id] = count($client_id_list);
        }
        return $group_client_count_map;
    }


    /**
     * 根據條件到gateway搜尋資料
     *
     * @param array $fields
     * @param array $where
     * @return array
     */
    protected static function select($fields = array('session','uid','groups'), $where = array())
    {
        $t = microtime(true);
        $gateway_data             = GatewayProtocol::$empty;
        $gateway_data['cmd']      = GatewayProtocol::CMD_SELECT;
        $gateway_data['ext_data'] = array('fields' => $fields, 'where' => $where);
        $gateway_data_list   = array();
        // 有client_id，能計算出需要和哪些gateway通訊，只和必要的gateway通訊能降低系統負載
        if (isset($where['client_id'])) {
            $client_id_list = $where['client_id'];
            unset($gateway_data['ext_data']['where']['client_id']);
            $gateway_data['ext_data']['where']['connection_id'] = array();
            foreach ($client_id_list as $client_id) {
                $address_data = Context::clientIdToAddress($client_id);
                if (!$address_data) {
                    continue;
                }
                $address = long2ip($address_data['local_ip']) . ":{$address_data['local_port']}";
                if (!isset($gateway_data_list[$address])) {
                    $gateway_data_list[$address] = $gateway_data;
                }
                $gateway_data_list[$address]['ext_data']['where']['connection_id'][$address_data['connection_id']] = $address_data['connection_id'];
            }
            foreach ($gateway_data_list as $address => $item) {
                $gateway_data_list[$address]['ext_data'] = json_encode($item['ext_data']);
            }
            // 有其它條件，則還是需要向所有gateway傳送
            if (count($where) !== 1) {
                $gateway_data['ext_data'] = json_encode($gateway_data['ext_data']);
                foreach (static::getAllGatewayAddress() as $address) {
                    if (!isset($gateway_data_list[$address])) {
                        $gateway_data_list[$address] = $gateway_data;
                    }
                }
            }
            $data = static::getBufferFromSomeGateway($gateway_data_list);
        } else {
            $gateway_data['ext_data'] = json_encode($gateway_data['ext_data']);
            $data = static::getBufferFromAllGateway($gateway_data);
        }

        return $data;
    }

    /**
     * 生成驗證包，用於驗證此客戶端的合法性
     * 
     * @return string
     */
    protected static function generateAuthBuffer()
    {
        $gateway_data         = GatewayProtocol::$empty;
        $gateway_data['cmd']  = GatewayProtocol::CMD_GATEWAY_CLIENT_CONNECT;
        $gateway_data['body'] = json_encode(array(
            'secret_key' => static::$secretKey,
        ));
        return GatewayProtocol::encode($gateway_data);
    }

    /**
     * 批次向某些gateway發包，並得到返回陣列
     *
     * @param array $gateway_data_array
     * @return array
     * @throws Exception
     */
    protected static function getBufferFromSomeGateway($gateway_data_array)
    {
        $gateway_buffer_array = array();
        $auth_buffer = static::$secretKey ? static::generateAuthBuffer() : '';
        foreach ($gateway_data_array as $address => $gateway_data) {
            if ($auth_buffer) {
                $gateway_buffer_array[$address] = $auth_buffer.GatewayProtocol::encode($gateway_data);
            } else {
                $gateway_buffer_array[$address] = GatewayProtocol::encode($gateway_data);
            }
        }
        return static::getBufferFromGateway($gateway_buffer_array);
    }

    /**
     * 批次向所有 gateway 發包，並得到返回陣列
     *
     * @param string $gateway_data
     * @return array
     * @throws Exception
     */
    protected static function getBufferFromAllGateway($gateway_data)
    {
        $addresses = static::getAllGatewayAddress();
        $gateway_buffer_array = array();
        $gateway_buffer = GatewayProtocol::encode($gateway_data);
        $gateway_buffer = static::$secretKey ? static::generateAuthBuffer() . $gateway_buffer : $gateway_buffer;
        foreach ($addresses as $address) {
            $gateway_buffer_array[$address] = $gateway_buffer;
        }

        return static::getBufferFromGateway($gateway_buffer_array);
    }

    /**
     * 獲取所有gateway內部通訊地址
     *
     * @return array
     * @throws Exception
     */
    protected static function getAllGatewayAddress()
    {
        if (isset(static::$businessWorker)) {
            $addresses = static::$businessWorker->getAllGatewayAddresses();
            if (empty($addresses)) {
                throw new Exception('businessWorker::getAllGatewayAddresses return empty');
            }
        } else {
            $addresses = static::getAllGatewayAddressesFromRegister();
            if (empty($addresses)) {
                return array();
            }
        }
        return $addresses;
    }

    /**
     * 批次向gateway傳送並獲取資料
     * @param $gateway_buffer_array
     * @return array
     */
    protected static function getBufferFromGateway($gateway_buffer_array)
    {
        $client_array = $status_data = $client_address_map = $receive_buffer_array = $recv_length_array = array();
        // 批次向所有gateway程序傳送請求資料
        foreach ($gateway_buffer_array as $address => $gateway_buffer) {
            $client = stream_socket_client("tcp://$address", $errno, $errmsg, static::$connectTimeout);
            if ($client && strlen($gateway_buffer) === stream_socket_sendto($client, $gateway_buffer)) {
                $socket_id                        = (int)$client;
                $client_array[$socket_id]         = $client;
                $client_address_map[$socket_id]   = explode(':', $address);
                $receive_buffer_array[$socket_id] = '';
            }
        }
        // 超時5秒
        $timeout    = 5;
        $time_start = microtime(true);
        // 批次接收請求
        while (count($client_array) > 0) {
            $write = $except = array();
            $read  = $client_array;
            if (@stream_select($read, $write, $except, $timeout)) {
                foreach ($read as $client) {
                    $socket_id = (int)$client;
                    $buffer    = stream_socket_recvfrom($client, 65535);
                    if ($buffer !== '' && $buffer !== false) {
                        $receive_buffer_array[$socket_id] .= $buffer;
                        $receive_length = strlen($receive_buffer_array[$socket_id]);
                        if (empty($recv_length_array[$socket_id]) && $receive_length >= 4) {
                            $recv_length_array[$socket_id] = current(unpack('N', $receive_buffer_array[$socket_id]));
                        }
                        if (!empty($recv_length_array[$socket_id]) && $receive_length >= $recv_length_array[$socket_id] + 4) {
                            unset($client_array[$socket_id]);
                        }
                    } elseif (feof($client)) {
                        unset($client_array[$socket_id]);
                    }
                }
            }
            if (microtime(true) - $time_start > $timeout) {
                break;
            }
        }
        $format_buffer_array = array();
        foreach ($receive_buffer_array as $socket_id => $buffer) {
            $local_ip                                    = ip2long($client_address_map[$socket_id][0]);
            $local_port                                  = $client_address_map[$socket_id][1];
            $format_buffer_array[$local_ip][$local_port] = unserialize(substr($buffer, 4));
        }
        return $format_buffer_array;
    }

    /**
     * 踢掉某個客戶端，並以$message通知被踢掉客戶端
     *
     * @param string $client_id
     * @param string $message
     * @return void
     */
    public static function closeClient($client_id, $message = null)
    {
        if ($client_id === Context::$client_id) {
            return static::closeCurrentClient($message);
        } // 不是發給當前使用者則使用儲存中的地址
        else {
            $address_data = Context::clientIdToAddress($client_id);
            if (!$address_data) {
                return false;
            }
            $address      = long2ip($address_data['local_ip']) . ":{$address_data['local_port']}";
            return static::kickAddress($address, $address_data['connection_id'], $message);
        }
    }

    /**
     * 踢掉當前客戶端，並以$message通知被踢掉客戶端
     *
     * @param string $message
     * @return bool
     * @throws Exception
     */
    public static function closeCurrentClient($message = null)
    {
        if (!Context::$connection_id) {
            throw new Exception('closeCurrentClient can not be called in async context');
        }
        $address = long2ip(Context::$local_ip) . ':' . Context::$local_port;
        return static::kickAddress($address, Context::$connection_id, $message);
    }

    /**
     * 踢掉某個客戶端並直接立即銷燬相關連線
     *
     * @param string $client_id
     * @return bool
     */
    public static function destoryClient($client_id)
    {
        if ($client_id === Context::$client_id) {
            return static::destoryCurrentClient();
        } // 不是發給當前使用者則使用儲存中的地址
        else {
            $address_data = Context::clientIdToAddress($client_id);
            if (!$address_data) {
                return false;
            }
            $address = long2ip($address_data['local_ip']) . ":{$address_data['local_port']}";
            return static::destroyAddress($address, $address_data['connection_id']);
        }
    }

    /**
     * 踢掉當前客戶端並直接立即銷燬相關連線
     *
     * @return bool
     * @throws Exception
     */
    public static function destoryCurrentClient()
    {
        if (!Context::$connection_id) {
            throw new Exception('destoryCurrentClient can not be called in async context');
        }
        $address = long2ip(Context::$local_ip) . ':' . Context::$local_port;
        return static::destroyAddress($address, Context::$connection_id);
    }

    /**
     * 將 client_id 與 uid 繫結
     *
     * @param string        $client_id
     * @param int|string $uid
     * @return void
     */
    public static function bindUid($client_id, $uid)
    {
        static::sendCmdAndMessageToClient($client_id, GatewayProtocol::CMD_BIND_UID, '', $uid);
    }

    /**
     * 將 client_id 與 uid 解除繫結
     *
     * @param string        $client_id
     * @param int|string $uid
     * @return void
     */
    public static function unbindUid($client_id, $uid)
    {
        static::sendCmdAndMessageToClient($client_id, GatewayProtocol::CMD_UNBIND_UID, '', $uid);
    }

    /**
     * 將 client_id 加入組
     *
     * @param string        $client_id
     * @param int|string $group
     * @return void
     */
    public static function joinGroup($client_id, $group)
    {

        static::sendCmdAndMessageToClient($client_id, GatewayProtocol::CMD_JOIN_GROUP, '', $group);
    }

    /**
     * 將 client_id 離開組
     *
     * @param string        $client_id
     * @param int|string $group
     *
     * @return void
     */
    public static function leaveGroup($client_id, $group)
    {
        static::sendCmdAndMessageToClient($client_id, GatewayProtocol::CMD_LEAVE_GROUP, '', $group);
    }

    /**
     * 取消分組
     *
     * @param int|string $group
     *
     * @return void
     */
    public static function ungroup($group)
    {
        if (!static::isValidGroupId($group)) {
            return false;
        }
        $gateway_data             = GatewayProtocol::$empty;
        $gateway_data['cmd']      = GatewayProtocol::CMD_UNGROUP;
        $gateway_data['ext_data'] = $group;
        return static::sendToAllGateway($gateway_data);

    }

    /**
     * 向所有 uid 傳送
     *
     * @param int|string|array $uid
     * @param string           $message
     * @param bool $raw
     *
     * @return void
     */
    public static function sendToUid($uid, $message, $raw = false)
    {
        $gateway_data         = GatewayProtocol::$empty;
        $gateway_data['cmd']  = GatewayProtocol::CMD_SEND_TO_UID;
        $gateway_data['body'] = $message;
        if ($raw) {
            $gateway_data['flag'] |= GatewayProtocol::FLAG_NOT_CALL_ENCODE;
        }

        if (!is_array($uid)) {
            $uid = array($uid);
        }

        $gateway_data['ext_data'] = json_encode($uid);

        static::sendToAllGateway($gateway_data);
    }

    /**
     * 向 group 傳送
     *
     * @param int|string|array $group             組（不允許是 0 '0' false null array()等為空的值）
     * @param string           $message           訊息
     * @param array            $exclude_client_id 不給這些client_id發
     * @param bool             $raw               傳送原始資料（即不呼叫gateway的協議的encode方法）
     *
     * @return void
     */
    public static function sendToGroup($group, $message, $exclude_client_id = null, $raw = false)
    {
        if (!static::isValidGroupId($group)) {
            return false;
        }
        $gateway_data         = GatewayProtocol::$empty;
        $gateway_data['cmd']  = GatewayProtocol::CMD_SEND_TO_GROUP;
        $gateway_data['body'] = $message;
        if ($raw) {
            $gateway_data['flag'] |= GatewayProtocol::FLAG_NOT_CALL_ENCODE;
        }

        if (!is_array($group)) {
            $group = array($group);
        }

        // 分組傳送，沒有排除的client_id，直接傳送
        $default_ext_data_buffer = json_encode(array('group'=> $group, 'exclude'=> null));
        if (empty($exclude_client_id)) {
            $gateway_data['ext_data'] = $default_ext_data_buffer;
            return static::sendToAllGateway($gateway_data);
        }

        // 分組傳送，有排除的client_id，需要將client_id轉換成對應gateway程序內的connectionId
        if (!is_array($exclude_client_id)) {
            $exclude_client_id = array($exclude_client_id);
        }

        $address_connection_array = static::clientIdArrayToAddressArray($exclude_client_id);
        // 如果有businessWorker例項，說明執行在workerman環境中，透過businessWorker中的長連線傳送資料
        if (static::$businessWorker) {
            foreach (static::$businessWorker->gatewayConnections as $address => $gateway_connection) {
                $gateway_data['ext_data'] = isset($address_connection_array[$address]) ?
                    json_encode(array('group'=> $group, 'exclude'=> $address_connection_array[$address])) :
                    $default_ext_data_buffer;
                /** @var TcpConnection $gateway_connection */
                $gateway_connection->send($gateway_data);
            }
        } // 執行在其它環境中，透過註冊中心得到gateway地址
        else {
            $addresses = static::getAllGatewayAddressesFromRegister();
            foreach ($addresses as $address) {
                $gateway_data['ext_data'] = isset($address_connection_array[$address]) ?
                    json_encode(array('group'=> $group, 'exclude'=> $address_connection_array[$address])) :
                    $default_ext_data_buffer;
                static::sendToGateway($address, $gateway_data);
            }
        }
    }

    /**
     * 更新 session，框架自動呼叫，開發者不要呼叫
     *
     * @param string    $client_id
     * @param string $session_str
     * @return bool
     */
    public static function setSocketSession($client_id, $session_str)
    {
        return static::sendCmdAndMessageToClient($client_id, GatewayProtocol::CMD_SET_SESSION, '', $session_str);
    }

    /**
     * 設定 session，原session值會被覆蓋
     *
     * @param string   $client_id
     * @param array $session
     *
     * @return void
     */
    public static function setSession($client_id, array $session)
    {
        if (Context::$client_id === $client_id) {
            $_SESSION = $session;
            Context::$old_session = $_SESSION;
        }
        static::setSocketSession($client_id, Context::sessionEncode($session));
    }
    
    /**
     * 更新 session，實際上是與老的session合併
     *
     * @param string   $client_id
     * @param array $session
     *
     * @return void
     */
    public static function updateSession($client_id, array $session)
    {
        if (Context::$client_id === $client_id) {
            $_SESSION = array_replace_recursive((array)$_SESSION, $session);
            Context::$old_session = $_SESSION;
        }
        static::sendCmdAndMessageToClient($client_id, GatewayProtocol::CMD_UPDATE_SESSION, '', Context::sessionEncode($session));
    }
    
    /**
     * 獲取某個client_id的session
     *
     * @param string   $client_id
     * @return mixed false表示出錯、null表示使用者不存在、array表示具體的session資訊 
     */
    public static function getSession($client_id)
    {
        $address_data = Context::clientIdToAddress($client_id);
        if (!$address_data) {
            return false;
        }
        $address      = long2ip($address_data['local_ip']) . ":{$address_data['local_port']}";
        if (isset(static::$businessWorker)) {
            if (!isset(static::$businessWorker->gatewayConnections[$address])) {
                return null;
            }
        }
        $gateway_data                  = GatewayProtocol::$empty;
        $gateway_data['cmd']           = GatewayProtocol::CMD_GET_SESSION_BY_CLIENT_ID;
        $gateway_data['connection_id'] = $address_data['connection_id'];
        return static::sendAndRecv($address, $gateway_data);
    }

    /**
     * 向某個使用者閘道器傳送命令和訊息
     *
     * @param string    $client_id
     * @param int    $cmd
     * @param string $message
     * @param string $ext_data
     * @param bool $raw
     * @return boolean
     */
    protected static function sendCmdAndMessageToClient($client_id, $cmd, $message, $ext_data = '', $raw = false)
    {
        // 如果是發給當前使用者則直接獲取上下文中的地址
        if ($client_id === Context::$client_id || $client_id === null) {
            $address       = long2ip(Context::$local_ip) . ':' . Context::$local_port;
            $connection_id = Context::$connection_id;
        } else {
            $address_data  = Context::clientIdToAddress($client_id);
            if (!$address_data) {
                return false;
            }
            $address       = long2ip($address_data['local_ip']) . ":{$address_data['local_port']}";
            $connection_id = $address_data['connection_id'];
        }
        $gateway_data                  = GatewayProtocol::$empty;
        $gateway_data['cmd']           = $cmd;
        $gateway_data['connection_id'] = $connection_id;
        $gateway_data['body']          = $message;
        if (!empty($ext_data)) {
            $gateway_data['ext_data'] = $ext_data;
        }
        if ($raw) {
            $gateway_data['flag'] |= GatewayProtocol::FLAG_NOT_CALL_ENCODE;
        }

        return static::sendToGateway($address, $gateway_data);
    }

    /**
     * 傳送資料並返回
     *
     * @param int   $address
     * @param mixed $data
     * @return bool
     * @throws Exception
     */
    protected static function sendAndRecv($address, $data)
    {
        $buffer = GatewayProtocol::encode($data);
        $buffer = static::$secretKey ? static::generateAuthBuffer() . $buffer : $buffer;
        $client = stream_socket_client("tcp://$address", $errno, $errmsg, static::$connectTimeout);
        if (!$client) {
            throw new Exception("can not connect to tcp://$address $errmsg");
        }
        if (strlen($buffer) === stream_socket_sendto($client, $buffer)) {
            $timeout = 5;
            // 阻塞讀
            stream_set_blocking($client, 1);
            // 1秒超時
            stream_set_timeout($client, 1);
            $all_buffer = '';
            $time_start = microtime(true);
            $pack_len = 0;
            while (1) {
                $buf = stream_socket_recvfrom($client, 655350);
                if ($buf !== '' && $buf !== false) {
                    $all_buffer .= $buf;
                } else {
                    if (feof($client)) {
                        throw new Exception("connection close tcp://$address");
                    } elseif (microtime(true) - $time_start > $timeout) {
                        break;
                    }
                    continue;
                }
                $recv_len = strlen($all_buffer);
                if (!$pack_len && $recv_len >= 4) {
                    $pack_len= current(unpack('N', $all_buffer));
                }
                // 回覆的資料都是以\n結尾
                if (($pack_len && $recv_len >= $pack_len + 4) || microtime(true) - $time_start > $timeout) {
                    break;
                }
            }
            // 返回結果
            return unserialize(substr($all_buffer, 4));
        } else {
            throw new Exception("sendAndRecv($address, \$bufer) fail ! Can not send data!", 502);
        }
    }

    /**
     * 傳送資料到閘道器
     *
     * @param string $address
     * @param array  $gateway_data
     * @return bool
     */
    protected static function sendToGateway($address, $gateway_data)
    {
        return static::sendBufferToGateway($address, GatewayProtocol::encode($gateway_data));
    }

    /**
     * 傳送buffer資料到閘道器
     * @param string $address
     * @param string $gateway_buffer
     * @return bool
     */
    protected static function sendBufferToGateway($address, $gateway_buffer)
    {
        // 有$businessWorker說明是workerman環境，使用$businessWorker傳送資料
        if (static::$businessWorker) {
            if (!isset(static::$businessWorker->gatewayConnections[$address])) {
                return false;
            }
            return static::$businessWorker->gatewayConnections[$address]->send($gateway_buffer, true);
        }
        // 非workerman環境
        $gateway_buffer = static::$secretKey ? static::generateAuthBuffer() . $gateway_buffer : $gateway_buffer;
        $flag           = static::$persistentConnection ? STREAM_CLIENT_PERSISTENT | STREAM_CLIENT_CONNECT : STREAM_CLIENT_CONNECT;
        $client         = stream_socket_client("tcp://$address", $errno, $errmsg, static::$connectTimeout, $flag);
        return strlen($gateway_buffer) == stream_socket_sendto($client, $gateway_buffer);
    }

    /**
     * 向所有 gateway 傳送資料
     *
     * @param string $gateway_data
     * @throws Exception
     *
     * @return void
     */
    protected static function sendToAllGateway($gateway_data)
    {
        $buffer = GatewayProtocol::encode($gateway_data);
        // 如果有businessWorker例項，說明執行在workerman環境中，透過businessWorker中的長連線傳送資料
        if (static::$businessWorker) {
            foreach (static::$businessWorker->gatewayConnections as $gateway_connection) {
                /** @var TcpConnection $gateway_connection */
                $gateway_connection->send($buffer, true);
            }
        } // 執行在其它環境中，透過註冊中心得到gateway地址
        else {
            $all_addresses = static::getAllGatewayAddressesFromRegister();
            foreach ($all_addresses as $address) {
                static::sendBufferToGateway($address, $buffer);
            }
        }
    }

    /**
     * 踢掉某個閘道器的 socket
     *
     * @param string $address
     * @param int    $connection_id
     * @return bool
     */
    protected static function kickAddress($address, $connection_id, $message)
    {
        $gateway_data                  = GatewayProtocol::$empty;
        $gateway_data['cmd']           = GatewayProtocol::CMD_KICK;
        $gateway_data['connection_id'] = $connection_id;
        $gateway_data['body'] = $message;
        return static::sendToGateway($address, $gateway_data);
    }

    /**
     * 銷燬某個閘道器的 socket
     *
     * @param string $address
     * @param int    $connection_id
     * @return bool
     */
    protected static function destroyAddress($address, $connection_id)
    {
        $gateway_data                  = GatewayProtocol::$empty;
        $gateway_data['cmd']           = GatewayProtocol::CMD_DESTROY;
        $gateway_data['connection_id'] = $connection_id;
        return static::sendToGateway($address, $gateway_data);
    }

    /**
     * 將clientid陣列轉換成address陣列
     *
     * @param array $client_id_array
     * @return array
     */
    protected static function clientIdArrayToAddressArray(array $client_id_array)
    {
        $address_connection_array = array();
        foreach ($client_id_array as $client_id) {
            $address_data = Context::clientIdToAddress($client_id);
            if ($address_data) {
                $address                                                            = long2ip($address_data['local_ip']) .
                    ":{$address_data['local_port']}";
                $address_connection_array[$address][$address_data['connection_id']] = $address_data['connection_id'];
            }
        }
        return $address_connection_array;
    }

    /**
     * 設定 gateway 例項
     *
     * @param \GatewayWorker\BusinessWorker $business_worker_instance
     */
    public static function setBusinessWorker($business_worker_instance)
    {
        static::$businessWorker = $business_worker_instance;
    }

    /**
     * 獲取透過註冊中心獲取所有 gateway 通訊地址
     *
     * @return array
     * @throws Exception
     */
    protected static function getAllGatewayAddressesFromRegister()
    {
        static $addresses_cache, $last_update;
        $time_now = time();
        $expiration_time = 1;
        $register_addresses = (array)static::$registerAddress;
        if(empty($addresses_cache) || $time_now - $last_update > $expiration_time) {
            foreach ($register_addresses as $register_address) {
                $client = stream_socket_client('tcp://' . $register_address, $errno, $errmsg, static::$connectTimeout);
                if ($client) {
                    break;
                }
            }
            if (!$client) {
                throw new Exception('Can not connect to tcp://' . $register_address . ' ' . $errmsg);
            }

            fwrite($client, '{"event":"worker_connect","secret_key":"' . static::$secretKey . '"}' . "\n");
            stream_set_timeout($client, 5);
            $ret = fgets($client, 655350);
            if (!$ret || !$data = json_decode(trim($ret), true)) {
                throw new Exception('getAllGatewayAddressesFromRegister fail. tcp://' .
                    $register_address . ' return ' . var_export($ret, true));
            }
            $last_update = $time_now;
            $addresses_cache = $data['addresses'];
        }
        if (!$addresses_cache) {
            throw new Exception('Gateway::getAllGatewayAddressesFromRegister() with registerAddress:' .
                json_encode(static::$registerAddress) . '  return ' . var_export($addresses_cache, true));
        }
        return $addresses_cache;
    }

    /**
     * 檢查群組id是否合法
     *
     * @param $group
     * @return bool
     */
    protected static function isValidGroupId($group)
    {
        if (empty($group)) {
            echo new \Exception('group('.var_export($group, true).') empty');
            return false;
        }
        return true;
    }
}

if (!class_exists('\Protocols\GatewayProtocol')) {
    class_alias('GatewayWorker\Protocols\GatewayProtocol', 'Protocols\GatewayProtocol');
}
