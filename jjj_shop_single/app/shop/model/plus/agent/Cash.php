<?php

namespace app\shop\model\plus\agent;

use app\common\model\app\AppMp as AppMpModel;
use app\common\model\app\AppOpen as AppOpenModel;
use app\common\model\app\AppWx as AppWxModel;
use app\common\model\plus\agent\Setting;
use app\common\library\easywechat\AppWx;
use app\common\library\easywechat\AppMp;
use app\common\service\message\MessageService;
use app\common\service\order\OrderService;
use app\common\library\easywechat\WxPay;
use app\common\model\plus\agent\Cash as CashModel;
use app\shop\model\user\User as UserModel;
use app\shop\service\order\ExportService;
use app\shop\model\plus\agent\User as AgentUserModel;

/**
 * 分銷商提現明細模型
 */
class Cash extends CashModel
{
    /**
     * 獲取器：申請時間
     */
    public function getAuditTimeAttr($value)
    {
        return $value > 0 ? date('Y-m-d H:i:s', $value) : 0;
    }

    /**
     * 獲取器：打款方式
     */
    public function getPayTypeAttr($value)
    {
        return ['text' => $this->payType[$value], 'value' => $value];
    }

    /**
     * 獲取分銷商提現列表
     */
    public function getList($user_id = null, $apply_status = -1, $pay_type = -1, $search = '')
    {
        $model = $this;
        // 構建查詢規則
        $model = $model->alias('cash')
            ->with(['user'])
            ->field('cash.*, agent.real_name, agent.mobile, user.nickName, user.avatarUrl')
            ->join('user', 'user.user_id = cash.user_id')
            ->join('agent_user agent', 'agent.user_id = cash.user_id')
            ->order(['cash.create_time' => 'desc']);
        // 查詢條件
        if ($user_id > 0) {
            $model = $model->where('cash.user_id', '=', $user_id);
        }
        if (!empty($search)) {
            $model = $model->where('agent.real_name|agent.mobile', 'like', '%' . $search . '%');
        }
        if ($apply_status > 0) {
            $model = $model->where('cash.apply_status', '=', $apply_status);
        }
        if ($pay_type > 0) {
            $model = $model->where('cash.pay_type', '=', $pay_type);
        }
        // 獲取列表資料
        return $model->paginate(15);
    }

