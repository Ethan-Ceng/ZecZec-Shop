<?php

namespace app\api\controller\plus\assemble;

use app\api\controller\Controller;
use app\api\model\plus\assemble\Product as ProductModel;
use app\api\model\plus\assemble\Active as ActiveModel;
use app\common\service\product\BaseProductService;
use app\api\model\plus\assemble\Bill as BillModel;

/**
 * 拼團控制器
 */
class Product extends Controller
{
    /**
     * 拼團活動
     */
    public function active()
    {
        $model = new ActiveModel();
        $list = $model->activityList();
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 拼團商品
     */
    public function product($assemble_activity_id)
    {
        $detail = ActiveModel::detailWithTrans($assemble_activity_id);
        if ($detail) {
            $detail['start_time'] = strtotime($detail['start_time']);
            $detail['end_time'] = strtotime($detail['end_time']);
        }
        $list = (new ProductModel())->getActivityList($assemble_activity_id);
        return $this->renderSuccess('', compact('detail', 'list'));
    }

    /**
     * 拼團商品詳情
     */
    public function detail($assemble_product_id, $url = '')
    {
        $model = new ProductModel();
        //詳情
        $detail = $model->getAssembleDetail($assemble_product_id);
        //活動
        $active = ActiveModel::detailWithTrans($detail['assemble_activity_id']);
        $active['start_time'] = strtotime($active['start_time']);
        $active['end_time'] = strtotime($active['end_time']);
        //規格
        $specData = BaseProductService::getSpecData($detail['product']);
        //拼團訂單
        $model = new BillModel();
        $bill = $model->getBill($detail['assemble_product_id'], $detail['assemble_activity_id'], $detail['assemble_num']);
        // 微信公眾號分享引數
        $share = $this->getShareParams($url, $detail['product']['product_name'], $detail['product']['product_name'], '/pages/plus/assemble/detail/detail', $detail['product']['image'][0]['file_path']);
        return $this->renderSuccess('', compact('detail', 'active', 'specData', 'bill', 'share'));
    }
}