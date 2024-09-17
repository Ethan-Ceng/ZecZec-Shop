<?php

namespace app\api\controller\card;

use app\api\model\plus\card\Code as CodeModel;
use app\api\controller\Controller;
use app\api\model\plus\card\Order as OrderModel;
use app\api\model\product\Product as ProductModel;
use app\api\model\settings\Setting as SettingModel;
use app\api\model\settings\Message as MessageModel;

/**
 * 普通訂單
 */
class Code extends Controller
{
    public function check()
    {
        $user = $this->getUser();
        $model = new CodeModel();
        $order_id = $model->check($user, $this->request->param());
        if ($order_id) {
            return $this->renderSuccess('', compact('order_id'));
        }
        return $this->renderError($model->getError() ?: '請求錯誤，請稍後再試');
    }

    /**
     * 訂單確認-立即購買
     */
    public function order($order_id, $platform = 'wx')
    {
        $user = $this->getUser();
        $detail = OrderModel::detail($order_id, ['card']);
        $product = ProductModel::detailNoWith($detail['card']['product_id']);
        if ($detail['user_id'] != $user['user_id']) {
            return $this->renderError('非法請求');
        }
        if ($this->request->isGet()) {
            // 如果來源是小程式, 則獲取小程式訂閱訊息id.獲取支付成功,發貨通知.
            $template_arr = MessageModel::getMessageByNameArr($platform, ['order_pick_user']);
            return $this->renderSuccess('', compact('detail', 'product', 'template_arr'));
        }
        if ($detail->confirm($user, $product, $this->postData())) {
            return $this->renderSuccess('兌換成功');
        }
        return $this->renderError($detail->getError() ?: '兌換失敗');
    }

    /**
     * 卡券設定
     */
    public function setting()
    {
        $setting = SettingModel::getItem('card');
        return $this->renderSuccess('', compact('setting'));
    }
}