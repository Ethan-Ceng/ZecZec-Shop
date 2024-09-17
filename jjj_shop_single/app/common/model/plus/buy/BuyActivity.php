<?php

namespace app\common\model\plus\buy;

use app\common\model\BaseModel;

/**
 * 買送模型
 */
class BuyActivity extends BaseModel
{
    protected $pk = 'buy_id';
    protected $name = 'buy_activity';
    //附加欄位
    protected $append = ['status_text', 'start_time_text', 'end_time_text'];

    /**
     * 商品列表
     * @return \think\model\relation\HasMany
     */
    public function limitProduct()
    {
        return $this->hasMany('app\\common\\model\\plus\\buy\\BuyActivityProduct', 'buy_id', 'buy_id');
    }

    /**
     * 贈送商品
     */
    public function getProductIdsAttr($value, $data)
    {
        return $value ? json_decode($value, true) : '';
    }

    /**
     * 有效期-開始時間
     */
    public function getStartTimeTextAttr($value, $data)
    {
        return date('Y-m-d H:i:s', $data['start_time']);
    }

    /**
     * 有效期-開始時間
     */
    public function getEndTimeTextAttr($value, $data)
    {
        return date('Y-m-d H:i:s', $data['end_time']);
    }

    /**
     * 狀態
     * @param $val
     * @return string
     */
    public function getStatusTextAttr($value, $data)
    {
        if ($data['start_time'] > time()) {
            return '未開始';
        }
        if ($data['end_time'] < time()) {
            return '已結束';
        }
        if ($data['start_time'] < time() && $data['end_time'] > time()) {
            return '進行中';
        }
        return '';
    }

    /**
     * 獲取詳情
     */
    public static function detail($buy_id)
    {
        return self::with(['limit_product'])->find($buy_id);
    }

    /**
     * 列表
     */
    public function getAll()
    {
        return $this->where('is_delete', '=', '0')
            ->order(['full_value' => 'asc'])
            ->select();
    }
}