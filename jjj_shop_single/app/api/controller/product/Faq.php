<?php

namespace app\api\controller\product;

use app\api\controller\Controller;
use app\api\model\product\Faq as FaqModel;
use app\api\model\product\Product as ProductModel;

/**
 * 商品評價控制器
 */
class Faq extends Controller
{
    /**
     * 商品FAQ 列表
     */
    public function lists($product_id)
    {
        $faqIds = (new ProductModel)->field('faq_ids')->where('product_id', '=', $product_id)->find();
        if (!empty($faqIds) && is_string($faqIds['faq_ids'])) {
            $array = explode(',', $faqIds['faq_ids']);
            if (!empty($array)) {
                $list = (new FaqModel)->where('faq_id', 'in', $array)->select();
            } else {
                $list = [];
            }
        } else {
            $list = [];
        }
        return $this->renderSuccess('', compact('list' ));

    }
}