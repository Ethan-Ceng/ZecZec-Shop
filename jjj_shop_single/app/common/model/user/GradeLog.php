<?php

namespace app\common\model\user;

use app\common\model\BaseModel;

/**
 * 使用者會員等級變更記錄模型
 */
class GradeLog extends BaseModel
{
    protected $name = 'user_grade_log';
    protected $pk = 'log_id';
    protected $updateTime = false;

    /**
     * 關聯會員記錄表
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\user\\User');
    }

    /**
     * 關聯會員記錄表
     */
    public function oldGrade()
    {
        return $this->belongsTo('app\\common\\model\\user\\Grade', 'old_grade_id', 'grade_id');
    }

    /**
     * 關聯會員記錄表
     */
    public function grade()
    {
        return $this->belongsTo('app\\common\\model\\user\\Grade',  'new_grade_id', 'grade_id');
    }

    public function getList($params)
    {
        $model = $this;
        if(isset($params['search']) && !empty($params['search'])){
            $model = $model->where('user.nickName', 'like', "%{$params['search']}%");
        }
        return $model->alias('log')->with(['user', 'oldGrade', 'grade'])
            ->join('user user', 'user.user_id = log.user_id','left')
            ->field('log.*')
            ->order(['log.create_time' => 'desc'])
            ->paginate($params);
    }
}