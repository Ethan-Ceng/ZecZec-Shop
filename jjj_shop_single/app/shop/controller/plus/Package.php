<?php

namespace app\shop\controller\plus;

use app\shop\controller\Controller;
use app\shop\model\plus\giftpackage\GiftPackage as GiftPackageModel;
use app\shop\model\plus\giftpackage\Order;
use app\common\service\qrcode\PackageService;

/**
 * 禮包購控制器
 */
class Package extends Controller
{
    /*
     * 禮包列表
     */
    public function index()
    {
        $model = new GiftPackageModel();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 獲取資料
     * @param null $id
     */
    public function getGiftPackage($id)
    {
        $model = new GiftPackageModel();
        $data = $model->getGiftPackage($id);
        return $this->renderSuccess('', compact('data'));
    }

    /**
     *儲存禮包
     */
    public function add()
    {
        $model = new GiftPackageModel();
        if ($model->saveGift($this->postData())) {
            return $this->renderSuccess('儲存成功');
        }
        return $this->renderError($model->getError() ?: '儲存失敗');
    }

    /**
     *修改
     */
    public function edit($gift_package_id)
    {
        if ($this->request->isGet()) {
            return $this->getGiftPackage($gift_package_id);
        }
        $model = GiftPackageModel::detail($gift_package_id);
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失敗');
    }

    /**
     * 釋出
     */
    public function send($id)
    {
        $model = new GiftPackageModel();
        if ($model->send($id)) {
            return $this->renderSuccess('釋出成功');
        }
        return $this->renderError('釋出失敗');
    }

    /**
     * 終止
     */
    public function end($id)
    {
        $model = new GiftPackageModel();
        if ($model->end($id)) {
            return $this->renderSuccess('終止成功');
        }
        return $this->renderError('終止失敗');
    }

    /**
     * 刪除
     */
    public function delete($id)
    {
        $model = new GiftPackageModel();
        if ($model->del($id)) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError('刪除失敗');
    }

    /**
     * 購買記錄
     */
    public function orderlist()
    {
        $model = new Order();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 獲取推廣二維碼
     */
    public function qrcode($id, $source)
    {
        $Qrcode = new PackageService($id, $source);
        $Qrcode->getImage();
    }
}