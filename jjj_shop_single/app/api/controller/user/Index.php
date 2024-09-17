<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\page\Page as AppPage;
use app\api\model\plus\agent\Setting;
use app\api\model\order\Order as OrderModel;
use app\api\model\settings\Setting as SettingModel;
use app\api\model\plus\coupon\UserCoupon as UserCouponModel;
use app\api\model\plus\invitationgift\InvitationGift as InvitationGiftModel;

/**
 * 個人中心主頁
 */
class Index extends Controller
{
    /**
     * 獲取個人中心diy資訊
     */
    public function center()
    {
        // 當前使用者資訊
        $user = $this->getUser();
        // 頁面元素
        $page = AppPage::getCenterPageData($user);
        return $this->renderSuccess('', [
            'page' => $page
        ]);
    }

    /**
     * 獲取當前使用者資訊
     */
    public function detail()
    {
        // 當前使用者資訊
        $user = $this->getUser();
        $coupon_model = new UserCouponModel();
        $coupon = $coupon_model->getCount($user['user_id']);
        // 訂單總數
        $model = new OrderModel;

        // 分銷商基本設定
        $setting = Setting::getItem('basic');
        // 是否開啟分銷功能
        $agent_open = $setting['is_open'];
        $invitation = InvitationGiftModel::getShow();
        return $this->renderSuccess('', [
            'coupon' => $coupon,
            'userInfo' => $user,
            'orderCount' => [
                'payment' => $model->getCount($user, 'payment'),
                'delivery' => $model->getCount($user, 'delivery'),
                'received' => $model->getCount($user, 'received'),
                'comment' => $model->getCount($user, 'comment'),
            ],
            'setting' => [
                'points_name' => SettingModel::getPointsName(),
                'agent_open' => $agent_open
            ],
            'sign' => SettingModel::getItem('sign'),
            'getPhone' => $this->isGetPhone(),
            'invitation' => $invitation
        ]);
    }

    /**
     * 當前使用者設定
     */
    public function setting()
    {
        // 當前使用者資訊
        $user = $this->getUser();
        return $this->renderSuccess('', [
            'userInfo' => $user
        ]);
    }

    private function isGetPhone()
    {
        $user = $this->getUser();
        if ($user['mobile'] != '') {
            return false;
        }
        return SettingModel::getItem('store')['mp_phone'];
    }
}