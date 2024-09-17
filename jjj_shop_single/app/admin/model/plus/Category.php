<?php

namespace app\admin\model\plus;

use app\common\model\plus\plus\Category as CategoryModel;
use app\admin\model\Access as AccessModel;
/**
 * 外掛分類模型
 */
class Category extends CategoryModel
{
    /**
     * 獲取所有外掛
     */
    public static function getAll()
    {
        $model = new static();
        $list = $model->order(['sort' => 'asc', 'create_time' => 'asc'])->select();
        // 查詢分類下的外掛
        foreach ($list as $category){
            $category['children'] = AccessModel::getListByPlusCategoryId($category['plus_category_id']);
        }
        return $list;
    }

    /**
     * 刪除許可權
     */
    public function remove()
    {
        return $this->delete();
    }
}