<?php

namespace app\api\controller\plus\advance;

use app\api\model\plus\advance\Product as ProductModel;
use app\api\service\order\settled\AdvanceOrderSettledService;
use app\api\service\order\settled\FrontOrderSettledService;
use app\api\controller\Controller;
use app\api\model\settings\Message as MessageModel;
use app\api\model\order\Order as OrderModel;
use app\api\model\order\OrderAdvance as OrderAdvanceModel;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\app\App as AppModel;

/**
 * 預售訂單
 */
class Order extends Controller
{
    /**
     * 支付定金訂單
     */
    public function frontBuy()
    {
        // 預售訂單：獲取訂單商品列表
        $params = $this->request->param();
        $productList = ProductModel::getAdvanceProduct($params);
        $user = $this->getUser();
        // 例項化訂單service
        $orderService = new FrontOrderSettledService($user, $productList, $params);
        // 獲取訂單資訊
        $orderInfo = $orderService->paySettlement();

        if ($this->request->isGet()) {
            // 如果來源是小程式, 則獲取小程式訂閱訊息id.獲取支付成功,發貨通知.
            $template_arr = MessageModel::getMessageByNameArr($params['pay_source'], ['order_pay_user', 'order_delivery_user']);
            return $this->renderSuccess('', compact('orderInfo', 'template_arr'));
        }
        // 訂單結算提交
        if ($orderService->hasError()) {
            return $this->renderError($orderService->getError());
        }
        // 建立訂單
        $order_id = $orderService->createPayOrder($orderInfo);
        if (!$order_id) {
            return $this->renderError($orderService->getError() ?: '訂單建立失敗');
        }
        // 返回結算資訊
        return $this->renderSuccess('', [
            'order_id' => $order_id,   // 訂單id
            'order_type' => OrderTypeEnum::FRONT, //訂單型別
        ]);
    }

    /**
     * 立即支付定金
     */
    public function pay($order_id)
    {
        $user = $this->getUser();
        // 獲取訂單詳情
        $model = OrderAdvanceModel::getUserOrderDetail($order_id, $user['user_id']);
        $params = $this->postData();
        if ($this->request->isGet()) {
            // 開啟的支付型別
            $payTypes = AppModel::getPayType($model['app_id'], $params['pay_source'], $user);
            // 支付金額
            $payPrice = $model['pay_price'];
            $balance = $user['balance'];
            return $this->renderSuccess('', compact('payTypes', 'payPrice', 'balance'));
        }
        // 訂單支付事件
        if (!$model->onPay()) {
            return $this->renderError($model->getError() ?: '訂單支付失敗');
        }
        $OrderModel = new OrderAdvanceModel;
        // 構建微信支付請求
        $payInfo = $OrderModel->OrderPay($params, $model, $user);
        if (!$payInfo) {
            return $this->renderError($OrderModel->getError() ?: '訂單支付失敗');
        }
        // 支付狀態提醒
        return $this->renderSuccess('', [
            'order_advance_id' => $order_id,   // 定金訂單id
            'order_id' => $model['order_id'],   // 主訂單id
            'pay_type' => $payInfo['payType'],  // 支付方式
            'payment' => $payInfo['payment'],   // 微信支付引數
            'order_type' => OrderTypeEnum::FRONT, //訂單型別
            'return_Url' => $params['pay_source'] == 'h5' ? urlencode(base_url() . "h5/pages/order/myorder") : '', //h5支付跳轉地址
        ]);
    }

    /**
     * 尾款訂單確認
     */
    public function buy()
    {
        // 預售訂單：獲取訂單商品列表
        $params = $this->request->param();
        $order = ProductModel::getAdvanceOrderProduct($params);
        $params = $order['params'];
        $user = $this->getUser();
        // 例項化訂單service
        $orderService = new AdvanceOrderSettledService($user, $order['product'], $params);
        // 獲取訂單資訊
        $orderInfo = $orderService->settlement();

        if ($this->request->isGet()) {
            // 如果來源是小程式, 則獲取小程式訂閱訊息id.獲取支付成功,發貨通知.
            $template_arr = MessageModel::getMessageByNameArr($params['pay_source'], ['order_pay_user', 'order_delivery_user']);
            return $this->renderSuccess('', compact('orderInfo', 'template_arr'));
        }
        // 訂單結算提交
        if ($orderService->hasError()) {
            return $this->renderError($orderService->getError());
        }
        // 建立訂單
        $order_id = $orderService->createOrder($orderInfo);
        if (!$order_id) {
            return $this->renderError($orderService->getError() ?: '訂單建立失敗');
        }
        // 返回結算資訊
        return $this->renderSuccess('', [
            'order_id' => $order_id,   // 訂單id
            'order_type' => OrderTypeEnum::ADVANCE, //訂單型別
        ]);
    }

    /**
     * 取消定金訂單
     */
    public function cancelFront($order_id)
    {
        $user = $this->getUser();
        $model = OrderAdvanceModel::getUserOrderDetail($order_id, $user['user_id']);
        if ($model->cancel($user)) {
            return $this->renderSuccess('訂單取消成功');
        }
        return $this->renderError($model->getError() ?: '訂單取消失敗');
    }
}