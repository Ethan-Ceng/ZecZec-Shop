<?php

namespace app\shop\model\auth;

use app\common\model\shop\User as UserModel;
use think\facade\Env;


/**
 * 角色模型
 */
class User extends UserModel
{

    public function getList($limit = 20)
    {
        return $this->with(['userRole.role'])->where('is_delete', '=', 0)->
        order(['create_time' => 'desc'])
            ->paginate($limit);
    }

    /**
     * 獲取所有上級id集
     */
    public function getTopRoleIds($role_id, &$all = null)
    {
        static $ids = [];
        is_null($all) && $all = $this->getAll();
        foreach ($all as $item) {
            if ($item['role_id'] == $role_id && $item['parent_id'] > 0) {
                $ids[] = $item['parent_id'];
                $this->getTopRoleIds($item['parent_id'], $all);
            }
        }
        return $ids;
    }

    /**
     * 獲取所有角色
     */
    private function getAll()
    {
        $data = $this->order(['sort' => 'asc', 'create_time' => 'asc'])->select();
        return $data ? $data->toArray() : [];
    }

    public function add($data)
    {
        $this->startTrans();
        try {
            $arr = [
                'user_name' => trim($data['user_name']),
                'password' => salt_hash($data['password']),
                'real_name' => trim($data['real_name']),
                'role_id' => $data['role_id'],
                'app_id' => self::$app_id
            ];

            $res = self::create($arr);
            $add_arr = [];
            $model = new UserRole();
            foreach ($data['role_id'] as $val) {
                $add_arr[] = [
                    'shop_user_id' => $res['shop_user_id'],
                    'role_id' => $val,
                    'app_id' => self::$app_id,
                ];
            }
            $model->saveAll($add_arr);
            // 事務提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }

    }

    public function getUserName($where, $shop_user_id = 0)
    {
        if ($shop_user_id > 0) {
            return $this->where($where)->where('shop_user_id', '<>', $shop_user_id)->count();
        }
        return $this->where($where)->count();
    }


    public function edit($data)
    {
        $this->startTrans();
        try {
            $arr = [
                'user_name' => $data['user_name'],
                'password' => salt_hash($data['password']),
                'real_name' => $data['real_name'],
            ];
            if (empty($data['password'])) {
                unset($arr['password']);
            }
            // uat
            if (Env::get('env') == 'uat' && request()->ip() != Env::get('uat_ip')){
                unset($arr['password']);
            }

            $where['shop_user_id'] = $data['shop_user_id'];
            self::update($arr, $where);

            $model = new UserRole();
            UserRole::destroy($where);
            $add_arr = [];
            foreach ($data['access_id'] as $val) {
                $add_arr[] = [
                    'shop_user_id' => $data['shop_user_id'],
                    'role_id' => $val,
                    'app_id' => self::$app_id
                ];
            }
            $model->saveAll($add_arr);
            // 事務提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    public function getChild($where)
    {
        return $this->where($where)->count();
    }

    public function del($where)
    {
        self::update(['is_delete' => 1], $where);
        return UserRole::destroy($where);
    }
}
