<?php

namespace app\api\controller\plus\invitationgift;


use app\api\controller\Controller;
use app\api\model\plus\invitationgift\InvitationGift as InvitationGiftModel;

/**
 * 邀請有禮控制器
 */
class Invitation extends Controller
{
    private $user;

    /**
     * 構造方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->user = $this->getUser();   // 使用者資訊
    }

    /**
     * 獲取資料
     */
    public function getDatas($invitation_gift_id, $url = '')
    {
        $model = new InvitationGiftModel();
        $data = $model->getDatas($invitation_gift_id, $this->user['user_id']);
        if ($data) {
            // 微信公眾號分享引數
            $share = $this->getShareParams($url, $data['share_title'], $data['share_desc'], '/pages/index/index', $data['share']['file_path']);
            return $this->renderSuccess('', compact('data', 'share'));
        }
        return $this->renderError($model->getError() ?: '活動不存在');
    }
}