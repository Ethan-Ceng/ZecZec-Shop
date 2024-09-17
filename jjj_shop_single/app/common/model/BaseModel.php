<?php

namespace app\common\model;

use think\Model;
use think\facade\Env;
use app\common\exception\BaseException;
/**
 * 模型基類
 */
class BaseModel extends Model
{
    public static $app_id;

    public static $base_url;

    protected $alias = '';

    protected $error = '';

    // 定義全域性的查詢範圍
    protected $globalScope = ['app_id'];

    /**
     * 模型基類初始化
     */
    public static function init()
    {
        parent::init();
        // 獲取當前域名
        self::$base_url = base_url();
        // 後期靜態繫結app_id
        self::bindAppId();
    }


    /**
     * 後期靜態繫結類名稱
     */
    private static function bindAppId()
    {
        if ($app = app('http')->getName()) {
            if($app != 'admin' && $app != 'job'){
                $callfunc = 'set' . ucfirst($app) . 'AppId';
                self::$callfunc();
            }
        }
    }

    /**
     * 設定app_id (shop模組)
     */
    protected static function setShopAppId()
    {
        self::$app_id = request()->header('appId');
        if (!self::$app_id) {
            self::$app_id = Request()->param('AppID');
        }
    }

    /**
     * 設定app_id (api模組)
     */
    protected static function setApiAppId()
    {
        self::$app_id = request()->param('app_id');
    }

    /**
     * 定義全域性的查詢範圍
     */
    public function scopeApp_id($query)
    {
        if (self::$app_id > 0) {
            $query->where($query->getTable() . '.app_id', self::$app_id);
        }
    }

    /**
     * 設定預設的檢索資料
     */
    protected function setQueryDefaultValue($query, $default = [])
    {
        $data = array_merge($default, $query);
        foreach ($query as $key => $val) {
            if (empty($val) && isset($default[$key])) {
                $data[$key] = $default[$key];
            }
        }
        return $data;
    }

    /**
     * 設定基礎查詢條件（用於簡化基礎alias和field）
     */
    public function setBaseQuery($alias = '', $join = [])
    {
        // 設定別名
        $aliasValue = $alias ?: $this->alias;
        $model = $this->alias($aliasValue)->field("{$aliasValue}.*");
        // join條件
        if (!empty($join)) : foreach ($join as $item):
            $model->join($item[0], "{$item[0]}.{$item[1]}={$aliasValue}."
                . (isset($item[2]) ? $item[2] : $item[1]));
        endforeach; endif;
        return $model;
    }

    /**
     * 批次更新資料(支援帶where條件)
     */
    public function updateAll($data)
    {
        return $this->transaction(function () use ($data) {
            $result = [];
            foreach ($data as $key => $item) {
                $result[$key] = self::update($item['data'], $item['where']);
            }
            return $this->toCollection($result);
        });
    }

    public static function onBeforeUpdate(Model $model){
        self::checkEnv();
        if($model->createTime && $model[$model->createTime]){
            unset($model[$model->createTime]);
        }
        if ($model->updateTime && $model[$model->updateTime]) {
            $model[$model->updateTime] = $model->autoWriteTimestamp();
        }
    }

    public static function onBeforeInsert(Model $model){
        self::checkEnv();
    }

    public static function onBeforeDelete(Model $model){
        self::checkEnv();
    }

    private static function checkEnv(){
        if (Env::get('env') == 'uat'
            && self::getCalledModule() == 'admin'
            && request()->ip() != Env::get('uat_ip')) {
            throw new BaseException(['msg' => '演示環境，資料不允許修改']);
        }
        if (Env::get('env') == 'uat'
            && self::getCalledModule() == 'shop'
            && self::$app_id == 10001
            && request()->ip() != Env::get('uat_ip')) {
            throw new BaseException(['msg' => '演示環境，資料不允許修改，如需測試修改請用test/123456登入或聯絡管理員']);
        }
    }

    /**
     * 獲取當前呼叫的模組名稱
     */
    protected static function getCalledModule()
    {
        if (preg_match('/app\\\(\w+)/', get_called_class(), $class)) {
            return $class[1];
        }
        return false;
    }
    /**
     * 返回模型的錯誤資訊
     */
    public function getError()
    {
        return $this->error;
    }
}
