# 快取


本專案使用 [doctrine/cache](https://github.com/doctrine/cache) 來完成快取工作，它支援基本目前所有的快取引擎。

在我們的 SDK 中的所有快取預設使用檔案快取，快取路徑取決於 PHP 的臨時目錄，如果你需要自定義快取，那麼你需要做如下的事情：

你可以參考[doctrine/cache官方文件](http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/reference/caching.html)來替換掉應用中預設的快取配置：

> 以 redis 為例
> 請先安裝 redis 拓展：https://github.com/phpredis/phpredis

```php

use Doctrine\Common\Cache\RedisCache;

$cacheDriver = new RedisCache();

// 建立 redis 例項
$redis = new Redis();
$redis->connect('redis_host', 6379);

$cacheDriver->setRedis($redis);

$options = [
    'debug'  => false,
    'app_id' => $wechatInfo['app_id'],
    'secret' => $wechatInfo['app_secret'],
    'token'  => $wechatInfo['token'],
    'aes_key' => $wechatInfo['aes_key'], // 可選
    'cache'   => $cacheDriver,
];

$wechatApp = new Application($options);
```

### Laravel 中使用

在 Laravel 中框架使用 [predis/predis](https://github.com/nrk/predis)，那麼我們就得使用 `Doctrine\Common\Cache\PredisCache`：

```php

use Doctrine\Common\Cache\PredisCache;

$predis = app('redis')->connection();// connection($name), $name 預設為 `default`
$cacheDriver = new PredisCache($predis);

$app->cache = $cacheDriver;
```

> 上面提到的 `app('redis')->connection($name)`, 這裡的 `$name` 是 laravel 專案中配置檔案 `database.php` 中 `redis` 配置名 `default`：https://github.com/laravel/laravel/blob/master/config/database.php#L118
> 如果你使用的其它連線，對應傳名稱就好了。
> 如果你在使用Laravel 5.4，應將`$predis = app('redis')->connection();`修改為：`$predis = app('redis')->connection()->client();`

## 使用自定義的快取方式

如果你發現 doctrine 提供的幾十種快取方式都滿足不了你的需求的話，那麼你可以自己建立一個類來完成快取操作，前提這個類得實現介面：[Doctrine\Common\Cache\Cache](https://github.com/doctrine/cache/blob/master/lib/Doctrine/Common/Cache/Cache.php)

該介面有以下方法需要實現：

```php
   public function fetch($id);    // 讀取快取
   public function contains($id);  // 檢查是否存在快取
   public function save($id, $data, $lifeTime = 0);   // 設定快取
   public function delete($id);  // 刪除快取
   public function getStats(); // 獲取狀態
```

下面為一個示例：

```php
<?php

use Doctrine\Common\Cache\Cache as CacheInterface;

class MyCacheDriver implements CacheInterface
{
    public function fetch($id)
    {
        // 你自己從你想實現的儲存方式讀取並返回
    }

    public function contains($id)
    {
        // 同理 返回存在與否 bool 值
    }

    public function save($id, $data, $lifeTime = 0)
    {
        // 用你的方式儲存該快取內容即可
    }

    public function delete($id)
    {
        // 刪除並返回 bool 值
    }

    public function getStats()
    {
        // 這個你可以不用實現，返回 null 即可
    }
}
```

然後例項化你的快取類並在 EasyWeChat 裡使用它：

```php
$myCacheDriver = new MyCacheDriver();

$config = [
    //...
    'cache'   => $myCacheDriver,
];

$wechatApp = new Application($options);
```

OK，這樣就完成了自定義快取的操作。
