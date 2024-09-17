<?php

namespace app\api\controller\message;

use app\api\controller\Controller;
use app\common\model\MessageBox as MessageBoxModel;
use app\common\model\MessageItem as MessageItemModel;
use think\Exception;

/**
 * 站內信控制器
 */
class Message extends Controller
{
    /**
     * 訊息列表列表
     */
    public function lists()
    {
        try {
            $user = $this->getUser();
            $model = new MessageBoxModel;
            $list = $model->with(['user', 'product'])->where('user_id', '=', $user->user_id)
                ->order(['create_time' => 'desc'])
                ->select();

            // 檢查是否獲取到資料
            if (!$list->isEmpty()) {
                return $this->renderSuccess('', compact('list'));
            } else {
                return $this->renderSuccess('沒有找到訊息記錄', compact('list'));
            }
        } catch (\Exception $e) {
            return $this->renderError($e->getMessage() ?: '獲取訊息列表失敗');
        }
    }

    /**
     * 傳送訊息
     * user_id
     * product_id
     * order_id
     * content 主題內容
     */
    public function sendMessage()
    {
        try {
            $user = $this->getUser();
            $data = $this->postData();
            // 例項化模型
            $messageBoxModel = new MessageBoxModel;
            $messageItemModel = new MessageItemModel;
            // message_box_id user_id - product_id - order_id

            $messageBoxId = isset($data['message_box_id']) ? $data['message_box_id'] : null;
            if (!isset($messageBoxId) || $messageBoxId === null || empty($messageBoxId)) {
                // 構建查詢條件
                $messageBoxId = $user['user_id'];

                if (!empty($data['product_id'])) {
                    $messageBoxId = $user['user_id'].'-'.$data['product_id'];
                }
                //

                // 查詢是否存在對話方塊
                $messagebox = $messageBoxModel->where('message_box_id', '=', $messageBoxId)->find();
                if (!empty($messagebox) && isset($messagebox['message_box_id'])) {
                    $messageBoxId = $messagebox->save([
                        'product_id' => $data['product_id'] ?? null,
                        'order_id' => $data['order_id'] ?? null,
                        'remark' => $data['remark'] ?? '',
                        'last_replied_at' => time(),
                    ]);
                } else {
                    // 如果不存在對話方塊，先建立新的對話方塊
                    $messageBoxData = [
                        'user_id' => $user->user_id,
                        'app_id' => $user->app_id,
                        'message_box_id' => $messageBoxId,
                        'product_id' => $data['product_id'] ?? null,
                        'order_id' => $data['order_id'] ?? null,
                        'remark' => $data['remark'] ?? '',
                        'last_replied_at' => time(),
                    ];
                    $messageBoxModel->save($messageBoxData); // 設定新建立的對話方塊ID
                }
            } else {
                // 查詢是否存在對話方塊
                $messageBoxModel->where('message_box_id', '=', $messageBoxId)->update(['last_replied_at' => time()]);
            }

            // 如果存在對話方塊，直接插入訊息項
            $messageItemModel->save([
                'message_box_id' => $messageBoxId,
                'content' => $data['content'],
                'app_id' => $user->app_id,
                'user_id' => $user->user_id,
            ]);

            return $this->renderSuccess('傳送成功');
        } catch (\Exception $e) {
            return $this->renderError($e->getMessage() ?: '傳送失敗');
        }
    }

    // 獲取對話詳情
    public function getMessageBoxDetail($messageBoxId)
    {
        $messageItemModel = new MessageItemModel();
        $messageItems = $messageItemModel->getMessageItems((int)$messageBoxId);

        if ($messageItems) {
            return $this->renderSuccess('對話詳情獲取成功', $messageItems);
        } else {
            return $this->renderError('獲取對話詳情失敗');
        }
    }


    /**
     * 刪除
     */
    public function delete($comment_id)
    {
        $model = new MessageBoxModel;
        if (!$model->remove($comment_id, $this->getUser())) {
            return $this->renderError($model->getError() ?: '刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }

}