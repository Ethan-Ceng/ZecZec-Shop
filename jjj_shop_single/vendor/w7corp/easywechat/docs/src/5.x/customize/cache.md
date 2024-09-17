# 快取


本專案使用 [symfony/cache](https://github.com/symfony/cache) 來完成快取工作，它支援基本目前所有的快取引擎。

在我們的 SDK 中的所有快取預設使用檔案快取，快取路徑取決於 PHP 的臨時目錄，如果你需要自定義快取，那麼你需要做如下的事情：

你可以參考[symfony/cache官方文件](https://symfony.com/doc/current/components/cache.html) 來替換掉應用中預設的快取配置：


## 以 redis 為例


### Symfony 4.3 + 

> 請先安裝 redis 拓展：`composer require predis/predis`

```php

use Symfony\Component\Cache\Adapter\RedisAdapter;

// 建立 redis 例項
$client = new \Predis\Client('tcp://10.0.0.1:6379');

// 建立快取例項
$cache = new RedisAdapter($client);

// 替換應用中的快取
$app->rebind('cache', $cache);
```

### Symfony 3.4 + 

> 請先安裝 redis 拓展：https://github.com/phpredis/phpredis

```php

use Symfony\Component\Cache\Simple\RedisCache;

// 建立 redis 例項
$redis = new Redis();
$redis->connect('redis_host', 6379);

// 建立快取例項
$cache = new RedisCache($redis);

// 替換應用中的快取
$app->rebind('cache', $cache);
```


### Laravel 中使用

在 Laravel 中框架使用 [predis/predis](https://github.com/nrk/predis)：

### Symfony 4.3 + 

> 請先安裝 redis 拓展：`composer require predis/predis`

```php

use Symfony\Component\Cache\Adapter\RedisAdapter;

// 建立快取例項
$cache = new RedisAdapter(app('redis')->connection()->client());
$app->rebind('cache', $cache);

```

### Symfony 3.4 + 

```php

use Symfony\Component\Cache\Simple\RedisCache;

$predis = app('redis')->connection()->client(); // connection($name), $name 預設為 `default`
$cache = new RedisCache($predis);

$app->rebind('cache', $cache);
```

> 上面提到的 `app('redis')->connection($name)`, 這裡的 `$name` 是 laravel 專案中配置檔案 `database.php` 中 `redis` 配置名 `default`：https://github.com/laravel/laravel/blob/master/config/database.php#L118
> 如果你使用的其它連線，對應傳名稱就好了。

## 使用自定義的快取方式

如果你發現 symfony 提供的十幾種快取方式都滿足不了你的需求的話，那麼你可以自己建立一個類來完成快取操作，前提這個類得實現介面：[PSR-16](http://www.php-fig.org/psr/psr-16/)

該介面有以下方法需要實現：

```php
   public function get($key, $default = null);
   public function set($key, $value, $ttl = null);
   public function delete($key);
   public function clear();
   public function getMultiple($keys, $default = null);
   public function setMultiple($values, $ttl = null);
   public function deleteMultiple($keys);
   public function has($key);
```

下面為一個示例：

```php
<?php

use Psr\SimpleCache\CacheInterface;

class MyCustomCache implements CacheInterface
{
    public function get($key, $default = null)
    {
        // your code
    }

    public function set($key, $value, $ttl = null)
    {
        // your code
    }

    public function delete($key)
    {
        // your code
    }

    public function clear()
    {
        // your code
    }

    public function getMultiple($keys, $default = null)
    {
        // your code
    }

    public function setMultiple($values, $ttl = null)
    {
        // your code
    }

    public function deleteMultiple($keys)
    {
        // your code
    }

    public function has($key)
    {
        // your code
    }
}
```

然後例項化你的快取類並在 EasyWeChat 裡使用它：

```php
$app->rebind('cache', new MyCustomCache());
```

OK，這樣就完成了自定義快取的操作。
