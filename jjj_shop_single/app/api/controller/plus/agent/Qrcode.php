<?php

namespace app\api\controller\plus\agent;

use app\api\controller\Controller;
use app\api\model\plus\agent\Setting;
use app\api\model\plus\agent\User as AgentUserModel;
use app\common\service\qrcode\PosterService;

/**
 * 推廣二維碼
 */
class Qrcode extends Controller
{
    // 當前使用者
    protected $user;
    // 分銷商
    protected $agent;
    // 分銷設定
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
        $this->agent = AgentUserModel::detail($this->user['user_id']);
        // 分銷商設定
        $this->setting = Setting::getAll();
    }

    /**
     * 獲取推廣二維碼
     */
    public function poster($source)
    {
        $Qrcode = new PosterService($this->agent, $source);
        return $this->renderSuccess('', [
            // 二維碼圖片地址
            'qrcode' => $Qrcode->getImage(),
        ]);
    }

}