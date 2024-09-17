<?php

namespace app\api\model\user;

use app\common\exception\BaseException;
use app\common\model\user\Cash as CashModel;
use app\api\model\settings\Setting as SettingModel;

/**
 * 餘額提現明細模型
 */
class Cash extends CashModel
{
    /**
     * 隱藏欄位
     */
    protected $hidden = [
        'update_time',
    ];

    /**
     * 獲取餘額提現明細
     */
    public function getList($user_id, $apply_status = -1, $limit = 15)
    {
        $model = $this;
        $apply_status > -1 && $model = $model->where('apply_status', '=', $apply_status);
        return $model->where('user_id', '=', $user_id)->order(['create_time' => 'desc'])
            ->paginate($limit);
    }

    /**
     * 提交申請
     */
    public function submit($user, $data)
    {
        // 資料驗證
        $this->validation($user, $data);
        $setting = SettingModel::getItem('balance_cash');
        $this->startTrans();
        try {
            // 新增申請記錄
            $this->save(array_merge($data, [
                'user_id' => $user['user_id'],
                'apply_status' => 10,
                'app_id' => self::$app_id,
                'real_money' => round($data['money'] * $setting['cash_ratio'] / 100, 2),
                'cash_ratio' => $setting['cash_ratio'],
            ]));
            // 凍結使用者資金
            $user->freezeMoney($data['money']);
            if ($data['pay_type'] == 10 && !$user['real_name']) {
                $user->save(['real_name' => $data['real_name']]);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 資料驗證
     */
    private function validation($user, $data)
    {
        // 結算設定
        $settlement = SettingModel::getItem('balance_cash');
        // 最低提現佣金
        if ($data['money'] <= 0) {
            throw new BaseException(['msg' => '提現金額不正確']);
        }
        if ($user['balance'] <= 0) {
            throw new BaseException(['msg' => '當前使用者沒有可提現餘額']);
        }
        if ($data['money'] > $user['balance']) {
            throw new BaseException(['msg' => '提現金額不能大於可提現餘額']);
        }
        if ($data['money'] < $settlement['min_money']) {
            throw new BaseException(['msg' => '最低提現金額為' . $settlement['min_money']]);
        }
        if ($data['pay_type'] == 10 && !$data['real_name']) {
            throw new BaseException(['msg' => '請輸入姓名']);
        }
        if ($data['pay_type'] == 20) {
            if (empty($data['alipay_name']) || empty($data['alipay_account'])) {
                throw new BaseException(['msg' => '請補全提現資訊']);
            }
        } elseif ($data['pay_type'] == 30) {
            if (empty($data['bank_name']) || empty($data['bank_account']) || empty($data['bank_card'])) {
                throw new BaseException(['msg' => '請補全提現資訊']);
            }
        }
    }

}