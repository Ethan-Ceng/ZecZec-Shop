<?php

namespace app\shop\model\page;
use app\common\model\page\CenterMenu as CenterMenuModel;
use think\facade\Cache;

/**
 * 模型
 */
class CenterMenu extends CenterMenuModel
{

    /**
     * 獲取列表
     */
    public function getList($params)
    {
        $count = $this->count();
        // 如果沒有資料、插入預設選單
        if($count == 0){
            // 系統選單
            $sys_menus = CenterMenuModel::getSysMenu();
            $save_data = [];
            foreach ($sys_menus as $menu) {
                $save_data[] = array_merge($sys_menus[$menu['sys_tag']], [
                    'sort' => 100,
                    'app_id' => self::$app_id
                ]);
            }
            $this->saveAll($save_data);
        }
        $list = $this->order(['sort' => 'asc'])
            ->paginate($params);
        foreach ($list as $menus){
            if(strpos($menus['icon'], 'http') !== 0){
                $menus['icon'] = self::$base_url . $menus['icon'];
            }
        }
        return $list;
    }
    /**
     * 新增新記錄
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        $this->deleteCache();
        return $this->save($data);
    }

    /**
     * 編輯記錄
     */
    public function edit($data)
    {
        $this->deleteCache();
        return $this->save($data);
    }

    /**
     * 刪除記錄
     */
    public function remove()
    {
        $this->deleteCache();
        return $this->delete();
    }

    /**
     * 刪除快取
     */
    private function deleteCache()
    {
        return Cache::delete('center_menu_' . static::$app_id);
    }
}