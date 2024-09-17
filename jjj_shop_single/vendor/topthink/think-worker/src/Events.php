<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace think\worker;

use GatewayWorker\Lib\Gateway;
use Workerman\Worker;

/**
 * Worker 命令列服務類
 */
class Events
{
    /**
     * onWorkerStart 事件回撥
     * 當businessWorker程序啟動時觸發。每個程序生命週期內都只會觸發一次
     *
     * @access public
     * @param  \Workerman\Worker    $businessWorker
     * @return void
     */
    public static function onWorkerStart(Worker $businessWorker)
    {
        $app = new Application;
        $app->initialize();
    }

    /**
     * onConnect 事件回撥
     * 當客戶端連線上gateway程序時(TCP三次握手完畢時)觸發
     *
     * @access public
     * @param  int       $client_id
     * @return void
     */
    public static function onConnect($client_id)
    {
        Gateway::sendToCurrentClient("Your client_id is $client_id");
    }

    /**
     * onWebSocketConnect 事件回撥
     * 當客戶端連線上gateway完成websocket握手時觸發
     *
     * @param  integer  $client_id 斷開連線的客戶端client_id
     * @param  mixed    $data
     * @return void
     */
    public static function onWebSocketConnect($client_id, $data)
    {
        var_export($data);
    }

    /**
     * onMessage 事件回撥
     * 當客戶端發來資料(Gateway程序收到資料)後觸發
     *
     * @access public
     * @param  int       $client_id
     * @param  mixed     $data
     * @return void
     */
    public static function onMessage($client_id, $data)
    {
        Gateway::sendToAll($data);
    }

    /**
     * onClose 事件回撥 當用戶斷開連線時觸發的方法
     *
     * @param  integer $client_id 斷開連線的客戶端client_id
     * @return void
     */
    public static function onClose($client_id)
    {
        GateWay::sendToAll("client[$client_id] logout\n");
    }

    /**
     * onWorkerStop 事件回撥
     * 當businessWorker程序退出時觸發。每個程序生命週期內都只會觸發一次。
     *
     * @param  \Workerman\Worker    $businessWorker
     * @return void
     */
    public static function onWorkerStop(Worker $businessWorker)
    {
        echo "WorkerStop\n";
    }
}
