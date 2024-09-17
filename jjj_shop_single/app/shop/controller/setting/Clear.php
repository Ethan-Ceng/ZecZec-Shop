<?php

namespace app\shop\controller\setting;

use app\shop\controller\Controller;

use think\facade\Cache;

/**
 * 清理快取控制器
 */
class Clear extends Controller
{
    /**
     * 清理快取
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $this->rmCache( $this->postData()['keys']);
        return $this->renderSuccess('操作成功');
    }

    /**
     * 獲取資料
     */
    public function fetchData()
    {
        $cacheList = $this->getItems();
        return $this->renderSuccess('', compact('cacheList'));
    }

    /**
     * 資料快取專案
     */
    private function getItems()
    {
        $app_id = $this->store['app']['app_id'];
        return [
            'category' => [
                'type' => 'cache',
                'key' => 'category_' . $app_id,
                'name' => '全部商品分類'
            ],
            'category_status' => [
                'type' => 'cache',
                'key' => 'category_status' . $app_id,
                'name' => '商品顯示分類'
            ],
            'setting' => [
                'type' => 'cache',
                'key' => 'setting_' . $app_id,
                'name' => '商城設定'
            ],
            'app' => [
                'type' => 'cache',
                'key' => 'app_' . $app_id,
                'name' => '應用設定'
            ],
            'agent' => [
                'type' => 'cache',
                'key' => 'agent_setting_' . $app_id,
                'name' => '分銷設定'
            ],
            'temp' => [
                'type' => 'file',
                'name' => '臨時圖片',
                'dirPath' => [
                    'temp' => root_path('public') . '/temp/' . $app_id . '/',
                    'runtime' => root_path('runtime') . '/image/' . $app_id . '/'
                ]
            ],
        ];
    }


    /**
     * 刪除快取
     */
    private function rmCache($keys)
    {
        $app_id = $this->store['app']['app_id'];
        $cacheList = $this->getItems();
        $keys = array_intersect(array_keys($cacheList), $keys);
        foreach ($keys as $key) {
            $item = $cacheList[$key];
            if ($item['type'] === 'cache') {
                Cache::has($item['key']) && Cache::delete($item['key']);
                //如果是app，則多刪除
                if($item['key'] == 'app'){
                    Cache::has('app_mp_' . $app_id) && Cache::delete('app_mp_' . $app_id);
                    Cache::has('app_wx_' . $app_id) && Cache::delete('app_wx_' . $app_id);
                }
            } elseif ($item['type'] === 'file') {
                $this->deltree($item['dirPath']);
            }
        }
    }

    /**
     * 刪除目錄下所有檔案
     */
    private function deltree($dirPath)
    {
        if (is_array($dirPath)) {
            foreach ($dirPath as $path)
                $this->deleteFolder($path);
        } else {
            return $this->deleteFolder($dirPath);
        }
        return true;
    }

    /**
     * 遞迴刪除指定目錄下所有檔案
     */
    private function deleteFolder($path)
    {
        if (!is_dir($path))
            return false;
        // 掃描一個資料夾內的所有資料夾和檔案
        foreach (scandir($path) as $val) {
            // 排除目錄中的.和..
            if (!in_array($val, ['.', '..'])) {
                // 如果是目錄則遞迴子目錄，繼續操作
                if (is_dir($path . $val)) {
                    // 子目錄中操作刪除資料夾和檔案
                    $this->deleteFolder($path . $val . '/');
                    // 目錄清空後刪除空資料夾
                    rmdir($path . $val . '/');
                } else {
                    // 如果是檔案直接刪除
                    unlink($path . $val);
                }
            }
        }
        return true;
    }

}
