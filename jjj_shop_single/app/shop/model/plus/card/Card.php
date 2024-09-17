<?php

namespace app\shop\model\plus\card;

use app\common\model\plus\card\Card as CardModel;
use app\common\model\plus\card\Code as CodeModel;

/**
 * 文章模型
 */
class Card extends CardModel
{
    /**
     * 獲取文章列表
     */
    public function getList($params)
    {
        return $this->with(['image', 'category'])
            ->where('is_delete', '=', 0)
            ->order(['card_sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($params);

    }

    /**
     * 新增記錄
     */
    public function add($data)
    {
        if (empty($data['card_content'])) {
            $this->error = '請輸入卡券內容';
            return false;
        }

        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 更新記錄
     */
    public function edit($data)
    {
        if (empty($data['card_content'])) {
            $this->error = '請輸入卡券內容';
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
     * 獲取文章總數量
     */
    public static function getArticleTotal($where)
    {
        $model = new static;
        return $model->where($where)->where('is_delete', '=', 0)->count();
    }

    /**
     * 生成提貨碼
     */
    public function code($params){
        $this->startTrans();
        try {
            $code_data = [];
            $start_num = $params['start_num'];
            for ($i=0;$i<$params['code_count'];$i++){
                $code_data[] = [
                    'card_id' => $params['card_id'],
                    'code_no' => $params['prefix'].$start_num,
                    'code_pwd' => $this->randStr($params['code_len']),
                    'start_time' => strtotime($params['start_time'].' 00:00:00'),
                    'end_time' => strtotime($params['end_time'].' 23:59:59'),
                    'app_id' => self::$app_id
                ];
                $start_num++;
            }
            (new CodeModel())->saveAll($code_data);
            // 更新code表資料
            $this->where('card_id', '=', $params['card_id'])->inc('wait_num', $params['code_count'])->update();
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    private function randStr($len)
    {
        $chars='0123456789';
        $string='';
        for(;$len>=1;$len--)
        {
            $position=rand()%strlen($chars);
            $string.=substr($chars,$position,1);
        }
        return $string;
    }
}