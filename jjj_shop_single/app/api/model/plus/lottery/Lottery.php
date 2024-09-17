<?php

namespace app\api\model\plus\lottery;

use app\api\model\settings\Setting as SettingModel;
use app\common\model\plus\lottery\Lottery as LotteryModel;
use app\api\model\plus\coupon\UserCoupon;
use think\facade\Cache;
use app\common\model\user\User as UserModel;

/**
 *轉盤模型
 */
class Lottery extends LotteryModel
{
    /**
     * 轉盤詳情
     * @param $data
     */
    public function getDetail()
    {
        $detail = self::detail();
        if ($detail) {
            $LotteryPrize = new LotteryPrize;
            $detail['prize'] = $LotteryPrize::detail($detail['lottery_id']);
            $points_name = SettingModel::getPointsName();
            if ($detail['prize']) {
                foreach ($detail['prize'] as &$item) {
                    if ($item['type'] == 2) {
                        $item['name'] = $points_name ? $item['points'] . $points_name : $item['name'];
                    }
                }
            }
        }
        return $detail;
    }

    /**
     * 已抽獎次數
     * @param $data
     */
    public function getNum($user)
    {
        //抽獎次數
        $time = strtotime(date('Y-m-d', time()));
        $num = Cache::get('draw_' . $time . $user['user_id']);
        return $num ? $num : 0;
    }

    /**
     * 抽獎
     * @param $data
     */
    public function getdraw($user)
    {
        $this->startTrans();
        try {
            //獎品詳情
            $detail = self::detail();
            if ($detail['status'] == 0) {
                $this->error = "抽獎未開啟";
                return false;
            }
            if ($user['points'] < $detail['points']) {
                $this->error = "積分不足";
                return false;
            }
            $time = strtotime(date('Y-m-d', time()));
            //判斷今日抽獎次數
            $num = Cache::get('draw_' . $time . $user['user_id']);
            if ($num && $num >= $detail['times']) {
                $this->error = "今日抽獎次數已用完";
                return false;
            }
            $LotteryPrize = new LotteryPrize;
            $drawArr = $LotteryPrize::detail($detail['lottery_id']);//json_decode($detail['lottery_data'], 1);
            //shuffle($drawArr); //打亂陣列順序
            $arr = [];
            $default = [];
            foreach ($drawArr as $key => $val) {
                if ($val['stock'] - $val['draw_num'] > 0) {
                    $arr[$key] = $val['stock'];//機率陣列
                }
                if ($val['is_default'] == 1) {
                    $default = $val;//預設中獎項
                }
            }
            if ($arr) {
                $rid = $this->get_rand($arr); //根據機率獲取獎項
                $result = $drawArr[$rid]; //中獎項
            } else {
                $result = $default; //預設中獎項
            }
            if (!$result) {
                $this->error = "禮品已抽完，請稍後再試";
                return false;
            }
            $arr && $LotteryPrize->where('prize_id', '=', $drawArr[$rid]['prize_id'])->inc('draw_num', 1)->update();
            //新增中獎記錄
            $record = [
                'record_name' => $result['name'],
                'user_id' => $user['user_id'],
                'prize_id' => $result['prize_id'],
                'prize_type' => $result['type'],
                'award_id' => $result['award_id'],
                'status' => $result['type'] == 3 ? 0 : 1,
                'app_id' => self::$app_id,
                'points' => $result['points'],
                'is_play' => $result['is_play'],
            ];
            //更新使用者積分
            $user->setIncPoints(-$detail['points'], '抽獎消費積分');
            //更新積分優惠券
            if (in_array($result['type'], [1, 2])) {
                $this->addDraw($result, $user);
            }
            (new Record)->save($record);
            //今日時間 記錄快取, 24小時
            if ($num) {
                Cache::inc('draw_' . $time . $user['user_id']);
            } else {
                $num = 0;
                Cache::set('draw_' . $time . $user['user_id'], $num + 1, 3600 * 24);
            }
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    //更新積分優惠券
    private function addDraw($data, $user)
    {
        if ($data['type'] == 1) {//優惠券
            $UserCouponModel = new UserCoupon;
            $UserCouponModel->addUserCoupon([$data['award_id']], $user);
        } elseif ($data['type'] == 2) {//積分
            // 重新查詢使用者
            $user = UserModel::detail($user['user_id']);
            $user->setIncPoints($data['points'], '抽獎獲取積分');
        }
    }

    //計算中獎
    private function get_rand($proArr)
    {
        $result = '';
        //機率陣列的總機率精度
        $proSum = array_sum($proArr);
        //機率陣列迴圈
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);  //返回隨機整數
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }
}