<?php

namespace app\common\service\qrcode;

use app\common\model\settings\Setting as SettingModel;
use Grafika\Color;
use Grafika\Grafika;
use Endroid\QrCode\QrCode;

class ProductService extends Base
{
    // 商品資訊
    private $product;

    // 使用者id
    private $user_id;

    // 商品型別：10商城商品 20拼團商品
    private $productType;

    // 來源，微信小程式，公眾號
    private $source;

    // 小程式碼連結
    private $pages = [
        10 => 'pages/product/detail/detail'
    ];

    /**
     * 構造方法
     */
    public function __construct($product, $user,$source, $productType = 10)
    {
        parent::__construct();
        // 商品資訊
        $this->product = $product;
        // 當前使用者id
        $this->user_id = $user ? $user['user_id'] : 0;
        // 商品型別：10商城商品
        $this->productType = $productType;
        //來源
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        // 判斷海報圖檔案存在則直接返回url
//        if (file_exists($this->getPosterPath())) {
//            return $this->getPosterUrl();
//        }
        // 小程式id
        $appId = $this->product['app_id'];
        // 商品海報背景圖
        $backdrop = __DIR__ . '/resource/product_bg.png';
        // 下載商品首圖
        $productUrl = $this->saveTempImage($appId, $this->product['image'][0]['file_path'], 'product');
        if($this->source == 'wx'){
            // 小程式碼引數
            $scene = "gid:{$this->product['product_id']},uid:" . ($this->user_id ?: '');
            // 下載小程式碼
            $qrcode = $this->saveQrcode($appId, $scene, $this->pages[$this->productType]);
        }else if($this->source == 'mp' || $this->source == 'h5'){
            $scene = "gid:{$this->product['product_id']},uid:" . ($this->user_id ?: '');
            $qrcode = new QrCode(base_url().'h5/pages/product/detail/detail?product_id='.$this->product['product_id'].'&referee_id='.$this->user_id ?: ''.'&app_id='.$this->product['app_id']);
            $qrcode = $this->saveMpQrcode($qrcode, $appId, $scene, 'mp');
        }
        // 拼接海報圖
        return $this->savePoster($backdrop, $productUrl, $qrcode);
    }

    /**
     * 拼接海報圖
     */
    private function savePoster($backdrop, $productUrl, $qrcode)
    {
        // 例項化影像編輯器
        $editor = Grafika::createEditor(['Gd']);
        // 字型檔案路徑
        $fontPath = public_path() . '/static/' . 'st-heiti-light.ttc';
        // 開啟海報背景圖
        $editor->open($backdropImage, $backdrop);
        // 開啟商品圖片
        $editor->open($productImage, $productUrl);
        // 重設商品圖片寬高
        $editor->resizeExact($productImage, 690, 690);
        // 商品圖片新增到背景圖
        $editor->blend($backdropImage, $productImage, 'normal', 1.0, 'top-left', 30, 30);
        // 商品名稱處理換行
        $fontSize = 30;
        $productName = $this->wrapText($fontSize, 0, $fontPath, $this->product['product_name'], 680, 2);
        // 寫入商品名稱
        $editor->text($backdropImage, $productName, $fontSize, 30, 750, new Color('#333333'), $fontPath);
        // 寫入商品價格
        $priceType = [10 => 'product_price'];
        $editor->text($backdropImage, $this->product['sku'][0][$priceType[$this->productType]], 38, 62, 964, new Color('#ff4444'));
        // 開啟小程式碼
        $editor->open($qrcodeImage, $qrcode);
        // 重設小程式碼寬高
        $editor->resizeExact($qrcodeImage, 140, 140);
        // 小程式碼新增到背景圖
        $editor->blend($backdropImage, $qrcodeImage, 'normal', 1.0, 'top-left', 570, 914);

        // 儲存圖片
        $editor->save($backdropImage, $this->getPosterPath());
        return $this->getPosterUrl();
    }

    /**
     * 處理文字超出長度自動換行
     */
    private function wrapText($fontsize, $angle, $fontface, $string, $width, $max_line = null)
    {
        // 這幾個變數分別是 字型大小, 角度, 字型名稱, 字串, 預設寬度
        $content = "";
        // 將字串拆分成一個個單字 儲存到陣列 letter 中
        $letter = [];
        for ($i = 0; $i < mb_strlen($string, 'UTF-8'); $i++) {
            $letter[] = mb_substr($string, $i, 1, 'UTF-8');
        }
        $line_count = 0;
        foreach ($letter as $l) {
            $testbox = imagettfbbox($fontsize, $angle, $fontface, $content . ' ' . $l);
            // 判斷拼接後的字串是否超過預設的寬度
            if (($testbox[2] > $width) && ($content !== "")) {
                $line_count++;
                if ($max_line && $line_count >= $max_line) {
                    $content = mb_substr($content, 0, -1, 'UTF-8') . "...";
                    break;
                }
                $content .= "\n";
            }
            $content .= $l;
        }
        return $content;
    }

    /**
     * 海報圖檔案路徑
     */
    private function getPosterPath()
    {
        // 儲存路徑
        $tempPath = root_path('public') . 'temp' . '/' . $this->product['app_id'] . '/' . $this->source. '/';
        !is_dir($tempPath) && mkdir($tempPath, 0755, true);
        return $tempPath . $this->getPosterName();
    }

    /**
     * 海報圖檔名稱
     */
    private function getPosterName()
    {
        return 'product_' . md5("{$this->user_id}_{$this->productType}_{$this->product['product_id']}") . '.png';
    }

    /**
     * 海報圖url
     */
    private function getPosterUrl()
    {
        return \base_url() . 'temp/' . $this->product['app_id'] . '/' .$this->source . '/' . $this->getPosterName() . '?t=' . time();
    }


}