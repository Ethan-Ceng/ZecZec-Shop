<?php

namespace app\api\controller\user;

use app\api\model\user\UserAddress;
use app\api\controller\Controller;
use app\common\model\settings\Region as RegionModel;

/**
 * 收貨地址控制器
 */
class Address extends Controller
{
    /**
     * 收貨地址列表
     */
    public function lists()
    {
        $user = $this->getUser();
        $model = new UserAddress;
        $list = $model->getList($user['user_id']);
        return $this->renderSuccess('', [
            'list' => $list,
            'default_id' => $user['address_id'],
        ]);
    }

    /**
     * 新增收貨地址
     */
    public function add()
    {
        $data = $this->request->post();
        if ($data['phone'] == '') {
            return $this->renderError('手機號不正確');
        }
        if ($data['name'] == '') {
            return $this->renderError('收貨人不能為空');
        }
        if ($data['detail'] == '') {
            return $this->renderError('收貨地址不能為空');
        }
        $model = new UserAddress;
        if ($model->add($this->getUser(), $data)) {
            return $this->renderSuccess('新增成功');
        }
        return $this->renderError('新增失敗');
    }

    /**
     * 收貨地址詳情
     */
    public function detail($address_id)
    {
        $user = $this->getUser();
        $detail = UserAddress::detail($user['user_id'], $address_id);
        $region = array_values($detail['region']);
        $regionData = RegionModel::getRegionForApi();
        return $this->renderSuccess('', compact('detail', 'region', 'regionData'));
    }

    /**
     * 編輯收貨地址
     */
    public function edit($address_id)
    {
        $user = $this->getUser();
        $model = UserAddress::detail($user['user_id'], $address_id);
        if ($model->edit($this->getUser(), $this->request->post())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError('更新失敗');
    }

    /**
     * 設為預設地址
     */
    public function setDefault($address_id)
    {
        $user = $this->getUser();
        $model = UserAddress::detail($user['user_id'], $address_id);
        if ($model->setDefault($user)) {
            return $this->renderSuccess('設定成功');
        }
        return $this->renderError('設定失敗');
    }

    /**
     * 刪除收貨地址
     */
    public function delete($address_id)
    {
        $user = $this->getUser();
        $model = UserAddress::detail($user['user_id'], $address_id);
        if ($model->remove($user)) {
            return $this->renderSuccess('刪除成功');
        }
        return $this->renderError('刪除失敗');
    }

}