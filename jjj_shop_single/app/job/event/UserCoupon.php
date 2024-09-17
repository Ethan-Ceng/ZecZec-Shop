<?php

namespace app\job\event;

use think\facade\Cache;
use app\job\model\plus\fans\UserCoupon as UserCouponModel;

/**
 * 優惠券事件管理
 */
class UserCoupon
{
    // 模型
    private $model;

    /**
     * 執行函式
     */
    public function handle()
    {
        try {
            $this->model = new UserCouponModel();
            $cacheKey = "task_space_UserCoupon";
            if (!Cache::has($cacheKey)) {
                // 設定優惠券過期狀態
                $this->setExpired();
                Cache::set($cacheKey, time(), 60);
            }
        } catch (\Throwable $e) {
            echo 'ERROR UserCoupon: ' . $e->getMessage() . PHP_EOL;
            log_write('UserCoupon TASK : ' . '__ ' . $e->getMessage(), 'task');
        }
        return true;
    }

    /**
     * 設定優惠券過期狀態
     */
    private function setExpired()
    {
        // 獲取已過期的優惠券ID集
        $couponIds = $this->model->getExpiredCouponIds();
        // 記錄日誌
        $this->dologs('setExpired', [
            'couponIds' => json_encode($couponIds),
        ]);
        // 更新已過期狀態
        return $this->model->setIsExpire($couponIds);
    }

    /**
     * 記錄日誌
     */
    private function dologs($method, $params = [])
    {
        $value = 'UserCoupon --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value, 'task');
    }

}
