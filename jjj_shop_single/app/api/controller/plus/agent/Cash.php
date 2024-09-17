<?php

namespace app\api\controller\plus\agent;

use app\api\controller\Controller;
use app\api\model\plus\agent\Setting;
use app\api\model\plus\agent\User as AgentUserModel;
use app\api\model\plus\agent\Cash as CashModel;
use app\api\model\settings\Message as MessageModel;

/**
 * 分銷商提現
 */
class Cash extends Controller
{
    private $user;

    private $Agent;
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
     * 提交提現申請
     */
    public function submit($data, $platform = 'wx')
    {
        if ($this->request->isGet()) {
            // 如果來源是小程式, 則獲取小程式訂閱訊息id.獲取支付成功,發貨通知.
            $template_arr = MessageModel::getMessageByNameArr($platform, ['agent_cash_user']);
            return $this->renderSuccess('', compact('template_arr'));
        }
        $formData = json_decode(htmlspecialchars_decode($data), true);

        $model = new CashModel;
        if ($model->submit($this->Agent, $formData)) {
            return $this->renderSuccess('申請提現成功');
        }
        return $this->renderError($model->getError() ?: '提交失敗');
    }

    /**
     * 分銷商提現明細
     */
    public function lists($status = -1)
    {

        $model = new CashModel;
        return $this->renderSuccess('', [
            // 提現明細列表
            'list' => $model->getList($this->user['user_id'], (int)$status, $this->postData()),
            // 頁面文字
            'words' => $this->setting['words']['values'],
        ]);
    }

}