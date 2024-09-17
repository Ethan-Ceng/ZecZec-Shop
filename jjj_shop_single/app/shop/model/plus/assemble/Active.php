<?php

namespace app\shop\model\plus\assemble;

use app\common\enum\order\OrderSourceEnum;
use app\common\model\plus\assemble\Active as ActiveModel;
use app\shop\model\order\Order as OrderModel;
use app\shop\model\plus\assemble\Product as AssembleProductModel;
use app\shop\model\plus\assemble\AssembleSku as AssembleSkuModel;
use app\common\model\plus\assemble\Bill as BillModel;
/**
 * 拼團活動模型
 */
class Active extends ActiveModel
{
    /**
     * 參與記錄列表
     */
    public function getList($param)
    {
        $model = $this;
        if (isset($param['status']) && $param['status'] > -1) {
            switch ($param['status']) {
                case 0:
                    $model = $model->where('start_time', '>', time());
                    break;
                case 1;
                    $model = $model->where('start_time', '<', time())->where('end_time', '>', time());
                    break;
                case 2;
                    $model = $model->where('end_time', '<', time());
                    break;
            }
        }
        if (isset($param['title']) && !empty($param['title'])) {
            $model = $model->where('title', 'like', '%' . trim($param['title']) . '%');
        }
        $list = $model->with(['file'])
            ->where('is_delete', '=', 0)
            ->order('create_time', 'desc')
            ->paginate($param);

        foreach ($list as $active) {
            //商品數
            $product_model = new AssembleProductModel();
            $active['product_num'] = $product_model->where('assemble_activity_id', '=', $active['assemble_activity_id'])->count();
            //訂單數
            $active['total_sales'] = $product_model->where('assemble_activity_id', '=', $active['assemble_activity_id'])->sum('total_sales');
        }
        return $list;
    }
    /**
     *獲取為開始的資料列表
     */
    public function getDatas()
    {
        return $this->where('end_time', '<', time())->select();
    }

    /**
     * 新增
     */
    public function add($data)
    {
        $this->startTrans();
        try {
            $arr = $this->setData($data);
            $this->save($arr);
            //新增商品
            $product_model = new AssembleProductModel();
            $product_model->add($this['assemble_activity_id'], $data['product_list']);
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
            $product_model = new AssembleProductModel();
            $product_model->edit($this['assemble_activity_id'], $data['product_list']);
            //刪除商品
            if(isset($data['product_del_ids']) && count($data['product_del_ids']) > 0){
                $product_model->where('assemble_product_id', 'in', $data['product_del_ids'])->delete();
                (new AssembleSkuModel)->where('assemble_product_id', 'in', $data['product_del_ids'])->delete();
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

    public function del()
    {
        //如果有正在拼團的商品
        $count = (new BillModel())->where('status', '=', 10)
            ->where('assemble_activity_id', '=', $this['assemble_activity_id'])
            ->count();
        if($count > 0){
            $this->error = '該活動下有正在拼團的訂單';
            return false;
        }
        // 如果有未付款訂單不能刪除
        $count = (new OrderModel())->where('pay_status', '=', 10)
            ->where('order_source', '=', OrderSourceEnum::ASSEMBLE)
            ->where('activity_id', '=', $this['assemble_activity_id'])
            ->where('is_delete', '=', 0)
            ->count();
        if($count > 0){
            $this->error = '該活動下有未付款的訂單';
            return false;
        }
        return $this->save([
            'is_delete' => 1
        ]);
    }

    /**
     * 驗證並組裝資料
     */
    private function setData($data)
    {
        return [
            'image_id' => $data['image_id'],
            'title' => $data['title'],
            'start_time' => strtotime($data['start_time']),
            'end_time' => strtotime($data['end_time']),
            'app_id' => self::$app_id,
            'together_time' => $data['together_time'],
            'sort' => $data['sort'],
            'status' => $data['status'],
            'fail_type' => $data['fail_type'],
            'is_single' => $data['is_single'],
        ];
    }

    /**
     * 獲取拼團商品列表
     */
    public function activityList()
    {
        $res = $this->where('start_time', '<=', time())
            ->where('end_time', '>=', time())->select();
        return !empty($res) ? array_column($res->toArray(), 'assemble_activity_id') : [];
    }
}