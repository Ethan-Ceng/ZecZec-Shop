<?php

namespace app\shop\model\plus\agent;

use app\api\model\plus\agent\Referee as RefereeModel;
use app\common\model\plus\agent\Apply as ApplyModel;
use app\common\service\message\MessageService;

/**
 * 分銷商入駐申請模型
 */
class Apply extends ApplyModel
{
    /**
     * 獲取分銷商申請列表
     */
    public function getList($search)
    {
        $model = $this->alias('apply')
            ->field('apply.*, user.nickName, user.avatarUrl')
            ->with(['referee'])
            ->join('user', 'user.user_id = apply.user_id')
            ->order(['apply.create_time' => 'desc']);
        if (!empty($search['nick_name'])) {
            $model = $model->where('user.nickName|apply.real_name|apply.mobile', 'like', '%' . $search['nick_name'] . '%');
        }

        // 獲取列表資料
        return $model->paginate($search['list_rows']);
    }

    /**
     * 分銷商入駐稽核
     * @param $data
     * @return bool
     */
    public function submit($data)
    {
        if ($data['apply_status'] == '30' && empty($data['reject_reason'])) {
            $this->error = '請填寫駁回原因';
            return false;
        }
        $this->startTrans();
        try {
            if ($data['apply_status'] == '20') {
                // 新增分銷商使用者
                User::add($data['user_id'], [
                    'real_name' => $data['real_name'],
                    'mobile' => $data['mobile'],
                    'referee_id' => $data['referee_id'],
                    'grade_id' => (new Grade())->getDefaultGradeId()
                ]);
            }
            $save_data = [
                'audit_time' => time(),
                'apply_status' => $data['apply_status'],
                'reject_reason' => $data['reject_reason'],
            ];
            $this->save($save_data);
            // 記錄推薦人關係
            if ($data['referee_id'] > 0) {
                RefereeModel::createRelation($data['user_id'], $data['referee_id']);
            }
            // 傳送模板訊息
            (new MessageService)->agent($this);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 獲取申請中的數量
     */
    public static function getApplyCount()
    {
        return (new static())->where('apply_status', '=', 10)->count();
    }

}