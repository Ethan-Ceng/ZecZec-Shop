<?php

namespace app\common\model\file;

use app\common\model\BaseModel;
/**
 * 檔案庫模型
 */
class UploadFile extends BaseModel
{
    protected $pk = 'file_id';
    protected $name = 'upload_file';
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $append = ['file_path'];

    /**
     * 關聯檔案庫分組表
     */
    public function uploadGroup()
    {
        return $this->belongsTo('UploadGroup', 'group_id');
    }

    /**
     * 獲取圖片完整路徑
     * @param $value
     * @param $data
     * @return string
     */
    public function getFilePathAttr($value, $data)
    {
        if ($data['storage'] === 'local') {
            return self::$base_url . 'uploads/' . $data['save_name'];
        }
        return $data['file_url'] . '/' . $data['file_name'];
    }

    /**
     * 檔案詳情
     */
    public static function detail($file_id)
    {
        return (new static())->find($file_id);
    }

    /**
     * 根據檔名查詢檔案id
     */
    public static function getFildIdByName($fileName)
    {
        return (new static)->where(['file_name' => $fileName])->value('file_id');
    }

    /**
     * 查詢檔案id
     */
    public static function getFileName($fileId)
    {
        return (new static)->where(['file_id' => $fileId])->value('file_name');
    }

    /**
     * 新增新記錄
     */
    public function add($data)
    {
        return $this->save($data);
    }

}
