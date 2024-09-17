<?php

namespace app\api\service\user;

use think\facade\Cache;

class UserService
{
    /**
     * 記憶上門自提聯絡人
     */
    public static function setLastExtract($userId, $linkman, $phone)
    {
        // 快取時間30天
        $expire = 86400 * 30;
        return Cache::set("{$userId}_LastExtract", compact('linkman', 'phone'), $expire);
    }

    /**
     * 記憶上門自提聯絡人
     */
    public static function getLastExtract($userId)
    {
        if ($lastExtract = Cache::get("{$userId}_LastExtract")) {
            return $lastExtract;
        }
        return ['linkman' => '', 'phone' => ''];
    }

}