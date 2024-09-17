<?php

namespace app\shop\controller\plus\invitation;

use app\shop\controller\Controller;
use app\shop\model\plus\invitationgift\InvitationGift as InvitationModel;
use app\shop\model\plus\invitationgift\Partake;
use app\common\service\qrcode\InvitationService;
use app\shop\model\plus\invitationgift\InvitationReceive;

/**
 * 邀請有禮活動控制器
 */
class Active extends Controller
{
    /*
       * 邀請列表
       */
    public function lists()
    {
        $model = new InvitationModel();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增
     */
    public function add()
    {
        $model = new InvitationModel();
        if ($model->add($this->postData())) {
            return $this->renderSuccess('儲存成功');
        }
        return $this->renderError('儲存失敗');
    }

    /**
     * 編輯
     */
    public function edit($invitation_gift_id)
    {
        if ($this->request->isGet()) {
            $model = new InvitationModel();
            $data = $model->getDatas($invitation_gift_id);
            return $this->renderSuccess('', compact('data'));
        }
        $model = new InvitationModel();
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('儲存成功');
        }
        return $this->renderError($model->getError() ?: '儲存失敗');
    }

    /**
     * 操作活動
     * @param $id
     */
    public function send($id)
    {
        $model = new InvitationModel();
        if ($model->send($id)) {
            return $this->renderSuccess('儲存成功');
        }
        return $this->renderError('儲存失敗');
    }

    /**
     * 終止
     * @param $id
     */
    public function end($id)
    {
        $model = new InvitationModel();
        if ($model->end($id)) {
            return $this->renderSuccess('儲存成功');
        }
        return $this->renderError('儲存失敗');
    }

    /**
     * 刪除
     * @param $id
     */
    public function delete($id)
    {
        $model = new InvitationModel();
        if ($model->del($id)) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError('刪除失敗');
    }

    /**
     * 獲取推廣二維碼
     */
    public function qrcode($id, $source)
    {
        $Qrcode = new InvitationService($id, $source);
        $Qrcode->getImage();
    }

    /**
     * 參與記錄
     */
    public function partake($id)
    {
        $model = new Partake();
        $list = $model->getList($id, $this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 參與記錄
     */
    public function receive($id)
    {
        $model = new InvitationReceive();
        $list = $model->getList($id, $this->postData());
        return $this->renderSuccess('', compact('list'));
    }
}