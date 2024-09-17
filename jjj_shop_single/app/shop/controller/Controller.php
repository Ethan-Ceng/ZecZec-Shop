<?php

namespace app\shop\controller;

use app\common\exception\BaseException;
use app\common\model\settings\Setting;
use app\common\model\shop\OptLog as OptLogModel;
use app\JjjController;
use app\shop\model\shop\User as UserModel;
use app\shop\service\AuthService;
use think\facade\Cache;
use think\facade\Env;

/**
 * 商戶後臺控制器基類
 */
class Controller extends JjjController
{
    /** @var array $store 商家登入資訊 */
    protected $store;

    /** @var string $route 當前控制器名稱 */
    protected $controller = '';

    /** @var string $route 當前方法名稱 */
    protected $action = '';

    /** @var string $route 當前路由uri */
    protected $routeUri = '';

    /** @var string $route 當前路由：分組名稱 */
    protected $group = '';

    /** @var string $route 當前路由：分組名稱 */
    protected $menu = '';
    /** @var array $allowAllAction 登入驗證白名單 */
    protected $allowAllAction = [
        // 登入頁面
        '/passport/login',
        /*登入資訊*/
        '/index/base'
    ];

    /**
     * 後臺初始化
     */
    public function initialize()
    {
        // 當前路由資訊
        $this->getRouteinfo();
        //  驗證登入狀態
        $this->checkLogin();
        // 寫入操作日誌
        $this->saveOptLog();
        // 驗證當前頁面許可權
        $this->checkPrivilege();
    }

    /**
     * 驗證當前頁面許可權
     */
    private function checkPrivilege()
    {
        if ($this->store == null) {
            return false;
        }
        $AuthService = new AuthService($this->store);
        if (!$AuthService->checkPrivilege($this->routeUri)) {
            throw new BaseException(['msg' => '很抱歉，沒有訪問許可權']);
        }
        return true;
    }

    /**
     * 解析當前路由引數 （分組名稱、控制器名稱、方法名）
     */
    protected function getRouteinfo()
    {
        // 控制器名稱
        $this->controller = strtolower($this->request->controller());
        $this->controller = str_replace(".", "/", $this->controller);
        // 方法名稱
        $this->action = Request()->action();
        // 控制器分組 (用於定義所屬模組)
        $groupstr = strstr($this->controller, '.', true);
        $this->group = $groupstr !== false ? $groupstr : $this->controller;
        // 當前uri
        $this->routeUri = '/' . $this->controller . '/' . $this->action;
    }

    /**
     * 驗證登入狀態
     */
    private function checkLogin()
    {
        // 驗證當前請求是否在白名單
        if (in_array($this->routeUri, $this->allowAllAction)) {
            return true;
        }
        $token = Request()->header('token');
        if (!$token) {
            $token = Request()->param('token');
        }
        if (!$token) {
            throw new BaseException(['msg' => '缺少必要的引數：token', 'code' => -1]);
        }
        $tokenStatus = Cache::get('shop_token_' . $token);
        if (!$tokenStatus) {
            throw new BaseException(['msg' => 'token失效', 'code' => -1]);
        }
        $data = checkToken($token, 'shop');
        if ($data['code'] != 1) {
            throw new BaseException(['msg' => $data['msg'], 'code' => -1]);
        }
        if ($data['data']['type'] != 'shop') {
            throw new BaseException(['msg' => '使用者資訊錯誤', 'code' => -1]);
        }
        if (!$user = UserModel::getUser($data['data'])) {
            throw new BaseException(['msg' => '沒有找到使用者資訊', 'code' => -1]);
        }
        // 儲存登入狀態
        $this->store = [
            'user' => [
                'shop_user_id' => $user['shop_user_id'],
                'user_name' => $user['user_name'],
            ],
            'app' => $user['app']->toArray(),
        ];
        return true;
    }

    /**
     * 操作日誌
     */
    private function saveOptLog()
    {
        if (Env::get('env') == 'uat') {
            return;
        }
        if ($this->store == null) {
            return;
        }
        $shop_user_id = $this->store['user']['shop_user_id'];
        if (!$shop_user_id) {
            return;
        }
        // 如果不記錄查詢日誌
        $config = Setting::getItem('store');
        if (!$config || !$config['is_get_log']) {
            return;
        }
        $model = new OptLogModel();
        $model->save([
            'shop_user_id' => $shop_user_id,
            'ip' => \request()->ip(),
            'request_type' => $this->request->isGet() ? 'Get' : 'Post',
            'url' => $this->routeUri,
            'content' => json_encode($this->request->param(), JSON_UNESCAPED_UNICODE),
            'browser' => get_client_browser(),
            'agent' => $_SERVER['HTTP_USER_AGENT'],
            'title' => (new AuthService($this->store))::getAccessNameByPath($this->routeUri, $this->store['app']['app_id']),
            'app_id' => $this->store['app']['app_id']
        ]);
    }

}
