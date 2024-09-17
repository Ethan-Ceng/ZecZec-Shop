<?php

namespace app\shop\model\plus\agent;

use app\common\model\plus\agent\Grade as AgentGradeModel;
use app\shop\model\plus\agent\User as AgentUserModel;

/**
 * 使用者會員等級模型
 */
class Grade extends AgentGradeModel
{
    /**
     * 獲取列表記錄
     */
    public function getList()
    {
        $list = $this->selectList();
        // 如果為空，則插入預設等級
        if(count($list) == 0){
            $this->save([
                'name' => '預設等級',
                'is_default' => 1,
                'weight' => 1,
                'grade_id' => Grade::getDefaultGradeId(),
                'app_id' => self::$app_id
            ]);
            // 更新之前的預設為0的id為此等級id
            (new AgentUserModel())->where('grade_id', '=', 0)->update([
                'grade_id' => $this['grade_id']
            ]);
            $list = $this->selectList();
        }
        return $list;
    }


    private function selectList(){
        return $this->where('is_delete', '=', 0)
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
        if($data['open_agent_money'] == 1){
            $money = sprintf('%.2f',$data['agent_money']);
            $remark .= "推廣金額滿{$money}元";
        }
        if($data['open_agent_user'] == 1){
            if(!empty($remark)){
                $remark .= '\r\n';
            }
            $remark .= "直推分銷商滿{$data['agent_user']}";
        }
        return $remark;
    }

    /**
     * 軟刪除
     */
    public function setDelete()
    {
        // 判斷該等級下是否存在會員
        if (AgentUserModel::checkExistByGradeId($this['grade_id'])) {
            return false;
        }
        return $this->save(['is_delete' => 1]);
    }

}