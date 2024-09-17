<?php

namespace app\api\controller\plus\seckill;

use app\api\model\plus\seckill\Product as ProductModel;
use app\api\service\order\settled\SeckillOrderSettledService;
use app\api\controller\Controller;
use app\api\model\settings\Message as MessageModel;
use app\api\model\order\Order as OrderModel;
use app\common\enum\order\OrderTypeEnum;
use app\common\enum\settings\SettingEnum;
use app\common\model\settings\Setting;


/**
 * 限時秒殺訂單
 */
class Order extends Controller
{
    /**
     * 訂單確認
     */
    public function buy()
    {
        // 限時秒殺訂單：獲取訂單商品列表
        $params = $this->request->param();
        $productList = ProductModel::getSeckillProduct($params);

        $user = $this->getUser();
        // 例項化訂單service
        $orderService = new SeckillOrderSettledService($user, $productList, $params);
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
        ]);
    }
}