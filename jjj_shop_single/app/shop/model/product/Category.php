<?php

namespace app\shop\model\product;

use think\facade\Cache;
use app\common\model\product\Category as CategoryModel;

/**
 * 商品分類模型
 */
class Category extends CategoryModel
{
    /**
     * 新增新記錄
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        $this->deleteCache();
        return $this->save($data);
    }

    /**
     * 編輯記錄
     */
    public function edit($data)
    {
        // 驗證：一級分類如果存在子類，則不允許移動
        if ($data['parent_id'] > 0 && static::hasSubCategory($this['category_id'])) {
            $this->error = '該分類下存在子分類，不可以移動';
            return false;
        }
        $this->deleteCache();
        !array_key_exists('image_id', $data) && $data['image_id'] = 0;
        return $this->save($data) !== false;
    }

    /**
     * 刪除商品分類
     */
    public function remove($categoryId)
    {
        // 判斷是否存在商品
        if ($productCount = (new Product)->getProductTotal(['category_id' => $categoryId])) {
            $this->error = '該分類下存在' . $productCount . '個商品，不允許刪除';
            return false;
        }
        // 判斷是否存在子分類
        if (static::hasSubCategory($categoryId)) {
            $this->error = '該分類下存在子分類，請先刪除';
            return false;
        }
        $this->deleteCache();
        return $this->delete();
    }

    /**
     * 刪除快取
     */
    private function deleteCache()
    {
        Cache::delete('category_' . static::$app_id);
        Cache::delete('category_status' . static::$app_id);
        return true;
    }

    /**
     * 編輯記錄
     */
    public function setStatus($data)
    {
        $this->deleteCache();
        return $this->save($data) !== false;
    }

}
