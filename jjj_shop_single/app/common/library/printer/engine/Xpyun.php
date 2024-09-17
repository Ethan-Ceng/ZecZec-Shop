<?php

namespace app\common\library\printer\engine;

use app\common\library\printer\party\XpHttpClient;

/**
 * 芯燁小票印表機API引擎
 */
class Xpyun extends Basics
{
    // 介面url
    const url = 'https://open.xpyun.net/api/openapi/xprinter/print';

    /**
     * 執行訂單列印
     */
    public function printTicket($content)
    {
        // 構建請求引數
        $request = $this->getParams($content);
        $jsonRequest = json_encode($request);
        $client = new XpHttpClient();

        $returnContent = $client->post(self::url, $jsonRequest);
        $result = json_decode($returnContent);
        // 返回狀態
        if ($result->code != 0) {
            $this->error = $result->msg;
            return false;
        }
        return true;
    }

    /**
     * 構建Api請求引數
     */
    private function getParams($content)
    {
        $config = json_decode($this->config, true);
        $time = time();
        return [
            'user' => $config['USER'],
            'timestamp' => $time,
            'sign' => sha1($config['USER'] . $config['UKEY'] . $time),
            'debug' => 0,//1返回非json格式的資料，僅測試時候使用
            'sn' => $config['SN'],
            'content' => $content,
            'copies' => $this->times,    // 列印次數
            'mode' => 0,
            'voice' => 2,
        ];
    }

}