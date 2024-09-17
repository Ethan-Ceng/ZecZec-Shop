<?php

namespace app\shop\model\plus\card;

use app\common\model\plus\card\Category as CategoryModel;
use app\shop\model\plus\card\Card as CardModel;

/**
 * 文章分類模型
 */
class Category extends CategoryModel
{
    /**
     * 分類詳情
     */
    public static function detail($category_id)
    {
        return (new static())->find($category_id);
    }

    /**
     * 新增新記錄
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 編輯記錄
     */
    public function edit($data)
    {
        $data['create_time'] = strtotime($data['create_time']);
        $data['update_time'] = time();
        return $this->save($data);
    }

    /**
     * 刪除商品分類
     */
    public function remove()
    {
        // 判斷是否存在文章
        $articleCount = CardModel::getArticleTotal(['category_id' => $this['category_id']]);
        if ($articleCount > 0) {
            $this->error = '該分類下存在' . $articleCount . '個卡券，不允許刪除';
            return false;
        }
        return $this->delete();
    }

}