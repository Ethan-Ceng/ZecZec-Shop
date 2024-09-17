<?php

namespace app\common\model\page;

use app\common\model\BaseModel;
use think\facade\Cache;

/**
 * 個人中心選單
 */
class CenterMenu extends BaseModel
{
    //表名
    protected $name = 'center_menu';
    //主鍵欄位名
    protected $pk = 'menu_id';

    /**
     * 詳情
     */
    public static function detail($menu_id)
    {
        return (new static())->find($menu_id);
    }
    /**
     * 查詢所有
     */
    public static function getAll(){
        $model = new static();
        if (!Cache::get('center_menu_' . $model::$app_id)) {
            $list = $model->order(['sort' => 'asc'])->select();
            if(count($list) == 0){
                $sys_menus = $model->getSysMenu();
                $save_data = [];
                foreach($sys_menus as $menu){
                    $save_data[] = array_merge($sys_menus[$menu['sys_tag']], [
                        'app_id' => self::$app_id
                    ]);
                }
                $model->saveAll($save_data);
                $list = $model->order(['sort' => 'asc'])->select();
            }
            Cache::tag('cache')->set('center_menu_' . $model::$app_id, $list);
        }
        return Cache::get('center_menu_' . $model::$app_id);
    }

    /**
     * 系統選單
     */
    public static function getSysMenu(){
      return [
          'address' => [
              'sys_tag' => 'address',
              'name' => '收貨地址',
              'path' => '/pages/user/address/address',
              'icon' => 'image/center_menu/address.png'
          ],
          'coupon' => [
              'sys_tag' => 'coupon',
              'name' => '領券中心',
              'path' => '/pages/coupon/coupon',
              'icon' => 'image/center_menu/coupon.png'
          ],
          'my_coupon' => [
              'sys_tag' => 'my_coupon',
              'name' => '我的優惠券',
              'path' => '/pages/user/my-coupon/my-coupon',
              'icon' => 'image/center_menu/my_coupon.png'
          ],
          'my_fav' => [
              'sys_tag' => 'my_fav',
              'name' => '我的收藏',
              'path' => '/pages/user/favorite/favorite',
              'icon' =>  'image/center_menu/my_fav.png'
          ],
          'agent' => [
              'sys_tag' => 'agent',
              'name' => '分銷中心',
              'path' => '/pages/agent/index/index',
              'icon' => 'image/center_menu/agent.png'
          ],
          'bargain' => [
              'sys_tag' => 'bargain',
              'name' => '我的砍價',
              'path' => '/pages/user/my-bargain/my-bargain',
              'icon' => 'image/center_menu/bargain.png'
          ],
          'sign' => [
              'sys_tag' => 'sign',
              'name' => '簽到',
              'path' => '/pages/plus/signin/signin',
              'icon' =>  'image/center_menu/sign.png'
          ],
          'gift' => [
              'sys_tag' => 'gift',
              'name' => '我的禮包',
              'path' => '/pages/plus/giftpackage/giftlist',
              'icon' =>  'image/center_menu/gift.png'
          ],
          'lottery' => [
              'sys_tag' => 'lottery',
              'name' => '我的抽獎',
              'path' => '/pages/plus/lottery/lottery',
              'icon' =>  'image/center_menu/lottery.png'
          ],
          'my_card' => [
              'sys_tag' => 'my_card',
              'name' => '提貨訂單',
              'path' => '/pages/order/codeorder',
              'icon' =>  'image/center_menu/my_card.png'
          ],
          'settings' => [
              'sys_tag' => 'settings',
              'name' => '設定',
              'path' => '/pages/user/set/set',
              'icon' => 'image/center_menu/settings.png'
          ],
          'task' => [
              'sys_tag' => 'task',
              'name' => '任務中心',
              'path' => '/pages/task/index',
              'icon' => 'image/center_menu/task.png'
          ],
          'assemble' => [
              'sys_tag' => 'assemble',
              'name' => '我的拼團',
              'path' => '/pages/order/assemble-order',
              'icon' => 'image/center_menu/assemble.png'
          ],
          'evaluate' => [
              'sys_tag' => 'evaluate',
              'name' => '我的評價',
              'path' => '/pages/user/evaluate/list',
              'icon' => 'image/center_menu/evaluate.png'
          ],
      ];
    }
}
