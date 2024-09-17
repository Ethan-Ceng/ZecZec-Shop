<?php

namespace app\api\model\shop;

use app\common\library\helper;
use app\common\model\shop\FullReduce as FullReduceModel;

/**
 * 滿減模型
 */
class FullReduce extends FullReduceModel
{
    /**
     * 獲取列表記錄
     */
    public static function getReductList($total_price, $total_num)
    {
        // 獲取所有滿減活動
        $list = (new self)->getAll();
        $data = [];
        foreach ($list as $reduce) {
            // 滿額
            if ($reduce['full_type'] == 1) {
                if ($total_price < $reduce['full_value']) {
                    continue;
                }
            } else {
                // 滿件數
                if ($total_num < $reduce['full_value']) {
                    continue;
                }
            }
            $key = $reduce['fullreduce_id'];
            // 計算減的金額
            $data[$key] = [
                'fullreduce_id' => $reduce['fullreduce_id'],
                'active_name' => $reduce['active_name'],
            ];
            // 計算滿減金額
            if ($reduce['reduce_type'] == 1) {
                //滿金額
                $data[$key]['reduced_price'] = $reduce['reduce_value'];
            } else{
                // 折扣比例
                $discountRatio = helper::bcdiv($reduce['reduce_value'], 100);
                $data[$key]['reduced_price'] = max(0.01, helper::bcmul($total_price, $discountRatio));
            }
        }
        if(count($data) == 0){
            return false;
        }else{
            // 根據折扣金額排序並返回第一個
            $reduce = array_sort($data, 'reduced_price', true);
            $reduce = current($reduce);
            return $reduce;
        }
    }

    /**
     * 獲取列表記錄
     */
    public static function getProductReductList($product_id, $total_price, $total_num)
    {
        // 獲取所有滿減活動
        $list = (new self)->getProductAll($product_id);
        $data = [];
        foreach ($list as $reduce) {
            // 滿額
            if ($reduce['full_type'] == 1) {
                if ($total_price < $reduce['full_value']) {
                    continue;
                }
            } else {
                // 滿件數
                if ($total_num < $reduce['full_value']) {
                    continue;
                }
            }
            $key = $reduce['fullreduce_id'];
            // 計算減的金額
            $data[$key] = [
                'fullreduce_id' => $reduce['fullreduce_id'],
                'active_name' => $reduce['active_name'],
            ];
            // 計算滿減金額
            if ($reduce['reduce_type'] == 1) {
                //滿金額
                $data[$key]['reduced_price'] = $reduce['reduce_value'];
            } else{
                // 折扣比例
                $discountRatio = helper::bcdiv($reduce['reduce_value'], 100);
                $data[$key]['reduced_price'] = max(0.01, helper::bcmul($total_price, $discountRatio));
            }
        }
        if(count($data) == 0){
            return false;
        }else{
            // 根據折扣金額排序並返回第一個
            $reduce = array_sort($data, 'reduced_price', true);
            $reduce = current($reduce);
            return $reduce;
        }
    }

    public static function getListByProduct($product_id){
        return (new self())->where('product_id', '=', $product_id)
            ->order(['fullreduce_id' => 'asc'])
            ->select();
    }
}