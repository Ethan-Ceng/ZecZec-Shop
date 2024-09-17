<?php

namespace app\api\controller\plus\assemble;

use app\api\controller\Controller;
use app\api\model\plus\assemble\Bill as BillModel;
use app\api\model\plus\assemble\Product as ProductModel;
/**
 * 參團詳情控制器
 */
class Bill extends Controller
{
    /**
     * 拼團商品詳情
     */
    public function detail($assemble_bill_id, $url = '')
    {
        $bill = BillModel::detail($assemble_bill_id, ['activity', 'user', 'billUser.user']);
        $product = ProductModel::detail($bill['assemble_product_id'], ['product' => ['sku', 'image.file'], 'assembleSku']);
        // 微信公眾號分享引數
        $dif_people = $product['assemble_num'] - $bill['actual_people'];
        $share = $this->getShareParams($url, "【僅限{$dif_people}個名額】，快來參與拼團吧", $product['product']['product_name'], '/pages/plus/assemble/fight-group-detail/fight-group-detail', $product['product']['image'][0]['file_path']);
        return $this->renderSuccess('', compact( 'bill', 'product', 'share'));
    }
}