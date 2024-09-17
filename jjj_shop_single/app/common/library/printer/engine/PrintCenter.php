<?php

namespace app\common\library\printer\engine;


class PrintCenter extends Basics
{
    // API地址
    const API = 'http://open.printcenter.cn:8080/addOrder';

    /**
     * 執行訂單列印
     */
    public function printTicket($content)
    {
        $config = json_decode($this->config, true);
        // 構建請求引數
        $context = stream_context_create([
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded ",
                'method' => 'POST',
                'content' => http_build_query([
                    'deviceNo' => $config['deviceNo'],
                    'key' => $config['key'],
                    'printContent' => $content,
                    'times' => $this->times
                ]),
            ]
        ]);
        // API請求：開始列印
        $result = file_get_contents(self::API, false, $context);
        // 處理返回結果
        $result = json_decode($result);
        log_write($result);
        // 返回狀態
        if ($result->responseCode != 0) {
            $this->error = $result->msg;
            return false;
        }
        return true;
    }

}