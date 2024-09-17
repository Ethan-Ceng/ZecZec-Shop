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
namespace GatewayWorker\Protocols;

/**
 * Gateway 與 Worker 間通訊的二進位制協議
 *
 * struct GatewayProtocol
 * {
 *     unsigned int        pack_len,
 *     unsigned char       cmd,//命令字
 *     unsigned int        local_ip,
 *     unsigned short      local_port,
 *     unsigned int        client_ip,
 *     unsigned short      client_port,
 *     unsigned int        connection_id,
 *     unsigned char       flag,
 *     unsigned short      gateway_port,
 *     unsigned int        ext_len,
 *     char[ext_len]       ext_data,
 *     char[pack_length-HEAD_LEN] body//包體
 * }
 * NCNnNnNCnN
 */
class GatewayProtocol
{
    // 發給worker，gateway有一個新的連線
    const CMD_ON_CONNECT = 1;

    // 發給worker的，客戶端有訊息
    const CMD_ON_MESSAGE = 3;

    // 發給worker上的關閉連結事件
    const CMD_ON_CLOSE = 4;

    // 發給gateway的向單個使用者傳送資料
    const CMD_SEND_TO_ONE = 5;

    // 發給gateway的向所有使用者傳送資料
    const CMD_SEND_TO_ALL = 6;

    // 發給gateway的踢出使用者
    // 1、如果有待發訊息，將在傳送完後立即銷燬使用者連線
    // 2、如果無待發訊息，將立即銷燬使用者連線
    const CMD_KICK = 7;

    // 發給gateway的立即銷燬使用者連線
    const CMD_DESTROY = 8;

    // 發給gateway，通知使用者session更新
    const CMD_UPDATE_SESSION = 9;

    // 獲取線上狀態
    const CMD_GET_ALL_CLIENT_SESSIONS = 10;

    // 判斷是否線上
    const CMD_IS_ONLINE = 11;

    // client_id繫結到uid
    const CMD_BIND_UID = 12;

    // 解綁
    const CMD_UNBIND_UID = 13;

    // 向uid傳送資料
    const CMD_SEND_TO_UID = 14;

    // 根據uid獲取繫結的clientid
    const CMD_GET_CLIENT_ID_BY_UID = 15;

    // 加入組
    const CMD_JOIN_GROUP = 20;

    // 離開組
    const CMD_LEAVE_GROUP = 21;

    // 向組成員發訊息
    const CMD_SEND_TO_GROUP = 22;

    // 獲取組成員
    const CMD_GET_CLIENT_SESSIONS_BY_GROUP = 23;

    // 獲取組線上連線數
    const CMD_GET_CLIENT_COUNT_BY_GROUP = 24;

    // 按照條件查詢
    const CMD_SELECT = 25;

    // 獲取線上的群組ID
    const CMD_GET_GROUP_ID_LIST = 26;

    // 取消分組
    const CMD_UNGROUP = 27;

    // worker連線gateway事件
    const CMD_WORKER_CONNECT = 200;

    // 心跳
    const CMD_PING = 201;

    // GatewayClient連線gateway事件
    const CMD_GATEWAY_CLIENT_CONNECT = 202;

    // 根據client_id獲取session
    const CMD_GET_SESSION_BY_CLIENT_ID = 203;

    // 發給gateway，覆蓋session
    const CMD_SET_SESSION = 204;

    // 當websocket握手時觸發，只有websocket協議支援此命令字
    const CMD_ON_WEBSOCKET_CONNECT = 205;

    // 包體是標量
    const FLAG_BODY_IS_SCALAR = 0x01;

    // 通知gateway在send時不呼叫協議encode方法，在廣播組播時提升效能
    const FLAG_NOT_CALL_ENCODE = 0x02;

    /**
     * 包頭長度
     *
     * @var int
     */
    const HEAD_LEN = 28;

    public static $empty = array(
        'cmd'           => 0,
        'local_ip'      => 0,
        'local_port'    => 0,
        'client_ip'     => 0,
        'client_port'   => 0,
        'connection_id' => 0,
        'flag'          => 0,
        'gateway_port'  => 0,
        'ext_data'      => '',
        'body'          => '',
    );

    /**
     * 返回包長度
     *
     * @param string $buffer
     * @return int return current package length
     */
    public static function input($buffer)
    {
        if (strlen($buffer) < self::HEAD_LEN) {
            return 0;
        }

        $data = unpack("Npack_len", $buffer);
        return $data['pack_len'];
    }

    /**
     * 獲取整個包的 buffer
     *
     * @param mixed $data
     * @return string
     */
    public static function encode($data)
    {
        $flag = (int)is_scalar($data['body']);
        if (!$flag) {
            $data['body'] = serialize($data['body']);
        }
        $data['flag'] |= $flag;
        $ext_len      = strlen($data['ext_data']);
        $package_len  = self::HEAD_LEN + $ext_len + strlen($data['body']);
        return pack("NCNnNnNCnN", $package_len,
            $data['cmd'], $data['local_ip'],
            $data['local_port'], $data['client_ip'],
            $data['client_port'], $data['connection_id'],
            $data['flag'], $data['gateway_port'],
            $ext_len) . $data['ext_data'] . $data['body'];
    }

    /**
     * 從二進位制資料轉換為陣列
     *
     * @param string $buffer
     * @return array
     */
    public static function decode($buffer)
    {
        $data = unpack("Npack_len/Ccmd/Nlocal_ip/nlocal_port/Nclient_ip/nclient_port/Nconnection_id/Cflag/ngateway_port/Next_len",
            $buffer);
        if ($data['ext_len'] > 0) {
            $data['ext_data'] = substr($buffer, self::HEAD_LEN, $data['ext_len']);
            if ($data['flag'] & self::FLAG_BODY_IS_SCALAR) {
                $data['body'] = substr($buffer, self::HEAD_LEN + $data['ext_len']);
            } else {
                $data['body'] = unserialize(substr($buffer, self::HEAD_LEN + $data['ext_len']));
            }
        } else {
            $data['ext_data'] = '';
            if ($data['flag'] & self::FLAG_BODY_IS_SCALAR) {
                $data['body'] = substr($buffer, self::HEAD_LEN);
            } else {
                $data['body'] = unserialize(substr($buffer, self::HEAD_LEN));
            }
        }
        return $data;
    }
}
