<?php

namespace app\common\model\user;

use app\common\model\BaseModel;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum;

/**
 * 使用者餘額變動明細模型
 */
class BalanceLog extends BaseModel
{
    protected $name = 'user_balance_log';
    protected $updateTime = false;

    /**
     * 獲取當前模型屬性
     */
    public static function getAttributes()
    {
        return [
            // 充值方式
            'scene' => BalanceLogSceneEnum::data(),
        ];
    }

    /**
     * 關聯會員記錄表
     */
    public function user()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->belongsTo("app\\{$module}\\model\\user\\User");
    }

    /**
     * 餘額變動場景
     */
    public function getSceneAttr($value)
    {
        return ['text' => BalanceLogSceneEnum::data()[$value]['name'], 'value' => $value];
    }

    /**
     * 新增記錄
     */
    public static function add($scene, $data, $describeParam)
    {
        $model = new static;
        $describe = $describeParam ? vsprintf(BalanceLogSceneEnum::data()[$scene]['describe'], $describeParam) : BalanceLogSceneEnum::data()[$scene]['describe'];
        $model->save(array_merge([
            'scene' => $scene,
            'describe' => $describe,
            'app_id' => $model::$app_id
        ], $data));
    }

}