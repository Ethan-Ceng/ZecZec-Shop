<?php

namespace app\shop\model\product;

use app\common\model\product\ProductFaq as ProductFaqModel;

/**
 * 產品圖片模型
 */
class ProductFaq extends ProductFaqModel
{

    /**
     * 新增記錄
     */
    public function add($data)
    {
        if (empty($data['question'])) {
            $this->error = '請輸入問題';
            return false;
        }

        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 更新記錄
     */
    public function edit($data)
    {
        $faq = $this->where('faq_id', '=', $data['faq_id'])->find();
        if (!$faq) {
            $this->error = '請輸入問題';
            return false;
        }
        $data['app_id'] = self::$app_id;
        return $faq->save($data);
    }

    /**
     * 軟刪除
     */
    public function setDelete($faq_id)
    {
        return $this->where('faq_id', '=', $faq_id)->save(['is_delete' => 1]);
    }
}
