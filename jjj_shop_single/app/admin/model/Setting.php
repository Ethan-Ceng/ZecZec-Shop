<?php

namespace app\admin\model;

use app\common\model\settings\Setting as SettingModel;

class Setting extends SettingModel
{
    /**
     * 新增預設配置
     */
    public function insertDefault($app_id, $store_name)
    {
        // 新增商城預設設定記錄
        $data = [];
        foreach ($this->defaultData($store_name) as $key => $item) {
            $data[] = array_merge($item, ['app_id' => $app_id]);
        }
        return $this->saveAll($data);
    }
}