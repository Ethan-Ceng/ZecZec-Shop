<?php

namespace app\common\model\user;

use app\common\model\BaseModel;

/**
 * 餘額充值模型
 */
class BalancePlan extends BaseModel
{
    protected $name = 'balance_plan';
    protected $pk = 'plan_id';

    /**
     * 詳情
     */
    public static function detail($plan_id)
    {
        return (new static())->find($plan_id);
    }

}
