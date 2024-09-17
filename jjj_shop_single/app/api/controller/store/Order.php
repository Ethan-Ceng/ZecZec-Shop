<?php

namespace app\api\controller\store;

use app\api\controller\Controller;
use app\api\model\settings\Setting as SettingModel;
use app\api\model\store\Clerk as ClerkModel;
use app\api\model\order\Order as OrderModel;

/**
 * 自提訂單管理
 */
class Order extends Controller
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
     * 核銷訂單詳情
     */
    public function detail($order_no)
    {
        // 訂單詳情
        $model = OrderModel::detailByNo($order_no);
        // 驗證是否為該門店的核銷員
        $clerkModel = ClerkModel::detail(['user_id' => $this->user['user_id']]);
        return $this->renderSuccess('', [
            'order' => $model,  // 訂單詳情
            'clerkModel' => $clerkModel,
            'setting' => [
                // 積分名稱
                'points_name' => SettingModel::getPointsName(),
            ],
        ]);
    }

    /**
     * 確認核銷
     */
    public function extract($order_id)
    {
        // 訂單詳情
        $order = OrderModel::detail($order_id);
        // 驗證是否為該門店的核銷員
        $ClerkModel = ClerkModel::detail(['user_id' => $this->user['user_id']]);
        if (!$ClerkModel->checkUser($order['extract_store_id'])) {
            return $this->renderError($ClerkModel->getError());
        }
        // 確認核銷
        if ($order->verificationOrder($ClerkModel['clerk_id'])) {
            return $this->renderSuccess('訂單核銷成功', []);
        }
        return $this->renderError($order->getError() ?:'核銷失敗');
    }

}