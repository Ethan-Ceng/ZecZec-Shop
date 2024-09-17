<?php

namespace app\common\model\settings;

use app\common\model\BaseModel;

/**
 * 電子面單模板
 */
class DeliveryTemplate extends BaseModel
{
    protected $name = 'delivery_template';
    protected $pk = 'template_id';


    /**
     * 獲取全部
     */
    public static function getAll()
    {
        $model = new static;
        return $model->order(['sort' => 'asc'])->where('is_delete', '=', 0)->select();
    }

    /**
     * 獲取列表
     */
    public function getList($data)
    {
        return $this->order(['sort' => 'asc'])
            ->where('is_delete', '=', 0)
            ->paginate($data);
    }

    /**
     * 模板詳情
     * @param $template_id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function detail($template_id)
    {
        return (new static())->find($template_id);
    }

}
