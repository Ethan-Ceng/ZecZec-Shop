<?php

namespace app\common\model\plus\article;

use app\common\model\BaseModel;

/**
 * 文章模型
 */
class Article extends BaseModel
{
    protected $name = 'article';
    protected $pk = 'article_id';

    /**
     * 關聯文章封面圖
     * @return \think\model\relation\HasOne
     */
    public function image()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'image_id');
    }

    /**
     * 關聯文章分類表
     * @return \think\model\relation\BelongsTo
     */
    public function category()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->BelongsTo("app\\{$module}\\model\\plus\\article\\Category", 'category_id', 'category_id');
    }

    /**
     * 展示的瀏覽次數
     * @param $data
     * @return mixed
     */
    public function getShowViewsAttr($data)
    {
        return $data['virtual_views'] + $data['actual_views'];
    }

    /**
     * 文章詳情
     * @param $article_id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function detail($article_id)
    {
        return (new static())->with(['image', 'category'])->find($article_id);
    }
}
