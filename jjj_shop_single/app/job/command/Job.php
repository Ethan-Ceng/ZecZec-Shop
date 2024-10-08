<?php
declare (strict_types = 1);

namespace app\job\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Worker;
use Workerman\Lib\Timer;

class Job extends Command
{
    protected $task;
    protected $second = 5;
    protected function configure()
    {
        // 指令配置
        $this->setName('job')
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status|connections", 'start')
            ->addOption('mode', 'm', Option::VALUE_OPTIONAL, 'Run the workerman server in daemon mode.')
            ->setDescription('定時任務');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令輸出
        $action = $input->getArgument('action');
        $mode = $input->getOption('mode');
        // 重新構造命令列引數,以便相容workerman的命令
        global $argv;
        $argv = [];
        array_unshift($argv, 'think', $action);
        if ($mode == 'd') {
            $argv[] = '-d';
        } else if ($mode == 'g') {
            $argv[] = '-g';
        }
        $worker = new Worker();
        $worker->onWorkerStart = [$this, 'start'];
        Worker::runAll();
    }

    /**
     * 定時器執行的內容
     * @return false|int
     */
    public function start()
    {
        return $this->task = Timer::add($this->second, function () use (&$task) {
            try {
                event('JobScheduler');
            } catch (\Throwable $e) {
                echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
            }
        });
    }
}
