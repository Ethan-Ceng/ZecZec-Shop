<?php

namespace app\shop\model\user;

use app\common\model\user\Grade as GradeModel;
use app\common\model\user\UserTag as UserTagModel;
use app\shop\model\user\GradeLog as GradeLogModel;
use app\shop\model\user\BalanceLog as BalanceLogModel;
use app\common\model\user\User as UserModel;
use app\common\enum\user\grade\ChangeTypeEnum;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum as SceneEnum;
use app\shop\model\user\PointsLog as PointsLogModel;
use app\shop\model\plus\agent\User as AgentUserModel;

/**
 * 使用者模型
 */
class User extends UserModel
{
    /**
     * 獲取當前使用者總數
     */
    public function getUserTotal($day = null)
    {
        $model = $this;
        if (!is_null($day)) {
            $startTime = strtotime($day);
            $model = $model->where('create_time', '>=', $startTime)
                ->where('create_time', '<', $startTime + 86400);
        }
        return $model->where('is_delete', '=', '0')->count();
    }

    /**
     * 獲取使用者id
     * @return \think\Collection
     */
    public function getUsers($where = null)
    {
        // 獲取使用者列表
        return $this->where('is_delete', '=', '0')
            ->where($where)
            ->order(['user_id' => 'asc'])
            ->field(['user_id'])
            ->select();
    }

    /**
     * 獲取使用者列表
     */
    public static function getList($nickName, $grade_id, $reg_date, $params)
    {
        $model = new static();
        //檢索：使用者名稱
        if (!empty($nickName)) {
            $model = $model->where('user.nickName|user.mobile|user.user_id', 'like', '%' . $nickName . '%');
        }
        // 檢索：會員等級
        if ($grade_id > 0) {
            $model = $model->where('user.grade_id', '=', (int)$grade_id);
        }
        //檢索：註冊時間
        if (!empty($reg_date[0])) {
            $model = $model->whereTime('user.create_time', 'between', [$reg_date[0], date('Y-m-d 23:59:59', strtotime($reg_date[1]))]);
        }
        // 檢索：標籤
        if (!empty($params['tag_id']) && $params['tag_id'] > 0) {
            $model = $model->where('tag.tag_id', '=', (int)$params['tag_id']);
        }
        if (isset($params['reg_source']) && $params['reg_source']) {
            $model = $model->where('user.reg_source', '=', $params['reg_source']);
        }
        // 獲取使用者列表
        return $model->alias('user')
            ->distinct(true)
            ->field(['user.*'])
            ->with(['grade', 'referee'])
            ->join('user_tag tag', 'user.user_id = tag.user_id', 'left')
            ->where('user.is_delete', '=', '0')
            ->order(['user.create_time' => 'desc'])
            ->hidden(['open_id', 'union_id'])
            ->paginate($params);
    }

    /**
     * 軟刪除
     */
    public function setDelete()
    {
        // 判斷是否為分銷商
        if (AgentUserModel::isAgentUser($this['user_id'])) {
            $this->error = '當前使用者為分銷商，不可刪除';
            return false;
        }
        return $this->transaction(function () {
            // 刪除使用者推薦關係
            (new AgentUserModel)->onDeleteReferee($this['user_id']);
            // 標記為已刪除
            return $this->save(['is_delete' => 1]);
        });
    }

    /**
     * 新增記錄
     * ->where('reg_source', 'in', ['h5', 'app'])
     */
    public function add($data)
    {
        $mobile = $this->where('mobile', '=', $data['mobile'])
            ->where('is_delete', '=', 0)
            ->count();
        if ($mobile) {
            $this->error = "手機號已存在";
            return false;
        }
        $data['password'] = md5($data['password']);
        $data['grade_id'] = GradeModel::getDefaultGradeId();
        $data['app_id'] = self::$app_id;
        $data['reg_source'] = 'h5';
        return $this->save($data);
    }

    /**
     * 修改記錄
     */
    public function edit($data)
    {
        $data['update_time'] = time();
        if ($data['mobile']) {
            if ($this['reg_source'] == 'h5' || $this['reg_source'] == 'app') {
                $reg_source = ['h5', 'app'];
            } else {
                $reg_source = [$this['reg_source']];
            }
            $mobile = $this->where('mobile', '=', $data['mobile'])
                ->where('user_id', '<>', $this['user_id'])
                ->where('reg_source', 'in', $reg_source)
                ->where('is_delete', '=', 0)
                ->count();
            if ($mobile) {
                $this->error = "手機號已存在";
                return false;
            }
        }
        if ($data['password']) {
            $data['password'] = md5($data['password']);
        } else {
            unset($data['password']);
        }
        return $this->allowField(['nickName', 'avatarUrl', 'gender', 'mobile', 'password'])->save($data);
    }

    /**
     * 修改使用者等級
     */
    public function updateGrade($data)
    {
        if (!isset($data['remark'])) {
            $data['remark'] = '';
        }
        // 變更前的等級id
        $oldGradeId = $this['grade_id'];
        return $this->transaction(function () use ($oldGradeId, $data) {
            // 更新使用者的等級
            $status = $this->save(['grade_id' => $data['grade_id']]);
            // 新增使用者等級修改記錄
            if ($status) {
                (new GradeLogModel)->save([
                    'user_id' => $this['user_id'],
                    'old_grade_id' => $oldGradeId,
                    'new_grade_id' => $data['grade_id'],
                    'change_type' => ChangeTypeEnum::ADMIN_USER,
                    'remark' => $data['remark'],
                    'app_id' => $this['app_id']
                ]);
            }
            return $status !== false;
        });
    }

