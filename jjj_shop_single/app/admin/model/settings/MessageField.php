<?php

namespace app\admin\model\settings;

use app\common\model\settings\MessageField as MessageFieldModel;

class MessageField extends MessageFieldModel
{
    /**
     * 獲取全部
     */
    public static function getAll($message_id)
    {
        $model = new static;
        return $model->where('message_id', '=', $message_id)
            ->where('is_delete', '=', 0)
            ->order(['sort' => 'asc'])->select();
    }

    /**
     * 新增
     */
    public function add($data)
    {
        $filedList = isset($data['fieldData'])?$data['fieldData']:[];
        $this->startTrans();
        try {
            $save_data = [];
            foreach ($filedList as $field){
                // 更新小程式設定
                if ($field['message_field_id'] > 0) {
                    $save_data[] = $field;
                }else {
                    $field['message_id'] = $data['message_id'];
                    unset($field['message_field_id']);
                    $save_data[] = $field;
                }
            }
            //儲存
            if(count($save_data) > 0){
                $this->saveAll($save_data);
            }
            //刪除
            if(isset($data['deleteIds']) && count($data['deleteIds']) > 0){
                $this->where('message_field_id', 'in', $data['deleteIds'])->update(['is_delete' => 1]);
            }
            //刪除
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

}