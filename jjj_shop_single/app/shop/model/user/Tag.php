<?php

namespace app\shop\model\user;

use app\common\model\user\Tag as TagModel;
use app\common\model\user\UserTag as UserTagModel;
/**
 * 使用者會員等級模型
 */
class Tag extends TagModel
{
    /**
     * 獲取列表記錄
     */
    public function getList($data)
    {
        $list = $this->order([ 'create_time' => 'asc'])
            ->paginate($data);
        foreach ($list as $item){
            $item['user_count'] = UserTagModel::getCountByTag($item['tag_id']);
        }
        return $list;
    }

    /**
     * 獲取列表記錄
     */
    public function getLists()
    {
        return $this->field('tag_id,tag_name')
            ->order(['create_time' => 'asc'])
            ->select();
    }


    /**
     * 新增記錄
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 編輯記錄
     */
    public function edit($data)
    {
        return $this->save($data);
    }


    /**
     * 軟刪除
     */
    public function deleteTag()
    {
        // 刪除關聯
        (new UserTagModel())->where('tag_id', '=', $this['tag_id'])->delete();
        return $this->delete();
    }

}