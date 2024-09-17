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

/**
 * 上下文 包含當前使用者 uid， 內部通訊 local_ip local_port socket_id，以及客戶端 client_ip client_port
 */
class Context
{
    /**
     * 內部通訊 id
     *
     * @var string
     */
    public static $local_ip;

    /**
     * 內部通訊埠
     *
     * @var int
     */
    public static $local_port;

    /**
     * 客戶端 ip
     *
     * @var string
     */
    public static $client_ip;

    /**
     * 客戶端埠
     *
     * @var int
     */
    public static $client_port;

    /**
     * client_id
     *
     * @var string
     */
    public static $client_id;

    /**
     * 連線 connection->id
     *
     * @var int
     */
    public static $connection_id;
    
    /**
     * 舊的session
     * 
     * @var string
     */
    public static $old_session;

    /**
     * 編碼 session
     *
     * @param mixed $session_data
     * @return string
     */
    public static function sessionEncode($session_data = '')
    {
        if ($session_data !== '') {
            return serialize($session_data);
        }
        return '';
    }

    /**
     * 解碼 session
     *
     * @param string $session_buffer
     * @return mixed
     */
    public static function sessionDecode($session_buffer)
    {
        return unserialize($session_buffer);
    }

    /**
     * 清除上下文
     *
     * @return void
     */
    public static function clear()
    {
        self::$local_ip = self::$local_port = self::$client_ip = self::$client_port =
        self::$client_id = self::$connection_id  = self::$old_session = null;
    }

    /**
     * 通訊地址到 client_id 的轉換
     *
     * @param int $local_ip
     * @param int $local_port
     * @param int $connection_id
     * @return string
     */
    public static function addressToClientId($local_ip, $local_port, $connection_id)
    {
        return bin2hex(pack('NnN', $local_ip, $local_port, $connection_id));
    }

    /**
     * client_id 到通訊地址的轉換
     *
     * @param string $client_id
     * @return array
     * @throws Exception
     */
    public static function clientIdToAddress($client_id)
    {
        if (strlen($client_id) !== 20) {
            echo new Exception("client_id $client_id is invalid");
            return false;
        }
        return unpack('Nlocal_ip/nlocal_port/Nconnection_id', pack('H*', $client_id));
    }
}
