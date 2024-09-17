<?php

namespace app\api\model\plus\card;

use app\common\model\plus\card\Code as CodeModel;
use app\api\model\plus\card\Order as OrderModel;
use app\common\service\order\OrderService;
/**
 * 滿減模型
 */
class Code extends CodeModel
{
   public function check($user, $params){
       $detail = $this->with(['card'])->where('code_no', '=', $params['code_no'])
           ->where('code_pwd', '=', $params['code_pwd'])
           ->find();
       if(!$detail){
           $this->error = '卡券不存在或密碼錯誤';
           return false;
       }
       if($detail['is_delete'] == 1){
           $this->error = '卡券不存在或已刪除';
           return false;
       }
       if($detail['code_status'] == 1){
           $this->error = '卡券已被提貨，請勿重複提貨';
           return false;
       }
       if($detail['start_time'] > time()){
           $this->error = '未到提貨時間';
           return false;
       }
       if(time() > $detail['end_time']){
           $this->error = '已超過提貨時間';
           return false;
       }
       if($detail['card']['card_status'] == 20 || $detail['card']['is_delete'] == 1){
           $this->error = '卡券已下架或已刪除';
           return false;
       }
       // 直接生成訂單
       if($detail['order_id'] == 0){
           $model = new OrderModel();
           $model->save([
               'order_no' => OrderService::createOrderNo(),
               'code_id' => $detail['code_id'],
               'card_id' => $detail['card_id'],
               'user_id' => $user['user_id'],
               'app_id' => self::$app_id
           ]);
           $detail->save([
               'order_id' => $model['order_id']
           ]);
           $order_id = $model['order_id'];
       }else{
           $order_id = $detail['order_id'];
       }
       return $order_id;
   }
}