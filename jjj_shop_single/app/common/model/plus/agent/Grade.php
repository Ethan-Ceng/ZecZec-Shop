<?php

namespace app\common\model\plus\agent;

use app\common\model\BaseModel;

/**
 * 分銷商申請模型
 */
class Grade extends BaseModel
{
    protected $name = 'agent_grade';
    protected $pk = 'grade_id';


    /**
     * 獲取詳情
     */
    public static function detail($grade_id)
    {
        return (new static())->find($grade_id);
    }

    /**
     * 獲取列表記錄
     */
    public function getLists()
    {
        return $this->where('is_delete', '=', 0)
            ->field('grade_id,name')
            ->order(['weight' => 'asc', 'create_time' => 'asc'])
            ->select();
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
        $model = new static();
        $grade = $model->where('is_default', '=', 1)->find();
        if(!$grade){
            $model->save([
                'name' => '預設等級',
                'is_default' => 1,
                'weight' => 1,
                'app_id' => self::$app_id
            ]);
            $grade_id = $model['grade_id'];
        }else{
            $grade_id = $grade['grade_id'];
        }
        return $grade_id;
    }
}