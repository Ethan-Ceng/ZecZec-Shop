<?php

namespace app\shop\controller\plus\agent;


use app\shop\controller\Controller;
use app\shop\model\plus\agent\Setting as SettingModel;
use app\shop\model\product\Product as ProductModel;

/**
 * 分銷設定控制器
 */
class Setting extends Controller
{

    public $pay_type = [
        ['id' => '10', 'name' => '微信支付'],
        ['id' => '20', 'name' => '支付寶'],
        ['id' => '30', 'name' => '銀行卡']
    ];

    public $pay_type1 = [
        10 => '微信支付',
        20 => '支付寶',
        30 => '銀行卡'
    ];

    /**
     * 分銷設定
     */
    public function index()
    {
        $pay_type = $this->pay_type;
        $data = SettingModel::getAll();
        // 購買指定商品成為分銷商：商品列表
        $product_ids = $data['condition']['values']['become__buy_product_ids'];
        $productList = [];
        if(count($product_ids) > 0){
            $productList = (new ProductModel)->getListByIds($product_ids);
        }
        return $this->renderSuccess('', compact('data', 'productList', 'pay_type'));
    }

    /**
     * 基礎資訊設定
     */
    public function basic()
    {
        $param = $this->postData();
        $data['basic'] = $param;
        return $this->edit($data);
    }

    /**
     * 分銷商條件設定
     */
    public function condition()
    {
        $param = $this->postData();
        $data['condition'] = $param;
        return $this->edit($data);
    }

    /**
     * 佣金設定
     */
    public function commission()
    {
        $param = $this->postData();
        $data['commission'] = $param;
        return $this->edit($data);
    }

    /**
     * 結算設定
     */
    public function settlement()
    {
        $param = $this->postData('form');
        $data['settlement'] = $param;
        return $this->edit($data);
    }

    /**
     * 自定義文字設定
     */
    public function words()
    {
        $param = $this->postData();
        $data['words'] = $param;
        return $this->edit($data);
    }

    /**
     * 申請協議設定
     */
    public function license()
    {
        $param = $this->postData();
        $data['license'] = $param;
        return $this->edit($data);
    }

    /**
     * 頁面背景設定
     */
    public function background()
    {
        $param = $this->postData();
        $data['background'] = $param;
        return $this->edit($data);
    }

    /**
     * 修改
     */
    public function edit($data)
    {
        $model = new SettingModel;
        if ($model->edit($data)) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }


    /**
     * 分銷海報
     */
    public function qrcode()
    {
        if (!$this->request->post()) {
            $data = SettingModel::getItem('qrcode');
            return $this->renderSuccess('', ['data' => $data]);
        }
        $model = new SettingModel;
        if ($model->edit(['qrcode' => $this->postData('form')])) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失敗');
    }


}