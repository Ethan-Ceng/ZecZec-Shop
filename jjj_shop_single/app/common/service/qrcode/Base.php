<?php

namespace app\common\service\qrcode;

use app\common\library\easywechat\AppWx;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

/**
 * 二維碼服務基類
 */
class Base
{
    /**
     * 構造方法
     */
    public function __construct()
    {
    }

    /**
     * 儲存小程式碼到檔案
     */
    protected function saveQrcode($app_id, $scene, $page)
    {
        // 檔案目錄
        $dirPath = root_path('public') . "/temp/{$app_id}/image_wx";
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        // 檔名稱
        $fileName = 'qrcode_' . md5($app_id . $scene . $page) . '.png';
        // 檔案路徑
        $savePath = "{$dirPath}/{$fileName}";
        if (file_exists($savePath)) return $savePath;
        // 小程式配置資訊
        $app = AppWx::getApp($app_id);
        // 請求api獲取小程式碼
        $response = $app->getClient()->postJson('/wxa/getwxacodeunlimit', [
            'scene' => $scene,
            'page' => $page,
            'width' => 430,
            'check_path' => false,
        ]);
        // 儲存小程式碼到檔案
        $response->saveAs($dirPath . '/' . $fileName);
        return $savePath;
    }

    /**
     * 儲存小程式碼到檔案
     */
    protected function saveMpQrcode(\Endroid\QrCode\QrCode $qrcode, $app_id, $scene, $source)
    {
        // 檔案目錄
        $dirPath = root_path('public') . "/temp/{$app_id}/{$source}";
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        // 檔名稱
        $fileName = 'qrcode_' . md5($app_id . $scene) . '.png';
        // 檔案路徑
        $savePath = "{$dirPath}/{$fileName}";
        if (file_exists($savePath)) return $savePath;
        // 儲存二維碼到檔案
        $write = new PngWriter();
        $write->write($qrcode)->saveToFile($savePath);
        return $savePath;
    }


    /**
     * 儲存小程式碼到檔案
     */
    protected function saveQrcodeToDir($app_id, $page, $savePath, $codeList)
    {
        // 小程式碼引數
        foreach ($codeList as $code) {
            // 小程式配置資訊
            $app = AppWx::getApp($app_id);
            $scene = "pid:{$code['gift_package_id']},cid:{$code['code']}";
            // 請求api獲取小程式碼
            $response = $app->getClient()->postJson('/wxa/getwxacodeunlimit', [
                'scene' => $scene,
                'page' => $page,
                'width' => 430,
                'check_path' => false,
            ]);
            // 儲存小程式碼到檔案
            $response->saveAs($savePath . '/' . $code['code'] . '.png');
        }
        return true;
    }

    /**
     * 儲存小程式碼到檔案
     */
    protected function saveMpQrcodeToDir($page, $savePath, $codeList, $app_id)
    {
        foreach ($codeList as $code) {
            $qrcode = new QrCode(base_url() . $page . '?package_id=' . $code['gift_package_id'] . '&code=' . $code['code'] . '&app_id=' . $app_id);
            // 儲存二維碼到檔案
            $path = "{$savePath}{$code['code']}" . '.png';
            $write = new PngWriter();
            $write->write($qrcode)->saveToFile($path);
        }
        return true;
    }

    /**
     * 儲存邀請好友小程式碼到檔案
     */
    protected function saveInvitQrcodeToDir($app_id, $page, $savePath, $id)
    {
        // 小程式配置資訊
        $app = AppWx::getApp($app_id);
        $scene = "invitid:$id";
        // 請求api獲取小程式碼
        $response = $app->getClient()->postJson('/wxa/getwxacodeunlimit', [
            'scene' => $scene,
            'page' => $page,
            'width' => 430,
            'check_path' => false,
        ]);
        // 儲存小程式碼到檔案
        $response->saveAs($savePath . '/' . $id . '.png');
        return true;
    }

    /**
     * 儲存邀請好友小程式碼到檔案
     */
    protected function saveInvitMpQrcodeToDir($page, $savePath, $id, $app_id)
    {
        $qrcode = new QrCode(base_url() . $page . '?invitation_id=' . $id . '&app_id=' . $app_id);
        // 儲存二維碼到檔案
        $path = "{$savePath}{$id}" . '.png';
        $write = new PngWriter();
        $write->write($qrcode)->saveToFile($path);
        return true;
    }

    /**
     * 獲取網路圖片到臨時目錄
     */
    protected function saveTempImage($app_id, $url, $mark = 'temp')
    {
        $dirPath = root_path('public') . "temp/{$app_id}/{$mark}";
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        $savePath = $dirPath . '/' . $mark . '_' . md5($url) . '.png';
        if (file_exists($savePath)) return $savePath;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $img = curl_exec($ch);
        curl_close($ch);
        $fp = fopen($savePath, 'w');
        fwrite($fp, $img);
        fclose($fp);
        return $savePath;
    }

}