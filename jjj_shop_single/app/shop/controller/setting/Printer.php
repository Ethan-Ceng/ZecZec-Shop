<?php

namespace app\shop\controller\setting;

use app\shop\controller\Controller;
use app\shop\model\settings\Printer as PrinterModel;

/**
 * 印表機控制器
 */
class Printer extends Controller
{
    /**
     * 印表機列表
     */
    public function index()
    {
        $model = new PrinterModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }


    /**
     * 印表機型別
     */
    public function type()
    {
        $model = new PrinterModel;
        $printerType = $model::getPrinterTypeList();
        return $this->renderSuccess('', compact('printerType'));
    }


    /**
     * 新增印表機
     */
    public function add()
    {
        if($this->request->isGet()){
            return  $this->type();
        }
        // 新增記錄
        $model = new PrinterModel;
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError($model->getError() ?: '新增失敗');
    }

    /**
     * 印表機詳情
     */
    public function detail($printer_id)
    {
        $detail = PrinterModel::detail($printer_id);
        $detail['printer_config'] = json_decode($detail['printer_config'], true);
        $printerType = $detail::getPrinterTypeList();
        return $this->renderSuccess('', compact('detail', 'printerType'));

    }

    public function edit($printer_id)
    {
        if($this->request->isGet()){
            return $this->detail($printer_id);
        }
        $model = PrinterModel::detail($printer_id);
        // 更新記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }

    /**
     * 刪除記錄
     */
    public function delete($printer_id)
    {
        $model = PrinterModel::detail($printer_id);
        if ($model->setDelete()) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError($model->getError() ?:'刪除失敗');
    }

}
