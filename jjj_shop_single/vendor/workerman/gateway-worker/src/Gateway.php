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
namespace GatewayWorker;

use GatewayWorker\Lib\Context;

use Workerman\Connection\TcpConnection;

use Workerman\Worker;
use Workerman\Lib\Timer;
use Workerman\Autoloader;
use Workerman\Connection\AsyncTcpConnection;
use GatewayWorker\Protocols\GatewayProtocol;

/**
 *
 * Gateway，基於Worker 開發
 * 用於轉發客戶端的資料給Worker處理，以及轉發Worker的資料給客戶端
 *
 * @author walkor<walkor@workerman.net>
 *
 */
class Gateway extends Worker
{
    /**
     * 版本
     *
     * @var string
     */
    const VERSION = '3.0.22';

    /**
     * 本機 IP
     *  單機部署預設 127.0.0.1，如果是分散式部署，需要設定成本機 IP
     *
     * @var string
     */
    public $lanIp = '127.0.0.1';

    /**
     * 本機埠
     *
     * @var string
     */
    public $lanPort = 0;

    /**
     * gateway 內部通訊起始埠，每個 gateway 例項應該都不同，步長1000
     *
     * @var int
     */
    public $startPort = 2000;

    /**
     * 註冊服務地址,用於註冊 Gateway BusinessWorker，使之能夠通訊
     *
     * @var string|array
     */
    public $registerAddress = '127.0.0.1:1236';

    /**
     * 是否可以平滑重啟，gateway 不能平滑重啟，否則會導致連線斷開
     *
     * @var bool
     */
    public $reloadable = false;

    /**
     * 心跳時間間隔
     *
     * @var int
     */
    public $pingInterval = 0;

    /**
     * $pingNotResponseLimit * $pingInterval 時間內，客戶端未傳送任何資料，斷開客戶端連線
     *
     * @var int
     */
    public $pingNotResponseLimit = 0;

    /**
     * 服務端向客戶端傳送的心跳資料
     *
     * @var string
     */
    public $pingData = '';
    
    /**
     * 秘鑰
     *
     * @var string
     */
    public $secretKey = '';

    /**
     * 路由函式
     *
     * @var callback
     */
    public $router = null;


    /**
     * gateway程序轉發給businessWorker程序的傳送緩衝區大小
     *
     * @var int
     */
    public $sendToWorkerBufferSize = 10240000;

    /**
     * gateway程序將資料發給客戶端時每個客戶端傳送緩衝區大小
     *
     * @var int
     */
    public $sendToClientBufferSize = 1024000;

    /**
     * 協議加速
     *
     * @var bool
     */
    public $protocolAccelerate = false;

    /**
     * BusinessWorker 連線成功之後觸發
     *
     * @var callback|null
     */
    public $onBusinessWorkerConnected = null;

    /**
     * BusinessWorker 關閉時觸發
     *
     * @var callback|null
     */
    public $onBusinessWorkerClose = null;

    /**
     * 儲存客戶端的所有 connection 物件
     *
     * @var array
     */
    protected $_clientConnections = array();

    /**
     * uid 到 connection 的對映，一對多關係
     */
    protected $_uidConnections = array();

    /**
     * group 到 connection 的對映，一對多關係
     *
     * @var array
     */
    protected $_groupConnections = array();

    /**
     * 儲存所有 worker 的內部連線的 connection 物件
     *
     * @var array
     */
    protected $_workerConnections = array();

    /**
     * gateway 內部監聽 worker 內部連線的 worker
     *
     * @var Worker
     */
    protected $_innerTcpWorker = null;

    /**
     * 當 worker 啟動時
     *
     * @var callback
     */
    protected $_onWorkerStart = null;

    /**
     * 當有客戶端連線時
     *
     * @var callback
     */
    protected $_onConnect = null;

    /**
     * 當客戶端發來訊息時
     *
     * @var callback
     */
    protected $_onMessage = null;

    /**
     * 當客戶端連線關閉時
     *
     * @var callback
     */
    protected $_onClose = null;

    /**
     * 當 worker 停止時
     *
     * @var callback
     */
    protected $_onWorkerStop = null;

    /**
     * 程序啟動時間
     *
     * @var int
     */
    protected $_startTime = 0;

    /**
     * gateway 監聽的埠
     *
     * @var int
     */
    protected $_gatewayPort = 0;
    
    /**
     * connectionId 記錄器
     * @var int
     */
    protected static $_connectionIdRecorder = 0;

