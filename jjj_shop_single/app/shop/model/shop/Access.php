<?php

namespace app\shop\model\shop;

use app\common\model\shop\Access as AccessModel;
use app\shop\model\auth\RoleAccess;
use app\shop\model\auth\UserRole;
/**
 * Class Access
 *  商家使用者許可權模型
 */
class Access extends AccessModel
{
    /**
     * 獲取許可權列表
     */
    public function getList()
    {
        $all = static::getAll(1);
        $res = $this->recursiveMenuArray($all, 0);
        return array_values($this->foo($res));
    }

    public function getListByUser($shop_user_id)
    {
        // 獲取當前使用者的角色集
        $roleIds = UserRole::getRoleIds($shop_user_id);
        // 根據已分配的許可權
        $accessIds = RoleAccess::getAccessIds($roleIds);
        // 獲取當前角色所有許可權連結
        $menus_list = AccessModel::getAccessList($accessIds);
        // 格式化
       return $this->formatTreeData($menus_list, 0);
    }

    // 迴圈獲取分類
    private function formatTreeData($all, $parent_id = 0)
    {
        $tree = array();
        foreach($all as $k => $v)
        {
            if($v['parent_id'] == $parent_id)
            {
                //父親找到兒子
                $v['children'] = $this->formatTreeData($all, $v['access_id']);
                $tree[] = $v;
            }
        }
        return $tree;
    }
    /**
     * 遞迴獲取獲取分類
     */
    private function recursiveMenuArray($data, $pid)
    {
        $re_data = [];
        foreach ($data as $key => $value) {
            if ($value['parent_id'] == $pid) {
                $re_data[$value['access_id']] = $value;
                $re_data[$value['access_id']]['children'] = $this->recursiveMenuArray($data, $value['access_id']);
            } else {
                continue;
            }
        }
        return $re_data;
    }

    /**
     * 格式化遞迴陣列下標
     */
    private function foo(&$ar)
    {
        if (!is_array($ar)) return [];
        foreach ($ar as $k => &$v) {
            if (is_array($v)) $this->foo($v);
            if ($k == 'children') $v = array_values($v);
        }
        return $ar;
    }
}