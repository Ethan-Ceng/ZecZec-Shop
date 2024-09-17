<?php

namespace app\common\model\settings;

use app\common\model\BaseModel;

/**
 * 電子面單設定
 */
class DeliverySetting extends BaseModel
{
    protected $name = 'delivery_setting';
    protected $pk = 'setting_id';

    /**
     * 關聯物流公司表
     * @return \think\model\relation\BelongsTo
     */
    public function express()
    {
        return $this->belongsTo('app\\common\\model\\settings\\Express', 'express_id', 'express_id');
    }

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
        return $this->with(['express'])->order(['sort' => 'asc'])
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
    public static function detail($setting_id, $with = [])
    {
        return (new static())->with($with)->find($setting_id);
    }
}
