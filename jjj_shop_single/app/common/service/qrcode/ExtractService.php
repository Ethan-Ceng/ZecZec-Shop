<?php

namespace app\common\service\qrcode;

use Endroid\QrCode\QrCode;

/**
 * 訂單核銷二維碼
 */
class ExtractService extends Base
{
    private $appId;

    //使用者
    private $user;

    private $orderId;

    private $orderNo;

    private $source;

    /**
     * 構造方法
     */
    public function __construct($appId, $user, $orderId, $source, $orderNo)
    {
        parent::__construct();
        $this->appId = $appId;
        $this->user = $user;
        $this->orderId = $orderId;
        $this->orderNo = $orderNo;
        $this->source = $source;
    }

    /**
     * 獲取小程式碼
     */
    public function getImage()
    {
        // 判斷二維碼檔案存在則直接返回url
        if (file_exists($this->getPosterPath())) {
            return $this->getPosterUrl();
        }
        $qrcode = new QrCode($this->orderNo);
        $qrcode = $this->saveMpQrcode($qrcode, $this->appId, $this->orderId, 'image_mp');
        return $this->savePoster($qrcode);
    }

    private function savePoster($qrcode)
    {
        copy($qrcode, $this->getPosterPath());
        return $this->getPosterUrl();
    }

    /**
     * 二維碼檔案路徑
     */
    private function getPosterPath()
    {
        $web_path = $_SERVER['DOCUMENT_ROOT'];
        // 儲存路徑
        $tempPath = $web_path . "/temp/{$this->appId}/{$this->source}/";
        !is_dir($tempPath) && mkdir($tempPath, 0755, true);
        return $tempPath . $this->getPosterName();
    }

    /**
     * 二維碼檔名稱
     */
    private function getPosterName()
    {
        return 'clerk_' . md5("{$this->orderId}_{$this->user['user_id']}}") . '.png';
    }

    /**
     * 二維碼url
     */
    private function getPosterUrl()
    {
        return \base_url() . "temp/{$this->appId}/{$this->source}/{$this->getPosterName()}" . '?t=' . time();
    }

}