<?php

namespace app\api\model\plus\invitationgift;

use app\common\model\plus\invitationgift\InvitationReceive as InvitationReceiveModel;

/**
 * 獲取禮品模型
 */
class InvitationReceive extends InvitationReceiveModel
{
    /**
     * 獲取使用者已領取的獎勵
     */
    public function getUserPrize($user_id, $invitation_gift_id)
    {
        $where = [
            'user_id' => $user_id,
            'invitation_gift_id' => $invitation_gift_id,
        ];
        return $this->with(['Reward'])->where($where)->select();
    }
}