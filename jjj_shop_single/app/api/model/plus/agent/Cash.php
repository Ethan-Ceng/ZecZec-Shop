<?php

namespace app\api\model\plus\agent;

use app\common\exception\BaseException;
use app\common\model\plus\agent\Cash as CashModel;

/**
 * 分銷商提現明細模型
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
     * 獲取分銷商提現明細
     */
    public function getList($user_id, $apply_status = -1,$limit=15)
    {
        $model = $this;
        $apply_status > -1 && $model = $model->where('apply_status', '=', $apply_status);
        return $model->where('user_id', '=', $user_id)->order(['create_time' => 'desc'])
            ->paginate($limit);
    }

    /**
     * 提交申請
     */
    public function submit($agent, $data)
    {
        // 資料驗證
        $this->validation($agent, $data);
        // 新增申請記錄
        $this->save(array_merge($data, [
            'user_id' => $agent['user_id'],
            'apply_status' => 10,
            'app_id' => self::$app_id,
        ]));
        // 凍結使用者資金
        $agent->freezeMoney($data['money']);
        return true;
    }

    /**
     * 資料驗證
     */
    private function validation($agent, $data)
    {
        // 結算設定
        $settlement = Setting::getItem('settlement');
        // 最低提現佣金
        if ($data['money'] <= 0) {
            throw new BaseException(['msg' => '提現金額不正確']);
        }
        if ($agent['money'] <= 0) {
            throw new BaseException(['msg' => '當前使用者沒有可提現佣金']);
        }
        if ($data['money'] > $agent['money']) {
            throw new BaseException(['msg' => '提現金額不能大於可提現佣金']);
        }
        if ($data['money'] < $settlement['min_money']) {
            throw new BaseException(['msg' => '最低提現金額為' . $settlement['min_money']]);
        }
        if (!in_array($data['pay_type'], $settlement['pay_type'])) {
            throw new BaseException(['msg' => '提現方式不正確']);
        }
        if ($data['pay_type'] == '20') {
            if (empty($data['alipay_name']) || empty($data['alipay_account'])) {
                throw new BaseException(['msg' => '請補全提現資訊']);
            }
        } elseif ($data['pay_type'] == '30') {
            if (empty($data['bank_name']) || empty($data['bank_account']) || empty($data['bank_card'])) {
                throw new BaseException(['msg' => '請補全提現資訊']);
            }
        }
    }

}