    /**
     * 消減使用者的實際消費金額
     */
    public function setDecUserExpend($userId, $expendMoney)
    {
        return $this->where(['user_id' => $userId])->dec('expend_money', $expendMoney)->update();
    }

    /**
     * 使用者充值
     */
    public function recharge($storeUserName, $source, $data)
    {
        if ($source == 0) {
            return $this->rechargeToBalance($storeUserName, $data['balance']);
        } elseif ($source == 1) {
            return $this->rechargeToPoints($storeUserName, $data['points']);
        }
        return false;
    }

    /**
     * 使用者充值：餘額
     */
    private function rechargeToBalance($storeUserName, $data)
    {
        if (!isset($data['money']) || $data['money'] === '' || $data['money'] < 0) {
            $this->error = '請輸入正確的金額';
            return false;
        }
        // 判斷充值方式，計算最終金額
        if ($data['mode'] === 'inc') {
            $diffMoney = $this['balance'] + $data['money'];
            $money = $data['money'];
        } elseif ($data['mode'] === 'dec') {
            $diffMoney = $this['balance'] - $data['money'] <= 0 ? 0 : $this['balance'] - $data['money'];
            $money = -$data['money'];
        } else {
            $diffMoney = $data['money'];
            $money = $diffMoney - $this['balance'];
        }
        // 更新記錄
        $this->transaction(function () use ($storeUserName, $data, $diffMoney, $money) {
            // 更新賬戶餘額
            $this->where('user_id', '=', $this['user_id'])->update(['balance' => $diffMoney]);
            // 新增餘額變動記錄
            BalanceLogModel::add(SceneEnum::ADMIN, [
                'user_id' => $this['user_id'],
                'money' => $money,
                'remark' => $data['remark'],
            ], [$storeUserName]);
        });
        return true;
    }

    /**
     * 使用者充值：積分
     */
    private function rechargeToPoints($storeUserName, $data)
    {
        if (!isset($data['value']) || $data['value'] === '' || $data['value'] < 0) {
            $this->error = '請輸入正確的積分數量';
            return false;
        }
        // 判斷充值方式，計算最終積分
        if ($data['mode'] === 'inc') {
            $diffMoney = $this['points'] + $data['value'];
            $points = $data['value'];
        } elseif ($data['mode'] === 'dec') {
            $diffMoney = $this['points'] - $data['value'] <= 0 ? 0 : $this['points'] - $data['value'];
            $points = -$data['value'];
        } else {
            $diffMoney = $data['value'];
            $points = $data['value'] - $this['points'];
        }
        // 更新記錄
        $this->transaction(function () use ($storeUserName, $data, $diffMoney, $points) {
            $totalPoints = $this['total_points'] + $points <= 0 ? 0 : $this['total_points'] + $points;
            // 更新賬戶積分
            $this->where('user_id', '=', $this['user_id'])->update([
                'points' => $diffMoney,
                'total_points' => $totalPoints
            ]);
            // 新增積分變動記錄
            PointsLogModel::add([
                'user_id' => $this['user_id'],
                'value' => $points,
                'describe' => "後臺管理員 [{$storeUserName}] 操作",
                'remark' => $data['remark'],
            ]);
        });
        event('UserGrade', $this['user_id']);
        return true;
    }


    /**
     * 獲取使用者統計數量
     */
    public function getUserData($startDate, $endDate, $type)
    {
        $model = $this;
        if (!is_null($startDate)) {
            $model = $model->where('create_time', '>=', strtotime($startDate));
        }
        if (is_null($endDate)) {
            $model = $model->where('create_time', '<', strtotime($startDate) + 86400);
        } else {
            $model = $model->where('create_time', '<', strtotime($endDate) + 86400);
        }
        if ($type == 'user_total' || $type == 'user_add') {
            return $model->count();
        } else if ($type == 'user_pay') {
            return $model->where('pay_money', '>', '0')->count();
        } else if ($type == 'user_no_pay') {
            return $model->where('pay_money', '=', '0')->count();
        }
        return 0;
    }

    public function editTag($data)
    {
        // 刪除所有標籤
        (new UserTagModel())->where('user_id', '=', $this['user_id'])
            ->delete();
        $tag_list = [];
        foreach ($data['checkedTag'] as $val) {
            $tag_list[] = [
                'user_id' => $this['user_id'],
                'tag_id' => $val,
                'app_id' => self::$app_id
            ];
        }
        return (new UserTagModel())->saveAll($tag_list);
    }

    /**
     * 提現打款成功：累積提現餘額
     */
    public static function totalMoney($user_id, $money)
    {
        $model = self::detail($user_id);
        return $model->save([
            'freeze_money' => $model['freeze_money'] - $money,
            'cash_money' => $model['cash_money'] + $money,
        ]);
    }

    /**
     * 提現駁回：解凍使用者餘額
     */
    public static function backFreezeMoney($user_id, $money)
    {
        $model = self::detail($user_id);
        return $model->save([
            'balance' => $model['balance'] + $money,
            'freeze_money' => $model['freeze_money'] - $money,
        ]);
    }
}
