<?php

namespace app\shop\model\plus\agent;

use think\facade\Cache;
use app\common\model\plus\agent\Setting as SettingModel;

/**
 * 分銷商設定模型
 */
class Setting extends SettingModel
{
    /**
     * 設定項描述
     */
    private $describe = [
        'basic' => '基礎設定',
        'condition' => '分銷商條件',
        'commission' => '佣金設定',
        'settlement' => '結算',
        'words' => '自定義文字',
        'license' => '申請協議',
        'background' => '頁面背景圖',
        'template_msg' => '模板訊息',
        'qrcode' => '分銷海報',
    ];

    /**
     * 更新系統設定
     */
    public function edit($data)
    {
        $this->startTrans();
        try {
            foreach ($data as $key => $values)
                $this->saveValues($key, $values);
            $this->commit();
            // 刪除系統設定快取
            Cache::delete('agent_setting_' . self::$app_id);
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 儲存設定項
     */
    private function saveValues($key, $values)
    {
        $where['key'] = $key;
        $res = $this->where($where)->select()->count();
        $data = [
            'describe' => $this->describe[$key],
            'values' => $values,
            'app_id' => self::$app_id,
        ];
        if ($res == 1) {
            return self::update($data, $where);
        }
        if ($res == 0) {
            $data['key'] = $key;
            return self::create($data);
        }
        return false;
    }

    /**
     * 驗證結算方式
     */
    private function validSettlement($values)
    {
        if (!isset($values['pay_type']) || empty($values['pay_type'])) {
            $this->error = '請設定 結算-提現方式';
            return false;
        }
        return true;
    }

}