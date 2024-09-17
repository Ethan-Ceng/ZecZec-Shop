<?php

namespace app\shop\model\plus\giftpackage;

use app\common\model\plus\giftpackage\Code as CodeModel;

/**
 * 禮包購碼
 */
class Code extends CodeModel
{
    /**
     * 生成碼,code生成規則，活動id+下標
     */
    public function geneCode($gift_package_id, $code_type, $total_num){
        // 一批一碼
        if($code_type == 10){
            $this->save([
                'gift_package_id' => $gift_package_id,
                'code' => $gift_package_id . '-1',
                'app_id' => self::$app_id
            ]);
        }else{
            // 當前碼數量
            $count = $this->where('gift_package_id', '=', $gift_package_id)->count();
            $data = [];
            for($i=0;$i<$total_num;$i++){
                $data[] = [
                    'gift_package_id' => $gift_package_id,
                    'code' => $gift_package_id . '-' . ($count + 1 + $i),
                    'app_id' => self::$app_id
                ];
            }
            $this->saveAll($data);
        }
    }
}