<?php

namespace app\shop\controller\setting;

use app\shop\controller\Controller;
use app\shop\model\settings\Message as MessageModel;
use app\shop\model\settings\MessageField as MessageFieldModel;
use app\shop\model\settings\MessageSettings as MessageSettingsModel;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 訊息控制器
 */
class Message extends Controller
{
    /**
     * 訊息首頁
     */
    public function index($message_to = 10)
    {
        $model = new MessageModel;
        $list = $model->getAll($message_to);
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 訊息欄位
     */
    public function field($message_id, $message_type)
    {
        $model = new MessageFieldModel;
        $list = $model->getAll($message_id);
        $message_settings = MessageSettingsModel::detailByMessageId($message_id);
        //公眾號設定
        $settings = null;
        if ($message_type == 'mp' && $message_settings) {
            $settings = $message_settings['mp_template'] ? json_decode($message_settings['mp_template'], true) : '';
        }
        if ($message_type == 'wx' && $message_settings) {
            $settings = $message_settings['wx_template'] ? json_decode($message_settings['wx_template'], true) : '';
        }
        if ($message_type == 'sms' && $message_settings) {
            $settings = $message_settings['sms_template'] ? json_decode($message_settings['sms_template'], true) : '';
        }
        //合併欄位
        return $this->renderSuccess('', compact('list', 'settings'));
    }

    /**
     * 儲存設定
     */
    public function saveSettings()
    {
        $data = $this->postData();
        // 新增記錄
        $model = MessageSettingsModel::detailByMessageId($data['message_id']);
        if (!$model) {
            $model = new MessageSettingsModel();
        }
        if ($model->saveSettings($data)) {
            return $this->renderSuccess('儲存成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }


    /**
     * 修改狀態
     */
    public function updateSettingsStatus($message_settings_id, $message_type)
    {
        $model = MessageSettingsModel::detail($message_settings_id);
        if ($model->updateSettingsStatus($message_type)) {
            return $this->renderSuccess('儲存成功');
        }
        return $this->renderError($model->getError() ?: '儲存失敗');
    }
}
