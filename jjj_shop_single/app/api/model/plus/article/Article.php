<?php

namespace app\api\model\plus\article;

use app\common\exception\BaseException;
use app\common\model\plus\article\Article as ArticleModel;

/**
 * 文章模型
 */
class Article extends ArticleModel
{
    /**
     * 追加欄位
     */
    protected $append = [
        'view_time'
    ];

    /**
     * 隱藏欄位
     * @var array
     */
    protected $hidden = [
        'is_delete',
        'app_id',
        'update_time'
    ];

    /**
     * 文章詳情：HTML實體轉換回普通字元
     */
    public function getArticleContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    public function getViewTimeAttr($value, $data)
    {
        return $data['virtual_views'] + $data['actual_views'];
    }

    /**
     * 文章詳情
     */
    public static function detail($article_id)
    {
        if (!$model = parent::detail($article_id)) {
            throw new BaseException(['msg' => '文章不存在']);
        }
        // 累積閱讀數
        $model->where('article_id', '=', $article_id)->inc('actual_views', 1)->update();
        return $model;
    }

    /**
     * 獲取文章列表
     */
    public function getList($category_id, $params)
    {
        $model = $this;
        $category_id > 0 && $model = $model->where('category_id', '=', $category_id);
        $list = $model->with(['image', 'category'])
            ->where('article_status', '=', 1)
            ->where('is_delete', '=', 0)
            ->order(['article_sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($params);
        foreach ($list as $item) {
            $item['add_time'] = date('Y-m-d', strtotime($item['create_time']));
        }
        return $list;
    }

}