    /**
     * 用於保持長連線的心跳時間間隔
     *
     * @var int
     */
    const PERSISTENCE_CONNECTION_PING_INTERVAL = 25;

    /**
     * 建構函式
     *
     * @param string $socket_name
     * @param array  $context_option
     */
    public function __construct($socket_name, $context_option = array())
    {
        parent::__construct($socket_name, $context_option);
		$this->_gatewayPort = substr(strrchr($socket_name,':'),1);
        $this->router = array("\\GatewayWorker\\Gateway", 'routerBind');

        $backtrace               = debug_backtrace();
        $this->_autoloadRootPath = dirname($backtrace[0]['file']);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // 儲存使用者的回撥，當對應的事件發生時觸發
        $this->_onWorkerStart = $this->onWorkerStart;
        $this->onWorkerStart  = array($this, 'onWorkerStart');
        // 儲存使用者的回撥，當對應的事件發生時觸發
        $this->_onConnect = $this->onConnect;
        $this->onConnect  = array($this, 'onClientConnect');

        // onMessage禁止使用者設定回撥
        $this->onMessage = array($this, 'onClientMessage');

        // 儲存使用者的回撥，當對應的事件發生時觸發
        $this->_onClose = $this->onClose;
        $this->onClose  = array($this, 'onClientClose');
        // 儲存使用者的回撥，當對應的事件發生時觸發
        $this->_onWorkerStop = $this->onWorkerStop;
        $this->onWorkerStop  = array($this, 'onWorkerStop');

        if (!is_array($this->registerAddress)) {
            $this->registerAddress = array($this->registerAddress);
        }

        // 記錄程序啟動的時間
        $this->_startTime = time();
        // 執行父方法
        parent::run();
    }

    /**
     * 當客戶端發來資料時，轉發給worker處理
     *
     * @param TcpConnection $connection
     * @param mixed         $data
     */
    public function onClientMessage($connection, $data)
    {
        $connection->pingNotResponseCount = -1;
        $this->sendToWorker(GatewayProtocol::CMD_ON_MESSAGE, $connection, $data);
    }

    /**
     * 當客戶端連線上來時，初始化一些客戶端的資料
     * 包括全域性唯一的client_id、初始化session等
     *
     * @param TcpConnection $connection
     */
    public function onClientConnect($connection)
    {
        $connection->id = self::generateConnectionId();
        // 儲存該連線的內部通訊的資料包報頭，避免每次重新初始化
        $connection->gatewayHeader = array(
            'local_ip'      => ip2long($this->lanIp),
            'local_port'    => $this->lanPort,
            'client_ip'     => ip2long($connection->getRemoteIp()),
            'client_port'   => $connection->getRemotePort(),
            'gateway_port'  => $this->_gatewayPort,
            'connection_id' => $connection->id,
            'flag'          => 0,
        );
        // 連線的 session
        $connection->session                       = '';
        // 該連線的心跳引數
        $connection->pingNotResponseCount          = -1;
        // 該連結傳送緩衝區大小
        $connection->maxSendBufferSize             = $this->sendToClientBufferSize;
        // 儲存客戶端連線 connection 物件
        $this->_clientConnections[$connection->id] = $connection;

        // 如果使用者有自定義 onConnect 回撥，則執行
        if ($this->_onConnect) {
            call_user_func($this->_onConnect, $connection);
            if (isset($connection->onWebSocketConnect)) {
                $connection->_onWebSocketConnect = $connection->onWebSocketConnect;
            }
        }
        if ($connection->protocol === '\Workerman\Protocols\Websocket' || $connection->protocol === 'Workerman\Protocols\Websocket') {
            $connection->onWebSocketConnect = array($this, 'onWebsocketConnect');
        }

        $this->sendToWorker(GatewayProtocol::CMD_ON_CONNECT, $connection);
    }

    /**
     * websocket握手時觸發
     *
     * @param $connection
     * @param $http_buffer
     */
    public function onWebsocketConnect($connection, $http_buffer)
    {
        if (isset($connection->_onWebSocketConnect)) {
            call_user_func($connection->_onWebSocketConnect, $connection, $http_buffer);
            unset($connection->_onWebSocketConnect);
        }
        $this->sendToWorker(GatewayProtocol::CMD_ON_WEBSOCKET_CONNECT, $connection, array('get' => $_GET, 'server' => $_SERVER, 'cookie' => $_COOKIE));
    }
    
