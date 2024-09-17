<?php

namespace app\admin\model;

use app\common\model\shop\Access as AccessModel;

/**
 * Class Access
 *  商家使用者許可權模型
 * @package app\admin\model
 */
class Access extends AccessModel
{
    /**
     * 獲取許可權列表
     */
    public function getList()
    {
        $all = static::getAll(-1);
        $res = $this->recursiveMenuArray($all, 0);
        return array_values($this->foo($res));

    }

    /**
     * 新增記錄
     */
    public function add($data)
    {
        // 校驗路徑
        if (!$this->validate($data)) {
            return false;
        }
        $data['access_id'] = time();
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 更新記錄
     */
    public function edit($data)
    {
        if ($data['access_id'] == $data['parent_id']) {
            $this->error = '上級選單不允許設定為當前選單';
            return false;
        }
        // 判斷上級角色是否為當前子級
        if ($data['parent_id'] > 0) {
            // 獲取所有上級id集
            $parentIds = $this->getTopAccessIds($data['parent_id']);
            if (in_array($data['access_id'], $parentIds)) {
                $this->error = '上級選單不允許設定為當前子選單';
                return false;
            }
        }
        // 校驗路徑,不限制大小寫
        if (strtolower($data['path']) !== strtolower($this['path'])) {
            if (!$this->validate($data)) {
                return false;
            }
        }

        $data['redirect_name'] = ($data['is_route'] == 1) ? $data['redirect_name'] : '';
        return $this->save($data);
    }

    /**
     * 驗證
     */
    private function validate($data)
    {
        $count = $this->where(['path' => $data['path']])->count();
        if ($count > 0) {
            $this->error = '路徑已存在，請重新更改';
            return false;
        }
        return true;
    }

    public function getChildCount($where)
    {
        return $this->where($where)->count();
    }


    /**
     * 刪除許可權
     */
    public function remove()
    {
        return $this->delete();
    }

    /**
     * 刪除外掛
     */
    public function removePlus()
    {
        return $this->save([
            'plus_category_id' => 0
        ]);
    }

    /**
     * 獲取所有上級id集
     */
    public function getTopAccessIds($access_id, &$all = null)
    {
        static $ids = [];
        is_null($all) && $all = $this->getAll();

        foreach ($all as $item) {
            if ($item['access_id'] == $access_id && $item['parent_id'] > 0) {
                $ids[] = $item['parent_id'];
                $this->getTopAccessIds($item['parent_id'], $all);
            }
        }

        return $ids;
    }

    /**
     * 遞迴獲取獲取分類
     */
    public function recursiveMenuArray($data, $pid)
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
    public function foo(&$ar)
    {
        if (!is_array($ar)) return [];
        foreach ($ar as $k => &$v) {
            if (is_array($v)) $this->foo($v);
            if ($k == 'children') $v = array_values($v);
        }
        return $ar;
    }

    /**
     * 更改顯示狀態
     */
    public function status($status)
    {
        return $this->save([
            'is_show' => $status
        ]);
    }

    /**
     * 獲取所有外掛
     */
    public static function getAllPlus()
    {
        $model = new static();
        $plus = $model->where('path', '=', '/plus/plus/index')->find();
        return $model->where('parent_id', '=', $plus['access_id'])
            ->where('plus_category_id', '=', 0)
            ->select();
    }

    /**
     * 儲存外掛分類
     * @param $data
     */
    public function addPlus($data)
    {
        $model = new self();
        return $model->where('access_id', '=', $data['access_id'])->save([
            'plus_category_id' => $data['plus_category_id']
        ]);
    }
}