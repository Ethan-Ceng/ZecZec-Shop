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

namespace think\worker\command;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Config;
use Workerman\Worker;

/**
 * Worker 命令列類
 */
class GatewayWorker extends Command
{
    public function configure()
    {
        $this->setName('worker:gateway')
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status|connections", 'start')
            ->addOption('host', 'H', Option::VALUE_OPTIONAL, 'the host of workerman server.', null)
            ->addOption('port', 'p', Option::VALUE_OPTIONAL, 'the port of workerman server.', null)
            ->addOption('daemon', 'd', Option::VALUE_NONE, 'Run the workerman server in daemon mode.')
            ->setDescription('GatewayWorker Server for ThinkPHP');
    }

    public function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');

        if (DIRECTORY_SEPARATOR !== '\\') {
            if (!in_array($action, ['start', 'stop', 'reload', 'restart', 'status', 'connections'])) {
                $output->writeln("Invalid argument action:{$action}, Expected start|stop|restart|reload|status|connections .");
                exit(1);
            }

            global $argv;
            array_shift($argv);
            array_shift($argv);
            array_unshift($argv, 'think', $action);
        } else {
            $output->writeln("GatewayWorker Not Support On Windows.");
            exit(1);
        }

        if ('start' == $action) {
            $output->writeln('Starting GatewayWorker server...');
        }

        $option = Config::get('gateway_worker');

        if ($input->hasOption('host')) {
            $host = $input->getOption('host');
        } else {
            $host = !empty($option['host']) ? $option['host'] : '0.0.0.0';
        }

        if ($input->hasOption('port')) {
            $port = $input->getOption('port');
        } else {
            $port = !empty($option['port']) ? $option['port'] : '2347';
        }

        $this->start($host, (int) $port, $option);
    }

    /**
     * 啟動
     * @access public
     * @param  string   $host 監聽地址
     * @param  integer  $port 監聽埠
     * @param  array    $option 引數
     * @return void
     */
    public function start(string $host, int $port, array $option = [])
    {
        $registerAddress = !empty($option['registerAddress']) ? $option['registerAddress'] : '127.0.0.1:1236';

        if (!empty($option['register_deploy'])) {
            // 分散式部署的時候其它伺服器可以關閉register服務
            // 注意需要設定不同的lanIp
            $this->register($registerAddress);
        }

        // 啟動businessWorker
        if (!empty($option['businessWorker_deploy'])) {
            $this->businessWorker($registerAddress, $option['businessWorker'] ?? []);
        }

        // 啟動gateway
        if (!empty($option['gateway_deploy'])) {
            $this->gateway($registerAddress, $host, $port, $option);
        }

        Worker::runAll();
    }

    /**
     * 啟動register
     * @access public
     * @param  string   $registerAddress
     * @return void
     */
    public function register(string $registerAddress)
    {
        // 初始化register
        new Register('text://' . $registerAddress);
    }

    /**
     * 啟動businessWorker
     * @access public
     * @param  string   $registerAddress registerAddress
     * @param  array    $option 引數
     * @return void
     */
    public function businessWorker(string $registerAddress, array $option = [])
    {
        // 初始化 bussinessWorker 程序
        $worker = new BusinessWorker();

        $this->option($worker, $option);

        $worker->registerAddress = $registerAddress;
    }

    /**
     * 啟動gateway
     * @access public
     * @param  string  $registerAddress registerAddress
     * @param  string  $host 服務地址
     * @param  integer $port 監聽埠
     * @param  array   $option 引數
     * @return void
     */
    public function gateway(string $registerAddress, string $host, int $port, array $option = [])
    {
        // 初始化 gateway 程序
        if (!empty($option['socket'])) {
            $socket = $option['socket'];
            unset($option['socket']);
        } else {
            $protocol = !empty($option['protocol']) ? $option['protocol'] : 'websocket';
            $socket   = $protocol . '://' . $host . ':' . $port;
            unset($option['host'], $option['port'], $option['protocol']);
        }

        $gateway = new Gateway($socket, $option['context'] ?? []);

        // 以下設定引數都可以在配置檔案中重新定義覆蓋
        $gateway->name                 = 'Gateway';
        $gateway->count                = 4;
        $gateway->lanIp                = '127.0.0.1';
        $gateway->startPort            = 2000;
        $gateway->pingInterval         = 30;
        $gateway->pingNotResponseLimit = 0;
        $gateway->pingData             = '{"type":"ping"}';
        $gateway->registerAddress      = $registerAddress;

        // 全域性靜態屬性設定
        foreach ($option as $name => $val) {
            if (in_array($name, ['stdoutFile', 'daemonize', 'pidFile', 'logFile'])) {
                Worker::${$name} = $val;
                unset($option[$name]);
            }
        }

        $this->option($gateway, $option);
    }

    /**
     * 設定引數
     * @access protected
     * @param  Worker $worker Worker物件
     * @param  array  $option 引數
     * @return void
     */
    protected function option(Worker $worker, array $option = [])
    {
        // 設定引數
        if (!empty($option)) {
            foreach ($option as $key => $val) {
                $worker->$key = $val;
            }
        }
    }

}
