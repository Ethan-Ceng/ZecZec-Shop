<?php

namespace app\api\model\product;

use app\api\model\order\OrderProduct;
use app\common\model\product\Comment as CommentModel;
use app\common\exception\BaseException;

class Comment extends CommentModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'status',
        'sort',
        'order_id',
        'product_id',
        'order_product_id',
        'is_delete',
        'update_time'
    ];

    /**
     * 關聯使用者表
     */
    public function users()
    {
        return $this->belongsTo('app\\common\\model\\user\\User')->field(['user_id', 'nickName', 'avatarUrl']);
    }

    /**
     * 獲取指定商品評價列表
     */
    public function getProductCommentList($product_id, $scoreType = -1, $limit = 15)
    {
        // 篩選條件
        $filter = [
            'product_id' => $product_id,
            'is_delete' => 0,
            'status' => 1,
        ];
        // 評分
        $scoreType > 0 && $filter['score'] = $scoreType;
        return $this->with(['OrderProduct', 'image.file', 'users'])
            ->where($filter)
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($limit);
    }

    /**
     * 獲取指定評分總數
     */
    public function getTotal($product_id)
    {
        return $this->field([
            'count(comment_id) AS `all`',
            'count(score = 10 OR NULL) AS `praise`',
            'count(score = 20 OR NULL) AS `review`',
            'count(score = 30 OR NULL) AS `negative`',
        ])->where([
            'product_id' => $product_id,
            'is_delete' => 0,
            'status' => 1
        ])->find();
    }

    /**
     * 驗證訂單是否允許評價
     */
    public function checkOrderAllowComment($order)
    {
        // 驗證訂單是否已完成
        if ($order['order_status']['value'] != 30) {
            $this->error = '該訂單未完成，無法評價';
            return false;
        }
        // 驗證訂單是否已評價
        if ($order['is_comment'] == 1) {
            $this->error = '該訂單已完成評價';
            return false;
        }
        return true;
    }

    /**
     * 根據已完成訂單商品 新增評價
     */
    public function addForOrder($order, $productList, $formJsonData)
    {
        // 生成 formData
        $formData = $this->formatFormData($formJsonData);
        // 生成評價資料
        $data = $this->createCommentData($order['user_id'], $order['order_id'], $productList, $formData);
        if (empty($data)) {
            $this->error = '沒有輸入評價內容';
            return false;
        }
        $status = $this->transaction(function () use ($order, $productList, $formData, $data) {
            // 記錄評價內容
            $result = $this->saveAll($data);
            // 記錄評價圖片
            $this->saveAllImages($result, $formData);
            // 更新訂單評價狀態
            $isComment = count($productList) === count($data);
            $this->updateOrderIsComment($order, $isComment, $data);
            return true;
        });
        if ($status) {
            $commentCount = (new OrderProduct)->where('order_id', '=', $order['order_id'])
                ->where('is_comment', '=', 0)
                ->count();
            if ($commentCount <= 0) {
                $order->save(['is_comment' => 1]);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 更新訂單評價狀態
     */
    private function updateOrderIsComment($order, $isComment, $commentList)
    {
        // 更新訂單商品
        $orderProductData = [];
        foreach ($commentList as $comment) {
            $orderProductData[] = [
                'data' => [
                    'is_comment' => 1
                ],
                'where' => [
                    'order_product_id' => $comment['order_product_id'],
                ],
            ];
        }
        // 更新訂單
        $isComment && $order->save(['is_comment' => 1]);
        return (new OrderProduct)->updateAll($orderProductData);
    }

    /**
     * 生成評價資料
     */
    private function createCommentData($user_id, $order_id, $productList, $formData)
    {
        $data = [];
        foreach ($productList as $product) {
            if (!isset($formData[$product['order_product_id']])) {
                throw new BaseException(['msg' => '提交的資料不合法']);
            }
            $item = $formData[$product['order_product_id']];
            !empty($item['content']) && $data[$product['order_product_id']] = [
                'score' => $item['score'],
                'content' => $item['content'],
                'is_picture' => !empty($item['image_list']),
                'sort' => 100,
                'status' => 0,
                'user_id' => $user_id,
                'order_id' => $order_id,
                'product_id' => $item['product_id'],
                'order_product_id' => $item['order_product_id'],
                'app_id' => self::$app_id
            ];
        }
        return $data;
    }

    /**
     * 格式化 formData
     */
    private function formatFormData($formJsonData)
    {
        return array_column(json_decode($formJsonData, true), null, 'order_product_id');
    }

    /**
     * 記錄評價圖片
     */
    private function saveAllImages($commentList, $formData)
    {
        // 生成評價圖片資料
        $imageData = [];
        foreach ($commentList as $comment) {
            $item = $formData[$comment['order_product_id']];
            foreach ($item['image_list'] as $imageId) {
                $imageData[] = [
                    'comment_id' => $comment['id'],
                    'image_id' => $imageId['file_id'],
                    'app_id' => self::$app_id
                ];
            }
        }
        $model = new CommentImage;
        return !empty($imageData) && $model->saveAll($imageData);
    }

    /**
     * 獲取使用者的評價列表
     */
    public function getUserCommentList($user, $data)
    {
        // 篩選條件
        $filter = [
            'is_delete' => 0,
            'user_id' => $user['user_id']
        ];
        return $this->with(['OrderProduct.image', 'image.file'])
            ->where($filter)
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($data);
    }

    /**
     * 刪除評價
     */
    public function remove($comment_id, $user)
    {
        return $this->where('comment_id', '=', $comment_id)
            ->where('user_id', '=', $user['user_id'])
            ->update(['is_delete' => 1]);
    }
}