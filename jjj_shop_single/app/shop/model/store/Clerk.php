<?php


namespace app\shop\model\store;

use app\common\model\store\Clerk as ClerkModel;

/**
 * 店員模型
 */
class Clerk extends ClerkModel
{

    const FORM_SCENE_ADD = 'add';
    const FORM_SCENE_EDIT = 'edit';

    /**
     * 獲取列表資料
     */
    public function getList($status, $store_id, $search, $params)
    {
        $model = $this;
        if ($status > -1) {
            $model = $model->where('status', '=', (int)$status);
        }
        if ($store_id > 0) {
            $model = $model->where('store_id', '=', (int)$store_id);
        }
        if (!empty($search)) {
            $model = $model->where('real_name|mobile', 'like', '%' . $search . '%');
        }
        // 查詢列表資料
        return $model->with(['store', 'user'])
            ->where('is_delete', '=', '0')
            ->order(['create_time' => 'desc'])
            ->paginate($params);
    }

    /**
     * 查詢所有列表資料
     */
    public function getAll()
    {
        // 查詢列表資料
        return $this->where('is_delete', '=', '0')->with(['store'])->select();

    }

    /**
     * 新增記錄
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        return self::create($data);
    }

    /**
     * 編輯記錄
     */
    public function edit($data)
    {
        // 表單驗證
        if (!$this->validateForm($data, self::FORM_SCENE_EDIT)) {
            return false;
        }

        return $this->save($data);
    }

    /**
     * 軟刪除
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }

    /**
     * 表單驗證
     */
    private function validateForm($data, $scene = self::FORM_SCENE_ADD)
    {
        if ($scene === self::FORM_SCENE_ADD) {
            if (!isset($data['user_id']) || empty($data['user_id'])) {
                $this->error = '請選擇使用者';
                return false;
            }
            if (self::detail(['user_id' => $data['user_id'], 'is_delete' => 0])) {
                $this->error = '該使用者已經是店員，無需重複新增';
                return false;
            }
        }
        return true;
    }

    //獲取核銷員
    public function getClerk($store_id)
    {
        return $this->where('store_id', '=', $store_id)
            ->where('is_delete', '=', 0)
            ->where('status', '=', 1)
            ->select();
    }

}