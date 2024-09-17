<?php

namespace app\shop\model\settings;

use app\common\model\settings\Express as ExpressModel;
use app\common\model\order\Order as OrderModel;
/**
 * 物流公司模型
 */
class Express extends ExpressModel
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
        // 判斷當前物流公司是否已被訂單使用
        $model = new OrderModel;
        if ($orderCount = $model->where(['express_id' => $this['express_id']])->count()) {
            $this->error = '當前物流公司已被' . $orderCount . '個訂單使用，不允許刪除';
            return false;
        }
        return $this->delete();
    }
}