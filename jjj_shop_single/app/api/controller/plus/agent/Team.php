<?php

namespace app\api\controller\plus\agent;

use app\api\controller\Controller;
use app\api\model\plus\agent\Setting;
use app\api\model\plus\agent\User as AgentUserModel;
use app\api\model\plus\agent\Referee as RefereeModel;

/**
 * 我的團隊
 */
class Team extends Controller
{
    // 使用者資訊
    private $user;
    // 分銷商使用者資訊
    private $Agent;
    // 分銷商設定
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
        $this->Agent = AgentUserModel::detail($this->user['user_id']);
        // 分銷商設定
        $this->setting = Setting::getAll();
    }

    /**
     * 我的團隊列表
     */
    public function lists($level = -1)
    {
        $model = new RefereeModel;
        return $this->renderSuccess('', [
            // 分銷商使用者資訊
            'agent' => $this->Agent,
            // 我的團隊列表
            'list' => $model->getList($this->user['user_id'], (int)$level),
            // 基礎設定
            'setting' => $this->setting['basic']['values'],
            // 頁面文字
            'words' => $this->setting['words']['values'],
        ]);
    }

}