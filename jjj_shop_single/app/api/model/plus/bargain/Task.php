<?php

namespace app\api\model\plus\bargain;

use app\api\model\product\Product as ProductModel;
use app\api\model\plus\bargain\Product as BargainProductModel;
use app\api\model\plus\bargain\ProductSku as BargainSkuModel;
use app\api\model\plus\bargain\Active as ActiveModel;
use app\api\model\settings\Setting as SettingModel;
use app\common\exception\BaseException;
use app\common\model\plus\bargain\Task as TaskModel;
use app\api\model\plus\bargain\TaskHelp as TaskHelpModel;
use app\api\service\bargain\Amount as AmountService;
use app\common\library\helper;

/**
 * 砍價任務模型
 */
class Task extends TaskModel
{
    /**
     * 隱藏的欄位
     * @var array
     */
    protected $hidden = [
        'peoples',
        'section',
        'is_delete',
        'app_id',
        'update_time',
    ];

    /**
     * 我的砍價列表
     */
    public function getList($user_id, $params)
    {
        // 砍價活動列表
        return $this->where('user_id', '=', $user_id)->with(['file'])
            ->where('status', '=', $params['status'])
            ->where('is_delete', '=', 0)
            ->order(['create_time' => 'desc'])
            ->paginate($params);
    }

    /**
     * 獲取砍價任務詳情
     */
    public function getTaskDetail($bargain_task_id, $user = false)
    {
        // 砍價任務詳情
        $task = static::detail($bargain_task_id, ['user']);
        if (empty($task)) {
            throw new BaseException(['msg' => '砍價任務不存在']);
        }
        // 砍價活動詳情
        $active = ActiveModel::detail($task['bargain_activity_id']);
        // 獲取商品詳情
        $product = BargainProductModel::detail($task['bargain_product_id'], ['product.image.file']);
        // 好友助力榜
        $help_list = TaskHelpModel::getListByTaskId($bargain_task_id);
        // 當前是否為發起人
        $is_creater = $this->isCreater($task, $user);
        // 當前是否已砍
        $is_cut = $this->isCut($help_list, $user);
        // 砍價規則
        $setting = SettingModel::getBargain();
        return compact('task', 'is_creater', 'is_cut', 'active', 'product', 'help_list', 'setting');
    }

    /**
     * 獲取砍價任務的商品列表（用於訂單結算）
     */
    public function getTaskGoods($taskId)
    {
        // 砍價任務詳情
        $task = static::detail($taskId);
        if (empty($task) || $task['is_delete'] || $task['status'] == false) {
            $this->error = '砍價任務不存在或已結束';
            return false;
        }
        if ($task['is_buy'] == true) {
            $this->error = '該砍價商品已購買';
            return false;
        }
        // 砍價商品詳情
        $goods = ProductModel::detail($task['goods_id']);
        // 商品sku資訊
        $goods['goods_sku'] = ProductModel::getGoodsSku($goods, $task['spec_sku_id']);
        // 商品列表
        $goodsList = [$goods->hidden(['category', 'content', 'image', 'sku'])];
        foreach ($goodsList as &$item) {
            // 商品單價
            $item['goods_price'] = $task['actual_price'];
            // 商品購買數量
            $item['total_num'] = 1;
            $item['spec_sku_id'] = $item['goods_sku']['spec_sku_id'];
            // 商品購買總金額
            $item['total_price'] = $task['actual_price'];
        }
        return $goodsList;
    }


    /**
     * 新增砍價任務
     */
    public function add($user_id, $bargain_activity_id, $bargain_product_id, $bargain_product_sku_id, $product_sku_id)
    {
        // 獲取活動詳情
        $active = ActiveModel::detail($bargain_activity_id);
        // 驗證能否建立砍價任務
        if (!$this->onVerify($active, $user_id)) {
            return false;
        }
        // 獲取商品詳情
        $product = BargainProductModel::detail($bargain_product_id, ['product.image']);
        // 商品sku資訊
        $product_sku = BargainSkuModel::detail($bargain_product_sku_id, ['productSku']);
        // 建立砍價任務
        return $this->addTask($user_id, $active, $product, $product_sku);
    }

