<?php

namespace app\api\controller\plus\agent;

use app\api\controller\Controller;
use app\api\model\plus\agent\Apply as AgentApplyModel;
use app\api\model\settings\Message as MessageModel;
use  app\common\model\plus\agent\Setting;
use app\common\exception\BaseException;

/**
 * 分銷商申請
 */
class Apply extends Controller
{
    // 當前使用者
    private $user;

    /**
     * 構造方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->user = $this->getUser();   // 使用者資訊
    }

    /**
     * 提交分銷商申請
     */
    public function submit($platform = 'wx')
    {
        $data = $this->postData();
        if ($this->request->isGet()) {
            // 如果來源是小程式, 則獲取小程式訂閱訊息id.獲取支付成功,發貨通知.
            $template_arr = MessageModel::getMessageByNameArr($platform, ['agent_apply_user']);
            return $this->renderSuccess('', compact('template_arr'));
        }
        if (empty($data['name']) || empty($data['mobile'])) {
            throw new BaseException(['msg' => '使用者名稱或者手機號為空']);
        }
        $model = new AgentApplyModel;
        if ($model->submit($this->user, $data)) {
            return $this->renderSuccess('成功');
        }
        return $this->renderError($model->getError() ?: '提交失敗');
    }

    /*
     *獲取分銷商協議
     */
    public function getAgreement()
    {
        $model = new Setting();
        $data = $model->getItem('license');
        return $this->renderSuccess('', compact('data'));
    }

}