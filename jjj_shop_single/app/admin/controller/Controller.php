<?php

namespace app\admin\controller;

use app\admin\model\admin\User as UserModel;
use app\common\exception\BaseException;
use app\JjjController;
use think\facade\Cache;

/**
 * 商戶後臺控制器基類
 */
class Controller extends JjjController
{

    // 商家登入資訊
    protected $admin;

    // 當前控制器名稱
    protected $controller = '';

    // 當前方法名稱
    protected $action = '';

    // 當前路由uri
    protected $routeUri = '';

    // 當前路由：分組名稱
    protected $group = '';

    // 登入驗證白名單
    protected $allowAllAction = [
        // 登入頁面
        'passport/login',
        /*基礎資訊*/
        'index/base'
    ];

    /**
     * 後臺初始化
     */
    public function initialize()
    {
        // 當前路由資訊
        $this->getRouteinfo();
        // 驗證登入
        $this->checkLogin();
    }

    /**
     * 解析當前路由引數 （分組名稱、控制器名稱、方法名）
     */
    protected function getRouteinfo()
    {
        // 控制器名稱
        $this->controller = toUnderScore(Request()->controller());
        // 方法名稱
        $this->action = Request()->action();
        // 控制器分組 (用於定義所屬模組)
        $groupstr = strstr($this->controller, '.', true);
        $this->group = $groupstr !== false ? $groupstr : $this->controller;
        // 當前uri
        $this->routeUri = $this->controller . '/' . $this->action;
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
            throw new BaseException(['msg' => '缺少必要的引數：token', 'code' => -1]);
        }
        $tokenStatus = Cache::get('admin_token_' . $token);
        if (!$tokenStatus) {
            throw new BaseException(['msg' => 'token失效', 'code' => -1]);
        }
        $data = checkToken($token, 'admin');
        if ($data['code'] != 1) {
            throw new BaseException(['msg' => $data['msg'], 'code' => -1]);
        }
        if ($data['data']['type'] != 'admin') {
            throw new BaseException(['msg' => '使用者資訊錯誤', 'code' => -1]);
        }
        if (!$user = UserModel::getUser($data['data'])) {
            throw new BaseException(['msg' => '沒有找到使用者資訊', 'code' => -1]);
        }
        $this->admin = [
            'admin_user_id' => $user['admin_user_id'],
            'user_name' => $user['user_name'],
        ];
        return true;
    }

}
