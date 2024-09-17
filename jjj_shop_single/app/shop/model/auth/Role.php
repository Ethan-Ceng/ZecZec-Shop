<?php

namespace app\shop\model\auth;

use app\common\model\shop\Role as RoleModel;
use app\shop\model\auth\UserRole as UserRoleModel;

/**
 * 角色模型
 */
class Role extends RoleModel
{
    /**
     * 獲取所有角色列表
     */
    public function getTreeData()
    {
        $all = $this->getAll();
        return $this->formatTreeData($all);
    }

    /**
     * 獲取所有角色
     */
    private function getAll()
    {
        $data = $this->order(['sort' => 'asc', 'create_time' => 'asc'])->select();
        return $data ? $data->toArray() : [];
    }

    /**
     * 獲取許可權列表
     */
    private function formatTreeData($all, $parent_id = 0, $deep = 1)
    {
        static $tempTreeArr = [];
        foreach ($all as $key => $val) {
            // 根據角色深度處理名稱字首
            $val['role_name_h1'] = $this->htmlPrefix($deep) . $val['role_name'];
            $tempTreeArr[] = $val;
        }
        return $tempTreeArr;
    }

    /**
     * 角色名稱 html格式字首
     */
    private function htmlPrefix($deep)
    {
        // 根據角色深度處理名稱字首
        $prefix = '';
        if ($deep > 1) {
            for ($i = 1; $i <= $deep - 1; $i++) {
                $prefix .= '   ├ ';
            }
            $prefix .= ' ';
        }
        return $prefix;
    }

    public function add($data)
    {
        $this->startTrans();
        try {
            $arr = [
                'role_name' => $data['role_name'],
                'sort' => $data['sort'],
                'app_id' => self::$app_id
            ];
            $res = self::create($arr);
            $arr1 = [];
            foreach ($data['access_id'] as $val) {
                $arr1[] = [
                    'role_id' => $res['role_id'],
                    'access_id' => $val,
                    'app_id' => self::$app_id
                ];
            }
            $model = new RoleAccess();
            $model->saveAll($arr1);
            // 事務提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 編輯
     * @param $data
     * @return bool
     */
    public function edit($data)
    {
        $this->startTrans();
        try {
            $this->save([
                'role_name' => $data['role_name'],
                'sort' => $data['sort'],
            ]);
            if (!isset($data['access_id'])) {
                $this->commit();
                return true;
            }

            $access_list = [];
            $access_model = new RoleAccess();
            $access_model->where(['role_id' => $data['role_id']])->delete();

            foreach ($data['access_id'] as $val) {
                $access_list[] = [
                    'role_id' => $data['role_id'],
                    'access_id' => $val,
                    'app_id' => self::$app_id
                ];
            }

            $access_model->saveAll($access_list);
            // 事務提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }


    public function del($role_id)
    {
        //如果角色下有使用者，則不能刪除
        if(UserRoleModel::getUserRoleCount($role_id) > 0){
            $this->error = '當前角色下存在使用者，不允許刪除';
            return false;
        }
        RoleAccess::destroy(['role_id', '=', $role_id]);
        return self::destroy(['role_id', '=', $role_id]);
    }

}
