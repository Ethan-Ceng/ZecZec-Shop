<?php

namespace app\api\model\plus\table;

use app\common\model\plus\table\Table as TableModel;
use app\common\model\plus\table\Record as RecordModel;
use app\common\model\order\OrderProduct as OrderProductModel;

/**
 * 表單模型
 */
class Table extends TableModel
{
    /**
     * 簽到
     */
    public function add($user, $data)
    {
        $this->startTrans();
        try {
            $data['content'] = $data['tableData'];
            $data['user_id'] = $user['user_id'];
            $data['app_id'] = self::$app_id;
            $record_model = new RecordModel();
            $record_model->save($data);
            $this->where('table_id', '=', $data['table_id'])->inc('total_count')->update();
            // 如果是訂單補資訊
            if(isset($data['order_product_id']) && $data['order_product_id'] > 0){
                (new OrderProductModel())->where('order_product_id', '=', $data['order_product_id'])->update([
                    'table_record_id' => $record_model['table_record_id']
                ]);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}
