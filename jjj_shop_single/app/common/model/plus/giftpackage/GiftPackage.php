<?php

namespace app\common\model\plus\giftpackage;

use app\common\model\BaseModel;

/**
 * Class GiftPackage
 * 禮包購模型
 * @package app\common\model\plus\giftpackage
 */
class GiftPackage extends BaseModel
{
    protected $name = 'gift_package';
    protected $pk = 'gift_package_id';

    /**
     * 禮包詳情
     */
    public static function detail($gift_package_id)
    {
        return (new static())->with(['image'])->find($gift_package_id);
    }
    /**
     * 開始時間
     */
    public function getStartTimeAttr($value)
    {
        return ['text' => date('Y-m-d H:i:s', $value), 'value' => $value];
    }

    /**
     * 有效期-結束時間
     */
    public function getEndTimeAttr($value)
    {
        return ['text' => date('Y-m-d H:i:s', $value), 'value' => $value];
    }

    /**
     * 狀態
     */
    public function getStatusAttr($value, $data)
    {
        $text = '';
        if($value == 1){
            $text = '未生效';
        }else{
            if ($data['start_time'] > time()) {
                $text = '未開始';
            }
            if ($data['end_time'] < time()) {
                $text = '已結束';
            }
            if ($data['start_time'] < time() && $data['end_time'] > time()) {
                $text = '進行中';
            }
        }
        return ['text' => $text, 'value' => $value];
    }
    /**
     * 關聯檔案庫
     */
    public function image()
    {
        return $this->belongsTo('app\\common\\model\\file\\UploadFile', 'image_id', 'file_id')
            ->bind(['file_path']);
    }
}