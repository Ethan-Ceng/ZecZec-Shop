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

use Workerman\Connection\TcpConnection;

use Workerman\Worker;
use Workerman\Lib\Timer;
use Workerman\Connection\AsyncTcpConnection;
use GatewayWorker\Protocols\GatewayProtocol;
use GatewayWorker\Lib\Context;

/**
 *
 * BusinessWorker 用於處理Gateway轉發來的資料
 *
 * @author walkor<walkor@workerman.net>
 *
 */
class BusinessWorker extends Worker
{
    /**
     * 儲存與 gateway 的連線 connection 物件
     *
     * @var array
     */
    public $gatewayConnections = array();

    /**
     * 註冊中心地址
     *
     * @var string|array
     */
    public $registerAddress = '127.0.0.1:1236';

    /**
     * 事件處理類，預設是 Event 類
     *
     * @var string
     */
    public $eventHandler = 'Events';

    /**
     * 業務超時時間，可用來定位程式卡在哪裡
     *
     * @var int
     */
    public $processTimeout = 30;

    /**
     * 業務超時時間，可用來定位程式卡在哪裡
     *
     * @var callable
     */
    public $processTimeoutHandler = '\\Workerman\\Worker::log';
    
    /**
     * 秘鑰
     *
     * @var string
     */
    public $secretKey = '';

    /**
     * businessWorker程序將訊息轉發給gateway程序的傳送緩衝區大小
     *
     * @var int
     */
    public $sendToGatewayBufferSize = 10240000;

    /**
     * 儲存使用者設定的 worker 啟動回撥
     *
     * @var callback
     */
    protected $_onWorkerStart = null;

    /**
     * 儲存使用者設定的 workerReload 回撥
     *
     * @var callback
     */
    protected $_onWorkerReload = null;
    
    /**
     * 儲存使用者設定的 workerStop 回撥
     *
     * @var callback
     */
    protected $_onWorkerStop= null;

    /**
     * 到註冊中心的連線
     *
     * @var AsyncTcpConnection
     */
    protected $_registerConnection = null;

    /**
     * 處於連線狀態的 gateway 通訊地址
     *
     * @var array
     */
    protected $_connectingGatewayAddresses = array();

    /**
     * 所有 geteway 內部通訊地址
     *
     * @var array
     */
    protected $_gatewayAddresses = array();

    /**
     * 等待連線個 gateway 地址
     *
     * @var array
     */
    protected $_waitingConnectGatewayAddresses = array();

    /**
     * Event::onConnect 回撥
     *
     * @var callback
     */
    protected $_eventOnConnect = null;

    /**
     * Event::onMessage 回撥
     *
     * @var callback
     */
    protected $_eventOnMessage = null;

    /**
     * Event::onClose 回撥
     *
     * @var callback
     */
    protected $_eventOnClose = null;

    /**
     * websocket回撥
     *
     * @var null
     */
    protected $_eventOnWebSocketConnect = null;

    /**
     * SESSION 版本快取
     *
     * @var array
     */
    protected $_sessionVersion = array();

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
    public function __construct($socket_name = '', $context_option = array())
    {
        parent::__construct($socket_name, $context_option);
        $backrace                = debug_backtrace();
        $this->_autoloadRootPath = dirname($backrace[0]['file']);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->_onWorkerStart  = $this->onWorkerStart;
        $this->_onWorkerReload = $this->onWorkerReload;
        $this->_onWorkerStop = $this->onWorkerStop;
        $this->onWorkerStop   = array($this, 'onWorkerStop');
        $this->onWorkerStart   = array($this, 'onWorkerStart');
        $this->onWorkerReload  = array($this, 'onWorkerReload');
        parent::run();
    }

    /**
     * 當程序啟動時一些初始化工作
     *
     * @return void
     */
    protected function onWorkerStart()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        if (!class_exists('\Protocols\GatewayProtocol')) {
            class_alias('GatewayWorker\Protocols\GatewayProtocol', 'Protocols\GatewayProtocol');
        }

        if (!is_array($this->registerAddress)) {
            $this->registerAddress = array($this->registerAddress);
        }
        $this->connectToRegister();

