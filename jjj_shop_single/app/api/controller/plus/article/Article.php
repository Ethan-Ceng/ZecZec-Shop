<?php

namespace app\api\controller\plus\article;

use app\api\controller\Controller;
use app\api\model\plus\article\Article as ArticleModel;
use app\api\model\plus\article\Category as CategoryModel;

/**
 * 文章控制器
 */
class Article extends Controller
{
    /**
     *獲取分類
     */
    public function category()
    {
        // 文章分類
        $category = CategoryModel::getAll();
        return $this->renderSuccess('', compact('category'));
    }

    /**
     * 文章列表
     */
    public function index($category_id = 0)
    {
        $model = new ArticleModel;
        $list = $model->getList($category_id, $this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     *文章詳情
     */
    public function detail($article_id, $url = "")
    {
        $detail = ArticleModel::detail($article_id);
        // 微信公眾號分享引數
        $share = $this->getShareParams($url, $detail['article_title'], $detail['article_title'], '/pages/article/detail/detail', $detail['image'] ? $detail['image']['file_path'] : '');
        return $this->renderSuccess('', compact('detail', 'share'));
    }

}