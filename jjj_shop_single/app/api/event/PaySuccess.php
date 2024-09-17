<?php


namespace app\api\event;

use app\common\enum\order\OrderTypeEnum;
use app\common\service\message\MessageService;
use app\api\model\order\Order;
use app\api\service\order\paysuccess\source\PaySourceSuccessFactory;

class PaySuccess
{
    public $order;
    public $appId;
    public $orderType;


    public function handle(Order $order)
    {
        $this->order = $order;
        $this->appId = $order['app_id'];
        $this->orderType = OrderTypeEnum::MASTER;
        // 訂單公共業務
        $this->onCommonEvent();
        // 訂單來源回撥業務
        $this->onSourceCallback();
        return true;
    }

    /**
     * 訂單公共業務
     */
    private function onCommonEvent()
    {
        // 傳送訊息通知
        (new MessageService)->payment($this->order, $this->orderType);
    }

    /**
     * 訂單來源回撥業務
     */
    private function onSourceCallback()
    {
        $model = PaySourceSuccessFactory::getFactory($this->order['order_source']);
        return $model->onPaySuccess($this->order);
    }
}