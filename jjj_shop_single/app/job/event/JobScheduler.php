<?php

namespace app\job\event;


use app\common\model\app\App as AppModel;
use think\facade\Cache;

/**
 * 訂單事件管理
 */
class JobScheduler
{

    /**
     * 執行函式
     */
    public function handle()
    {
        // 查詢所有appid
        $appList = AppModel::getAll();
        // 涉及到應用單獨配置的，迴圈執行
        foreach ($appList as $app) {
            // 訂單任務
            event('Order', $app['app_id']);
            // 同步直播
            event('Live', $app['app_id']);
            // 同步直播商品
            event('LiveProduct', $app['app_id']);
        }
        // 預售訂單
        event('AdvanceOrder');
        // 拼團任務
        event('AssembleBill');
        // 砍價任務
        event('BargainTask');
        // 使用者優惠券
        event('UserCoupon');
        // 分銷商訂單
        event('AgentOrder');
        return true;
    }

}
