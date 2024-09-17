<?php

namespace app\api\model\plus\seckill;

use app\common\model\plus\seckill\Active as ActiveModel;
use app\common\service\product\BaseProductService;

/**
 * 秒殺活動模型
 */
class Active extends ActiveModel
{
    public function checkOrderTime($data)
    {
        $result = ['code' => 0];
        if ($data['start_time'] > time()) {
            $result = ['code' => 10, 10 => '該活動未開始'];
        }
        if ($data['end_time'] < time()) {
            $result = ['code' => 20, 20 => '該活動已結束'];
        }
        $now_start_time = strtotime(date('Y-m-d') . ' ' . $data['day_start_time']);
        $now_end_time = strtotime(date('Y-m-d') . ' ' . $data['day_end_time']);
        if ($now_start_time > time()) {
            $result = ['code' => 30, 30 => '該活動今天未開始'];
        }
        if ($now_end_time < time()) {
            $result = ['code' => 40, 40 => '該活動今天已結束'];
        }
        return $result;
    }

    /**
     * 取最近要結束的一條記錄
     * 6.10修改未開始也顯示
     */
    public static function getActive()
    {
        return (new static())->where('end_time', '>', time())
            ->where('status', '=', 1)
            ->where('is_delete', '=', 0)
            ->order(['start_time' => 'asc', 'sort' => 'asc'])
            ->find();
    }

    /**
     * 獲取秒殺活動
     * 6.10顯示未開始
     */
    public function activityList()
    {
        return $this->where('end_time', '>', time())
            ->where('status', '=', 1)
            ->where('is_delete', '=', 0)
            ->select();
    }

    /**
     * 獲取秒殺商品
     * 6.10顯示未開始
     */
    public static function getProductSecKillDetail($product_id)
    {
        $active = (new static())->alias('a')
            ->where('a.end_time', '>', time())
            ->where('a.status', '=', 1)
            ->where('a.is_delete', '=', 0)
            ->join('seckill_product s', 's.seckill_activity_id=a.seckill_activity_id')
            ->where('product_id', '=', $product_id)
            ->field('s.*')
            ->find();
        $detail = "";
        if ($active) {
            $detail = (new Product())->getSeckillDetail($active['seckill_product_id']);
            $detail['active'] = self::detailWithTrans($active['seckill_activity_id']);
            $detail['specData'] = BaseProductService::getSpecData($detail['product']);
        }
        return $detail;
    }
}
