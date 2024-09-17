<?php

namespace app\shop\model\plus\bargain;

use app\common\model\plus\bargain\BargainSku as BargainSkuModel;

/**
 * Class BargainProductSku
 * 砍價商品規格模型
 * @package app\shop\model\plus\bargain
 */
class BargainSku extends BargainSkuModel
{
    /**
     * @param $data
     * 儲存資料
     * @return \think\Collection
     */
    public function editAll($data)
    {
        return self::saveAll($data);
    }

    /**
     *刪除商品規格
     */
    public function delSku($id)
    {
        $this->where('bargain_product_id', '=', $id)->delete();
        return true;
    }
}