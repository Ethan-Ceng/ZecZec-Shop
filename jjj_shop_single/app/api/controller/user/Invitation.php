<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\plus\invitationgift\Partake;
use app\api\model\plus\invitationgift\InvitationReward;
use app\api\model\user\User as UserModel;

/**
 * 使用者邀請有禮控制器
 */
class Invitation extends Controller
{
    private $model;

    private $user;

    /**
     * 構造方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->user = $this->getUser();
        $this->model = new Partake;
    }

    /**
     *領獎
     */
    public function getPrize($invitation_reward_id, $invitation_gift_id)
    {
        $count = (new UserModel())->getCountInv($this->user['user_id']);
        $reward = InvitationReward::detail($invitation_reward_id);
        if (empty($reward)) {
            return $this->renderError('獎項不存在', '');
        }
        if ($count < $reward['invitation_num']) {
            return $this->renderError('未達邀請到人數', '');
        }
        if ($this->model->checkReward($invitation_reward_id, $invitation_gift_id, $this->user['user_id'])) {
            return $this->renderError('已經領過該獎品', '');
        }
        if ($this->model->getPrize($invitation_reward_id, $invitation_gift_id, $this->user['user_id'], $reward)) {
            return $this->renderSuccess('領取成功', '');
        }
        return $this->renderError('領取失敗', '');
    }

}