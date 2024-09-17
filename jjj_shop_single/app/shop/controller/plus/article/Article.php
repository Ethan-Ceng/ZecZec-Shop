<?php

namespace app\shop\controller\plus\article;

use app\shop\controller\Controller;
use app\shop\model\plus\article\Article as ArticleModel;
use app\shop\model\plus\article\Category as CategoryModel;

/**
 * 文章控制器
 */
class Article extends Controller
{
    /**
     * 文章列表
     */
    public function index()
    {
        $model = new ArticleModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增文章
     */
    public function add()
    {
        if($this->request->isGet()){
            // 文章分類
            $catgory = CategoryModel::getAll();
            return $this->renderSuccess('', compact('catgory'));
        }
        $model = new ArticleModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     *文章詳情
     */
    public function detail($article_id)
    {
        $model = new ArticleModel;
        return $this->renderSuccess('', $model->detail($article_id));
    }

    /**
     * 更新文章
     */
    public function edit($article_id)
    {
        if($this->request->isGet()){
            // 文章分類
            $catgory = CategoryModel::getAll();
            $model = ArticleModel::detail($article_id);
            return $this->renderSuccess('', compact('catgory', 'model'));
        }
        // 文章詳情
        $model = ArticleModel::detail($article_id);
        // 更新記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 刪除文章
     */
    public function delete($article_id)
    {
        // 文章詳情
        $model = ArticleModel::detail($article_id);
        if ($model->setDelete()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?:'刪除失敗');
    }

}