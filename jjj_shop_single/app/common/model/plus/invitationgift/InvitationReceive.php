<?php

namespace app\common\model\plus\invitationgift;

use app\common\model\BaseModel;

/**
 * 邀請有禮模型
 */
class InvitationReceive extends BaseModel
{
    protected $name = 'invitation_receive';
    protected $pk = 'invitation_receive_id';
    /**
     * 禮包詳情
     */
    public static function detail($invitation_receive_id)
    {
        return (new static())->with(['image'])->find($invitation_receive_id);
    }
    /**
     * 關聯獎勵
     */
    public function reward()
    {
        return $this->hasMany('app\\common\\model\\plus\\invitationgift\\InvitationReward', 'invitation_reward_id', 'invitation_reward_id')->order(['invitation_num' => 'asc']);
    }
    /**
     * 關聯獎勵
     */
    public function invite()
    {
        return $this->belongsTo('app\\common\\model\\plus\\invitationgift\\InvitationGift', 'invitation_gift_id', 'invitation_gift_id')->bind(['name']);
    }
    /**
     * 關聯使用者表
     */
    public function user()
    {
        return $this->hasOne('app\\common\\model\\user\\User', 'user_id', 'user_id')->field('user_id,nickName');
    }
}