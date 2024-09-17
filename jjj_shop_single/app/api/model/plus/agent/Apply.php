<?php

namespace app\api\model\plus\agent;

use app\api\model\plus\agent\Referee as RefereeModel;
use app\common\model\plus\agent\Apply as ApplyModel;
use app\common\model\plus\agent\Grade as GradeModel;

/**
 * 分銷商申請模型
 */
class Apply extends ApplyModel
{
    /**
     * 隱藏欄位
     * @var array
     */
    protected $hidden = [
        'create_time',
        'update_time',
    ];

    /**
     * 是否為分銷商申請中
     */
    public static function isApplying($user_id)
    {
        $detail = self::detail(['user_id' => $user_id]);
        return $detail ? ((int)$detail['apply_status']['value'] === 10) : false;
    }

    /**
     * 提交申請
     */
    public function submit($user, $data)
    {
        // 成為分銷商條件
        $config = Setting::getItem('condition');
        // 如果之前有關聯分銷商，則繼續關聯之前的分銷商
        $has_referee_id = Referee::getRefereeUserId($user['user_id'], 1);
        if ($has_referee_id > 0) {
            $referee_id = $has_referee_id;
        } else {
            $referee_id = $data['referee_id'] > 0 ? $data['referee_id'] : 0;
        }
        // 資料整理
        $data = [
            'user_id' => $user['user_id'],
            'real_name' => $user['nickName'] ?? $user['email'],
            'mobile' => $user['mobile'] ?? '',
            'email' => $user['email'] ?? '',
            'referee_id' => $referee_id,
            'apply_type' => $config['become'],
            'apply_time' => time(),
            'app_id' => self::$app_id,
        ];
        if ($config['become'] == 10) {
            $data['apply_status'] = 10;
        } elseif ($config['become'] == 20) {
            $data['apply_status'] = 20;
        }
        return $this->add($user, $data);
    }

    /**
     * 更新分銷商申請資訊
     */
    private function add($user, $data)
    {
        // 例項化模型
        $model = self::detail(['user_id' => $user['user_id']]) ?: $this;
        // 更新記錄
        $this->startTrans();
        try {
            // 儲存申請資訊
            $model->save($data);
            // 無需稽核，自動透過
            if ($data['apply_type'] == 20) {
                // 新增分銷商使用者記錄
                User::add($user['user_id'], [
                    'real_name' => $data['real_name'],
                    'mobile' => $data['mobile'],
                    'referee_id' => $data['referee_id'],
                    'grade_id' => (new GradeModel())->getDefaultGradeId()
                ]);
            }
            // 記錄推薦人關係
            if ($data['referee_id'] > 0) {
                RefereeModel::createRelation($user['user_id'], $data['referee_id']);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

}
