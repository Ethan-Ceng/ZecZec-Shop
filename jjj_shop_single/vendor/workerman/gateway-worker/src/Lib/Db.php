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

use Config\Db as DbConfig;
use Exception;

/**
 * 資料庫類
 */
class Db
{
    /**
     * 例項陣列
     *
     * @var array
     */
    protected static $instance = array();

    /**
     * 獲取例項
     *
     * @param string $config_name
     * @return DbConnection
     * @throws Exception
     */
    public static function instance($config_name)
    {
        if (!isset(DbConfig::$$config_name)) {
            echo "\\Config\\Db::$config_name not set\n";
            throw new Exception("\\Config\\Db::$config_name not set\n");
        }

        if (empty(self::$instance[$config_name])) {
            $config                       = DbConfig::$$config_name;
            self::$instance[$config_name] = new DbConnection($config['host'], $config['port'],
                $config['user'], $config['password'], $config['dbname'],$config['charset']);
        }
        return self::$instance[$config_name];
    }

    /**
     * 關閉資料庫例項
     *
     * @param string $config_name
     */
    public static function close($config_name)
    {
        if (isset(self::$instance[$config_name])) {
            self::$instance[$config_name]->closeConnection();
            self::$instance[$config_name] = null;
        }
    }

    /**
     * 關閉所有資料庫例項
     */
    public static function closeAll()
    {
        foreach (self::$instance as $connection) {
            $connection->closeConnection();
        }
        self::$instance = array();
    }
}