        \GatewayWorker\Lib\Gateway::setBusinessWorker($this);
        \GatewayWorker\Lib\Gateway::$secretKey = $this->secretKey;
        if ($this->_onWorkerStart) {
            call_user_func($this->_onWorkerStart, $this);
        }
        
        if (is_callable($this->eventHandler . '::onWorkerStart')) {
            call_user_func($this->eventHandler . '::onWorkerStart', $this);
        }

        if (function_exists('pcntl_signal')) {
            // 業務超時訊號處理
            pcntl_signal(SIGALRM, array($this, 'timeoutHandler'), false);
        } else {
            $this->processTimeout = 0;
        }

        // 設定回撥
        if (is_callable($this->eventHandler . '::onConnect')) {
            $this->_eventOnConnect = $this->eventHandler . '::onConnect';
        }

        if (is_callable($this->eventHandler . '::onMessage')) {
            $this->_eventOnMessage = $this->eventHandler . '::onMessage';
        } else {
            echo "Waring: {$this->eventHandler}::onMessage is not callable\n";
        }

        if (is_callable($this->eventHandler . '::onClose')) {
            $this->_eventOnClose = $this->eventHandler . '::onClose';
        }

        if (is_callable($this->eventHandler . '::onWebSocketConnect')) {
            $this->_eventOnWebSocketConnect = $this->eventHandler . '::onWebSocketConnect';
        }

    }

    /**
     * onWorkerReload 回撥
     *
     * @param Worker $worker
     */
    protected function onWorkerReload($worker)
    {
        // 防止程序立刻退出
        $worker->reloadable = false;
        // 延遲 0.05 秒退出，避免 BusinessWorker 瞬間全部退出導致沒有可用的 BusinessWorker 程序
        Timer::add(0.05, array('Workerman\Worker', 'stopAll'));
        // 執行使用者定義的 onWorkerReload 回撥
        if ($this->_onWorkerReload) {
            call_user_func($this->_onWorkerReload, $this);
        }
    }
    
    /**
     * 當程序關閉時一些清理工作
     *
     * @return void
     */
    protected function onWorkerStop()
    {
        if ($this->_onWorkerStop) {
            call_user_func($this->_onWorkerStop, $this);
        }
        if (is_callable($this->eventHandler . '::onWorkerStop')) {
            call_user_func($this->eventHandler . '::onWorkerStop', $this);
        }
    }

    /**
     * 連線服務註冊中心
     * 
     * @return void
     */
    public function connectToRegister()
    {
        foreach ($this->registerAddress as $register_address) {
            $register_connection = new AsyncTcpConnection("text://{$register_address}");
            $secret_key = $this->secretKey;
            $register_connection->onConnect = function () use ($register_connection, $secret_key, $register_address) {
                $register_connection->send('{"event":"worker_connect","secret_key":"' . $secret_key . '"}');
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
            $register_connection->onMessage = array($this, 'onRegisterConnectionMessage');
            $register_connection->connect();
        }
    }


    /**
     * 當註冊中心發來訊息時
     *
     * @return void
     */
    public function onRegisterConnectionMessage($register_connection, $data)
    {
        $data = json_decode($data, true);
        if (!isset($data['event'])) {
            echo "Received bad data from Register\n";
            return;
        }
        $event = $data['event'];
        switch ($event) {
            case 'broadcast_addresses':
                if (!is_array($data['addresses'])) {
                    echo "Received bad data from Register. Addresses empty\n";
                    return;
                }
                $addresses               = $data['addresses'];
                $this->_gatewayAddresses = array();
                foreach ($addresses as $addr) {
                    $this->_gatewayAddresses[$addr] = $addr;
                }
                $this->checkGatewayConnections($addresses);
                break;
            default:
                echo "Receive bad event:$event from Register.\n";
        }
    }

    /**
     * 當 gateway 轉發來資料時
     *
     * @param TcpConnection $connection
     * @param mixed         $data
     */
    public function onGatewayMessage($connection, $data)
    {
        $cmd = $data['cmd'];
        if ($cmd === GatewayProtocol::CMD_PING) {
            return;
        }
        // 上下文資料
        Context::$client_ip     = $data['client_ip'];
        Context::$client_port   = $data['client_port'];
        Context::$local_ip      = $data['local_ip'];
        Context::$local_port    = $data['local_port'];
        Context::$connection_id = $data['connection_id'];
        Context::$client_id     = Context::addressToClientId($data['local_ip'], $data['local_port'],
            $data['connection_id']);
        // $_SERVER 變數
        $_SERVER = array(
            'REMOTE_ADDR'       => long2ip($data['client_ip']),
            'REMOTE_PORT'       => $data['client_port'],
            'GATEWAY_ADDR'      => long2ip($data['local_ip']),
            'GATEWAY_PORT'      => $data['gateway_port'],
            'GATEWAY_CLIENT_ID' => Context::$client_id,
        );
        // 檢查session版本，如果是過期的session資料則拉取最新的資料
        if ($cmd !== GatewayProtocol::CMD_ON_CLOSE && isset($this->_sessionVersion[Context::$client_id]) && $this->_sessionVersion[Context::$client_id] !== crc32($data['ext_data'])) {
            $_SESSION = Context::$old_session = \GatewayWorker\Lib\Gateway::getSession(Context::$client_id);
            $this->_sessionVersion[Context::$client_id] = crc32($data['ext_data']);
        } else {
            if (!isset($this->_sessionVersion[Context::$client_id])) {
                $this->_sessionVersion[Context::$client_id] = crc32($data['ext_data']);
            }
            // 嘗試解析 session
            if ($data['ext_data'] != '') {
                Context::$old_session = $_SESSION = Context::sessionDecode($data['ext_data']);
            } else {
                Context::$old_session = $_SESSION = null;
            }
        }

        if ($this->processTimeout) {
            pcntl_alarm($this->processTimeout);
        }
        // 嘗試執行 Event::onConnection、Event::onMessage、Event::onClose
        switch ($cmd) {
            case GatewayProtocol::CMD_ON_CONNECT:
                if ($this->_eventOnConnect) {
                    call_user_func($this->_eventOnConnect, Context::$client_id);
                }
                break;
            case GatewayProtocol::CMD_ON_MESSAGE:
                if ($this->_eventOnMessage) {
                    call_user_func($this->_eventOnMessage, Context::$client_id, $data['body']);
                }
                break;
            case GatewayProtocol::CMD_ON_CLOSE:
                unset($this->_sessionVersion[Context::$client_id]);
                if ($this->_eventOnClose) {
                    call_user_func($this->_eventOnClose, Context::$client_id);
                }
                break;
            case GatewayProtocol::CMD_ON_WEBSOCKET_CONNECT:
                if ($this->_eventOnWebSocketConnect) {
                    call_user_func($this->_eventOnWebSocketConnect, Context::$client_id, $data['body']);
                }
                break;
        }
        if ($this->processTimeout) {
            pcntl_alarm(0);
        }
        
        // session 必須是陣列
        if ($_SESSION !== null && !is_array($_SESSION)) {
            throw new \Exception('$_SESSION must be an array. But $_SESSION=' . var_export($_SESSION, true) . ' is not array.');
        }

        // 判斷 session 是否被更改
        if ($_SESSION !== Context::$old_session && $cmd !== GatewayProtocol::CMD_ON_CLOSE) {
            $session_str_now = $_SESSION !== null ? Context::sessionEncode($_SESSION) : '';
            \GatewayWorker\Lib\Gateway::setSocketSession(Context::$client_id, $session_str_now);
            $this->_sessionVersion[Context::$client_id] = crc32($session_str_now);
        }

        Context::clear();
    }

    /**
     * 當與 Gateway 的連線斷開時觸發
     *
     * @param TcpConnection $connection
     * @return  void
     */
    public function onGatewayClose($connection)
    {
        $addr = $connection->remoteAddress;
        unset($this->gatewayConnections[$addr], $this->_connectingGatewayAddresses[$addr]);
        if (isset($this->_gatewayAddresses[$addr]) && !isset($this->_waitingConnectGatewayAddresses[$addr])) {
            Timer::add(1, array($this, 'tryToConnectGateway'), array($addr), false);
            $this->_waitingConnectGatewayAddresses[$addr] = $addr;
        }
    }

    /**
     * 嘗試連線 Gateway 內部通訊地址
     *
     * @param string $addr
     */
    public function tryToConnectGateway($addr)
    {
        if (!isset($this->gatewayConnections[$addr]) && !isset($this->_connectingGatewayAddresses[$addr]) && isset($this->_gatewayAddresses[$addr])) {
            $gateway_connection                    = new AsyncTcpConnection("GatewayProtocol://$addr");
            $gateway_connection->remoteAddress     = $addr;
            $gateway_connection->onConnect         = array($this, 'onConnectGateway');
            $gateway_connection->onMessage         = array($this, 'onGatewayMessage');
            $gateway_connection->onClose           = array($this, 'onGatewayClose');
            $gateway_connection->onError           = array($this, 'onGatewayError');
            $gateway_connection->maxSendBufferSize = $this->sendToGatewayBufferSize;
            if (TcpConnection::$defaultMaxSendBufferSize == $gateway_connection->maxSendBufferSize) {
                $gateway_connection->maxSendBufferSize = 50 * 1024 * 1024;
            }
            $gateway_data         = GatewayProtocol::$empty;
            $gateway_data['cmd']  = GatewayProtocol::CMD_WORKER_CONNECT;
            $gateway_data['body'] = json_encode(array(
                'worker_key' =>"{$this->name}:{$this->id}", 
                'secret_key' => $this->secretKey,
            ));
            $gateway_connection->send($gateway_data);
            $gateway_connection->connect();
            $this->_connectingGatewayAddresses[$addr] = $addr;
        }
        unset($this->_waitingConnectGatewayAddresses[$addr]);
    }

    /**
     * 檢查 gateway 的通訊埠是否都已經連
     * 如果有未連線的埠，則嘗試連線
     *
     * @param array $addresses_list
     */
    public function checkGatewayConnections($addresses_list)
    {
        if (empty($addresses_list)) {
            return;
        }
        foreach ($addresses_list as $addr) {
            if (!isset($this->_waitingConnectGatewayAddresses[$addr])) {
                $this->tryToConnectGateway($addr);
            }
        }
    }

    /**
     * 當連線上 gateway 的通訊埠時觸發
     * 將連線 connection 物件儲存起來
     *
     * @param TcpConnection $connection
     * @return void
     */
    public function onConnectGateway($connection)
    {
        $this->gatewayConnections[$connection->remoteAddress] = $connection;
        unset($this->_connectingGatewayAddresses[$connection->remoteAddress], $this->_waitingConnectGatewayAddresses[$connection->remoteAddress]);
    }

    /**
     * 當與 gateway 的連接出現錯誤時觸發
     *
     * @param TcpConnection $connection
     * @param int           $error_no
     * @param string        $error_msg
     */
    public function onGatewayError($connection, $error_no, $error_msg)
    {
        echo "GatewayConnection Error : $error_no ,$error_msg\n";
    }

    /**
     * 獲取所有 Gateway 內部通訊地址
     *
     * @return array
     */
    public function getAllGatewayAddresses()
    {
        return $this->_gatewayAddresses;
    }

    /**
     * 業務超時回撥
     *
     * @param int $signal
     * @throws \Exception
     */
    public function timeoutHandler($signal)
    {
        switch ($signal) {
            // 超時時鐘
            case SIGALRM:
                // 超時異常
                $e         = new \Exception("process_timeout", 506);
                $trace_str = $e->getTraceAsString();
                // 去掉第一行timeoutHandler的呼叫棧
                $trace_str = $e->getMessage() . ":\n" . substr($trace_str, strpos($trace_str, "\n") + 1) . "\n";
                // 開發者沒有設定超時處理函式，或者超時處理函式返回空則執行退出
                if (!$this->processTimeoutHandler || !call_user_func($this->processTimeoutHandler, $trace_str, $e)) {
                    Worker::stopAll();
                }
                break;
        }
    }
}
