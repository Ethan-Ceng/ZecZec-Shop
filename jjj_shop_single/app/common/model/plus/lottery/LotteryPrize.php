<?php

namespace app\common\model\plus\lottery;

use app\common\model\BaseModel;

/**
 * Class GiftPackage
 * 轉盤獎項模型
 * @package app\common\model\plus\giftpackage
 */
class LotteryPrize extends BaseModel
{
    protected $name = 'lottery_prize';
    protected $pk = 'prize_id';
    /**
     * 追加欄位
     * @var string[]
     */
    protected $append = ['status_text'];

    /**
     * 轉盤詳情
     */
    public static function detail($lottery_id)
    {
        return (new static())->where('status', '=', 10)->where('lottery_id', '=', $lottery_id)->select();
    }

    /**
     * 獎項下架列表
     */
    public static function getlist($data, $lottery_id)
    {
        return (new static())->where('status', '=', 20)
            ->where('lottery_id', '=', $lottery_id)
            ->paginate($data);
    }

    /**
     * 狀態
     */
    public function getStatusTextAttr($value, $data)
    {
        if ($data['status'] == 10) {
            $text = '上架';
        } else {
            $text = '下架';
        }
        return $text;
    }
}