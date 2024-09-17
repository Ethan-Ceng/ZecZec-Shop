<?php

namespace app\shop\controller\plus\agent;

use app\common\model\plus\agent\Setting as AgentSetting;
use app\shop\controller\Controller;
use app\shop\model\plus\agent\Grade as AgentGradeModel;
use app\common\model\plus\agent\GradeLog as GradeLogModel;
/**
 * 會員等級
 */
class Grade extends Controller
{
    /**
     * 會員等級列表
     */
    public function index()
    {
        $model = new AgentGradeModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增等級
     */
    public function add()
    {
        if($this->request->isGet()){
            // 平臺分銷規則
            $basicSetting = AgentSetting::getItem('basic');
            return $this->renderSuccess('', compact( 'basicSetting'));
        }
        $model = new AgentGradeModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError('新增失敗');
    }

    /**
     * 編輯會員等級
     */
    public function edit()
    {
        if($this->request->isGet()){
            // 平臺分銷規則
            $basicSetting = AgentSetting::getItem('basic');
            return $this->renderSuccess('', compact( 'basicSetting'));
        }
        $grade_id = $this->postData('grade_id');
        $model = AgentGradeModel::detail($grade_id);
        // 修改記錄
        if ($model->edit($this->postData())) {
            return $this->renderSuccess();
        }
        return $this->renderError();
    }

    /**
     * 刪除會員等級
     */
    public function delete($grade_id)
    {
        // 會員等級詳情
        $model = AgentGradeModel::detail($grade_id);
        if (!$model->setDelete()) {
            return $this->renderError('已存在使用者，刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }

    /**
     * 分銷商申請列表
     */
    public function log()
    {
        $model = new GradeLogModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }
}