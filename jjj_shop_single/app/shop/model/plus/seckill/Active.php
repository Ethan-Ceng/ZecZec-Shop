<?php

namespace app\shop\model\plus\seckill;

use app\common\enum\order\OrderSourceEnum;
use app\common\model\plus\seckill\Active as ActiveModel;
use app\shop\model\plus\seckill\Product as SeckillProductModel;
use app\shop\model\plus\seckill\SeckillSku as SeckillSkuModel;
use app\shop\model\order\Order as OrderModel;
/**
 * 秒殺活動
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
            $product_model = new SeckillProductModel();
            $active['product_num'] = $product_model->where('seckill_activity_id', '=', $active['seckill_activity_id'])->count();
            //訂單數
            $active['total_sales'] = $product_model->where('seckill_activity_id', '=', $active['seckill_activity_id'])->sum('total_sales');
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


    public function add($data)
    {
        $this->startTrans();
        try {
            $arr = $this->setData($data);
            $this->save($arr);
            //新增商品
            $product_model = new SeckillProductModel();
            $product_model->add($this['seckill_activity_id'], $data['product_list']);
            // 事務提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    public function edit($data)
    {
        $this->startTrans();
        try {
            $arr = $this->setData($data);
            $this->save($arr);
            //新增商品
            $product_model = new SeckillProductModel();
            $product_model->edit($this['seckill_activity_id'], $data['product_list']);
            //刪除商品
            if(isset($data['product_del_ids']) && count($data['product_del_ids']) > 0){
                $product_model->where('seckill_product_id', 'in', $data['product_del_ids'])->delete();
                (new SeckillSkuModel)->where('seckill_product_id', 'in', $data['product_del_ids'])->delete();
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
     * 活動刪除
     */
    public function del()
    {
        // 如果有未付款訂單不能刪除
        $count = (new OrderModel())->where('pay_status', '=', 10)
            ->where('order_source', '=', OrderSourceEnum::SECKILL)
            ->where('activity_id', '=', $this['seckill_activity_id'])
            ->where('order_status', '<>', 20)
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
     * @param $data array  新增/新增資料
     * @param $type  string 型別
     * @return array
     */
    private function setData($data)
    {
        $data['active_time'][0] = substr($data['active_time'][0],0, 5);
        $data['active_time'][1] = substr($data['active_time'][1],0, 5);
        return  [
            'image_id' => $data['image_id'],
            'title' => $data['title'],
            'status' => $data['status'],
            'sort' => $data['sort'],
            'start_time' => strtotime($data['start_date']. ' ' . $data['active_time'][0] . ':00'),
            'end_time' => strtotime($data['end_date']. ' '. $data['active_time'][1] . ':59'),
            'day_start_time' => $data['active_time'][0] . ':00',
            'day_end_time' => $data['active_time'][1]. ':59',
            'app_id' => self::$app_id,
        ];
    }

    /**
     * 獲取diy秒殺活動商品
     */
    public function getDiyProduct()
    {
        $res = $this->with(['seckillProduct.seckillSku', 'seckillProduct.product'])->where('start_time', '<=', time())
            ->where('end_time', '>=', time())->find();
        if (isset($res['seckillProduct'])) {
            $list = [];
            foreach ($res['seckillProduct'] as $k => $val) {
                $list[$k]['product_name'] = $val['product']['product_name'];
                $list[$k]['product_id'] = $val['product_id'];
                $list[$k]['product_name'] = $val['product']['product_name'];
            }
            return $res['seckillProduct'];
        }
        return [];
    }
}