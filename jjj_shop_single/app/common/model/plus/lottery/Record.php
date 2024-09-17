<?php

namespace app\common\model\plus\lottery;

use app\common\model\BaseModel;

/**
 * Class GiftPackage
 * 記錄模型
 * @package app\common\model\plus\giftpackage
 */
class Record extends BaseModel
{
    protected $name = 'lottery_record';
    protected $pk = 'record_id';
    /**
     * 追加欄位
     * @var string[]
     */
    protected $append = ['status_text', 'lottery_type_text'];

    /**
     * 禮包詳情
     */
    public static function detail($record_id)
    {
        return (new static())->find($record_id);
    }

    /**
     * 狀態
     */
    public function getStatusTextAttr($value, $data)
    {
        $text = '';
        if ($data['status'] == 1) {
            $text = '已使用';
        } else {
            $text = '未使用';
        }
        return $text;
    }

    /**
     * 狀態
     */
    public function getLotteryTypeTextAttr($value, $data)
    {
        $text = [0 => '無禮品', 1 => '優惠券', 2 => '積分', 3 => '商品'];
        return $text[$data['prize_type']];
    }

    /**
     * 關聯會員
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\user\\User', 'user_id', 'user_id');
    }
}