    /**
     * 生成connection id
     * @return int
     */
    protected function generateConnectionId()
    {
        $max_unsigned_int = 4294967295;
        if (self::$_connectionIdRecorder >= $max_unsigned_int) {
            self::$_connectionIdRecorder = 0;
        }
        while(++self::$_connectionIdRecorder <= $max_unsigned_int) {
            if(!isset($this->_clientConnections[self::$_connectionIdRecorder])) {
                break;
            }
        }
        return self::$_connectionIdRecorder;
    }

    /**
     * 傳送資料給 worker 程序
     *
     * @param int           $cmd
     * @param TcpConnection $connection
     * @param mixed         $body
     * @return bool
     */
    protected function sendToWorker($cmd, $connection, $body = '')
    {
        $gateway_data             = $connection->gatewayHeader;
        $gateway_data['cmd']      = $cmd;
        $gateway_data['body']     = $body;
        $gateway_data['ext_data'] = $connection->session;
        if ($this->_workerConnections) {
            // 呼叫路由函式，選擇一個worker把請求轉發給它
            /** @var TcpConnection $worker_connection */
            $worker_connection = call_user_func($this->router, $this->_workerConnections, $connection, $cmd, $body);
            if (false === $worker_connection->send($gateway_data)) {
                $msg = "SendBufferToWorker fail. May be the send buffer are overflow. See http://doc2.workerman.net/send-buffer-overflow.html";
                static::log($msg);
                return false;
            }
        } // 沒有可用的 worker
        else {
            // gateway 啟動後 1-2 秒內 SendBufferToWorker fail 是正常現象，因為與 worker 的連線還沒建立起來，
            // 所以不記錄日誌，只是關閉連線
            $time_diff = 2;
            if (time() - $this->_startTime >= $time_diff) {
                $msg = 'SendBufferToWorker fail. The connections between Gateway and BusinessWorker are not ready. See http://doc2.workerman.net/send-buffer-to-worker-fail.html';
                static::log($msg);
            }
            $connection->destroy();
            return false;
        }
        return true;
    }

    /**
     * 隨機路由，返回 worker connection 物件
     *
     * @param array         $worker_connections
     * @param TcpConnection $client_connection
     * @param int           $cmd
     * @param mixed         $buffer
     * @return TcpConnection
     */
    public static function routerRand($worker_connections, $client_connection, $cmd, $buffer)
    {
        return $worker_connections[array_rand($worker_connections)];
    }

    /**
     * client_id 與 worker 繫結
     *
     * @param array         $worker_connections
     * @param TcpConnection $client_connection
     * @param int           $cmd
     * @param mixed         $buffer
     * @return TcpConnection
     */
    public static function routerBind($worker_connections, $client_connection, $cmd, $buffer)
    {
        if (!isset($client_connection->businessworker_address) || !isset($worker_connections[$client_connection->businessworker_address])) {
            $client_connection->businessworker_address = array_rand($worker_connections);
        }
        return $worker_connections[$client_connection->businessworker_address];
    }

    /**
     * 當客戶端關閉時
     *
     * @param TcpConnection $connection
     */
    public function onClientClose($connection)
    {
        // 嘗試通知 worker，觸發 Event::onClose
        $this->sendToWorker(GatewayProtocol::CMD_ON_CLOSE, $connection);
        unset($this->_clientConnections[$connection->id]);
        // 清理 uid 資料
        if (!empty($connection->uid)) {
            $uid = $connection->uid;
            unset($this->_uidConnections[$uid][$connection->id]);
            if (empty($this->_uidConnections[$uid])) {
                unset($this->_uidConnections[$uid]);
            }
        }
        // 清理 group 資料
        if (!empty($connection->groups)) {
            foreach ($connection->groups as $group) {
                unset($this->_groupConnections[$group][$connection->id]);
                if (empty($this->_groupConnections[$group])) {
                    unset($this->_groupConnections[$group]);
                }
            }
        }
        // 觸發 onClose
        if ($this->_onClose) {
            call_user_func($this->_onClose, $connection);
        }
    }

