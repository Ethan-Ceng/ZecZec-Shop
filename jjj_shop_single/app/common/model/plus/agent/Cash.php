<?php

namespace app\common\model\plus\agent;

use app\common\model\BaseModel;

/**
 * 分銷商提現明細模型
 */
class Cash extends BaseModel
{
    protected $name = 'agent_cash';
    protected $pk = 'id';

    /**
     * 打款方式
     * @var array
     */
    public $payType = [
        10 => '微信',
        20 => '支付寶',
        30 => '銀行卡',
    ];

    /**
     * 申請狀態
     * @var array
     */
    public $applyStatus = [
        10 => '待稽核',
        20 => '稽核透過',
        30 => '駁回',
        40 => '已打款',
    ];

    /**
     * 關聯分銷商使用者表
     */
    public function user()
    {
        return $this->belongsTo('User');
    }

    /**
     * 提現詳情
     * @param $id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function detail($id)
    {
        return (new static())->find($id);
    }

    /**
     * 稽核狀態
     * @param $value
     * @return array
     */
    public function getApplyStatusAttr($value)
    {
        $method = [10 => '待稽核', 20 => '稽核透過', 30 => '駁回', 40 => '已打款'];
        return ['text' => $method[$value], 'value' => $value];
    }

}