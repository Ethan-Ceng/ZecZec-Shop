<?php

namespace app\shop\model\user;

use app\common\model\settings\Setting as SettingModel;
use app\common\library\easywechat\AppWx;
use app\common\library\easywechat\AppMp;
use app\common\model\app\App as AppModel;
use app\common\model\app\AppMp as AppMpModel;
use app\common\model\app\AppOpen as AppOpenModel;
use app\common\model\app\AppWx as AppWxModel;
use app\common\service\order\OrderService;
use app\common\library\easywechat\WxPay;
use app\common\model\user\Cash as CashModel;
use app\shop\model\user\User as UserModel;
use app\shop\service\order\ExportService;
use app\common\model\user\BalanceLog as BalanceLogModel;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum;

/**
 * 餘額提現明細模型
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
     * 獲取提現列表
     */
    public function getList($user_id = null, $apply_status = -1, $pay_type = -1, $search = '')
    {
        $model = $this;
        // 構建查詢規則
        $model = $model->alias('cash')
            ->with(['user'])
            ->field('cash.*, user.nickName, user.avatarUrl')
            ->join('user user', 'user.user_id = cash.user_id')
            ->order(['cash.create_time' => 'desc']);
        // 查詢條件
        if ($user_id > 0) {
            $model = $model->where('cash.user_id', '=', $user_id);
        }
        if (!empty($search)) {
            $model = $model->where('user.nickName|user.mobile', 'like', '%' . $search . '%');
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
     * 提現稽核
     */
    public function submit($param)
    {
        $data = ['apply_status' => $param['apply_status']];
        if ($param['apply_status'] == 30) {
            $data['reject_reason'] = $param['reject_reason'];
        }
        // 更新申請記錄
        $data['audit_time'] = time();
        self::update($data, ['id' => $param['id']]);
        // 提現駁回：解凍餘額
        if ($param['apply_status'] == 30) {
            User::backFreezeMoney($param['user_id'], $param['money']);
        }
        return true;
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

            // 更新累積提現
            User::totalMoney($this['user_id'], $this['money']);
            //新增餘額記錄
            BalanceLogModel::add(BalanceLogSceneEnum::CASH, [
                'user_id' => $this['user_id'],
                'money' => -$this['money'],
                'app_id' => self::$app_id,
            ], '');
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
        $wxPay = new WxPay(null);
        $user_name = $wxPay->getEncrypt($user['real_name'], $user['app_id']);
        $pars = [];
        $pars['appid'] = $app_id;//直連商戶的appid
        $pars['out_batch_no'] = 'sjzz' . date('Ymd') . mt_rand(1000, 9999);//商戶系統內部的商家批次單號，要求此引數只能由數字、大小寫字母組成，在商戶系統內部唯一
        $pars['batch_name'] = $desc;//該筆批次轉賬的名稱
        $pars['batch_remark'] = $desc;//轉賬說明，UTF8編碼，最多允許32個字元
        $pars['total_amount'] = intval($this['real_money'] * 100);//轉賬總金額 單位為“分”
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

    /**
     * 提現：微信支付企業付款
     */
    public function wechatPay0()
    {
        // 微信使用者資訊
        $user = UserModel::detail($this['user_id']);
        // 生成付款訂單號
        $orderNO = OrderService::createOrderNo();
        // 付款描述
        $desc = '餘額提現付款';
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
        if ($WxPay->transfers($orderNO, $open_id, $this['real_money'], $desc, $user['real_name'])) {
            // 確認已打款
            $this->money();
            return true;
        }
        return false;
    }

    /*
    *統計提現總數量
    */
    public function getUserApplyTotal($apply_status)
    {
        return $this->where('apply_status', '=', $apply_status)->count();
    }

    /**
     * 匯出使用者餘額提現
     */
    public function exportList($user_id = null, $apply_status = -1, $pay_type = -1, $search = '')
    {
        $model = $this;
        // 構建查詢規則
        $model = $model->alias('cash')
            ->with(['user'])
            ->field('cash.*, user.nickName, user.avatarUrl,user.mobile')
            ->join('user user', 'user.user_id = cash.user_id')
            ->order(['cash.create_time' => 'desc']);
        // 查詢條件
        if ($user_id > 0) {
            $model = $model->where('cash.user_id', '=', $user_id);
        }
        if (!empty($search)) {
            $model = $model->where('user.nickName|user.mobile', 'like', '%' . $search . '%');
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
        (new Exportservice)->userCashList($list);
    }

    public function getPayType()
    {
        $pay_type = [
            ['id' => '10', 'name' => '微信支付'],
            ['id' => '20', 'name' => '支付寶'],
            ['id' => '30', 'name' => '銀行卡']
        ];
        return $pay_type;
    }
}