<?php

namespace app\shop\controller\auth;

use app\shop\model\shop\Access as AccessModel;
use app\common\model\settings\Setting as SettingModel;
use app\shop\controller\Controller;
use app\shop\model\auth\User as UserModel;
use app\shop\model\auth\Role;
use app\shop\model\auth\User as AuthUserModel;

/**
 * 管理員
 */
class User extends Controller
{
    /**
     * 首頁列表
     * @return \think\response\Json
     */
    public function index()
    {
        $model = new UserModel();
        $list = $model->getList($this->postData());
        $roleModel = new Role();
        // 角色列表
        $roleList = $roleModel->getTreeData();
        return $this->renderSuccess('', compact('list', 'roleList'));
    }

    /**
     * 新增資訊
     * @return \think\response\Json
     */
    public function addInfo()
    {
        $model = new Role();
        // 角色列表
        $roleList = $model->getTreeData();
        return $this->renderSuccess('', compact('roleList'));
    }

    /**
     * 新增
     * @return \think\response\Json
     */
    public function add()
    {
        $data = $this->postData();
        $model = new UserModel();
        $num = $model->getUserName(['user_name' => $data['user_name']]);
        if ($num > 0) {
            return $this->renderError('使用者名稱已存在');
        }
        if (!isset($data['role_id'])) {
            return $this->renderError('請選擇所屬角色');
        }
        if ($data['confirm_password'] != $data['password']) {
            return $this->renderError('確認密碼和登入密碼不一致');
        }
        $model = new UserModel();
        if ($model->add($data)) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError('新增失敗');
    }

    /**
     * 修改資訊
     * @param $shop_user_id
     * @return \think\response\Json
     */
    public function editInfo($shop_user_id)
    {
        $info = UserModel::detail(['shop_user_id' => $shop_user_id], ['UserRole']);

        $role_arr = array_column($info->toArray()['UserRole'], 'role_id');

        $model = new Role();
        // 角色列表
        $roleList = $model->getTreeData();
        return $this->renderSuccess('', compact('info', 'roleList', 'role_arr'));
    }

    /**
     * 編輯
     * @param $shop_user_id
     * @return \think\response\Json
     */
    public function edit($shop_user_id)
    {
        $data = $this->postData();
        if ($this->request->isGet()) {
            return $this->editInfo($shop_user_id);
        }

        $model = new UserModel();
        $num = $model->getUserName(['user_name' => $data['user_name']], $data['shop_user_id']);
        if ($num > 0) {
            return $this->renderError('使用者名稱已存在');
        }
        if (!isset($data['access_id'])) {
            return $this->renderError('請選擇所屬角色');
        }
        if (isset($data['password']) && !empty($data['password'])) {
            if (!isset($data['confirm_password'])) {
                return $this->renderError('請輸入確認密碼');
            } else {
                if ($data['confirm_password'] != $data['password']) {
                    return $this->renderError('確認密碼和登入密碼不一致');
                }
            }
        }
        if (empty($data['password'])) {
            if (isset($data['confirm_password']) && !empty($data['confirm_password'])) {
                return $this->renderError('請輸入登入密碼');
            }
        }

        // 更新記錄
        if ($model->edit($data)) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');


    }

    /**
     * 刪除
     */
    public function delete($shop_user_id)
    {
        $model = new UserModel();
        if ($model->del(['shop_user_id' => $shop_user_id])) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError('刪除失敗');
    }

    /**
     * 獲取角色選單資訊
     */
    public function getRoleList()
    {
        $user = $this->store['user'];
        $user_info = (new AuthUserModel())->find($user['shop_user_id']);

        if ($user_info['is_super'] == 1) {
            $model = new AccessModel();
            $menus = $model->getList();
        } else {
            $model = new AccessModel();
            $menus = $model->getListByUser($user['shop_user_id']);

            foreach ($menus as $key => $val) {
                if ($val['redirect_name'] != $val['children'][0]['path']) {
                    $menus[$key]['redirect_name'] = $menus[$key]['children'][0]['path'];
                }
            }
        }
        return $this->renderSuccess('', compact('menus'));
    }

    /**
     * 獲取使用者資訊
     */
    public function getUserInfo()
    {
        $store = $this->store;
        $user = [];
        if (!empty($store)) {
            $user = $store['user'];
        }
        // 商城名稱
        $shop_name = SettingModel::getItem('store')['name'];
        //當前系統版本
        $version = get_version();
        return $this->renderSuccess('', compact('user', 'shop_name', 'version'));
    }
}
