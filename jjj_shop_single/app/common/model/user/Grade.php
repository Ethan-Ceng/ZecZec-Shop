<?php

namespace app\common\model\user;

use app\common\model\BaseModel;

/**
 * 使用者等級模型
 */
class Grade extends BaseModel
{
    protected $pk = 'grade_id';
    protected $name = 'user_grade';

    /**
     * 使用者等級模型初始化
     */
    public static function init()
    {
        parent::init();
    }

    /**
     * 獲取詳情
     */
    public static function detail($grade_id)
    {
        return (new static())->find($grade_id);
    }

    /**
     * 獲取可用的會員等級列表
     */
    public static function getUsableList($appId = null)
    {
        $model = new static;
        $appId = $appId ? $appId : $model::$app_id;
        return $model->where('is_delete', '=', '0')
            ->where('app_id', '=', $appId)
            ->order(['weight' => 'asc', 'create_time' => 'asc'])
            ->select();
    }

    /**
     * 獲取預設等級id
     */
    public static function getDefaultGradeId(){
        $grade = (new static())->where('is_default', '=', 1)->find();
        return $grade['grade_id'];
    }
}