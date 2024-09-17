<?php

namespace app\shop\model\page;

use app\common\model\page\PageCategory as PageCategoryModel;

/**
 * app分類頁模板模型
 */
class PageCategory extends PageCategoryModel
{
    /**
     * 編輯記錄
     */
    public function edit($data)
    {
        return $this->save($data);
    }

}
