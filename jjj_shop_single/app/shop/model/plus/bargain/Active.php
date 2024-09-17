<?php

namespace app\shop\model\plus\bargain;

use app\common\enum\order\OrderSourceEnum;
use app\common\model\plus\bargain\Task as TaskModel;
use app\common\model\plus\bargain\Active as ActiveModel;
use app\shop\model\order\Order as OrderModel;
use app\shop\model\plus\bargain\Product as BargainProductModel;
use app\shop\model\plus\bargain\BargainSku as BargainSkuModel;

/**
 * 砍價模型
 */
class Active extends ActiveModel
{
    /**
     *列表
     */
    public function getList($param)
    {
        $model = $this;
        //檢索活動名稱
        if (isset($param['search']) && $param['search']) {
            $model = $model->where('title', 'like', '%' . trim($param['search']) . '%');
        }
        $list = $model->with(['file'])
            ->order('sort', 'desc')
            ->where('is_delete', '=', 0)
            ->paginate($param);
        foreach ($list as $active) {
            //商品數
            $product_model = new BargainProductModel();
            $active['product_num'] = $product_model->where('bargain_activity_id', '=', $active['bargain_activity_id'])->count();
            //訂單數
            $active['total_sales'] = $product_model->where('bargain_activity_id', '=', $active['bargain_activity_id'])->sum('total_sales');
        }
        return $list;
    }

    /**
     * 新增
     * @param $data
     */
    public function add($data)
    {
        $this->startTrans();
        try {
            $arr = $this->setData($data);
            $this->save($arr);
            //新增商品
            $product_model = new BargainProductModel();
            $product_model->add($this['bargain_activity_id'], $data['product_list']);
            // 事務提交
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
     */
    public function edit($data)
    {
        $this->startTrans();
        try {
            $arr = $this->setData($data);
            $this->save($arr);
            //新增商品
            $product_model = new BargainProductModel();
            $product_model->edit($this['bargain_activity_id'], $data['product_list']);
            //刪除商品
            if (isset($data['product_del_ids']) && count($data['product_del_ids']) > 0) {
                $product_model->where('bargain_product_id', 'in', $data['product_del_ids'])->delete();
                (new BargainSkuModel)->where('bargain_product_id', 'in', $data['product_del_ids'])->delete();
            }
            // 事務提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     *刪除
     */
    public function del()
    {
        //如果有正在拼團的商品
        $count = (new TaskModel())->whereRaw('(status = 0) OR (status = 1 and is_buy = 0)')
            ->where('bargain_activity_id', '=', $this['bargain_activity_id'])
            ->where('is_delete', '=', 0)
            ->count();
        if ($count > 0) {
            $this->error = '該活動下有正在砍價的訂單';
            return false;
        }
        // 如果有未付款訂單不能刪除
        $count = (new OrderModel())->where('pay_status', '=', 10)
            ->where('order_source', '=', OrderSourceEnum::BARGAIN)
            ->where('activity_id', '=', $this['bargain_activity_id'])
            ->where('is_delete', '=', 0)
            ->count();
        if ($count > 0) {
            $this->error = '該活動下有未付款的訂單';
            return false;
        }
        return $this->save([
            'is_delete' => 1
        ]);
    }


    /**
     * 修改資訊
     * @param $data
     */
    public function editBargain($param)
    {
        $data = array(
            'name' => $param['name'],
            'start_time' => $param['start_time']['value'],
            'end_time' => $param['end_time']['value'],
            'image_id' => $param['image_id'],
            'conditions' => $param['conditions'],
            'status' => $param['status']['value'],
            'sort' => $param['sort'],
        );
        $this->where('bargain_id', '=', $param['bargain_id'])->save($data);
        return true;
    }


    /**
     * 驗證並組裝資料
     * @param $data array  新增/新增資料
     * @return array
     */
    private function setData($data)
    {
        return [
            'image_id' => $data['image_id'],
            'title' => $data['title'],
            'start_time' => strtotime($data['start_time']),
            'end_time' => strtotime($data['end_time']),
            'conditions' => $data['conditions'],
            'together_time' => $data['together_time'],
            'status' => $data['status'],
            'sort' => $data['sort'],
            'app_id' => self::$app_id,
        ];
    }
}
