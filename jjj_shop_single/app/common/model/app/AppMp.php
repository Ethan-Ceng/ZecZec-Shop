<?php

namespace app\common\model\app;

use app\common\exception\BaseException;
use think\facade\Cache;
use app\common\model\BaseModel;

/**
 * 微信公眾號模型
 */
class AppMp extends BaseModel
{
    protected $name = 'app_mp';
    protected $pk = 'app_id';


    /**
     * 獲取公眾號資訊
     */
    public static function detail($app_id = null)
    {
        if($app_id == null){
            $app_id = self::$app_id;
        }
        return (new static())->find($app_id);
    }

    /**
     * 從快取中獲取公眾號資訊
     */
    public static function getAppMpCache($app_id = null)
    {
        if (is_null($app_id)) {
            $self = new static();
            $app_id = $self::$app_id;
        }
        if (!$data = Cache::get('app_mp_' . $app_id)) {
            $data = self::detail($app_id);
            if (empty($data)) throw new BaseException(['msg' => '未找到當前公眾號資訊']);
            Cache::tag('cache')->set('app_mp_' . $app_id, $data);
        }
        return $data;
    }

}
