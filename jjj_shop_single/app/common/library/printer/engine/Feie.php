<?php

namespace app\common\library\printer\engine;

use app\common\library\printer\party\FeieHttpClient;

/**
 * 飛鵝印表機API引擎
 */
class Feie extends Basics
{
    // 介面IP或域名
    const IP = 'api.feieyun.cn';

    // 介面IP埠
    const PORT = 80;

    // 介面路徑
    const PATH = '/Api/Open/';

    /**
     * 執行訂單列印
     */
    public function printTicket($content)
    {
        // 構建請求引數
        $params = $this->getParams($content);
        // API請求：開始列印
        $client = new FeieHttpClient(self::IP, self::PORT);
        if (!$client->post(self::PATH, $params)) {
            $this->error = $client->getError();
            return false;
        }
        // 處理返回結果
        $result = json_decode($client->getContent());
        log_write($result);
        // 返回狀態
        if ($result->ret != 0) {
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
            'stime' => $time,
            'sig' => sha1("{$config['USER']}{$config['UKEY']}{$time}"),
            'apiname' => 'Open_printMsg',
            'sn' => $config['SN'],
            'content' => $content,
            'times' => $this->times    // 列印次數
        ];
    }

}