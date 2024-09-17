<?php

namespace app\shop\controller\plus\coupon;

use app\shop\controller\Controller;
use app\shop\model\plus\coupon\Coupon as CouponModel;
use app\shop\model\plus\coupon\UserCoupon as UserCouponModel;
use app\shop\model\user\Grade as GradeModel;
use app\shop\model\product\Product as ProductModel;
use app\shop\model\product\Category as CategoryModel;

/**
 * 優惠券控制器
 */
class Coupon extends Controller
{
    /* @var CouponModel $model */
    private $model;

    /**
     * 構造方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new CouponModel;
    }

    /**
     * 優惠券列表
     */
    public function index()
    {
        $list = $this->model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增優惠券
     */
    public function add()
    {
        $data = $this->postData();
        // 新增記錄
        if ($this->model->add($data)) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError('新增失敗');
    }

    /**
     * 優惠券詳情
     */
    public function couponDetail($coupon_id)
    {
        // 優惠券詳情
        $detail = CouponModel::detail($coupon_id)->toArray();
        if ($detail['expire_type'] == 20) {
            $detail['active_time'][0] = date('Y-m-d H:i:s', $detail['start_time']['value']);
            $detail['active_time'][1] = date('Y-m-d H:i:s', $detail['end_time']['value']);
        }
        if ($detail['apply_range'] == 20) {
            $detail['product_ids'] = explode(',', $detail['product_ids']);
            $detail['product_list'] = (new ProductModel())->getListByIds($detail['product_ids']);
        }
        if ($detail['apply_range'] == 30) {
            $category_ids = json_decode($detail['category_ids'], true);
            $detail['category_list']['first'] = (new CategoryModel())->getListByIds($category_ids['first']);
            $detail['category_list']['second'] = (new CategoryModel())->getListByIds($category_ids['second']);
            foreach ($detail['category_list']['second'] as &$item) {
                $item['parent'] = CategoryModel::detail($item['parent_id'])['name'];
            }
        }

        return $this->renderSuccess('', compact('detail'));
    }

    private function getParentName($first, $parent_id)
    {
        $name = '';
        foreach ($first as $item) {
            if ($item['category_id'] == $parent_id) {
                $name = $item['name'];
            }
        }
        return $name;
    }

    /**
     * 更新優惠券
     */
    public function edit($coupon_id)
    {
        if ($this->request->isGet()) {
            return $this->couponDetail($coupon_id);
        }
        $data = $this->postData();
        unset($data['state']);
        $model = new CouponModel;
        // 更新記錄
        if ($model->edit($data)) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError('更新失敗');
    }

    /**
     * 刪除優惠券
     */
    public function delete($coupon_id)
    {
        $coupon_id = $this->postData('coupon_id/i');
        // 優惠券詳情
        $model = new CouponModel;
        // 更新記錄
        if ($model->setDelete(['coupon_id' => $coupon_id])) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError('刪除失敗');
    }

    /**
     * 領取記錄
     */
    public function receive()
    {
        $model = new UserCouponModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 傳送優惠券
     */
    public function SendCoupon()
    {
        if ($this->request->isGet()) {
            $model = new GradeModel;
            $list = $model->getLists();
            return $this->renderSuccess('', compact('list'));
        }
        $model = new UserCouponModel;
        if ($model->SendCoupon($this->postData())) {
            return $this->renderSuccess('傳送成功');
        }
        return $this->renderError('傳送失敗');
    }

}