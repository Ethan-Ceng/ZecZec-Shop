<?php

namespace app\common\model\file;

use app\common\model\BaseModel;

/**
 * 相簿庫模型
 */
class UploadImage extends BaseModel
{
    protected $pk = 'category_id';
    protected $name = 'image_bank';

    /**
     * 檔案詳情
     */
    public static function detail($category_id)
    {
        return (new static())->find($category_id);
    }

    /**
     * 新增新記錄
     */
    public function add($data)
    {
        return $this->save($data);
    }

    /**
     * 新增新記錄
     */
    public function edit($name)
    {
        return $this->save(['name' => $name]);
    }

    /**
     * 新增新記錄
     */
    public function remove()
    {
        return $this->delete();
    }

    /**
     * 批次刪除
     */
    public function deleteFiles($imageIds)
    {
        return $this->where('category_id', 'in', $imageIds)->delete();
    }

    /**
     * 獲取列表記錄
     */
    public function getList($data)
    {
        $model = $this->withoutGlobalScope();
        // 檔案類別
        if (isset($data['parentId']) && $data['parentId']) {
            $model = $model->where('parent_id', '=', $data['parentId']);
        }
        // 查詢列表資料
        return $model->where('image', '<>', '')
            ->order(['category_id' => 'desc'])
            ->paginate($data);
    }

    /**
     * 獲取列表記錄
     */
    public function getCategoryList()
    {
        $model = $this->withoutGlobalScope();
        // 查詢列表資料
        return $model->where('parent_id', '=', 0)
            ->order(['sort' => 'asc', 'category_id' => 'desc'])
            ->select();
    }
}
