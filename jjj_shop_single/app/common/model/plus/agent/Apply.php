<?php

namespace app\common\model\plus\agent;

use app\common\model\BaseModel;

/**
 * 分銷商申請模型
 */
class Apply extends BaseModel
{
    protected $name = 'agent_apply';
    protected $pk = 'apply_id';

    /**
     * 申請狀態
     * @var array
     */
    public $applyStatus = [
        10 => '待稽核',
        20 => '稽核透過',
        30 => '駁回',
    ];

    /**
     * 申請時間
     * @param $value
     * @return false|string
     */
    public function getApplyTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 稽核時間
     * @param $value
     * @return false|int|string
     */
    public function getAuditTimeAttr($value)
    {
        return $value > 0 ? date('Y-m-d H:i:s', $value) : 0;
    }

    /**
     * 關聯推薦人表
     * @return \think\model\relation\BelongsTo
     */
    public function referee()
    {
        return $this->belongsTo('app\common\model\user\User', 'referee_id')
            ->field(['user_id', 'nickName']);
    }

    /**
     * 銷商申請記錄詳情
     * @param $where
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function detail($where)
    {
        $filter = is_array($where) ? $where : ['apply_id' => $where];
        return (new static())->where($filter)->find();
    }

    /**
     * 購買指定商品成為分銷商
     * @param $userId
     * @param $productIds
     * @param $appId
     * @return bool
     */
    public function becomeAgentUser($userId, $productIds, $appId)
    {
        // 驗證是否設定
        $config = Setting::getItem('condition', $appId);
        if ($config['become__buy_product'] != '1' || empty($config['become__buy_product_ids'])) {
            return false;
        }
        // 判斷商品是否在設定範圍內
        $intersect = array_intersect($productIds, $config['become__buy_product_ids']);
        if (empty($intersect)) {
            return false;
        }
        // 新增分銷商使用者
        User::add($userId, [
            'referee_id' => Referee::getRefereeUserId($userId, 1),
            'app_id' => $appId,
            'grade_id' => (new Grade())->getDefaultGradeId()
        ]);
        return true;
    }


    /**
     * 稽核狀態
     * @param $value
     * @return array
     */
    public function getApplyStatusAttr($value)
    {
        $method = [10 => '待稽核', 20 => '稽核透過', '30' => '駁回'];
        return ['text' => $method[$value], 'value' => $value];
    }

    /**
     * 稽核方式
     * @param $value
     * @return array
     */
    public function getApplyTypeAttr($value)
    {
        $method = [10 => '後臺稽核', 20 => '無需稽核'];
        return ['text' => $method[$value], 'value' => $value];
    }

}