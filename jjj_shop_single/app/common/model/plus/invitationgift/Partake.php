<?php

namespace app\common\model\plus\invitationgift;

use app\common\model\BaseModel;

/**
 * Class Partake
 * 參與記錄模型
 * @package app\common\model\plus\invitationgift
 */
class Partake extends BaseModel
{
    protected $name = 'invitation_partake';
    protected $pk = 'invitation_partake_id';

    /**
     * 關聯使用者表
     */
    public function user()
    {
        return $this->hasOne('app\\common\\model\\user\\User', 'user_id', 'user_id')->field('user_id,nickName');
    }

    public function partake()
    {
        return $this->hasOne('app\\common\\model\\user\\User', 'user_id', 'partake_id')->field('user_id,nickName');
    }

    /**
     *關聯獎勵表
     */
    public function reward()
    {
        return $this->hasOne('app\\common\\model\\plus\\invitationgift\\InvitationReward', 'invitation_reward_id', 'invitation_reward_id');
    }

    //新增邀請記錄
    public function addPartake($invitation_id, $user_id, $partake_id)
    {
        $InvitationModel = new InvitationGift();
        $invitation = $InvitationModel->find($invitation_id);
        $data = [
            'invitation_gift_id' => $invitation_id,
            'user_id' => $user_id,
            'name' => $invitation['name'],
            'app_id' => $invitation['app_id'],
            'partake_id' => $partake_id
        ];

        // 新增邀請記錄
        $this->save($data);
        //更新參與數量
        $InvitationModel->where(['invitation_gift_id' => $data['invitation_gift_id']])->update(['partake_num' => $invitation['partake_num'] + 1]);
        if ($invitation['inv_condition'] == 0) {//邀請會員送好禮
            $detail['user_id'] = $data['user_id'];
            $detail['invitation_gift_id'] = $data['invitation_gift_id'];
            $detail['type'] = 0;
            event('Invitation', $detail);
        }
        return true;

    }
}