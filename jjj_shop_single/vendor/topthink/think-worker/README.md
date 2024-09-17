ThinkPHP Workerman 擴充套件
===============

由於workerman4.x架構調整較大，如需支援Workerman 4.x的版本，請期待新版！
## 安裝

composer require topthink/think-worker

## 使用方法

### HttpServer

在命令列啟動服務端
~~~
php think worker
~~~

然後就可以透過瀏覽器直接訪問當前應用

~~~
http://localhost:2346
~~~

linux下面可以支援下面指令
~~~
php think worker [start|stop|reload|restart|status]
~~~

workerman的引數可以在應用配置目錄下的worker.php裡面配置。

由於onWorkerStart執行的時候沒有HTTP_HOST，因此最好在應用配置檔案中設定app_host

### SocketServer

在命令列啟動服務端
~~~
php think worker:server
~~~

預設會在0.0.0.0:2345開啟一個websocket服務。

如果需要自定義引數，可以在config/worker_server.php中進行配置，包括：

配置引數 | 描述
--- | ---
protocol| 協議
host | 監聽地址
port | 監聽埠
socket | 完整的socket地址

並且支援workerman所有的引數。
也支援使用閉包方式定義相關事件回撥。

~~~
return [
	'socket' 	=>	'http://127.0.0.1:8000',
	'name'		=>	'thinkphp',
	'count'		=>	4,
	'onMessage'	=>	function($connection, $data) {
		$connection->send(json_encode($data));
	},
];
~~~

也支援使用自定義類作為Worker服務入口檔案類。例如，我們可以建立一個服務類（必須要繼承 think\worker\Server），然後設定屬性和添加回調方法

~~~
<?php
namespace app\http;

use think\worker\Server;

class Worker extends Server
{
	protected $socket = 'http://0.0.0.0:2346';

	public function onMessage($connection,$data)
	{
		$connection->send(json_encode($data));
	}
}
~~~
支援workerman所有的回撥方法定義（回撥方法必須是public型別）

然後在worker_server.php中增加配置引數：
~~~
return [
	'worker_class'	=>	'app\http\Worker',
];
~~~

定義該引數後，其它配置引數均不再有效。

在命令列啟動服務端
~~~
php think worker:server
~~~

然後在瀏覽器裡面訪問
~~~
http://localhost:2346
~~~

如果在Linux下面，同樣支援reload|restart|stop|status 操作
~~~
php think worker:server reload
~~~