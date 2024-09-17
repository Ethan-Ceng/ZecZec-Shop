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

use think\Cookie as BaseCookie;
use Workerman\Protocols\Http as WorkerHttp;

/**
 * Workerman Cookie類
 */
class Cookie extends BaseCookie
{
    /**
     * 儲存Cookie
     * @access public
     * @param  string $name cookie名稱
     * @param  string $value cookie值
     * @param  int    $expire cookie過期時間
     * @param  string $path 有效的伺服器路徑
     * @param  string $domain 有效域名/子域名
     * @param  bool   $secure 是否僅僅透過HTTPS
     * @param  bool   $httponly 僅可透過HTTP訪問
     * @param  string $samesite 防止CSRF攻擊和使用者追蹤
     * @return void
     */
    protected function saveCookie(string $name, string $value, int $expire, string $path, string $domain, bool $secure, bool $httponly, string $samesite): void
    {
        WorkerHttp::setCookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

}
