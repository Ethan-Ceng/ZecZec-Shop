<?php

namespace app\common\model\shop;

use app\common\model\BaseModel;
/**
 * 商家使用者許可權模型
 */
class Access extends BaseModel
{
    protected $name = 'shop_access';
    protected $pk = 'access_id';

    /*
     * 獲取所有許可權
     */
    protected static function getAll($isShow = 1)
    {
        if($isShow != -1){
            $data = static::withoutGlobalScope()
                ->where('is_show', '=', $isShow)
                ->order(['sort' => 'asc', 'create_time' => 'asc'])
                ->select();
        }else{
            $data = static::withoutGlobalScope()
                ->order(['sort' => 'asc', 'create_time' => 'asc'])
                ->select();
        }

        return $data ? $data->toArray() : [];
    }

    /**
     * 許可權資訊
     */
    public static function detail($where)
    {
        if(is_array($where)){
            return (new static())->where($where)->find();
        } else{
            return (new static())->where('access_id', '=', $where)->find();
        }
    }

    /**
     * 獲取許可權url集
     */
    public static function getAccessUrls($accessIds)
    {
        $urls = [];
        foreach (static::getAll(1) as $item) {
            in_array($item['access_id'], $accessIds) && $urls[] = $item['path'];
        }
        return $urls;
    }

    /**
     * 獲取許可權url集
     */
    public static function getAccessList($accessIds)
    {
        return (new static)::withoutGlobalScope()->where('access_id', 'in', $accessIds)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->select();
    }


    /**
     * 透過外掛分類id查詢
     */
    public static function getListByPlusCategoryId($category_id){
        $model = new static();
        return $model::withoutGlobalScope()->where('plus_category_id', '=', $category_id)
            ->where('is_show', '=', 1)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->select();
    }
}