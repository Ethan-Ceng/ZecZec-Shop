<?php

namespace app\shop\model\settings;

use think\facade\Cache;
use app\common\model\settings\Setting as SettingModel;
use app\common\enum\settings\SettingEnum;

class Setting extends SettingModel
{
    /**
     * 更新系統設定
     */
    public function edit($key, $values)
    {
        $model = self::detail($key) ?: $this;
        // 刪除系統設定快取
        Cache::delete('setting_' . self::$app_id);
        return $model->save([
                'key' => $key,
                'describe' => SettingEnum::data()[$key]['describe'],
                'values' => $values,
                'app_id' => self::$app_id,
            ]) !== false;
    }

    /**
     * 驗證商城設定
     */
    private function validStore($values)
    {
        if (!isset($values['delivery_type']) || empty($values['delivery_type'])) {
            $this->error = '配送方式至少選擇一個';
            return false;
        }
        return true;
    }

    /**
     * 驗證小票印表機設定
     */
    private function validPrinter($values)
    {
        if ($values['is_open'] == false) {
            return true;
        }
        if (!$values['printer_id']) {
            $this->error = '請選擇訂單印表機';
            return false;
        }
        if (empty($values['order_status'])) {
            $this->error = '請選擇訂單列印方式';
            return false;
        }
        return true;
    }

}
