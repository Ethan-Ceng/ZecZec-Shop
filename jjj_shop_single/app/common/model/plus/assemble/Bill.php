<?php

namespace app\common\model\plus\assemble;

use app\common\enum\order\OrderTypeEnum;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\library\helper;
use app\common\model\BaseModel;
use app\common\model\order\Order as OrderModel;
use app\common\model\plus\assemble\BillUser as BillUserModel;
use app\common\service\order\OrderCompleteService;
use app\common\service\order\OrderPrinterService;

/**
 * 參與記錄模型
 */
class Bill extends BaseModel
{
    protected $name = 'assemble_bill';
    protected $pk = 'assemble_bill_id';

    /**
     * 詳情
     */
    public static function detail($assemble_bill_id, $with = [])
    {
        return (new static())->with($with)->where('assemble_bill_id', '=', $assemble_bill_id)->find();
    }

    /**
     * 關聯活動表
     */
    public function activity()
    {
        return $this->belongsTo('Active', 'assemble_activity_id', 'assemble_activity_id');
    }

    /**
     * 關聯建立者
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\user\\User', 'creator_id', 'user_id')
            ->field(['user_id', 'nickName', 'avatarUrl']);
    }

    /**
     * 關聯拼團成員表
     */
    public function billUser()
    {
        return $this->hasMany('app\\common\\model\\plus\\assemble\\BillUser', 'assemble_bill_id', 'assemble_bill_id')
            ->field(['user_id', 'assemble_bill_id'])
            ->order(['create_time' => 'asc']);
    }

    /**
     * 新拼團訂單
     */
    public function newOrder($product, $sku)
    {
        $active = Active::detail($sku['assemble_activity_id']);
        //插入主表
        $this->save([
            'assemble_product_id' => $sku['assemble_product_id'],
            'actual_people' => 1,
            'creator_id' => $product['user_id'],
            'assemble_activity_id' => $sku['assemble_activity_id'],
            'end_time' => time() + $active['together_time'] * 60 * 60,
            'app_id' => $active['app_id'],
        ]);
        //插入拼團記錄表
        $bill_user_model = new BillUser();
        $bill_user_model->save([
            'assemble_bill_id' => $this['assemble_bill_id'],
            'order_id' => $product['order_id'],
            'user_id' => $product['user_id'],
            'is_creator' => 1,
            'app_id' => $active['app_id'],
        ]);
        //拼團訂單商品
        $product->save([
            'bill_source_id' => $this['assemble_bill_id']
        ]);
        //更新主訂單表拼團狀態
        (new OrderModel)->where('order_id', '=', $product['order_id'])
            ->save([
                'assemble_status' => 10
            ]);
    }

    /**
     * 參團訂單
     */
    public function updateOrder($product, $sku)
    {
        //更新拼團人數
        $this->where('assemble_bill_id', '=', $this['assemble_bill_id'])->inc('actual_people', 1)->update();
        //插入拼團記錄表
        $bill_user_model = new BillUser();
        $bill_user_model->save([
            'assemble_bill_id' => $this['assemble_bill_id'],
            'order_id' => $product['order_id'],
            'user_id' => $product['user_id'],
            'is_creator' => 0,
            'app_id' => $product['app_id'],
        ]);
        //開團資訊
        $asemble_product = Product::detail($sku['assemble_product_id']);
        //判斷拼團是否成功
        if ($this['actual_people'] + 1 >= $asemble_product['assemble_num']) {
            $this->save([
                'status' => 20
            ]);
            $order_list = (new BillUserModel)
                ->field(['order_id'])
                ->where('assemble_bill_id', '=', $this['assemble_bill_id'])
                ->select();
            $orderIds = helper::getArrayColumn($order_list, 'order_id');
            //更新主訂單表拼團狀態
            (new OrderModel)->where('order_id', 'in', $orderIds)
                ->save([
                    'assemble_status' => 20
                ]);
            // 是否是虛擬商品
            $order = OrderModel::detail($product['order_id']);
            if ($order['delivery_type']['value'] == DeliveryTypeEnum::NO_EXPRESS && $order['virtual_auto'] == 1) {
                (new OrderModel)->where('order_id', 'in', $orderIds)
                    ->save([
                        'assemble_status' => 20,
                        'delivery_status' => 20,
                        'delivery_time' => time(),
                        'receipt_status' => 20,
                        'receipt_time' => time(),
                        'order_status' => 30,
                        'virtual_content' => $order['product'][0]['virtual_content']
                    ]);
                $orderList = (new OrderModel)->where('order_id', 'in', $orderIds)
                    ->with(['product' => ['image', 'refund'], 'address', 'express', 'extractStore', 'advance'])
                    ->select();
                foreach ($orderList as $item) {
                    // 執行訂單完成後的操作
                    $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
                    $OrderCompleteService->complete([$item], $item['app_id']);
                    $item->sendWxExpress('', '');
                }
            }
            // 拼團訂單列印
            foreach ($orderIds as $orderId) {
                (new OrderPrinterService)->printTicket(OrderModel::detail($orderId));
            }
        } else {
            //更新主訂單表拼團狀態
            (new OrderModel)->where('order_id', '=', $product['order_id'])
                ->save([
                    'assemble_status' => 10
                ]);
        }
    }
}