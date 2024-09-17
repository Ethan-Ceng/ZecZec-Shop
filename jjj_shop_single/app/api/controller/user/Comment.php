<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\order\Order as OrderModel;
use app\api\model\order\OrderProduct as OrderProductModel;
use app\api\model\product\Comment as CommentModel;

/**
 * 訂單評價管理
 */
class Comment extends Controller
{
    /**
     * 待評價訂單商品列表
     */
    public function order($order_id)
    {
        // 使用者資訊
        $user = $this->getUser();
        // 訂單資訊
        $order = OrderModel::getUserOrderDetail($order_id, $user['user_id']);
        // 驗證訂單是否已完成
        $model = new CommentModel;
        if (!$model->checkOrderAllowComment($order)) {
            return $this->renderError($model->getError());
        }
        // 待評價商品列表
        /* @var \think\Collection $productList */
        $OrderProductModel = new OrderProductModel;
        $productList = $OrderProductModel->getNotCommentProductList($order_id);
        if ($productList->isEmpty()) {
            return $this->renderError('該訂單沒有可評價的商品');
        }
        // 提交商品評價
        if ($this->request->isPost()) {
            $formData = $this->request->post('formData');
            if ($model->addForOrder($order, $productList, $formData)) {
                return $this->renderSuccess('評價發表成功');
            }
            return $this->renderError($model->getError() ?: '評價發表失敗');
        }
        return $this->renderSuccess('', compact('productList'));
    }

}