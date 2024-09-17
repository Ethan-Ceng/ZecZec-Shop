<?php

namespace app\common\model\user;

use app\common\model\BaseModel;

class PointsLog extends BaseModel
{
    protected $name = 'user_points_log';
    protected $pk = 'log_id';
    protected $updateTime = false;

    /**
     * 關聯會員記錄表
     */
    public function user()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->belongsTo("app\\{$module}\\model\\user\\User");
    }

    /**
     * 新增記錄
     */
    public static function add($data)
    {
        $static = new static;
        $static->save(array_merge(['app_id' => $static::$app_id], $data));
    }

    /**
     * 新增記錄 (批次)
     */
    public function onBatchAdd($saveData)
    {
        return $this->saveAll($saveData);
    }

}