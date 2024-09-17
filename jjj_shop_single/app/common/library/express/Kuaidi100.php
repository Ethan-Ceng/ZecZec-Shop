<?php

namespace app\common\library\express;

use think\facade\Cache;


/**
 * 快遞100API模組
 */
class Kuaidi100
{
    // 配置
    private $config;

    // 錯誤資訊
    private $error;

    /**
     * 構造方法
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 執行查詢
     */
    public function query($express_code, $express_no, $phone)
    {
        // 快取索引
        $cacheIndex = 'express_' . $express_code . '_' . $express_no . '_' . $phone;
        if ($data = Cache::get($cacheIndex)) {
            return $data;
        }
        // 引數設定
        $postData = [
            'customer' => $this->config['customer'],
            'param' => json_encode([
                'resultv2' => '1',
                'com' => $express_code,
                'num' => $express_no,
                'phone' => $phone
            ])
        ];
        $postData['sign'] = strtoupper(md5($postData['param'] . $this->config['key'] . $postData['customer']));
        // 請求快遞100 api
        $url = 'http://poll.kuaidi100.com/poll/query.do';
        $result = curlPost($url, http_build_query($postData));
        $express = json_decode($result, true);
        // 記錄錯誤資訊
        if (isset($express['returnCode']) || !isset($express['data'])) {
            $this->error = isset($express['message']) ? $express['message'] : '查詢失敗';
            return false;
        }
        // 記錄快取, 時效5分鐘
        Cache::set($cacheIndex, $express['data'], 300);
        return $express['data'];
    }

    /**
     * 返回錯誤資訊
     */
    public function getError()
    {
        return $this->error;
    }

}