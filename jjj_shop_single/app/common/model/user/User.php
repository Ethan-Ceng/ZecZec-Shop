<?php


namespace app\common\model\user;

use app\common\model\BaseModel;
use app\common\model\user\PointsLog as PointsLogModel;
use app\common\model\settings\Setting as SettingModel;
use app\common\model\plus\agent\Referee as RefereeModel;
use app\common\model\plus\invitationgift\Partake;

/**
 * 使用者模型
 */
class User extends BaseModel
{
    protected $pk = 'user_id';
    protected $name = 'user';

    /**
     * 預設頭像
     */
    public function getAvatarUrlAttr($value)
    {
        return $value ? $value : SettingModel::getItem('store', self::$app_id)['avatarUrl'];
    }

    /**
     * 關聯會員等級表
     */
    public function grade()
    {
        return $this->belongsTo('app\\common\\model\\user\\Grade', 'grade_id', 'grade_id');
    }

    /**
     * 關聯收貨地址表
     */
    public function address()
    {
        return $this->hasMany('app\\common\\model\\user\\UserAddress', 'address_id', 'address_id');
    }

    /**
     * 關聯收貨地址表 (預設地址)
     */
    public function addressDefault()
    {
        return $this->belongsTo('app\\common\\model\\user\\UserAddress', 'address_id', 'address_id');
    }

    /**
     * 關聯推薦人
     */
    public function referee()
    {
        return $this->belongsTo('app\\common\\model\\user\\User', 'referee_id', 'user_id')->where('is_delete', '=', 0)->field(['user_id', 'nickName']);
    }

    /**
     * 獲取使用者資訊
     */
    public static function detail($where)
    {
        $model = new static;
        $filter = ['is_delete' => 0];
        if (is_array($where)) {
            $filter = array_merge($filter, $where);
        } else {
            $filter['user_id'] = (int)$where;
        }
        return $model->where($filter)->with(['address', 'addressDefault', 'grade'])->find();
    }

    /**
     * 獲取使用者資訊
     */
    public static function detailByUnionid($unionid)
    {
        $model = new static;
        $filter = ['is_delete' => 0];
        $filter = array_merge($filter, ['union_id' => $unionid]);
        return $model->where($filter)->with(['address', 'addressDefault', 'grade'])->find();
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

    /**
     * 累積使用者總消費金額
     */
    public function setIncPayMoney($money)
    {
        return $this->where('user_id', '=', $this['user_id'])->inc('pay_money', $money)->update();
    }

    /**
     * 累積使用者實際消費的金額 (批次)
     */
    public function onBatchIncExpendMoney($data)
    {
        foreach ($data as $userId => $expendMoney) {
            $this->where(['user_id' => $userId])->inc('expend_money', $expendMoney)->update();
            event('UserGrade', $userId);
        }
        return true;
    }

    /**
     * 累積使用者的可用積分數量 (批次)
     */
    public function onBatchIncPoints($data)
    {
        foreach ($data as $userId => $expendPoints) {
            $this->where(['user_id' => $userId])
                ->inc('points', $expendPoints)
                ->inc('total_points', $expendPoints)
                ->update();
            event('UserGrade', $this['user_id']);
        }
        return true;
    }

    /**
     * 累積使用者的可用積分
     */
    public function setIncPoints($points, $describe, $upgrade = true)
    {
        // 新增積分變動明細
        PointsLogModel::add([
            'user_id' => $this['user_id'],
            'value' => $points,
            'describe' => $describe,
            'app_id' => $this['app_id']
        ]);

        // 更新使用者可用積分
        $data['points'] = ($this['points'] + $points <= 0) ? 0 : $this['points'] + $points;
        // 使用者總積分
        if ($points > 0) {
            $data['total_points'] = $this['total_points'] + $points;
        }
        $this->where('user_id', '=', $this['user_id'])->update($data);
        if ($upgrade) {
            event('UserGrade', $this['user_id']);
        }
        return true;
    }

    /**
     * 累計邀請書
     */
    public function setIncInvite($user_id)
    {
        $this->where('user_id', '=', $user_id)->inc('total_invite')->update();
        event('UserGrade', $user_id);
    }

    /**
     * 註冊之後的操作
     */
    public function afterReg($user)
    {
        $register_points = SettingModel::getItem('points')['register_points'];
        if ($register_points > 0) {
            $user->setIncPoints($register_points, '新使用者註冊贈送');
        }
    }

    /**
     * 註冊之後關係繫結
     */
    public function saveRelation($user, $refereeId, $invitation_id)
    {
        if ($refereeId > 0) {
            // 記錄推薦人關係
            RefereeModel::createRelation($user['user_id'], $refereeId);
            //更新使用者邀請數量
            $this->setIncInvite($refereeId);
            //邀請好友送好禮
            $invitation_id > 0 && (new Partake())->addPartake($invitation_id, $refereeId, $user['user_id']);
        }
        $this->afterReg($user);
    }
}