    /**
     * 分銷商提現稽核
     */
    public function submit($param)
    {
        $this->startTrans();
        try {
            $data = ['apply_status' => $param['apply_status']];
            if ($param['apply_status'] == 30) {
                $data['reject_reason'] = $param['reject_reason'];
            }
            // 更新申請記錄
            $data['audit_time'] = time();
            self::update($data, ['id' => $param['id']]);
            // 提現駁回：解凍分銷商資金
            if ($param['apply_status'] == 30) {
                User::backFreezeMoney($param['user_id'], $param['money']);
            }
            $detail = self::detail($param['id']);
            // 傳送模板訊息
            (new MessageService)->cash($detail);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 確認已打款
     */
    public function money()
    {
        $this->startTrans();
        try {
            // 更新申請狀態
            $data = ['apply_status' => 40, 'audit_time' => time()];
            self::update($data, ['id' => $this['id']]);

            // 更新分銷商累積提現佣金
            User::totalMoney($this['user_id'], $this['money']);

            // 記錄分銷商資金明細
            Capital::add([
                'user_id' => $this['user_id'],
                'flow_type' => 20,
                'money' => -$this['money'],
                'describe' => '申請提現',
            ]);
            // 傳送模板訊息
            //(new Message)->withdraw($this);
            // 事務提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }


    /**
     * 分銷商提現：微信支付企業付款
     */
    public function wechatPay0()
    {
        // 微信使用者資訊
        $user = UserModel::detail($this['user_id']);
        // 生成付款訂單號
        $orderNO = OrderService::createOrderNo();
        // 付款描述
        $desc = '分銷商提現付款';
        // 微信支付api：企業付款到零錢
        $open_id = '';
        $app = AppWx::getWxPayApp($user['app_id']);
        if ($user['reg_source'] == 'mp') {
            $open_id = $user['mpopen_id'];
        } else if ($user['reg_source'] == 'wx') {
            $open_id = $user['open_id'];
        } else if ($user['reg_source'] == 'app') {
            $open_id = $user['appopen_id'];
        }

        if ($open_id == '') {
            $this->error = '未找到使用者open_id';
            return false;
        }

        $WxPay = new WxPay($app);
        // 請求付款api
        if ($WxPay->transfers($orderNO, $open_id, $this['money'], $desc)) {
            // 確認已打款
            $this->money();
            return true;
        }
        return false;
    }

    /**
     * 商家轉賬到零錢
     */
    public function wechatPay()
    {
        // 微信使用者資訊
        $user = UserModel::detail($this['user_id']);
        // 生成付款訂單號
        $orderNO = OrderService::createOrderNo();
        // 付款描述
        $desc = '餘額提現付款';
        // 微信支付api：企業付款到零錢
        $open_id = '';
        $app_id = '';
        if ($user['reg_source'] == 'mp') {
            $open_id = $user['mpopen_id'];
            $wxConfig = AppMpModel::getAppMpCache($app_id);
            $app_id = $wxConfig['mpapp_id'];
        } else if ($user['reg_source'] == 'wx') {
            $open_id = $user['open_id'];
            $wxConfig = AppWxModel::getAppWxCache($app_id);
            $app_id = $wxConfig['wxapp_id'];
        } else if ($user['reg_source'] == 'app') {
            $open_id = $user['appopen_id'];
            $wxConfig = AppOpenModel::getAppOpenCache($app_id);
            $app_id = $wxConfig['openapp_id'];
        }

        if ($open_id == '') {
            $this->error = '未找到使用者open_id';
            return false;
        }
        $agentUser = AgentUserModel::getAgentDetail($this['user_id']);
        $wxPay = new WxPay(null);
        $user_name = $wxPay->getEncrypt($agentUser['real_name'], $user['app_id']);
        $pars = [];
        $pars['appid'] = $app_id;//直連商戶的appid
        $pars['out_batch_no'] = 'sjzz' . date('Ymd') . mt_rand(1000, 9999);//商戶系統內部的商家批次單號，要求此引數只能由數字、大小寫字母組成，在商戶系統內部唯一
        $pars['batch_name'] = $desc;//該筆批次轉賬的名稱
        $pars['batch_remark'] = $desc;//轉賬說明，UTF8編碼，最多允許32個字元
        $pars['total_amount'] = intval($this['money'] * 100);//轉賬總金額 單位為“分”
        $pars['total_num'] = 1;//轉賬總筆數
        $pars['transfer_detail_list'][0] = [
            'out_detail_no' => 'Dh' . $orderNO,
            'transfer_amount' => $pars['total_amount'],
            'transfer_remark' => $desc,
            'openid' => $open_id,
            'user_name' => $user_name
        ];//轉賬明細列表
        //獲取token
        $res = $wxPay->wechatTrans($pars, $user['app_id']);
        $resArr = json_decode($res, true);
        if (isset($resArr['batch_id'])) {
            $this->save([
                'batch_id' => $resArr['batch_id']
            ]);
            // 確認打款
            $this->money();
            return true;
        } else {
            $this->error = $resArr['message'];
            return false;
        }
    }

    /*
     *統計提現總數量
     */
    public function getAgentCashTotal()
    {
        return $this->where('apply_status', '=', 10)->count();
    }

    /*
    *統計提現總數量
    */
    public function getAgentApplyTotal($apply_status = 10)
    {
        return $this->where('apply_status', '=', $apply_status)->count();
    }

    /**
     * 匯出分銷商提現
     */
    public function exportList($user_id = null, $apply_status = -1, $pay_type = -1, $search = '')
    {
        $model = $this;
        // 構建查詢規則
        $model = $model->alias('cash')
            ->with(['user'])
            ->field('cash.*, agent.real_name, agent.mobile, user.nickName, user.avatarUrl')
            ->join('user', 'user.user_id = cash.user_id')
            ->join('agent_user agent', 'agent.user_id = cash.user_id')
            ->order(['cash.create_time' => 'desc']);
        // 查詢條件
        if ($user_id > 0) {
            $model = $model->where('cash.user_id', '=', $user_id);
        }
        if (!empty($search)) {
            $model = $model->where('agent.real_name|agent.mobile', 'like', '%' . $search . '%');
        }
        if ($apply_status > 0) {
            $model = $model->where('cash.apply_status', '=', $apply_status);
        }
        if ($pay_type > 0) {
            $model = $model->where('cash.pay_type', '=', $pay_type);
        }
        // 獲取列表資料
        $list = $model->select();
        // 匯出excel檔案
        (new Exportservice)->cashList($list);

    }
}