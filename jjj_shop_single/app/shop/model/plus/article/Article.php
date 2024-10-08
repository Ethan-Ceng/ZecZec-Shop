<?php

namespace app\shop\model\plus\article;

use app\common\model\plus\article\Article as ArticleModel;

/**
 * 文章模型
 */
class Article extends ArticleModel
{
    /**
     * 獲取文章列表
     */
    public function getList($params)
    {
        return $this->with(['image', 'category'])
            ->where('is_delete', '=', 0)
            ->order(['article_sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($params);

    }

    /**
     * 新增記錄
     */
    public function add($data)
    {
        if (empty($data['article_content'])) {
            $this->error = '請輸入文章內容';
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
        if (empty($data['article_content'])) {
            $this->error = '請輸入文章內容';
            return false;
        }
        return $this->save($data);
    }

    /**
     * 軟刪除
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }

    /**
     * 獲取文章總數量
     */
    public static function getArticleTotal($where)
    {
        $model = new static;
        return $model->where($where)->where('is_delete', '=', 0)->count();
    }
}