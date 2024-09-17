<?php

namespace app\api\controller\plus\bargain;

use app\api\controller\Controller;
use app\api\model\settings\Setting as SettingModel;
use app\common\service\product\BaseProductService;
use app\api\model\plus\bargain\Active as ActiveModel;
use app\api\model\plus\bargain\Product as ProductModel;

/**
 * 砍價商品控制器
 */
class Product extends Controller
{
    /**
     * 砍價活動
     */
    public function active()
    {
        $model = new ActiveModel();
        $list = $model->activityList();
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 砍價商品
     */
    public function product($bargain_activity_id)
    {
        $detail = ActiveModel::detailWithTrans($bargain_activity_id);
        if ($detail) {
            $detail['start_time'] = strtotime($detail['start_time']);
            $detail['end_time'] = strtotime($detail['end_time']);
        }
        $list = (new ProductModel())->getActivityList($bargain_activity_id);
        return $this->renderSuccess('', compact('detail', 'list'));
    }

    /**
     * 砍價商品詳情
     */
    public function detail($bargain_product_id, $url = '')
    {
        $model = new ProductModel();
        //詳情
        $detail = $model->getBargainDetail($bargain_product_id);
        //活動
        $active = ActiveModel::detailWithTrans($detail['bargain_activity_id']);
        $active['start_time'] = strtotime($active['start_time']);
        $active['end_time'] = strtotime($active['end_time']);
        //規格
        $specData = BaseProductService::getSpecData($detail['product']);
        // 砍價規則
        $setting = SettingModel::getBargain();
        // 微信公眾號分享引數
        $share = $this->getShareParams($url, $detail['product']['product_name'], $detail['product']['product_name'], '/pages/plus/assemble/detail/detail', $detail['product']['image'][0]['file_path']);
        return $this->renderSuccess('', compact('detail', 'active', 'specData', 'setting', 'share'));
    }
}