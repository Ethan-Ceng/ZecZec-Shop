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

use Workerman\Worker;

/**
 * Worker控制器擴充套件類
 */
abstract class Server
{
    protected $worker;
    protected $socket   = '';
    protected $protocol = 'http';
    protected $host     = '0.0.0.0';
    protected $port     = '2346';
    protected $option   = [];
    protected $context  = [];
    protected $event    = ['onWorkerStart', 'onConnect', 'onMessage', 'onClose', 'onError', 'onBufferFull', 'onBufferDrain', 'onWorkerReload', 'onWebSocketConnect'];

    /**
     * 架構函式
     * @access public
     */
    public function __construct()
    {
        // 例項化 Websocket 服務
        $this->worker = new Worker($this->socket ?: $this->protocol . '://' . $this->host . ':' . $this->port, $this->context);

        // 設定引數
        if (!empty($this->option)) {
            foreach ($this->option as $key => $val) {
                $this->worker->$key = $val;
            }
        }

        // 設定回撥
        foreach ($this->event as $event) {
            if (method_exists($this, $event)) {
                $this->worker->$event = [$this, $event];
            }
        }

        // 初始化
        $this->init();
    }

    protected function init()
    {
    }

    public function start()
    {
        Worker::runAll();
    }

    public function __set($name, $value)
    {
        $this->worker->$name = $value;
    }

    public function __call($method, $args)
    {
        call_user_func_array([$this->worker, $method], $args);
    }
}
