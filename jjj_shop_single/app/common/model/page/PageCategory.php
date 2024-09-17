<?php

namespace app\common\model\page;

use app\common\model\BaseModel;

/**
 * app分類頁模板模型
 */
class PageCategory extends BaseModel
{
    //表名
    protected $name = 'page_category';
    //主鍵欄位名
    protected $pk = 'app_id';

    /**
     * 獲取應用資訊
     */
    public static function detail()
    {
        //全域性有app_id，不用加
        $model = (new static())->find();
        // 如果沒有預設值,先插入
        if(!$model){
            $model = new self();
            $model->save([
                'app_id' => self::$app_id,
                'category_style' => 10
            ]);
        }
        return $model;
    }

}
