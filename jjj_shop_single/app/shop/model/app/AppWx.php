<?php

namespace app\shop\model\app;

use app\common\model\app\AppWx as AppWxModel;
use think\facade\Cache;

/**
 * 微信小程式模型
 */
class AppWx extends AppWxModel
{
    /**
     * 更新小程式設定
     */
    public function edit($data)
    {
        $this->startTrans();
        try {
            // 刪除app快取
            self::deleteCache();
            $where['app_id'] = self::$app_id;

            $count = $this->count($where);
            // 更新小程式設定
            if ($count > 0) {
                self::update($data, $where);
            }
            if ($count == 0) {
                $data['app_id'] = self::$app_id;
                self::create($data);
            }

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    public function count($where)
    {
        return $this->where($where)->count();
    }
    /**
     * 刪除app快取
     */
    public static function deleteCache()
    {
        return Cache::delete('app_wx_' . self::$app_id);
    }

}
