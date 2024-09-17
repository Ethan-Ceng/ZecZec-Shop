<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\user\Cash as CashModel;
use app\api\model\settings\Setting as SettingModel;

/**
 * 使用者餘額提現
 */
class Cash extends Controller
{
    private $user;

    /**
     * 構造方法
     */
    public function initialize()
    {
        parent::initialize();
        // 使用者資訊
        $this->user = $this->getUser();
    }

    /**
     * 提現資料
     */
    public function index()
    {
        $setting = SettingModel::getItem('balance_cash');
        $cash_ratio = $setting['cash_ratio'];
        $balance = $this->user['balance'];
        $real_name = $this->user['real_name'];
        $pay_type = $setting['pay_type'];
        return $this->renderSuccess('', compact('cash_ratio', 'balance', 'real_name', 'pay_type'));
    }

    /**
     * 提交提現申請
     */
    public function submit($data)
    {
        $formData = json_decode(htmlspecialchars_decode($data), true);
        $model = new CashModel;
        if ($model->submit($this->user, $formData)) {
            return $this->renderSuccess('申請提現成功');
        }
        return $this->renderError($model->getError() ?: '提交失敗');
    }

    /**
     * 餘額提現明細
     */
    public function lists($status = -1)
    {

        $model = new CashModel;
        return $this->renderSuccess('', [
            // 提現明細列表
            'list' => $model->getList($this->user['user_id'], (int)$status, $this->postData()),
        ]);
    }

}