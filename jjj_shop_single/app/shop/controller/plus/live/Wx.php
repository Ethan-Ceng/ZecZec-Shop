<?php

namespace app\shop\controller\plus\live;

use app\common\enum\settings\SettingEnum;
use app\shop\controller\Controller;
use app\shop\model\plus\live\WxLive as WxLiveModel;
use app\shop\model\settings\Setting as SettingModel;

/**
 * 微信小程式直播控制器
 */
class Wx extends Controller
{
    /**
     *直播列表
     */
    public function index()
    {
        $model = new WxLiveModel();
        $list = $model->getList($this->postData());
        $auto_syn = SettingModel::getItem(SettingEnum::LIVE)['auto_syn'];
        return $this->renderSuccess('', compact('list', 'auto_syn'));
    }

    /**
     * 建立直播
     */
    public function add()
    {
        // 直播間詳情
        $model = new WxLiveModel();
        if (!$model->createRoom($this->postData())) {
            return $this->renderError($model->getError() ?: '建立失敗');
        }
        return $this->renderSuccess('建立成功');
    }

    /**
     * 編輯直播
     */
    public function edit($live_id)
    {
        $model = WxLiveModel::detail($live_id);
        if (!$model->editRoom($this->postData())) {
            return $this->renderError($model->getError() ?: '修改失敗');
        }
        return $this->renderSuccess('修改成功');
    }

    /**
     * 刪除直播
     */
    public function delete($live_id)
    {
        $model = WxLiveModel::detail($live_id);
        if (!$model->delRoom()) {
            return $this->renderError($model->getError() ?: '刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }

    /**
     *直播列表同步
     */
    public function syn()
    {
        $model = new WxLiveModel();
        if($model->syn()){
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }

    /**
     * 修改直播間置頂狀態
     */
    public function settop($live_id)
    {
        // 直播間詳情
        $model = WxLiveModel::detail($live_id);
        if (!$model->setTop($this->postData())) {
            return $this->renderError('操作失敗');
        }
        return $this->renderSuccess('操作成功');
    }

    /**
     * 修改自動同步
     */
    public function setSyn()
    {
        $model = new SettingModel;
        $data = $this->request->param();
        $arr = [
            'auto_syn' => $data['auto_syn'],
        ];
        if ($model->edit(SettingEnum::LIVE, $arr)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失敗');
    }
}