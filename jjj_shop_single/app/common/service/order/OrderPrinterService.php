<?php

namespace app\common\service\order;

use app\common\model\settings\Setting as SettingModel;
use app\common\model\settings\Printer as PrinterModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\library\printer\Driver as PrinterDriver;

/**
 * 訂單列印服務類
 */
class OrderPrinterService
{
    /**
     * 執行訂單列印
     */
    public function printTicket($order, $scene = 20)
    {
        // 印表機設定
        $printerConfig = SettingModel::getItem('printer', $order['app_id']);
        // 判斷是否開啟列印設定
        if (!$printerConfig['is_open']
            || !$printerConfig['printer_id']
            || !in_array($scene, $printerConfig['order_status'])) {
            return false;
        }
        // 獲取當前的印表機
        $printer = PrinterModel::detail($printerConfig['printer_id']);
        if (empty($printer) || $printer['is_delete']) {
            return false;
        }
        // 例項化印表機驅動
        $PrinterDriver = new PrinterDriver($printer);
        // 獲取訂單列印內容
        $content = $this->getPrintContent($order);
        // 執行列印請求
        return $PrinterDriver->printTicket($content);
    }

    /**
     * 構建訂單列印的內容
     */
    private function getPrintContent($order)
    {
        // 商城名稱
        $storeName = SettingModel::getItem('store', $order['app_id'])['name'];
        // 收貨地址
        $address = $order['address'];
        // 拼接模板內容
        $content = "<CB>{$storeName}</CB><BR>";
        $content .= '--------------------------------<BR>';
        $content .= "暱稱：{$order['user']['nickName']} [{$order['user_id']}]<BR>";
        $content .= "訂單號：{$order['order_no']}<BR>";
        $content .= '付款時間：' . date('Y-m-d H:i:s', $order['pay_time']) . '<BR>';
        // 收貨人資訊
        if ($order['delivery_type']['value'] == DeliveryTypeEnum::EXPRESS) {
            $content .= "--------------------------------<BR>";
            $content .= "收貨人：{$address['name']}<BR>";
            $content .= "聯絡電話：{$address['phone']}<BR>";
            $content .= '收貨地址：' . $address->getFullAddress() . '<BR>';
        }
        // 自提資訊
        if ($order['delivery_type']['value'] == DeliveryTypeEnum::EXTRACT && !empty($order['extract'])) {
            $content .= "--------------------------------<BR>";
            $content .= "聯絡人：{$order['extract']['linkman']}<BR>";
            $content .= "聯絡電話：{$order['extract']['phone']}<BR>";
            $content .= "自提門店：{$order['extract_store']['shop_name']}<BR>";
        }
        // 商品資訊
        $content .= '=========== 商品資訊 ===========<BR>';
        foreach ($order['product'] as $key => $product) {
            $content .= ($key + 1) . ".商品名稱：{$product['product_name']}<BR>";
            !empty($product['product_attr']) && $content .= "　商品規格：{$product['product_attr']}<BR>";
            $content .= "　購買數量：{$product['total_num']}<BR>";
            $content .= "　商品總價：{$product['total_price']}元<BR>";
            $content .= '--------------------------------<BR>';
        }
        // 買家備註
        if (!empty($order['buyer_remark'])) {
            $content .= '============ 買家備註 ============<BR>';
            $content .= "<B>{$order['buyer_remark']}</B><BR>";
            $content .= '--------------------------------<BR>';
        }
        // 訂單金額
        if ($order['coupon_money'] > 0) {
            $content .= "優惠券：-{$order['coupon_money']}元<BR>";
        }
        if ($order['points_num'] > 0) {
            $content .= "積分抵扣：-{$order['points_money']}元<BR>";
        }
        if ($order['update_price']['value'] != '0.00') {
            $content .= "後臺改價：{$order['update_price']['symbol']}{$order['update_price']['value']}元<BR>";
        }
        // 運費
        if ($order['delivery_type']['value'] == DeliveryTypeEnum::EXPRESS) {
            $content .= "運費：{$order['express_price']}元<BR>";
            $content .= '------------------------------<BR>';
        }
        // 實付款
        $content .= "<C>實付款：<BOLD><B>{$order['pay_price']}</B></BOLD>元</C><BR>";
        return $content;
    }

}