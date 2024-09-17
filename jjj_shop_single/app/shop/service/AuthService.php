<?php

namespace app\shop\service;

use app\common\model\shop\Access;
use think\facade\Cache;
use think\facade\Session;
use app\shop\model\auth\User;
use app\shop\model\auth\UserRole;
use app\shop\model\auth\RoleAccess;

/**
 * 商家後臺許可權業務
 */
class AuthService
{
    // 存放例項
    static public $instance;

    // 商家登入資訊
    private $store;

    // 商家使用者資訊
    private $user;

    // 許可權驗證白名單
    protected $allowAllAction = [
        // 使用者登入
        '/passport/login',
        // 退出登入
        '/passport/logout',
        // 首頁
        '/index/index',
        // 修改當前使用者資訊
        '/passport/editPass',
        // 圖片上傳
        '/file/file/*',
        '/file/upload/image',
        // 資料選擇
        '/data/*',
        // 新增商品規格
        '/product/spec/*',
        // 使用者資訊
        '/auth/user/getUserInfo',
        // 角色列表
        '/auth/user/getRoleList',
        // 統計
        '/statistics/sales/order',
        '/statistics/sales/product',
        '/statistics/user/scale',
        '/statistics/user/new_user',
        '/statistics/user/pay_user'
    ];

    /** @var array $accessUrls 商家使用者許可權url */
    private $accessUrls = [];

    /**
     * 公有化獲取例項方法
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof AuthService)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 私有化構造方法
     */
    public function __construct($store)
    {
        // 商家登入資訊
        $this->store = $store;
        // 當前使用者資訊
        $this->user = User::detail($this->store['user']['shop_user_id']);
    }

    /**
     * 私有化克隆方法
     */
    private function __clone()
    {
    }

    /**
     * 驗證指定url是否有訪問許可權
     */
    public function checkPrivilege($url, $strict = true)
    {
        if (!is_array($url)):
            return $this->checkAccess($url);
        else:
            foreach ($url as $val):
                if ($strict && !$this->checkAccess($val)) {
                    return false;
                }
                if (!$strict && $this->checkAccess($val)) {
                    return true;
                }
            endforeach;
        endif;
        return true;
    }

    /**
     * @param string $url
     */
    private function checkAccess($url)
    {
        // 超級管理員無需驗證
        if ($this->user['is_super']) {
            return true;
        }
        // 驗證當前請求是否在白名單
        if (in_array($url, $this->allowAllAction)) {
            return true;
        }

        // 萬用字元支援
        foreach ($this->allowAllAction as $action) {
            if (strpos($action, '*') !== false
                && preg_match('/^' . str_replace('/', '\/', $action) . '/', $url)
            ) {
                return true;
            }
        }
        // 獲取當前使用者的許可權url列表
        if (!in_array($url, $this->getAccessUrls())) {
            return false;
        }
        return true;
    }

    /**
     * 獲取當前使用者的許可權url列表
     */
    private function getAccessUrls()
    {
        if (empty($this->accessUrls)) {
            // 獲取當前使用者的角色集
            $roleIds = UserRole::getRoleIds($this->user['shop_user_id']);
            // 根據已分配的許可權
            $accessIds = RoleAccess::getAccessIds($roleIds);
            // 獲取當前角色所有許可權連結
            $this->accessUrls = Access::getAccessUrls($accessIds);
        }
        return $this->accessUrls;
    }

    public static function getAccessNameByPath($path, $app_id){
        $arr = Cache::get('path_access_' . $app_id);
        if (!$arr) {
            // 查詢訪問資源
            $list = (new Access())->withoutGlobalScope()->field(['name', 'path'])->select();
            foreach ($list as $access){
                $arr[$access['path']] = $access['name'];
            }
            Cache::tag('cache')->set('path_access_' . $app_id, $arr, 3600);
        }
        return isset($arr[$path])?$arr[$path]:'';
    }
}