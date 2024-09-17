<?php

namespace app\shop\model\plus\giftpackage;

use app\common\model\plus\giftpackage\GiftPackage as GiftPackageModel;
use app\shop\model\plus\coupon\Coupon;
use app\shop\model\product\Product;
use app\shop\model\plus\giftpackage\Code as CodeModel;

/**
 * 禮包購模型
 */
class GiftPackage extends GiftPackageModel
{
    /**
     * 禮包列表
     * @param $data
     */
    public function getList($data)
    {
        $model = $this;
        //檢索活動名稱
        if (isset($data['search']) && $data['search']) {
            $model = $model->where('name', 'like', '%' . trim($data['search']) . '%');
        }
        return $model->where('is_delete', '=', 0)
            ->order('create_time', 'desc')
            ->paginate($data);
    }

    /**
     * 獲取未結束的資料列表
     * 單碼
     */
    public function getDatas()
    {
        return $this->where('status', '=', 0)
            ->where('code_type', '=', 10)
            ->where('end_time', '>', time())
            ->where('is_delete', '=', 0)
            ->select();
    }

    /**
     * 獲取資料
     * @param $id
     */
    public function getGiftPackage($id)
    {
        $data = self::detail($id);
//        $data = $data->toArray();
        $data['value1'] = [
            $data['start_time']['text'], $data['end_time']['text']
        ];
//        $data['value1'][] = $data['start_time']['text'];
//        $data['value1'][] = $data['end_time']['text'];
        $data['grade_ids'] = explode(',', $data['grade_ids']);
        if ($data['is_coupon'] == '1') {
            $data['is_coupon'] = true;
        }
        if ($data['is_product'] == '1') {
            $data['is_product'] = true;
        }
        if ($data['is_point'] == '1') {
            $data['is_point'] = true;
        }
        if ($data['coupon_ids'] != '') {
            $coupon = json_decode($data['coupon_ids'], true);
            $data['coupon'] = $coupon;
        }
        if ($data['product_ids'] != '') {
            $ProductModel = new Product();
            $product = $ProductModel->getProduct($data['product_ids']);
            $data['product_list'] = $product->toArray();
            $data['product'] = explode(',', $data['product_ids']);
        }
        return $data;
    }

    /**
     * 儲存資料
     * @param $data
     */
    public function saveGift($data)
    {
        $this->startTrans();
        try {
            $this->buildData($data);
            $data['app_id'] = self::$app_id;
            // 儲存主表
            $this->save($data);
            // 儲存碼
            (new CodeModel())->geneCode($this['gift_package_id'], $this['code_type'], $this['total_num']);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 修改
     * @param $value
     */
    public function edit($data)
    {
        $this->startTrans();
        try {
            $this->buildData($data);
            // 一批一碼
            if ($this['code_type'] == 10) {
                $data['total_num'] = $this['total_num'] + $data['add_num'];
            }
            // 儲存主表
            $this->save($data);
            // 儲存碼
            if ($this['code_type'] == 20) {
                (new CodeModel())->geneCode($this['gift_package_id'], $this['code_type'], $this['add_num']);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 構造資料
     */
    private function buildData(&$data)
    {
        // 優惠券
        if ($data['is_coupon'] == 'true') {
            $data['is_coupon'] = 1;
            $data['coupon_ids'] = $data['coupon'] ? json_encode($data['coupon']) : [];
        } else {
            $data['is_coupon'] = 0;
            $data['coupon_ids'] = [];
        }
        // 商品
        if ($data['is_product'] == 'true') {
            $data['is_product'] = 1;
            $data['product_ids'] = implode(',', array_unique($data['product']));
        } else {
            $data['is_product'] = 0;
            $data['product_ids'] = '';
        }
        // 積分
        if ($data['is_point'] == 'true') {
            $data['is_point'] = 1;
        } else {
            $data['is_point'] = 0;
        }
        // 等級限制
        if ($data['is_grade'] == 1) {
            $data['grade_ids'] = implode(',', $data['grade_ids']);
        } else {
            $data['grade_ids'] = '';
        }
        // 購買次數
        if ($data['is_times'] == 0) {
            $data['times'] = 0;
        }
        $data['start_time'] = strtotime(array_shift($data['value1']));
        $data['end_time'] = strtotime(array_pop($data['value1']));
    }


    /**
     * 釋出
     */
    public function send($id)
    {
        return $this->where('gift_package_id', '=', $id)->update(['status' => 0]);
    }

    /**
     * 終止
     */
    public function end($id)
    {
        return $this->where('gift_package_id', '=', $id)->update(['status' => 1]);
    }

    /**
     * 刪除
     * @param $id
     */
    public function del($id)
    {
        return $this->where('gift_package_id', '=', $id)->update(['is_delete' => 1]);
    }
}