    /**
     * 當 Gateway 啟動的時候觸發的回撥函式
     *
     * @return void
     */
    public function onWorkerStart()
    {
        // 分配一個內部通訊埠
        $this->lanPort = $this->startPort + $this->id;

        // 如果有設定心跳，則定時執行
        if ($this->pingInterval > 0) {
            $timer_interval = $this->pingNotResponseLimit > 0 ? $this->pingInterval / 2 : $this->pingInterval;
            Timer::add($timer_interval, array($this, 'ping'));
        }

        // 如果BusinessWorker ip不是127.0.0.1，則需要加gateway到BusinessWorker的心跳
        if ($this->lanIp !== '127.0.0.1') {
            Timer::add(self::PERSISTENCE_CONNECTION_PING_INTERVAL, array($this, 'pingBusinessWorker'));
        }

        if (!class_exists('\Protocols\GatewayProtocol')) {
            class_alias('GatewayWorker\Protocols\GatewayProtocol', 'Protocols\GatewayProtocol');
        }

         //如為公網IP監聽，直接換成0.0.0.0 ，否則用內網IP
        $listen_ip=filter_var($this->lanIp,FILTER_VALIDATE_IP,FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)?'0.0.0.0':$this->lanIp;
        // 初始化 gateway 內部的監聽，用於監聽 worker 的連線已經連線上發來的資料
        $this->_innerTcpWorker = new Worker("GatewayProtocol://{$listen_ip}:{$this->lanPort}");
        $this->_innerTcpWorker->reusePort = false;
        $this->_innerTcpWorker->listen();
        $this->_innerTcpWorker->name = 'GatewayInnerWorker';

        // 重新設定自動載入根目錄
        Autoloader::setRootPath($this->_autoloadRootPath);

        // 設定內部監聽的相關回調
        $this->_innerTcpWorker->onMessage = array($this, 'onWorkerMessage');

        $this->_innerTcpWorker->onConnect = array($this, 'onWorkerConnect');
        $this->_innerTcpWorker->onClose   = array($this, 'onWorkerClose');

        // 註冊 gateway 的內部通訊地址，worker 去連這個地址，以便 gateway 與 worker 之間建立起 TCP 長連線
        $this->registerAddress();

        if ($this->_onWorkerStart) {
            call_user_func($this->_onWorkerStart, $this);
        }
    }


    /**
     * 當 worker 透過內部通訊埠連線到 gateway 時
     *
     * @param TcpConnection $connection
     */
    public function onWorkerConnect($connection)
    {
        $connection->maxSendBufferSize = $this->sendToWorkerBufferSize;
        $connection->authorized = $this->secretKey ? false : true;
    }

