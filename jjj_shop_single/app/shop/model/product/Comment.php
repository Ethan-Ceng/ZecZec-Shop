<?php

namespace app\shop\model\product;

use app\common\model\product\Comment as CommentModel;

class Comment extends CommentModel
{
    /**
     * 軟刪除
     */
    public function setDelete($comment_id)
    {
        return $this->where('comment_id', '=', $comment_id)->save(['is_delete' => 1]);
    }

    /**
     * 獲取評價總數量
     */
    public function getCommentTotal($day = "")
    {
        $model = $this;
        if ($day) {
            $model = $model->where('create_time', 'between', [strtotime($day), strtotime($day) + 86399]);
        }
        return $model->where(['is_delete' => 0])->count();
    }

    /**
     * 獲取待稽核商品評價總數量
     */
    public function getReviewCommentTotal()
    {
        return $this->where(['is_delete' => 0, 'status' => 0])->count();
    }


    /**
     * 更新記錄
     */
    public function edit($data)
    {
        $this->where('comment_id', '=', $data['comment_id'])->save([
            'status' => $data['status']
        ]);
        return true;
    }

}