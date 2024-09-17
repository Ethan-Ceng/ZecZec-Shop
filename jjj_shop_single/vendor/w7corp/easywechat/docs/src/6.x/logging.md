# 日誌

如果沒有在配置中指定日誌選項，將不會記錄任何日誌。僅在配置了相關日誌策略時啟用。

## 日誌配置

你可以配置多個日誌的 `channel`，每個 `channel` 裡的 `driver` 對應不同的日誌驅動，內建可用的 `driver` 如下表：

| 名稱       | 描述                                                                                                                                                        |
| ---------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `stack`    | 複合型，可以包含下面多種驅動的混合模式                                                                                                                      |
| `single`   | 基於 `StreamHandler` 的單一檔案日誌，引數有 `path`，`level`                                                                                                 |
| `daily`    | 基於 `RotatingFileHandler` 按日期生成日誌檔案，引數有 `path`，`level`，`days`(預設 7 天)                                                                    |
| `slack`    | 基於 `SlackWebhookHandler` 的 Slack 元件，引數請參考原始碼：[LogManager.php](https://github.com/w7corp/wechat/blob/master/src/Kernel/Log/LogManager.php#L247) |
| `syslog`   | 基於 `SyslogHandler` Monolog 驅動，引數有 `facility` 預設為 `LOG_USER`，`level`                                                                             |
| `errorlog` | 記錄日誌到系統錯誤日誌，基於 `ErrorLogHandler`，引數有 `type`，預設為 `ErrorLogHandler::OPERATING_SYSTEM`                                                   |

### 自定義日誌驅動

由於日誌使用的是 [Monolog](https://github.com/Seldaek/monolog)，所以，除了預設的檔案式日誌外，你可以自定義日誌處理器：

```php
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;


// 註冊自定義日誌
$app->getLogger()->extend('mylog', function($app, $config){
    return new Logger($this->parseChannel($config), [
        $this->prepareHandler(new RotatingFileHandler(
            $config['path'], $config['days'], $this->level($config)
        )),
    ]);
});
```

> 在你自定義的閉包函式中，可以使用 `EasyWeChat\Kernel\Log\LogManager` 中的方法，具體請檢視 SDK 原始碼。

配置檔案中在 `driver` 部分即可使用你自定義的驅動了：

```php
'logging' => [
    'default' => 'dev', // 預設使用的 channel，生產環境可以改為下面的 prod
    'channels' => [
        // 測試環境
        'dev' => [
            'driver' => 'mylog',
            'path' => '/tmp/easywechat.log',
            'level' => 'debug',
            'days' => 5,
        ],

        //...
    ],
],
```
