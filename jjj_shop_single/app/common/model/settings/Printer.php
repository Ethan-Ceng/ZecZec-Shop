<?php

namespace app\common\model\settings;

use app\common\enum\settings\PrinterTypeEnum;
use app\common\model\BaseModel;

/**
 * 印表機模型
 */
class Printer extends BaseModel
{
    protected $name = 'printer';
    protected $pk = 'printer_id';
    /**
     * 獲取印表機型別列表
     */
    public static function getPrinterTypeList()
    {
        static $printerTypeEnum = [];
        if (empty($printerTypeEnum)) {
            $printerTypeEnum = PrinterTypeEnum::getTypeName();
        }
        return $printerTypeEnum;
    }

    /**
     * 印表機型別名稱
     */
    public function getPrinterTypeAttr($value)
    {
        $printerType = self::getPrinterTypeList();
        return ['value' => $value, 'text' => $printerType[$value]];
    }

    /**
     * 自動轉換printer_config為array格式
     */
    public function getPrinterConfigAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * 自動轉換printer_config為json格式
     */
    public function setPrinterConfigAttr($value)
    {
        return json_encode($value);
    }

    /**
     * 獲取全部
     */
    public static function getAll()
    {
        return (new static)->where('is_delete', '=', 0)
            ->order(['sort' => 'asc'])->select();
    }

    /**
     * 獲取列表
     */
    public function getList($limit = 10)
    {
        return $this->where('is_delete', '=', 0)
            ->order(['sort' => 'asc'])
            ->paginate($limit);
    }

    /**
     * 物流公司詳情
     */
    public static function detail($printer_id)
    {
        return (new static())->find($printer_id);
    }

}
