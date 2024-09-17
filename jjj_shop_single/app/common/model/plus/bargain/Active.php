<?php

namespace app\common\model\plus\bargain;

use app\common\model\BaseModel;

/**
 * 砍價模型
 */
class Active extends BaseModel
{
    protected $name = 'bargain_activity';
    protected $pk = 'bargain_activity_id';

    protected $append = ['status_text', 'start_time_text', 'end_time_text'];

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
     */
    public function getStatusTextAttr($value, $data)
    {
        if ($data['status'] == 0) {
            return '未生效';
        }
        if ($data['start_time'] > time()) {
            return '未開始';
        }
        if ($data['end_time'] < time()) {
            return '已結束';
        }
        if ($data['start_time'] < time() && $data['end_time'] > time()) {
            return '生效-進行中';
        }
        return '';
    }

    /**
     *關聯商品表
     */
    public function product()
    {
        return $this->hasMany('app\\common\\model\\plus\\bargain\\BargainProduct', 'bargain_id', 'bargain_id');
    }


    /**
     *關聯圖片
     */
    public function file()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'image_id');
    }


    /**
     * 砍價活動詳情
     */
    public static function detail($bargain_activity_id, $with = [])
    {
        return (new static())->with($with)->find($bargain_activity_id);
    }

    /**
     * 處理過的詳情資料
     */
    public static function detailWithTrans($bargain_activity_id)
    {
        $model = (new static())->with(['file'])->where('bargain_activity_id', '=', $bargain_activity_id)->find();

        if ($model) {
            return [
                'title' => $model['title'],
                'image_id' => $model['image_id'],
                'file_path' => $model['file']['file_path'],
                'sort' => $model['sort'],
                'is_delete' => $model['is_delete'],
                'conditions' => $model['conditions'],
                'together_time' => $model['together_time'],
                'status' => $model['status'],
                'start_time' => date('Y-m-d H:i:s', $model['start_time']),
                'end_time' => date('Y-m-d H:i:s', $model['end_time']),
            ];
        }
        return [];
    }
}