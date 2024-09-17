<?php

namespace app\api\controller\order;

use app\api\model\order\Cart as CartModel;
use app\api\model\order\Order as OrderModel;
use app\api\model\plus\buy\BuyActivity as BuyActivityModel;
use app\api\service\order\settled\MasterOrderSettledService;
use app\api\controller\Controller;
use app\api\model\settings\Message as MessageModel;
use app\api\service\pay\PayService;

/**
 * 普通訂單
 */
class Order extends Controller
{
    /**
     * 訂單確認-立即購買
     */
    public function buy()
    {
        // 立即購買：獲取訂單商品列表
        $params = $this->request->param();
        $productList = OrderModel::getOrderProductListByNow($params);
        $user = $this->getUser();
        // 例項化訂單service
        $orderService = new MasterOrderSettledService($user, $productList, $params);
        // 獲取訂單資訊
        $orderInfo = $orderService->settlement();
        //獲取買送商品
        $orderInfo['buyProduct'] = (new BuyActivityModel)->getDetail($productList);
        // 錯誤資訊提示
        if ($orderService->hasError()) {
            return $this->renderError($orderService->getError());
        }
        if ($this->request->isGet()) {
            // 如果來源是小程式, 則獲取小程式訂閱訊息id.獲取支付成功,發貨通知.
            $template_arr = MessageModel::getMessageByNameArr($params['pay_source'], ['order_pay_user', 'order_delivery_user']);
            return $this->renderSuccess('', compact('orderInfo', 'template_arr'));
        }
        // 建立訂單
        $order_id = $orderService->createOrder($orderInfo);
        if (!$order_id) {
            return $this->renderError($orderService->getError() ?: '訂單建立失敗');
        }
        // 返回結算資訊
        return $this->renderSuccess('', [
            'order_id' => $order_id,   // 訂單id
        ]);
    }

    /**
     * 訂單確認-立即購買
     */
    public function cart()
    {
        // 立即購買：獲取訂單商品列表
        $params = $this->request->param();
        $user = $this->getUser();
        // 商品結算資訊
        $CartModel = new CartModel();
        // 購物車商品列表
        $productList = $CartModel->getList($user, $params['cart_ids']);
        if (!$productList) {
            return $this->renderError('商品不能為空');
        }
        // 例項化訂單service
        $orderService = new MasterOrderSettledService($user, $productList, $params);
        // 獲取訂單資訊
        $orderInfo = $orderService->settlement();
        //獲取買送商品
        $orderInfo['buyProduct'] = (new BuyActivityModel)->getDetail($productList);
        // 錯誤資訊提示
        if ($orderService->hasError()) {
            return $this->renderError($orderService->getError());
        }
        if ($this->request->isGet()) {
            // 如果來源是小程式, 則獲取小程式訂閱訊息id.獲取支付成功,發貨通知.
            $template_arr = MessageModel::getMessageByNameArr($params['pay_source'], ['order_pay_user', 'order_delivery_user']);
            // 是否開啟支付寶支付
            $show_alipay = PayService::isAlipayOpen($params['pay_source'], $user['app_id']);
            // 使用者餘額
            $balance = $user['balance'];
            return $this->renderSuccess('', compact('orderInfo', 'template_arr', 'show_alipay', 'balance'));
        }
        // 建立訂單
        $order_id = $orderService->createOrder($orderInfo);
        if (!$order_id) {
            return $this->renderError($orderService->getError() ?: '訂單建立失敗');
        }
        // 移出購物車中已下單的商品
        $CartModel->clearAll($user, $params['cart_ids']);
        // 返回結算資訊
        return $this->renderSuccess('', [
            'order_id' => $order_id,   // 訂單id
        ]);
    }

    /**
     * 訂單確認-購買
     * 商品引數
     * coupon_code
     * products [
     * {product_id, product_sku_id, product_num}
     * ]
     */
    public function getBuyList()
    {
        // 立即購買：獲取訂單商品列表
        $params = $this->request->param();
        $products = $params['products'];
        if (!count($products)) {
            return $this->renderError('請選擇商品');
        }
        $productList = [];
        foreach ($products as $value) {
            $productItem = OrderModel::getOrderProductListByNow($value);
            $productList = array_merge($productList, $productItem);
        }

        $user = $this->getUser();
        // 例項化訂單service
        $orderService = new MasterOrderSettledService($user, $productList, $params);
        // 獲取訂單資訊
        $orderInfo = $orderService->settlement();
        //獲取買送商品
        $orderInfo['buyProduct'] = (new BuyActivityModel)->getDetail($productList);
        // 錯誤資訊提示
        if ($orderService->hasError()) {
            return $this->renderError($orderService->getError());
        }
//        if ($this->request->isGet()) {
            // 如果來源是小程式, 則獲取小程式訂閱訊息id.獲取支付成功,發貨通知.
            $template_arr = MessageModel::getMessageByNameArr($params['pay_source'], ['order_pay_user', 'order_delivery_user']);
            return $this->renderSuccess('', compact('orderInfo', 'template_arr'));
//        }
        // 建立訂單
//        $order_id = $orderService->createOrder($orderInfo);
//        if (!$order_id) {
//            return $this->renderError($orderService->getError() ?: '訂單建立失敗');
//        }
//        // 返回結算資訊
//        return $this->renderSuccess('', [
//            'order_id' => $order_id,   // 訂單id
//        ]);
    }

    public function buyList()
    {
        // 立即購買：獲取訂單商品列表
        $params = $this->request->param();
        $products = $params['products'];
        if (!count($products)) {
            return $this->renderError('請選擇商品');
        }
        $productList = [];
        foreach ($products as $value) {
            $productItem = OrderModel::getOrderProductListByNow($value);
            $productList = array_merge($productList, $productItem);
        }

        $user = $this->getUser();
        // 例項化訂單service
        $orderService = new MasterOrderSettledService($user, $productList, $params);
        // 獲取訂單資訊
        $orderInfo = $orderService->settlement();
        //獲取買送商品
        $orderInfo['buyProduct'] = (new BuyActivityModel)->getDetail($productList);
        // 錯誤資訊提示
        if ($orderService->hasError()) {
            return $this->renderError($orderService->getError());
        }
        if ($this->request->isGet()) {
            // 如果來源是小程式, 則獲取小程式訂閱訊息id.獲取支付成功,發貨通知.
            $template_arr = MessageModel::getMessageByNameArr($params['pay_source'], ['order_pay_user', 'order_delivery_user']);
            return $this->renderSuccess('', compact('orderInfo', 'template_arr'));
        }
        // 建立訂單
        $order_id = $orderService->createOrder($orderInfo);
        if (!$order_id) {
            return $this->renderError($orderService->getError() ?: '訂單建立失敗');
        }
        // 返回結算資訊
        return $this->renderSuccess('', [
            'order_id' => $order_id,   // 訂單id
        ]);
    }
}