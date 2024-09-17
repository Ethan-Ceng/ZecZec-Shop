<?php

namespace app\shop\model\store;

use app\common\model\store\Store as StoreModel;
use Lvht\GeoHash;

/**
 * 門店模型
 */
class Store extends StoreModel
{
    /**
     * 隱藏欄位
     * @var array
     */
    protected $hidden = [
        'app_id',
        'update_time',
    ];
    /**
     * 獲取列表資料
     */
    public function getList($data = null, $status = '')
    {
        $model = $this;
        !empty($status) && $model = $model->where('status', '=', (int)$status);
        return $model->with(['logo'])->where('is_delete', '=', '0')
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($data);
    }

    /**
     * 獲取所有門店列表
     */
    public static function getAllList()
    {
        return (new self)->where('is_delete', '=', '0')
            ->order(['sort' => 'asc', 'create_time' => 'desc'])
            ->select();
    }

    /**
     * 新增記錄
     */
    public function add($data)
    {
        $data = $this->createData($data);
        return self::create($data);
    }

    /**
     * 編輯記錄
     */
    public function edit($data)
    {
        return $this->save($this->createData($data));
    }

    /**
     * 軟刪除
     */
    public function setDelete($where)
    {
        return self::update(['is_delete' => 1], $where);
    }

    /**
     * 建立資料
     */
    private function createData($data)
    {
        $data['app_id'] = self::$app_id;
        // 格式化座標資訊
        $coordinate = explode(',', $data['coordinate']);
        $data['latitude'] = $coordinate[0];
        $data['longitude'] = $coordinate[1];

        // 生成geohash
        $Geohash = new Geohash;
        $data['geohash'] = $Geohash->encode($data['longitude'], $data['latitude']);
        return $data;
    }
}