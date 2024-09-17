<?php

namespace app\api\model\user;

use app\api\model\plus\invitationgift\InvitationGift as InvitationGiftModel;
use app\api\model\settings\Setting as SettingModel;
use app\common\model\user\TaskLog as TaskLogModel;
use app\api\model\plus\sign\Sign as SignModel;
use think\facade\Cache;

/**
 * 任務記錄模型
 */
class TaskLog extends TaskLogModel
{
    public function getTask($user)
    {
        //獲取任務設定
        $data = SettingModel::getItem('task');
        foreach ($data['grow_task'] as &$item) {
            $item['status'] = $this->where('user_id', '=', $user['user_id'])
                ->where('task_type', '=', $item['task_type'])
                ->count();
        }
        foreach ($data['day_task'] as &$item) {
            $status = Cache::get('task_' . $item['task_type'] . date('Y-m-d') . $user['user_id']);
            $item['status'] = $status ? $status : 0;
        }
        //合併消費任務
        $order = [
            'name' => '消費任務',
            'image' => base_url() . 'image/task/order.png',
            'is_open' => '1',
            'task_type' => 'order',
            'rule' => '在商城消費獲取積分',
            'points' => 0,
            'status' => 0
        ];
        $setting = SettingModel::getItem('points');
        // 條件：後臺開啟購物送積分
        if ($setting['is_shopping_gift']) {
            $data['day_task'] = array_merge($data['day_task'], [$order]);
        }
        //查詢簽到是否開啟
        $sign = SettingModel::getItem('sign');
        if ($sign['is_open']) {
            $status = SignModel::where('user_id', '=', $user['user_id'])
                ->where('sign_date', '=', date('Y-m-d'))
                ->count();
            $array = [
                'name' => '簽到打卡',
                'image' => base_url() . 'image/task/sign.png',
                'is_open' => '1',
                'task_type' => 'sign',
                'rule' => '每日簽到獲取積分',
                'points' => 0,
                'status' => $status
            ];
            $data['day_task'] = array_merge($data['day_task'], [$array]);
        }
        //查詢邀請有禮
        $invitation = InvitationGiftModel::getShow();
        $data['invitation_id'] = 0;
        if ($invitation['is_open'] && $invitation['invitation_id']) {
            $inviteArray = [
                'name' => '邀請有禮',
                'image' => base_url() . 'image/task/invite.png',
                'is_open' => '1',
                'task_type' => 'invite',
                'rule' => '邀請好友獲取獎勵',
                'points' => 0,
                'status' => 0
            ];
            $data['day_task'] = array_merge($data['day_task'], [$inviteArray]);
            $data['invitation_id'] = $invitation['invitation_id'];
        }
        return $data;
    }
}