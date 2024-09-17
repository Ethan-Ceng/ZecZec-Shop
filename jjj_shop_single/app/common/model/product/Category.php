<?php

namespace app\common\model\product;

use think\facade\Cache;
use app\common\model\BaseModel;

/**
 * 產品分類模型
 */
class Category extends BaseModel
{
    protected $pk = 'category_id';
    protected $name = 'category';

    /**
     * 分類圖片
     */
    public function images()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'image_id');
    }

    /**
     * 充值套餐詳情
     */
    public static function detail($category_id)
    {
        return (new static())->find($category_id);
    }


    public function detailWithImage($where)
    {
        return $this->with(['image'])->where($where)->find();
    }

    /**
     * 所有分類
     */
    public static function getALL()
    {
        $model = new static;
        if (!Cache::get('category_' . $model::$app_id)) {
            $data = $model->with(['images'])->order(['sort' => 'asc', 'create_time' => 'asc'])->select();
            $all = !empty($data) ? $data->toArray() : [];
            $tree = [];
            foreach ($all as $first) {
                if ($first['parent_id'] != 0) continue;
                $twoTree = [];
                foreach ($all as $two) {
                    if ($two['parent_id'] != $first['category_id']) continue;
                    $threeTree = [];
                    foreach ($all as $three)
                        $three['parent_id'] == $two['category_id']
                        && $threeTree[] = $three;
                    !empty($threeTree) && $two['child'] = $threeTree;
                    array_push($twoTree, $two);
                }
                if (!empty($twoTree)) {
                    $temp_two_tree = array_column($twoTree, 'sort');
                    array_multisort($temp_two_tree, SORT_ASC, $twoTree);
                    $first['child'] = $twoTree;
                }
                array_push($tree, $first);
            }
            Cache::tag('cache')->set('category_' . $model::$app_id, compact('all', 'tree'));
        }
        return Cache::get('category_' . $model::$app_id);
    }

    /**
     * 所有分類
     */
    public static function getShowALL()
    {
        $model = new static;
        if (!Cache::get('category_status' . $model::$app_id)) {
            $data = $model->with(['images'])->where('status', '=', 1)->order(['sort' => 'asc', 'create_time' => 'asc'])->select();
            $all = !empty($data) ? $data->toArray() : [];
            $tree = [];
            foreach ($all as $first) {
                if ($first['parent_id'] != 0) continue;
                $twoTree = [];
                foreach ($all as $two) {
                    if ($two['parent_id'] != $first['category_id']) continue;
                    $threeTree = [];
                    foreach ($all as $three)
                        $three['parent_id'] == $two['category_id']
                        && $threeTree[] = $three;
                    !empty($threeTree) && $two['child'] = $threeTree;
                    array_push($twoTree, $two);
                }
                if (!empty($twoTree)) {
                    $temp_two_tree = array_column($twoTree, 'sort');
                    array_multisort($temp_two_tree, SORT_ASC, $twoTree);
                    $first['child'] = $twoTree;
                }
                array_push($tree, $first);
            }
            Cache::tag('cache')->set('category_status' . $model::$app_id, compact('all', 'tree'));
        }
        return Cache::get('category_status' . $model::$app_id);
    }

    /**
     * 獲取所有分類
     */
    public static function getShowCacheAll()
    {
        return self::getShowALL()['all'];
    }

    /**
     * 獲取所有分類(樹狀結構)
     */
    public static function getShowCacheTree()
    {
        return self::getShowALL()['tree'];
    }

    /**
     * 獲取所有分類
     */
    public static function getCacheAll()
    {
        return self::getALL()['all'];
    }

    /**
     * 獲取所有分類(樹狀結構)
     */
    public static function getCacheTree()
    {
        return self::getALL()['tree'];
    }

    /**
     * 獲取所有分類(樹狀結構)
     * @return string
     */
    public static function getCacheTreeJson()
    {
        return json_encode(static::getCacheTree());
    }

    /**
     * 獲取指定分類下的所有子分類id
     */
    public static function getSubCategoryId($parent_id, $all = [])
    {
        $arrIds = [$parent_id];
        empty($all) && $all = self::getShowCacheAll();
        foreach ($all as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                unset($all[$key]);
                $subIds = self::getSubCategoryId($item['category_id'], $all);
                !empty($subIds) && $arrIds = array_merge($arrIds, $subIds);
            }
        }
        return $arrIds;
    }

    /**
     * 指定的分類下是否存在子分類
     */
    protected static function hasSubCategory($parentId)
    {
        $all = self::getCacheAll();
        foreach ($all as $item) {
            if ($item['parent_id'] == $parentId) {
                return true;
            }
        }
        return false;
    }


    /**
     * 關聯圖片
     */
    public function image()
    {
        return $this->belongsTo('app\common\model\file\UploadFile', 'image_id', 'file_id');
    }

    /**
     * 關聯圖片
     */
    public function parent()
    {
        return $this->belongsTo('app\common\model\product\Category', 'parent_id', 'category_id');
    }

    public function getListByIds($ids){
        return $this->field(['category_id', 'name', 'parent_id'])->where('category_id', 'in', $ids)->select();
    }
}
