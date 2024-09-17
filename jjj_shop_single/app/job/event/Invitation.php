<?php

namespace app\job\event;

use app\api\model\plus\invitationgift\InvitationReward as InvitationRewardModel;
use app\api\model\plus\invitationgift\InvitationReceive as InvitationReceiveModel;
use app\api\model\plus\invitationgift\Partake as PartakeModel;
use app\job\model\user\User as UserModel;
use app\job\model\order\Order as OrderModel;
use app\api\model\plus\coupon\UserCoupon;
use app\common\model\user\BalanceLog as BalanceLogModel;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum;

/**
 * 邀請好友管理
 */
class Invitation
{
    private $user_id;
    private $invitation_gift_id;

    /**
     * 執行函式
     */
    public function handle($data)
    {
        $this->user_id = $data['user_id'];
        $this->invitation_gift_id = $data['invitation_gift_id'];
        if ($data['type'] == 0) {
            $this->inviteStatus();
        } else if ($data['type'] == 1) {
            $this->inviteOrderStatus();
        }
        return true;
    }

    //判斷邀請好友數量
    public function inviteStatus()
    {
        $PartakeModel = new PartakeModel();
        //邀請總人數
        $count = $PartakeModel->where('invitation_gift_id', '=', $this->invitation_gift_id)
            ->where('user_id', '=', $this->user_id)
            ->count();
        $data['count'] = $count;
        $this->receiveGift($data);

    }

    //判斷邀請好友且消費
    public function inviteOrderStatus()
    {
        $PartakeModel = new PartakeModel();
        //邀請總人數
        $partake_id = $PartakeModel->where('invitation_gift_id', '=', $this->invitation_gift_id)
            ->where('user_id', '=', $this->user_id)
            ->column('partake_id');
        $count = (new OrderModel())->where('user_id', 'in', $partake_id)
            ->where('pay_status', '=', 20)
            ->count();
        $data['count'] = $count;
        $this->receiveGift($data);

    }

    //判斷是否滿足條件贈送禮品
    private function receiveGift($data)
    {
        $count = $data['count'];
        //獎品
        $InvitationRewardModel = new InvitationRewardModel();
        $reward = $InvitationRewardModel->where('invitation_gift_id', '=', $this->invitation_gift_id)
            ->order('invitation_num desc')
            ->select();
        $gift = [];
        foreach ($reward as $key => $value) {
            if ($count >= $value['invitation_num']) {
                $gift['invitation_reward_id'] = $value['invitation_reward_id'];
                $gift['point'] = $value['is_point'] == 1 ? $value['point'] : 0;
                $gift['coupon_ids'] = $value['is_coupon'] == 1 ? $value['coupon_ids'] : '';
                $gift['coupon_name'] = $value['is_coupon'] == 1 ? $value['coupon_name'] : '';
                $gift['balance'] = $value['is_balance'] == 1 ? $value['balance'] : 0;
                $gift['app_id'] = $value['app_id'];
                break;
            }
        }
        //滿足條件送禮品
        if ($gift) {
            //判斷有沒有送
            $receive = (new InvitationReceiveModel())->where('user_id', '=', $this->user_id)
                ->where('invitation_gift_id', '=', $this->invitation_gift_id)
                ->where('invitation_reward_id', '=', $gift['invitation_reward_id'])
                ->find();
            if (!$receive) {
                $gift['user_id'] = $this->user_id;
                $gift['invitation_gift_id'] = $this->invitation_gift_id;
                //增加記錄
                (new InvitationReceiveModel())->save($gift);
                // 使用者模型
                $user = UserModel::detail($this->user_id);
                // 修改使用者積分
                if ($gift['point'] > 0) {
                    $user->setIncPoints($gift['point'], '邀請有禮獎勵');
                }
                if ($gift['balance'] > 0) {
                    $user->save(['balance' => $user['balance'] + $gift['balance']]);
                    BalanceLogModel::add(BalanceLogSceneEnum::AWARD, [
                        'user_id' => $user['user_id'],
                        'money' => $gift['balance'],
                        'app_id' => $user['app_id'],
                    ], ['order_no' => $gift['invitation_gift_id']]);
                }
                //新增優惠券
                if ($gift['coupon_ids']) {
                    $UserCouponModel = new UserCoupon;
                    $UserCouponModel->addUserCoupon($gift['coupon_ids'], $user);
                }
            }
        }
        return true;
    }
}