    /**
     * 當 worker 發來資料時
     *
     * @param TcpConnection $connection
     * @param mixed         $data
     * @throws \Exception
     *
     * @return void
     */
    public function onWorkerMessage($connection, $data)
    {
        $cmd = $data['cmd'];
        if (empty($connection->authorized) && $cmd !== GatewayProtocol::CMD_WORKER_CONNECT && $cmd !== GatewayProtocol::CMD_GATEWAY_CLIENT_CONNECT) {
            self::log("Unauthorized request from " . $connection->getRemoteIp() . ":" . $connection->getRemotePort());
            $connection->close();
            return;
        }
        switch ($cmd) {
            // BusinessWorker連線Gateway
            case GatewayProtocol::CMD_WORKER_CONNECT:
                $worker_info = json_decode($data['body'], true);
                if ($worker_info['secret_key'] !== $this->secretKey) {
                    self::log("Gateway: Worker key does not match ".var_export($this->secretKey, true)." !== ". var_export($this->secretKey));
                    $connection->close();
                    return;
                }
                $key = $connection->getRemoteIp() . ':' . $worker_info['worker_key'];
                // 在一臺伺服器上businessWorker->name不能相同
                if (isset($this->_workerConnections[$key])) {
                    self::log("Gateway: Worker->name conflict. Key:{$key}");
		            $connection->close();
                    return;
                }
		        $connection->key = $key;
                $this->_workerConnections[$key] = $connection;
                $connection->authorized = true;
                if ($this->onBusinessWorkerConnected) {
                    call_user_func($this->onBusinessWorkerConnected, $connection);
                }
                return;
            // GatewayClient連線Gateway
            case GatewayProtocol::CMD_GATEWAY_CLIENT_CONNECT:
                $worker_info = json_decode($data['body'], true);
                if ($worker_info['secret_key'] !== $this->secretKey) {
                    self::log("Gateway: GatewayClient key does not match ".var_export($this->secretKey, true)." !== ".var_export($this->secretKey, true));
                    $connection->close();
                    return;
                }
                $connection->authorized = true;
                return;
            // 向某客戶端傳送資料，Gateway::sendToClient($client_id, $message);
            case GatewayProtocol::CMD_SEND_TO_ONE:
                if (isset($this->_clientConnections[$data['connection_id']])) {
                    $raw = (bool)($data['flag'] & GatewayProtocol::FLAG_NOT_CALL_ENCODE);
                    $body = $data['body'];
                    if (!$raw && $this->protocolAccelerate && $this->protocol) {
                        $body = $this->preEncodeForClient($body);
                        $raw = true;
                    }
                    $this->_clientConnections[$data['connection_id']]->send($body, $raw);
                }
                return;
            // 踢出使用者，Gateway::closeClient($client_id, $message);
            case GatewayProtocol::CMD_KICK:
                if (isset($this->_clientConnections[$data['connection_id']])) {
                    $this->_clientConnections[$data['connection_id']]->close($data['body']);
                }
                return;
            // 立即銷燬使用者連線, Gateway::destroyClient($client_id);
            case GatewayProtocol::CMD_DESTROY:
                if (isset($this->_clientConnections[$data['connection_id']])) {
                    $this->_clientConnections[$data['connection_id']]->destroy();
                }
                return;
            // 廣播, Gateway::sendToAll($message, $client_id_array)
            case GatewayProtocol::CMD_SEND_TO_ALL:
                $raw = (bool)($data['flag'] & GatewayProtocol::FLAG_NOT_CALL_ENCODE);
                $body = $data['body'];
                if (!$raw && $this->protocolAccelerate && $this->protocol) {
                    $body = $this->preEncodeForClient($body);
                    $raw = true;
                }
                $ext_data = $data['ext_data'] ? json_decode($data['ext_data'], true) : '';
                // $client_id_array 不為空時，只廣播給 $client_id_array 指定的客戶端
                if (isset($ext_data['connections'])) {
                    foreach ($ext_data['connections'] as $connection_id) {
                        if (isset($this->_clientConnections[$connection_id])) {
                            $this->_clientConnections[$connection_id]->send($body, $raw);
                        }
                    }
                } // $client_id_array 為空時，廣播給所有線上客戶端
                else {
                    $exclude_connection_id = !empty($ext_data['exclude']) ? $ext_data['exclude'] : null;
                    foreach ($this->_clientConnections as $client_connection) {
                        if (!isset($exclude_connection_id[$client_connection->id])) {
                            $client_connection->send($body, $raw);
                        }
                    }
                }
                return;
            case GatewayProtocol::CMD_SELECT:
                $client_info_array = array();
                $ext_data = json_decode($data['ext_data'], true);
                if (!$ext_data) {
                    echo 'CMD_SELECT ext_data=' . var_export($data['ext_data'], true) . '\r\n';
                    $buffer = serialize($client_info_array);
                    $connection->send(pack('N', strlen($buffer)) . $buffer, true);
                    return;
                }
                $fields = $ext_data['fields'];
                $where  = $ext_data['where'];
                if ($where) {
                    $connection_box_map = array(
                        'groups'        => $this->_groupConnections,
                        'uid'           => $this->_uidConnections
                    );
                    // $where = ['groups'=>[x,x..], 'uid'=>[x,x..], 'connection_id'=>[x,x..]]
                    foreach ($where as $key => $items) {
                        if ($key !== 'connection_id') {
                            $connections_box = $connection_box_map[$key];
                            foreach ($items as $item) {
                                if (isset($connections_box[$item])) {
                                    foreach ($connections_box[$item] as $connection_id => $client_connection) {
                                        if (!isset($client_info_array[$connection_id])) {
                                            $client_info_array[$connection_id] = array();
                                            // $fields = ['groups', 'uid', 'session']
                                            foreach ($fields as $field) {
                                                $client_info_array[$connection_id][$field] = isset($client_connection->$field) ? $client_connection->$field : null;
                                            }
                                        }
                                    }

                                }
                            }
                        } else {
                            foreach ($items as $connection_id) {
                                if (isset($this->_clientConnections[$connection_id])) {
                                    $client_connection = $this->_clientConnections[$connection_id];
                                    $client_info_array[$connection_id] = array();
                                    // $fields = ['groups', 'uid', 'session']
                                    foreach ($fields as $field) {
                                        $client_info_array[$connection_id][$field] = isset($client_connection->$field) ? $client_connection->$field : null;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    foreach ($this->_clientConnections as $connection_id => $client_connection) {
                        foreach ($fields as $field) {
                            $client_info_array[$connection_id][$field] = isset($client_connection->$field) ? $client_connection->$field : null;
                        }
                    }
                }
                $buffer = serialize($client_info_array);
                $connection->send(pack('N', strlen($buffer)) . $buffer, true);
                return;
            // 獲取線上群組列表
            case GatewayProtocol::CMD_GET_GROUP_ID_LIST:
                $buffer = serialize(array_keys($this->_groupConnections));
                $connection->send(pack('N', strlen($buffer)) . $buffer, true);
                return;
            // 重新賦值 session
            case GatewayProtocol::CMD_SET_SESSION:
                if (isset($this->_clientConnections[$data['connection_id']])) {
                    $this->_clientConnections[$data['connection_id']]->session = $data['ext_data'];
                }
                return;
            // session合併
            case GatewayProtocol::CMD_UPDATE_SESSION:
                if (!isset($this->_clientConnections[$data['connection_id']])) {
                    return;
                } else {
                    if (!$this->_clientConnections[$data['connection_id']]->session) {
                        $this->_clientConnections[$data['connection_id']]->session = $data['ext_data'];
                        return;
                    }
                    $session = Context::sessionDecode($this->_clientConnections[$data['connection_id']]->session);
                    $session_for_merge = Context::sessionDecode($data['ext_data']);
                    $session = array_replace_recursive($session, $session_for_merge);
                    $this->_clientConnections[$data['connection_id']]->session = Context::sessionEncode($session);
                }
                return;
            case GatewayProtocol::CMD_GET_SESSION_BY_CLIENT_ID:
                if (!isset($this->_clientConnections[$data['connection_id']])) {
                    $session = serialize(null);
                } else {
                    if (!$this->_clientConnections[$data['connection_id']]->session) {
                        $session = serialize(array());
                    } else {
                        $session = $this->_clientConnections[$data['connection_id']]->session;
                    }
                }
                $connection->send(pack('N', strlen($session)) . $session, true);
                return;
            // 獲得客戶端sessions
            case GatewayProtocol::CMD_GET_ALL_CLIENT_SESSIONS:
                $client_info_array = array();
                foreach ($this->_clientConnections as $connection_id => $client_connection) {
                    $client_info_array[$connection_id] = $client_connection->session;
                }
                $buffer = serialize($client_info_array);
                $connection->send(pack('N', strlen($buffer)) . $buffer, true);
                return;
            // 判斷某個 client_id 是否線上 Gateway::isOnline($client_id)
            case GatewayProtocol::CMD_IS_ONLINE:
                $buffer = serialize((int)isset($this->_clientConnections[$data['connection_id']]));
                $connection->send(pack('N', strlen($buffer)) . $buffer, true);
                return;
            // 將 client_id 與 uid 繫結
            case GatewayProtocol::CMD_BIND_UID:
                $uid = $data['ext_data'];
                if (empty($uid)) {
                    echo "bindUid(client_id, uid) uid empty, uid=" . var_export($uid, true);
                    return;
                }
                $connection_id = $data['connection_id'];
                if (!isset($this->_clientConnections[$connection_id])) {
                    return;
                }
                $client_connection = $this->_clientConnections[$connection_id];
                if (isset($client_connection->uid)) {
                    $current_uid = $client_connection->uid;
                    unset($this->_uidConnections[$current_uid][$connection_id]);
                    if (empty($this->_uidConnections[$current_uid])) {
                        unset($this->_uidConnections[$current_uid]);
                    }
                }
                $client_connection->uid                      = $uid;
                $this->_uidConnections[$uid][$connection_id] = $client_connection;
                return;
            // client_id 與 uid 解綁 Gateway::unbindUid($client_id, $uid);
            case GatewayProtocol::CMD_UNBIND_UID:
                $connection_id = $data['connection_id'];
                if (!isset($this->_clientConnections[$connection_id])) {
                    return;
                }
                $client_connection = $this->_clientConnections[$connection_id];
                if (isset($client_connection->uid)) {
                    $current_uid = $client_connection->uid;
                    unset($this->_uidConnections[$current_uid][$connection_id]);
                    if (empty($this->_uidConnections[$current_uid])) {
                        unset($this->_uidConnections[$current_uid]);
                    }
                    $client_connection->uid_info = '';
                    $client_connection->uid      = null;
                }
                return;
            // 傳送資料給 uid Gateway::sendToUid($uid, $msg);
            case GatewayProtocol::CMD_SEND_TO_UID:
                $raw = (bool)($data['flag'] & GatewayProtocol::FLAG_NOT_CALL_ENCODE);
                $body = $data['body'];
                if (!$raw && $this->protocolAccelerate && $this->protocol) {
                    $body = $this->preEncodeForClient($body);
                    $raw = true;
                }
                $uid_array = json_decode($data['ext_data'], true);
                foreach ($uid_array as $uid) {
                    if (!empty($this->_uidConnections[$uid])) {
                        foreach ($this->_uidConnections[$uid] as $connection) {
                            /** @var TcpConnection $connection */
                            $connection->send($body, $raw);
                        }
                    }
                }
                return;
            // 將 $client_id 加入使用者組 Gateway::joinGroup($client_id, $group);
            case GatewayProtocol::CMD_JOIN_GROUP:
                $group = $data['ext_data'];
                if (empty($group)) {
                    echo "join(group) group empty, group=" . var_export($group, true);
                    return;
                }
                $connection_id = $data['connection_id'];
                if (!isset($this->_clientConnections[$connection_id])) {
                    return;
                }
                $client_connection = $this->_clientConnections[$connection_id];
                if (!isset($client_connection->groups)) {
                    $client_connection->groups = array();
                }
                $client_connection->groups[$group]               = $group;
                $this->_groupConnections[$group][$connection_id] = $client_connection;
                return;
            // 將 $client_id 從某個使用者組中移除 Gateway::leaveGroup($client_id, $group);
            case GatewayProtocol::CMD_LEAVE_GROUP:
                $group = $data['ext_data'];
                if (empty($group)) {
                    echo "leave(group) group empty, group=" . var_export($group, true);
                    return;
                }
                $connection_id = $data['connection_id'];
                if (!isset($this->_clientConnections[$connection_id])) {
                    return;
                }
                $client_connection = $this->_clientConnections[$connection_id];
                if (!isset($client_connection->groups[$group])) {
                    return;
                }
                unset($client_connection->groups[$group], $this->_groupConnections[$group][$connection_id]);
                if (empty($this->_groupConnections[$group])) {
                    unset($this->_groupConnections[$group]);
                }
                return;
            // 解散分組
            case GatewayProtocol::CMD_UNGROUP:
                $group = $data['ext_data'];
                if (empty($group)) {
                    echo "leave(group) group empty, group=" . var_export($group, true);
                    return;
                }
                if (empty($this->_groupConnections[$group])) {
                    return;
                }
                foreach ($this->_groupConnections[$group] as $client_connection) {
                    unset($client_connection->groups[$group]);
                }
                unset($this->_groupConnections[$group]);
                return;
            // 向某個使用者組傳送訊息 Gateway::sendToGroup($group, $msg);
            case GatewayProtocol::CMD_SEND_TO_GROUP:
                $raw = (bool)($data['flag'] & GatewayProtocol::FLAG_NOT_CALL_ENCODE);
                $body = $data['body'];
                if (!$raw && $this->protocolAccelerate && $this->protocol) {
                    $body = $this->preEncodeForClient($body);
                    $raw = true;
                }
                $ext_data = json_decode($data['ext_data'], true);
                $group_array = $ext_data['group'];
                $exclude_connection_id = $ext_data['exclude'];

                foreach ($group_array as $group) {
                    if (!empty($this->_groupConnections[$group])) {
                        foreach ($this->_groupConnections[$group] as $connection) {
                            if(!isset($exclude_connection_id[$connection->id]))
                            {
                                /** @var TcpConnection $connection */
                                $connection->send($body, $raw);
                            }
                        }
                    }
                }
                return;
            // 獲取某使用者組成員資訊 Gateway::getClientSessionsByGroup($group);
            case GatewayProtocol::CMD_GET_CLIENT_SESSIONS_BY_GROUP:
                $group = $data['ext_data'];
                if (!isset($this->_groupConnections[$group])) {
                    $buffer = serialize(array());
                    $connection->send(pack('N', strlen($buffer)) . $buffer, true);
                    return;
                }
                $client_info_array = array();
                foreach ($this->_groupConnections[$group] as $connection_id => $client_connection) {
                    $client_info_array[$connection_id] = $client_connection->session;
                }
                $buffer = serialize($client_info_array);
                $connection->send(pack('N', strlen($buffer)) . $buffer, true);
                return;
            // 獲取使用者組成員數 Gateway::getClientCountByGroup($group);
            case GatewayProtocol::CMD_GET_CLIENT_COUNT_BY_GROUP:
                $group = $data['ext_data'];
                $count = 0;
                if ($group !== '') {
                    if (isset($this->_groupConnections[$group])) {
                        $count = count($this->_groupConnections[$group]);
                    }
                } else {
                    $count = count($this->_clientConnections);
                }
                $buffer = serialize($count);
                $connection->send(pack('N', strlen($buffer)) . $buffer, true);
                return;
            // 獲取與某個 uid 繫結的所有 client_id Gateway::getClientIdByUid($uid);
            case GatewayProtocol::CMD_GET_CLIENT_ID_BY_UID:
                $uid = $data['ext_data'];
                if (empty($this->_uidConnections[$uid])) {
                    $buffer = serialize(array());
                } else {
                    $buffer = serialize(array_keys($this->_uidConnections[$uid]));
                }
                $connection->send(pack('N', strlen($buffer)) . $buffer, true);
                return;
            default :
                $err_msg = "gateway inner pack err cmd=$cmd";
                echo $err_msg;
        }
    }


    /**
     * 當worker連線關閉時
     *
     * @param TcpConnection $connection
     */
    public function onWorkerClose($connection)
    {
        if (isset($connection->key)) {
            unset($this->_workerConnections[$connection->key]);
            if ($this->onBusinessWorkerClose) {
                call_user_func($this->onBusinessWorkerClose, $connection);
            }
        }
    }

    /**
     * 儲存當前 Gateway 的內部通訊地址
     *
     * @return bool
     */
    public function registerAddress()
    {
        $address = $this->lanIp . ':' . $this->lanPort;
        foreach ($this->registerAddress as $register_address) {
            $register_connection = new AsyncTcpConnection("text://{$register_address}");
            $secret_key = $this->secretKey;
            $register_connection->onConnect = function($register_connection) use ($address, $secret_key, $register_address){
                $register_connection->send('{"event":"gateway_connect", "address":"' . $address . '", "secret_key":"' . $secret_key . '"}');
                // 如果Register伺服器不在本地伺服器，則需要保持心跳
                if (strpos($register_address, '127.0.0.1') !== 0) {
                    $register_connection->ping_timer = Timer::add(self::PERSISTENCE_CONNECTION_PING_INTERVAL, function () use ($register_connection) {
                        $register_connection->send('{"event":"ping"}');
                    });
                }
            };
            $register_connection->onClose = function ($register_connection) {
                if(!empty($register_connection->ping_timer)) {
                    Timer::del($register_connection->ping_timer);
                }
                $register_connection->reconnect(1);
            };
            $register_connection->connect();
        }
    }


    /**
     * 心跳邏輯
     *
     * @return void
     */
    public function ping()
    {
        $ping_data = $this->pingData ? (string)$this->pingData : null;
        $raw = false;
        if ($this->protocolAccelerate && $ping_data && $this->protocol) {
            $ping_data = $this->preEncodeForClient($ping_data);
            $raw = true;
        }
        // 遍歷所有客戶端連線
        foreach ($this->_clientConnections as $connection) {
            // 上次傳送的心跳還沒有回覆次數大於限定值就斷開
            if ($this->pingNotResponseLimit > 0 &&
                $connection->pingNotResponseCount >= $this->pingNotResponseLimit * 2
            ) {
                $connection->destroy();
                continue;
            }
            // $connection->pingNotResponseCount 為 -1 說明最近客戶端有發來訊息，則不給客戶端傳送心跳
            $connection->pingNotResponseCount++;
            if ($ping_data) {
                if ($connection->pingNotResponseCount === 0 ||
                    ($this->pingNotResponseLimit > 0 && $connection->pingNotResponseCount % 2 === 1)
                ) {
                    continue;
                }
                $connection->send($ping_data, $raw);
            }
        }
    }

    /**
     * 向 BusinessWorker 傳送心跳資料，用於保持長連線
     *
     * @return void
     */
    public function pingBusinessWorker()
    {
        $gateway_data        = GatewayProtocol::$empty;
        $gateway_data['cmd'] = GatewayProtocol::CMD_PING;
        foreach ($this->_workerConnections as $connection) {
            $connection->send($gateway_data);
        }
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    protected function preEncodeForClient($data)
    {
        foreach ($this->_clientConnections as $client_connection) {
            return call_user_func(array($client_connection->protocol, 'encode'), $data, $client_connection);
        }
    }

    /**
     * 當 gateway 關閉時觸發，清理資料
     *
     * @return void
     */
    public function onWorkerStop()
    {
        // 嘗試觸發使用者設定的回撥
        if ($this->_onWorkerStop) {
            call_user_func($this->_onWorkerStop, $this);
        }
    }

    /**
     * Log.
     * @param string $msg
     */
    public static function log($msg){
        Timer::add(1, function() use ($msg) {
            Worker::log($msg);
        }, null, false);
    }
}
