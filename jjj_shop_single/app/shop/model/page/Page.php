<?php

namespace app\shop\model\page;

use app\common\model\page\Page as PageModel;
use app\shop\model\app\App;
use app\shop\model\product\Product as ProductModel;

/**
 * 微信小程式diy頁面模型
 */
class Page extends PageModel
{
    /**
     * 獲取列表
     */
    public function getList($params, $page_type = 20)
    {
        return $this->where(['is_delete' => 0])
            ->where(['page_type' => $page_type])
            ->order(['is_default' => 'desc', 'create_time' => 'desc'])
            ->hidden(['page_data'])
            ->paginate($params);
    }

    /**
     * 獲取所有自定義頁面
     */
    public function getLists()
    {
        return $this->where(['is_delete' => 0])
            ->where(['page_type' => 20])
            ->hidden(['page_data'])
            ->order(['create_time' => 'desc'])
            ->select();
    }

    /**
     * 新增頁面
     */
    public function add($data, $page_type = 20)
    {
        // 刪除app快取
        App::deleteCache();
        return $this->save([
            'page_type' => $page_type,
            'page_name' => $data['page']['params']['name'],
            'page_data' => $data,
            'app_id' => self::$app_id
        ]);
    }

    /**
     * 更新頁面
     */
    public function edit($data)
    {
        // 刪除app快取
        App::deleteCache();
        // 儲存資料
        return $this->save([
                'page_name' => $data['page']['params']['name'],
                'page_data' => $data
            ]) !== false;
    }

    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }

    /**
     * 設定預設頁
     */
    public function setDefault($type)
    {
        $this->save(['is_default' => 1]);
        $this->where('page_id', '<>', $this['page_id'])->where('page_type', '=', $type)->update(['is_default' => 0]);
        return true;
    }

    /**
     * 獲取商品組資料
     */
    public function getPageProduct($jsonData)
    {
        $data['page'] = $jsonData['page'];
        foreach ($jsonData['items'] as $item) {
            if ($item['type'] === 'product' && $item['params']['source'] === 'choice') {
                foreach ($item['data'] as $key => $value) {
                    $productInfo = ProductModel::detail($value['product_id']);
                    $show_sku = ProductModel::getShowSku($productInfo);
                    if ($productInfo) {
                        $item['data'][$key]['product_name'] = $productInfo['product_name'];
                        $item['data'][$key]['product_price'] = $productInfo['product_price'];
                        $item['data'][$key]['line_price'] = $show_sku['line_price'];
                        $item['data'][$key]['image'] = $productInfo['image'] ? $productInfo['image'][0]['file_path'] : $value['image'];
                    }
                }
            }
            $data['items'][] = $item;
        }
        return $data;
    }
}
