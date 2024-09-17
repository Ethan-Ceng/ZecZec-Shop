<?php

namespace app\common\service\order;

use app\common\enum\order\OrderTypeEnum;
use app\common\model\order\Order as OrderModel;
/**
 * 訂單服務類
 */
class OrderService
{

    /**
     * 生成訂單號
     */
    public static function createOrderNo()
    {
        return date('Ymd') . substr(implode('', array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    /**
     * 整理訂單列表 (根據order_type獲取不同型別的訂單記錄)
     */
    public static function getOrderList($data, $orderIndex = 'order', $with = [])
    {
        // 整理訂單id
        $orderIds = [];
        foreach ($data as &$item) {
            $orderIds[$item['order_type']['value']][] = $item['order_id'];
        }
        // 獲取訂單列表
        $orderList = [];
        foreach ($orderIds as $orderType => $values) {
            $model = new OrderModel();
            $orderList[$orderType] = $model->getListByIds($values, $with);
        }
        // 格式化到資料來源
        foreach ($data as $key => &$item) {
            if (!isset($orderList[$item['order_type']['value']][$item['order_id']])) {
                $item->delete();
                unset($data[$key]);
                continue;
            }
            $item[$orderIndex] = $orderList[$item['order_type']['value']][$item['order_id']];
        }
        return $data;
    }

}