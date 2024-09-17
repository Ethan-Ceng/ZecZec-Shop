<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\plus\agent\Referee;
use app\api\model\plus\agent\Setting;
use app\api\model\plus\agent\User as AgentUserModel;
use app\api\model\plus\agent\Apply as AgentApplyModel;
use app\api\model\settings\Message as MessageModel;

/**
 * 分銷中心
 */
class Agent extends Controller
{
    // 使用者
    private $user;
    // 分銷商
    private $agent;
    // 分銷設定
    private $setting;

    /**
     * 構造方法
     */
    public function initialize()
    {
        parent::initialize();
        // 使用者資訊
        $this->user = $this->getUser();
        // 分銷商使用者資訊
        $this->agent = AgentUserModel::detail($this->user['user_id']);
        // 分銷商設定
        $this->setting = Setting::getAll();
    }

    /**
     * 分銷商中心
     */
    public function center()
    {
        return $this->renderSuccess('', [
            // 當前是否為分銷商
            'is_agent' => $this->isAgentUser(),
            // 當前是否在申請中
            'is_applying' => AgentApplyModel::isApplying($this->user['user_id']),
            // 當前使用者資訊
            'user' => $this->user,
            // 分銷商使用者資訊
            'agent' => $this->agent,
            // 背景圖
            'background' => $this->setting['background']['values']['index'],
            // 頁面文字
            'words' => $this->setting['words']['values'],
        ]);
    }

    /**
     * 分銷商申請狀態
     */
    public function apply($referee_id = null, $platform= '')
    {
        // 推薦人暱稱
        $referee_name = '平臺';
        // 如果之前有關聯分銷商，則繼續關聯之前的分銷商
        $has_referee_id = Referee::getRefereeUserId($this->user['user_id'], 1);
        if($has_referee_id > 0){
            $referee_id = $has_referee_id;
        }
        if ($referee_id > 0 && ($referee = AgentUserModel::detail($referee_id))) {
            $referee_name = $referee['user']['nickName'];
        }

        return $this->renderSuccess('', [
            // 當前是否為分銷商
            'is_agent' => $this->isAgentUser(),
            // 當前是否在申請中
            'is_applying' => AgentApplyModel::isApplying($this->user['user_id']),
            // 推薦人暱稱
            'referee_name' => $referee_name,
            // 背景圖
            'background' => $this->setting['background']['values']['apply'],
            // 頁面文字
            'words' => $this->setting['words']['values'],
            // 申請協議
            'license' => $this->setting['license']['values']['license'],
            // 如果來源是小程式, 則獲取小程式訂閱訊息id.獲取售後通知.
            'template_arr' => MessageModel::getMessageByNameArr($platform, ['agent_apply_user']),
        ]);
    }

    /**
     * 分銷商提現資訊
     */
    public function cash($platform = '')
    {
        // 如果來源是小程式, 則獲取小程式訂閱訊息id.獲取售後通知.
        $template_arr = MessageModel::getMessageByNameArr($platform, ['agent_cash_user']);
        return $this->renderSuccess('', [
            // 分銷商使用者資訊
            'agent' => $this->agent,
            // 結算設定
            'settlement' => $this->setting['settlement']['values'],
            // 背景圖
            'background' => $this->setting['background']['values']['cash_apply'],
            // 頁面文字
            'words' => $this->setting['words']['values'],
            // 小程式訊息
            'template_arr' => $template_arr
        ]);
    }

    /**
     * 當前使用者是否為分銷商
     */
    private function isAgentUser()
    {
        return !!$this->agent && !$this->agent['is_delete'];
    }

}