<?php

namespace app\common\model\plus\seckill;

use app\common\model\BaseModel;

/**
 * Class Partake
 * 參與記錄模型
 * @package app\common\model\plus\invitationgift
 */
class Active extends BaseModel
{
    protected $name = 'seckill_activity';
    protected $pk = 'seckill_activity_id';
    //附加欄位
    protected $append = ['status_text', 'start_time_text','end_time_text'];

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
        if($data['status'] == 0){
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
     * 處理過的詳情資料
     */
    public static function detailWithTrans($seckill_activity_id)
    {
        $model = (new static())->with(['file'])->where('seckill_activity_id', '=', $seckill_activity_id)->find();
        return [
            'title' => $model['title'],
            'image_id' => $model['image_id'],
            'status' => $model['status'],
            'sort' => $model['sort'],
            'start_time' => $model['start_time'],
            'end_time' => $model['end_time'],
            'file_path' => $model['file']['file_path'],
            'start_date' => date('Y-m-d', $model['start_time']),
            'end_date' => date('Y-m-d', $model['end_time']),
            'active_time' => [
               $model['day_start_time'],
               $model['day_end_time'],
            ]
        ];
    }

    public static function detail($seckill_activity_id)
    {
        return (new static())->find($seckill_activity_id);
    }

    public function file()
    {
        return $this->belongsTo('app\\common\\model\\file\\UploadFile', 'image_id', 'file_id');
    }

    public function seckillProduct()
    {
        return $this->hasMany('app\\common\\model\\plus\\seckill\\Product', 'seckill_activity_id', 'seckill_activity_id');
    }


}