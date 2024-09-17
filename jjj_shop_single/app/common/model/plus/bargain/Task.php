<?php

namespace app\common\model\plus\bargain;

use app\common\model\BaseModel;
use app\common\library\helper;

/**
 * 砍價任務模型
 * Class Task
 * @package app\common\model\bargain
 */
class Task extends BaseModel
{
    protected $name = 'bargain_task';
    protected $pk = 'bargain_task_id';

    /**
     * 追加的欄位
     * @var array $append
     */
    protected $append = [
        'is_end',   // 是否已結束
        'surplus_money',    // 剩餘砍價金額
        'bargain_rate', // 砍價進度百分比(0-100)
        'end_time_text', //砍價結束時間格式化
    ];

    /**
     * 關聯使用者表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->BelongsTo('app\\common\\model\\user\\User');
    }
    /**
     *關聯活動
     */
    public function active()
    {
        return $this->belongsTo('app\\common\\model\\plus\\bargain\\Active', 'bargain_activity_id', 'bargain_activity_id');
    }

    /**
     * 關聯檔案庫
     */
    public function file()
    {
        return $this->belongsTo('app\\common\\model\\file\\UploadFile', 'image_id', 'file_id')
            ->bind(['file_path', 'file_name', 'file_url']);
    }


    /**
     * 有效期-開始時間
     */
    public function getEndTimeTextAttr($value, $data)
    {
        return date('Y-m-d H:i:s', $data['end_time']);
    }

    /**
     * 獲取器：活動是否已結束
     * @param $value
     * @param $data
     * @return false|string
     */
    public function getIsEndAttr($value, $data)
    {
        return $value ?: $data['end_time'] <= time();
    }

    /**
     * 獲取器：剩餘砍價金額
     * @param $value
     * @param $data
     * @return false|string
     */
    public function getSurplusMoneyAttr($value, $data)
    {
        $maxCutMoney = helper::bcsub($data['product_price'], $data['bargain_price']);
        return $value ?: helper::bcsub($maxCutMoney, $data['cut_money']);
    }

    /**
     * 獲取器：砍價進度百分比
     * @param $value
     * @param $data
     * @return false|string
     */
    public function getBargainRateAttr($value, $data)
    {
        $maxCutMoney = helper::bcsub($data['product_price'], $data['bargain_price']);
        $rate =  helper::bcdiv($data['cut_money'], $maxCutMoney) * 100;
        return $value ?:  helper::number2($rate);
    }

    /**
     * 獲取器：砍價金額區間
     * @param $value
     * @return mixed
     */
    public function getSectionAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * 修改器：砍價金額區間
     * @param $value
     * @return string
     */
    public function setSectionAttr($value)
    {
        return json_encode($value);
    }

    /**
     * 砍價任務詳情
     */
    public static function detail($bargain_task_id, $with = ['user'])
    {
        return (new static())->with($with)->find($bargain_task_id);
    }



}