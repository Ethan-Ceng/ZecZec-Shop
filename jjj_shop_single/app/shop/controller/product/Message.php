<?php

namespace app\shop\controller\product;

use app\shop\controller\Controller;
use app\common\model\MessageBox as MessageBoxModel;
use app\common\model\MessageItem as MessageItemModel;


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
            $params = $this->postData();

            $model = new MessageBoxModel;
            $list = $model->with(['user', 'product'])
                ->order(['create_time' => 'desc'])
                ->paginate($params);


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
            $data = $this->postData();
            // 例項化模型
            $messageBoxModel = new MessageBoxModel;
            $messageItemModel = new MessageItemModel;

            $messageBoxId = $data['message_box_id'];
            if (!$messageBoxId) {
                // 構建查詢條件
               $this->renderError('信息錯誤');
            }

            // 如果存在對話方塊，直接插入訊息項
            $messageItemModel->save([
                'message_box_id' => $messageBoxId,
                'content' => $data['content'],
                'app_id' => MessageBoxModel::$app_id,
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