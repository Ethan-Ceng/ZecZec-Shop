<?php

namespace app\api\model\settings;

use app\common\model\settings\Message as MessageModel;
use app\common\model\settings\MessageSettings as MessageSettingsModel;

/**
 * 訊息模型
 */
class Message extends MessageModel
{
    /**
     * 獲取訊息
     */
    public static function getMessageByNameArr($platform, $message_ename_arr)
    {
        $template_arr = [];
        //只適用於微信和公眾號
        if ($platform != 'wx' && $platform != 'mp') {
            return $template_arr;
        }
        $status = $platform . "_status";
        $templates = $platform . "_template";
        $model = new self();
        //子查詢先過濾條件
        $settings_model = new MessageSettingsModel;
        $subsql = $settings_model->where($status, '=', 1)
            ->where('app_id', '=', self::$app_id)
            ->buildSql();

        $template_list = $model->withoutGlobalScope()->alias('message')->field(['message.*', 'settings.' . $status, 'settings.'.$templates])
            ->where('message.message_ename', 'in', $message_ename_arr)
            ->where('message.is_delete', '=', 0)
            ->join([$subsql => 'settings'], 'settings.message_id = message.message_id', 'left')
            ->order(['message.sort' => 'asc'])
            ->select()->toArray();

        foreach ($template_list as $template) {
            if ($template[$templates]) {
                $json = json_decode($template[$templates], true);
                array_push($template_arr, $json['template_id']);
            }
        }
        return $template_arr;
    }
}