    /**
     * 建立砍價任務記錄
     */
    private function addTask($user_id, $active, $product, $product_sku)
    {
        // 分配砍價金額區間
        $section = $this->calcBargainSection(
            $product_sku['product_price'],
            $product_sku['bargain_price'],
            $product_sku['bargain_num']
        );
        // 新增記錄
        return $this->save([
            'bargain_activity_id' => $active['bargain_activity_id'],
            'user_id' => $user_id,
            'bargain_product_id' => $product['bargain_product_id'],
            'bargain_product_sku_id' => $product_sku['bargain_product_sku_id'],
            'product_price' => $product_sku['product_price'],
            'bargain_price' => $product_sku['bargain_price'],
            'actual_price' => $product_sku['product_price'],
            'peoples' => $product_sku['bargain_num'],
            'image_id' => $product['product']['image'][0]['image_id'],
            'product_name' => $product['product']['product_name'],
            'product_attr' => $product_sku['product_attr'],
            'product_sku_id' => $product_sku['product_sku_id'],
            'cut_people' => 0,
            'section' => $section,
            'cut_money' => 0.00,
            'end_time' => time() + ($active['together_time'] * 3600),
            'app_id' => self::$app_id,
        ]);
    }
    /**
     * 幫砍一刀
     */
    public function helpCut($user)
    {
        // 好友助力榜
        $helpList = TaskHelpModel::getListByTaskId($this['bargain_task_id']);
        // 當前是否已砍
        if ($this->isCut($helpList, $user)) {
            $this->error = '您已參與砍價，請不要重複操作';
            return false;
        }
        // 幫砍一刀事件
        return $this->transaction(function () use ($user) {
            return $this->onCutEvent($user['user_id'], $this->isCreater($this, $user));
        });
    }

    /**
     * 砍一刀的金額
     */
    public function getCutMoney()
    {
        return $this['section'][$this['cut_people']];
    }

    /**
     * 幫砍一刀事件
     */
    private function onCutEvent($userId, $isCreater = false)
    {
        // 砍價金額
        $cutMoney = $this->getCutMoney();
        // 砍價助力記錄
        $model = new TaskHelpModel;
        $model->add($this, $userId, $cutMoney, $isCreater);
        // 實際購買金額
        $actualPrice = helper::bcsub($this['actual_price'], $cutMoney);
        // 更新砍價任務資訊
        $this->save([
            'cut_people' => ['inc', 1],
            'cut_money' => ['inc', $cutMoney],
            'actual_price' => $actualPrice,
            'is_floor' => helper::bcequal($actualPrice, $this['bargain_price']),
            'status' => helper::bcequal($actualPrice, $this['bargain_price']) == 0?0:1,
        ]);
        return true;
    }



    /**
     * 砍價任務標記為已購買
     */
    public function setIsBuy()
    {
        return $this->save(['is_buy' => 1]);
    }

    /**
     * 分配砍價金額區間
     */
    private function calcBargainSection($product_price, $bargain_price, $bargain_num)
    {
        $AmountService = new AmountService(helper::bcsub($product_price, $bargain_price), $bargain_num);
        return $AmountService->handle()['items'];
    }

    /**
     * 當前是否為發起人
     */
    private function isCreater($task, $user)
    {
        if ($user === false) return false;
        return $user['user_id'] == $task['user_id'];
    }

    /**
     * 當前是否已砍
     */
    private function isCut($helpList, $user)
    {
        if ($user === false) return false;
        foreach ($helpList as $item) {
            if ($item['user_id'] == $user['user_id']) return true;
        }
        return false;
    }

    /**
     * 驗證能否建立砍價任務
     */
    private function onVerify($active, $userId)
    {
        // 活動是否開始
        if ($active['start_time'] > time()) {
            $this->error = '很抱歉，當前砍價活動未開始';
            return false;
        }
        // 活動是否到期合法
        if ($active['end_time'] < time()) {
            $this->error = '很抱歉，當前砍價活動已結束';
            return false;
        }
        return true;
    }

}