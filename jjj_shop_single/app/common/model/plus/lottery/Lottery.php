<?php

namespace app\common\model\plus\lottery;

use app\common\model\BaseModel;

/**
 * Class GiftPackage
 * 轉盤模型
 * @package
 */
class Lottery extends BaseModel
{
    protected $name = 'lottery';
    protected $pk = 'lottery_id';
    /**
     * 追加欄位
     * @var string[]
     */
    protected $append = ['status_text'];

    /**
     * 轉盤詳情
     */
    public static function detail()
    {
        return (new static())->with(['image'])->find();
    }

    /**
     * 狀態
     */
    public function getStatusTextAttr($value, $data)
    {
        $text = '';
        if ($value == 1) {
            $text = '開啟';
        } else {
            $text = '關閉';
        }
        return $text;
    }
    /**
     * 關聯獎項
     */
    public function prize()
    {
        return $this->hasMany('app\\common\\model\\plus\\lottery\\LotteryPrize', 'lottery_id', 'lottery_id');
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