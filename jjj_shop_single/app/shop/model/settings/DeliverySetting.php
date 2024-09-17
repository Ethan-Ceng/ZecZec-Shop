<?php

namespace app\shop\model\settings;

use app\common\model\settings\DeliverySetting as DeliverySettingModel;

/**
 * 電子面單設定
 */
class DeliverySetting extends DeliverySettingModel
{
    /**
     * 新增新記錄
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 編輯記錄
     */
    public function edit($data)
    {
        return $this->save($data);
    }

    /**
     * 刪除記錄
     */
    public function remove()
    {
        return $this->save(['is_delete' => 1]);
    }
}
