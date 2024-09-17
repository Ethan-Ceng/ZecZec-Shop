<?php

namespace app\common\model\app;

use app\common\exception\BaseException;
use think\facade\Cache;
use app\common\model\BaseModel;

/**
 * 微信小程式模型
 */
class AppWx extends BaseModel
{
    protected $name = 'app_wx';
    protected $pk = 'app_id';

    /**
     * 獲取小程式資訊
     */
    public static function detail($app_id = null)
    {
        $self = new static();
        empty($app_id) && $app_id = $self::$app_id;
        return $self->find($app_id);
    }

    /**
     * 從快取中獲取小程式資訊
     * @param null $app_id
     */
    public static function getAppWxCache($app_id = null)
    {
        if (is_null($app_id)) {
            $self = new static();
            $app_id = $self::$app_id;
        }
        if (!$data = Cache::get('app_wx_' . $app_id)) {
            $data = self::detail($app_id);
            if (empty($data)) throw new BaseException(['msg' => '未找到當前小程式資訊']);
            Cache::set('app_wx_' . $app_id, $data);
        }
        return $data;
    }

}
