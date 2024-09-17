<?php

namespace app\shop\controller\plus\card;

use app\shop\controller\Controller;
use app\shop\model\plus\card\Card as CardModel;
use app\shop\model\plus\card\Category as CategoryModel;
use app\shop\model\product\Product as ProductModel;

/**
 * 文章控制器
 */
class Card extends Controller
{
    /**
     * 文章列表
     */
    public function index()
    {
        $model = new CardModel;
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
            $category = CategoryModel::getAll();
            // 所有商品
            $product = ProductModel::getAll();
            return $this->renderSuccess('', compact('category', 'product'));
        }
        $model = new CardModel;
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
        $model = new CardModel;
        return $this->renderSuccess('', $model->detail($article_id));
    }

    /**
     * 更新文章
     */
    public function edit($card_id)
    {
        if($this->request->isGet()){
            // 文章分類
            $category = CategoryModel::getAll();
            $product = ProductModel::getAll();
            $model = CardModel::detail($card_id);
            return $this->renderSuccess('', compact('category', 'product', 'model'));
        }
        // 文章詳情
        $model = CardModel::detail($card_id);
        // 更新記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 更新文章
     */
    public function code($card_id)
    {
        if($this->request->isGet()){
            $model = CardModel::detail($card_id);
            return $this->renderSuccess('', compact('model'));
        }
        // 文章詳情
        $model = CardModel::detail($card_id);
        // 更新記錄
        if ($model->code($this->postData())) {
            return $this->renderSuccess('生成成功');
        }
        return $this->renderError($model->getError() ?: '生成失敗');
    }

    /**
     * 刪除文章
     */
    public function delete($card_id)
    {
        // 文章詳情
        $model = CardModel::detail($card_id);
        if ($model->setDelete()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?:'刪除失敗');
    }

}