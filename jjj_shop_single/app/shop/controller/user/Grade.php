<?php

namespace app\shop\controller\user;

use app\common\model\user\GradeLog as GradeLogModel;
use app\shop\controller\Controller;
use app\shop\model\user\Grade as GradeModel;

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
        $model = new GradeModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 新增等級
     */
    public function add()
    {
        $model = new GradeModel;
        // 新增記錄
        if ($model->add($this->postData())) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError('新增失敗');
    }

    /**
     * 編輯會員等級
     */
    public function edit($grade_id)
    {
        $model = GradeModel::detail($grade_id);
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
        $model = GradeModel::detail($grade_id);
        if (!$model->setDelete()) {
            return $this->renderError('已存在使用者，刪除失敗');
        }
        return $this->renderSuccess('刪除成功');
    }

    /**
     * 會員等級日誌
     */
    public function log()
    {
        $model = new GradeLogModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }
}