<?php

namespace app\shop\model\plus\agent;

use app\common\model\user\User as RealUserModel;
use app\shop\model\plus\agent\Referee as RefereeModel;
use app\common\model\plus\agent\User as UserModel;

/**
 * 分銷商使用者模型
 * Class User
 * @package app\shop\model\plus\agent
 */
class User extends UserModel
{
    /**
     * 獲取分銷商使用者列表
     */
    public function getList($search, $limit = 15)
    {
        // 構建查詢規則
        $model = $this->alias('agent')
            ->field('agent.*, user.nickName, user.avatarUrl')
            ->with(['referee', 'grade'])
            ->join('user', 'user.user_id = agent.user_id')
            ->where('agent.is_delete', '=', 0)
            ->order(['agent.create_time' => 'desc']);
        // 查詢條件
        if (!empty($search)) {
            $model = $model->where('user.nickName|agent.real_name|agent.mobile', 'like', '%' . $search . '%');
        }
        // 獲取列表資料
        $list = $model->paginate($limit);
        foreach ($list as $user) {
            $user['total_money'] = sprintf('%.2f', $user['money'] + $user['freeze_money'] + $user['total_money']);
        }
        return $list;
    }

    /**
     * 編輯分銷商使用者
     * @param $data
     * @return bool
     */
    public function edit($data)
    {
        // 開啟事務
        $this->startTrans();
        try {
            if (isset($data['referee_id']) && $data['referee_id'] > 0) {
                // 刪除原推薦人關係
                $this->onDeleteReferee($this['user_id']);
                // 修改使用者推薦人
                (new RealUserModel())->where('user_id', '=', $this['user_id'])->update([
                    'referee_id' => $data['referee_id']
                ]);
                // 記錄推薦人關係，
                RefereeModel::updateRelation($this['user_id'], $data['referee_id']);
            }
            $this->save($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }


    /**
     * 新增分銷商使用者
     * @param $data
     * @return bool
     */
    public function addAgent($data)
    {
        // 判斷是否已經是分銷員

        // 開啟事務
        $this->startTrans();
        try {
            $data['app_id'] = self::$app_id;
            $this->save($data);
            if (isset($data['referee_id']) && $data['referee_id'] > 0) {
                $refereeInfo = self::detail($data['referee_id']);
                if (!$refereeInfo) {
                    $this->error = '推薦人不存在';
                    return false;
                }
                // 修改使用者推薦人
                (new RealUserModel())->where('user_id', '=', $data['user_id'])->update([
                    'referee_id' => $data['referee_id']
                ]);
                // 記錄推薦人關係，
                RefereeModel::updateRelation($data['user_id'], $data['referee_id']);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 刪除分銷商使用者
     * @return mixed
     */
    public function setDelete()
    {
        return $this->transaction(function () {
            // 獲取一級團隊成員ID集
            $RefereeModel = new RefereeModel;
            $team1Ids = $RefereeModel->getTeamUserIds($this['user_id'], 1);
            if (!empty($team1Ids)) {
                // 一級團隊成員歸屬到平臺
                $this->setFromplatform($team1Ids);
                // 一級推薦人ID
                $referee1Id = RefereeModel::getRefereeUserId($this['user_id'], 1, true);
                if ($referee1Id > 0) {
                    // 一級推薦人的成員數量(二級)
                    $this->setDecTeamNum($referee1Id, 2, count($team1Ids));
                    // 一級推薦人的成員數量(三級)
                    $team2Ids = $RefereeModel->getTeamUserIds($this['user_id'], 2);
                    !empty($team2Ids) && $this->setDecTeamNum($referee1Id, 3, count($team2Ids));
                    // 二級推薦人的成員數量(三級)
                    $referee2Id = RefereeModel::getRefereeUserId($this['user_id'], 2, true);
                    $referee2Id > 0 && $this->setDecTeamNum($referee2Id, 3, count($team1Ids));
                    // 清空分銷商下級成員與上級推薦人的關係記錄
                    $RefereeModel->onClearTop(array_merge($team1Ids, $team2Ids));
                }
            }
            // 清空上級推薦記錄
            $this->onDeleteReferee($this['user_id']);
            // 清空下級推薦記錄
            $RefereeModel->onClearTeam($this['user_id']);
            // 標記當前分銷商記錄為已刪除
            return $this->save([
                'is_delete' => 1
            ]);
        });
    }

    /**
     * 一級團隊成員歸屬到平臺
     * @param $userIds
     * @return false|int
     */
    private function setFromplatform($userIds)
    {
        return $this->where('user_id', 'in', $userIds)
            ->where('is_delete', '=', 0)
            ->save(['referee_id' => 0]);
    }

    /**
     * 遞減分銷商成員數量
     */
    private function setDecTeamNum($agent_id, $level, $number)
    {
        $field = [1 => 'first_num', 2 => 'second_num', 3 => 'third_num'];
        $agentUser = $this->where('user_id', '=', $agent_id)
            ->where('is_delete', '=', 0)
            ->field("first_num,second_num,third_num")
            ->find();
        if ($agentUser) {
            $total_num = $agentUser[$field[$level]] - $number;
            $total_num = $total_num > 0 ? $total_num : 0;
            $agentUser->save([$field[$level] => $total_num]);
        }
        return true;
    }

    /**
     * 提現打款成功：累積提現佣金
     */
    public static function totalMoney($user_id, $money)
    {
        $model = self::detail($user_id);
        return $model->save([
            'freeze_money' => $model['freeze_money'] - $money,
            'total_money' => $model['total_money'] + $money,
        ]);
    }

    /**
     * 提現駁回：解凍分銷商資金
     */
    public static function backFreezeMoney($user_id, $money)
    {
        $model = self::detail($user_id);
        return $model->save([
            'money' => $model['money'] + $money,
            'freeze_money' => $model['freeze_money'] - $money,
        ]);
    }

    /**
     * 刪除使用者的上級推薦關係
     * @param $userId
     * @return bool
     * @throws \think\Exception
     */
    public function onDeleteReferee($userId)
    {
        // 獲取推薦人列表
        $list = RefereeModel::getRefereeList($userId);
        if (!$list->isEmpty()) {
            // 遞減推薦人的下級成員數量
            foreach ($list as $item) {
                $item['agent1'] && $this->setDecTeamNum($item['agent_id'], $item['level'], 1);
            }
            // 清空上級推薦關係
            (new RefereeModel)->onClearReferee($userId);
        }
        return true;
    }

    /**
     * 指定會員等級下是否存在使用者
     */
    public static function checkExistByGradeId($gradeId)
    {
        $model = new static;
        return !!$model->where('grade_id', '=', (int)$gradeId)
            ->where('is_delete', '=', 0)
            ->value('user_id');
    }
}