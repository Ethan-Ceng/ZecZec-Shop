<?php

namespace app\shop\model\settings;

use app\common\model\settings\Printer as PrinterModel;

class Printer extends PrinterModel
{
    /**
     * 新增新記錄
     */
    public function add($data)
    {
        if (empty($data['printer_type'])) {
            $this->error = '請選擇印表機型別';
            return false;
        }
        $data['printer_config'] = json_encode($data[$data['printer_type']]);
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 編輯記錄
     */
    public function edit($data)
    {
        $data['printer_config'] = json_encode($data[$data['printer_type']]);
        return $this->save($data);
    }

    /**
     * 刪除記錄
     * @return bool|int
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }

}