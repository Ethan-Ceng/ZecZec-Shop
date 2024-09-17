<?php

namespace app\common\model\settings;

use app\common\library\express\Kuaidi100;
use app\common\model\BaseModel;

/**
 * 物流公司模型
 */
class Express extends BaseModel
{
    protected $name = 'express';
    protected $pk = 'express_id';

    /**
     * 獲取全部
     */
    public function getAll()
    {
        return $this->order(['sort' => 'asc'])->select();
    }

    /**
     * 獲取列表
     */
    public function getList($limit = 20)
    {
        return $this->order(['sort' => 'asc'])
            ->paginate($limit);
    }

    /**
     * 物流公司詳情
     */
    public static function detail($express_id)
    {
        return (new static())->find($express_id);
    }

    /**
     * 物流公司詳情
     */
    public static function findByName($express_name)
    {
        return (new static())->where('express_name', '=', $express_name)->find();
    }

    /**
     * 獲取物流動態資訊
     */
    public function dynamic($express_name, $express_code, $express_no, $phone)
    {
        $data = [
            'express_name' => $express_name,
            'express_no' => $express_no
        ];
        // 例項化快遞100類
        $config = Setting::getItem('store');
        $Kuaidi100 = new Kuaidi100($config['kuaidi100']);
        // 請求查詢介面
        $data['list'] = $Kuaidi100->query($express_code, $express_no, $phone);
        if ($data['list'] === false) {
            $this->error = $Kuaidi100->getError();
            return false;
        }
        return $data;
    }
}