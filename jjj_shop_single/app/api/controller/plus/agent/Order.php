<?php

namespace app\api\controller\plus\agent;

use app\api\controller\Controller;
use app\api\model\plus\agent\Setting;
use app\api\model\plus\agent\User as AgentUserModel;
use app\api\model\plus\agent\Order as OrderModel;

/**
 * 分銷商訂單
 */
class Order extends Controller
{
    // 當前使用者
    protected $user;
    // 分銷商使用者資訊
    protected $Agent;
    // 分銷商設定
    protected $setting;

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
     * 分銷商訂單列表
     */
    public function lists($settled = -1)
    {
        $model = new OrderModel;
        return $this->renderSuccess('', [
            // 提現明細列表
            'list' => $model->getList($this->user['user_id'], (int)$settled),
            // 頁面文字
            'words' => $this->setting['words']['values'],
        ]);
    }

}