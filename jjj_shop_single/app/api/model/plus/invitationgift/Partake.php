<?php

namespace app\api\model\plus\invitationgift;

use app\common\model\plus\invitationgift\Partake as PartakeModel;

/**
 * 領取記錄表
 */
class Partake extends PartakeModel
{

    /**
     * 判斷使用者是否領過獎品
     */
    public function checkReward($invitation_reward_id, $invitation_gift_id, $user_id)
    {
        $where = [
            'invitation_reward_id' => $invitation_reward_id,
            'invitation_gift_id' => $invitation_gift_id,
            'user_id' => $user_id,
        ];
        return $this->where($where)->find();
    }

    /**
     * 獲取使用者已領取的獎勵
     */
    public function getUserPrizes($user_id, $invitation_gift_id)
    {
        $where = [
            'user_id' => $user_id,
            'invitation_gift_id' => $invitation_gift_id,
        ];
        return $this->with(['reward'])->where($where)->select();
    }

}