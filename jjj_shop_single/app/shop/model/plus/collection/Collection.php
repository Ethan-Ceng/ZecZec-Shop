<?php

namespace app\shop\model\plus\collection;

use app\common\model\plus\collection\Collection as CollectionModel;

/**
 * 引導收藏 模型
 */
class Collection extends CollectionModel
{
    /**
     *獲取appid的收藏狀態
     */
    public function getAppData()
    {
        $app_id = self::$app_id;
        return $this->where('app_id', '=', $app_id)->find();
    }

    /**
     * 儲存資料
     * @param $data
     * @return bool
     */
    public function saveData($data)
    {
        if (isset($data['app_id'])) {
            $this->where('app_id', '=', $data['app_id'])->update($data);
            return true;
        }
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }
}