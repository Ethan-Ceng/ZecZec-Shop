<?php

namespace app\common\model\app;

use app\common\exception\BaseException;
use think\facade\Cache;
use app\common\model\BaseModel;

/**
 * app模型
 */
class AppOpen extends BaseModel
{
    protected $name = 'app_open';
    protected $pk = 'app_id';


    /**
     * 獲取app資訊
     */
    public static function detail($app_id = null)
    {
        if($app_id == null){
            $app_id = self::$app_id;
        }
        return (new static())->find($app_id);
    }

    /**
     * 從快取中獲取app資訊
     */
    public static function getAppOpenCache($app_id = null)
    {
        if (is_null($app_id)) {
            $self = new static();
            $app_id = $self::$app_id;
        }
        if (!$data = Cache::get('app_open_' . $app_id)) {
            $data = self::detail($app_id);
            if (empty($data)) throw new BaseException(['msg' => '未找到當前app資訊']);
            Cache::tag('cache')->set('app_open_' . $app_id, $data);
        }
        return $data;
    }

}
