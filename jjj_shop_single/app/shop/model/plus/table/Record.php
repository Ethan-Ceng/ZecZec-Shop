<?php

namespace app\shop\model\plus\table;

use app\common\model\plus\table\Record as RecordModel;
use app\shop\service\order\ExportService;

/**
 * 優惠券模型
 */
class Record extends RecordModel
{
    /**
     * 獲取優惠券列表
     */
    public function getList($data, $page = true)
    {
        $model = $this;
        if (isset($data['table_id']) && $data['table_id'] > 0) {
            $model = $model->where('record.table_id', '=', $data['table_id']);
        }
        if (isset($data['search']) && $data['search']) {
            $model = $model->where('user.user_id|user.nickName|user.mobile', 'like', '%' . $data['search'] . '%');
        }
        $model = $model->alias('record')
            ->field(['record.*'])
            ->with(['tableM', 'user'])
            ->join('table table', 'table.table_id = record.table_id')
            ->join('user user', 'user.user_id = record.user_id')
            ->where('record.is_delete', '=', 0)
            ->where('table.is_delete', '=', 0)
            ->where('user.is_delete', '=', 0)
            ->order(['record.create_time' => 'desc']);
        if ($page) {
            $list = $model->paginate($data);
            foreach ($list as &$item) {
                $item['tableData'] = json_decode($item['content'], true);
                unset($item['content']);
            }
        } else {
            $list = $model->select();
        }
        return $list;
    }

    /**
     * 刪除記錄 (軟刪除)
     */
    public function setDelete()
    {
        $this->startTrans();
        try {
            $this->save([
                'is_delete' => 1
            ]);
            (new Table())->where('table_id', '=', $this['table_id'])->dec('total_count')->update();
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 訂單匯出
     */
    public function exportList($data)
    {
        if (!isset($data['table_id']) || $data['table_id'] <= 0) {
            return "";
        }
        $tableInfo = (new Table())->find($data['table_id']);
        // 獲取訂單列表
        $list = $this->getList($data, false);
        // 匯出excel檔案
        return (new Exportservice)->tableList($tableInfo, $list);
    }

}
