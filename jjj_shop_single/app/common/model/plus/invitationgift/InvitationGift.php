<?php

namespace app\common\model\plus\invitationgift;

use app\common\model\BaseModel;

/**
 * 邀請有禮模型
 */
class InvitationGift extends BaseModel
{
    protected $name = 'invitation_gift';
    protected $pk = 'invitation_gift_id';
    /**
     * 禮包詳情
     */
    public static function detail($invitation_gift_id)
    {
        return (new static())->with(['image'])->find($invitation_gift_id);
    }
    /**
     * 關聯獎勵
     */
    public function reward()
    {
        return $this->hasMany('app\\common\\model\\plus\\invitationgift\\InvitationReward', 'invitation_gift_id', 'invitation_gift_id')->order(['invitation_num' => 'asc']);
    }

    /**
     * 開始時間
     */
    public function getStartTimeAttr($value)
    {
        return ['text' => date('Y-m-d H:i:s', $value), 'value' => $value];
    }

    /**
     * 有效期-結束時間
     */
    public function getEndTimeAttr($value)
    {
        return ['text' => date('Y-m-d H:i:s', $value), 'value' => $value];
    }
    /**
     * 狀態
     */
    public function getStatusAttr($value, $data)
    {
        $text = '';
        if($value == 1){
            $text = '未生效';
        }else{
            if ($data['start_time'] > time()) {
                $text = '未開始';
            }
            if ($data['end_time'] < time()) {
                $text = '已結束';
            }
            if ($data['start_time'] < time() && $data['end_time'] > time()) {
                $text = '進行中';
            }
        }
        return ['text' => $text, 'value' => $value];
    }
    /**
     * 關聯檔案庫
     */
    public function image()
    {
        return $this->belongsTo('app\\common\\model\\file\\UploadFile', 'image_id', 'file_id')
            ->bind(['file_path']);
    }
    /**
     * 關聯檔案庫
     */
    public function share()
    {
        return $this->belongsTo('app\\common\\model\\file\\UploadFile', 'share_image_id', 'file_id');
    }

}