<?php

namespace app\shop\model\user;

use app\common\model\user\Grade as GradeModel;
use app\shop\model\user\User as UserModel;

/**
 * 使用者會員等級模型
 */
class Grade extends GradeModel
{
    /**
     * 獲取列表記錄
     */
    public function getList($data)
    {
        return $this->where('is_delete', '=', 0)
            ->order(['weight' => 'asc', 'create_time' => 'asc'])
            ->paginate($data);
    }

    /**
     * 獲取列表記錄
     */
    public function getLists()
    {
        return $this->where('is_delete', '=', 0)
            ->field('grade_id,name')
            ->order(['weight' => 'asc', 'create_time' => 'asc'])
            ->select();
    }


    /**
     * 新增記錄
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        $data['is_default'] = 0;
        $data['remark'] = $this->setRemark($data);
        return $this->save($data);
    }

    /**
     * 編輯記錄
     */
    public function edit($data)
    {
        if($this['is_default'] == 0){

            $data['remark'] = $this->setRemark($data);
        }
        return $this->save($data);
    }

    private function setRemark($data){
        $remark = '';
        if($data['open_money'] == 1){
            $money = sprintf('%.2f',$data['upgrade_money']);
            $remark .= "會員消費滿{$money}元可升級到此等級";
        }
        if($data['open_points'] == 1){
            if(!empty($remark)){
                $remark .= '\r\n';
            }
            $remark .= "會員積分滿{$data['upgrade_points']}可升級到此等級";
        }
        if($data['open_invite'] == 1){
            if(!empty($remark)){
                $remark .= '\r\n';
            }
            $remark .= "會員邀請人數滿{$data['upgrade_invite']}可升級到此等級";
        }
        return $remark;
    }

    /**
     * 軟刪除
     */
    public function setDelete()
    {
        // 判斷該等級下是否存在會員
        if (UserModel::checkExistByGradeId($this['grade_id'])) {
            return false;
        }
        return $this->save(['is_delete' => 1]);
    }

}