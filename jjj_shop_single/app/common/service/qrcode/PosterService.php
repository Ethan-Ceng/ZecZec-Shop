<?php

namespace app\common\service\qrcode;

use app\common\model\settings\Setting as SettingModel;
use Endroid\QrCode\QrCode;
use Grafika\Color;
use Grafika\Grafika;
use app\common\model\plus\agent\Setting;

/**
 * 分銷二維碼
 */
class PosterService extends Base
{
    // 分銷商使用者資訊
    private $agent;
    // 分銷商海報設定
    private $config;
    // 來源
    private $source;

    /**
     * 構造方法
     */
    public function __construct($agent, $source)
    {
        parent::__construct();
        // 分銷商使用者資訊
        $this->agent = $agent;
        $this->source = $source;
        // 分銷商海報設定
        $this->config = Setting::getItem('qrcode', $agent['app_id']);
    }

    /**
     * 獲取分銷二維碼
     */
    public function getImage()
    {
//        if (file_exists($this->getPosterPath())) {
//            return $this->getPosterUrl();
//        }
        // 小程式id
        $appId = $this->agent['app_id'];
        // 1. 下載背景圖
        $backdrop = $this->saveTempImage($appId, $this->config['backdrop']['src'], 'backdrop');
        // 2. 下載使用者頭像
        $avatarUrl = $this->saveTempImage($appId, $this->agent['user']['avatarUrl'], 'avatar');
        if ($this->source == 'wx') {
            // 3. 下載小程式碼
            $scene = 'uid:' . $this->agent['user_id'];
            $qrcode = $this->saveQrcode($appId, $scene, 'pages/index/index');
        } else if ($this->source == 'mp' || $this->source == 'h5') {
            $scene = 'uid:' . $this->agent['user_id'];
            $qrcode = new QrCode(base_url() . 'h5/pages/index/index?referee_id=' . $this->agent['user_id'] . '&app_id=' . $appId);
            $qrcode = $this->saveMpQrcode($qrcode, $appId, $scene, 'mp');
        } else if ($this->source == 'android' || $this->source == 'ios') {
            $appshare = SettingModel::getItem('appshare');
            if ($appshare['type'] == 1) {
                $down_url = $appshare['open_site'] . '?app_id=' . $this->agent['app_id'] . '&referee_id=' . $this->agent['user_id'];
            } else {
                //下載頁
                if ($appshare['bind_type'] == 1) {
                    $down_url = $appshare['down_url'];
                } else {
                    $down_url = base_url() . "/index.php/api/user.useropen/invite?app_id=" . $this->agent['app_id'] . "&referee_id=" . $this->agent['user_id'];
                }
            }

            $scene = 'uid:' . $this->agent['user_id'];
            $qrcode = new QrCode($down_url);
            $qrcode = $this->saveMpQrcode($qrcode, $appId, $scene, 'mp');
        }
        // 4. 拼接海報圖
        return $this->savePoster($backdrop, $avatarUrl, $qrcode);
    }

    /**
     * 海報圖檔案路徑
     */
    private function getPosterPath()
    {
        // 儲存路徑
        $tempPath = root_path('public') . 'temp/' . $this->agent['app_id'] . '/' . $this->source . '/';
        !is_dir($tempPath) && mkdir($tempPath, 0755, true);
        return $tempPath . $this->getPosterName();
    }

    /**
     * 海報圖檔名稱
     */
    private function getPosterName()
    {
        return 'poster_' . md5($this->agent['user_id']) . '.png';
    }

    /**
     * 海報圖url
     */
    private function getPosterUrl()
    {
        return \base_url() . 'temp/' . $this->agent['app_id'] . '/' . $this->source . '/' . $this->getPosterName() . '?t=' . time();
    }

    /**
     * 拼接海報圖
     */
    private function savePoster($backdrop, $avatarUrl, $qrcode)
    {
        // 例項化影像編輯器
        $editor = Grafika::createEditor(['Gd']);
        // 開啟海報背景圖
        $editor->open($backdropImage, $backdrop);
        // 生成圓形使用者頭像
        $this->config['avatar']['style'] === 'circle' && $this->circular($avatarUrl, $avatarUrl);
        // 開啟使用者頭像
        $editor->open($avatarImage, $avatarUrl);
        // 重設使用者頭像寬高
        $avatarWidth = $this->config['avatar']['width'] * 2;
        $editor->resizeExact($avatarImage, $avatarWidth, $avatarWidth);
        // 使用者頭像新增到背景圖
        $avatarX = $this->config['avatar']['left'] * 2;
        $avatarY = $this->config['avatar']['top'] * 2;
        $editor->blend($backdropImage, $avatarImage, 'normal', 1.0, 'top-left', $avatarX, $avatarY);

        // 生成圓形小程式碼，僅小程式支援
        if ($this->source == 'wx') {
            $this->config['qrcode']['style'] === 'circle' && $this->circular($qrcode, $qrcode);
        }
        // 開啟小程式碼
        $editor->open($qrcodeImage, $qrcode);
        // 重設小程式碼寬高
        $qrcodeWidth = $this->config['qrcode']['width'] * 2;
        $editor->resizeExact($qrcodeImage, $qrcodeWidth, $qrcodeWidth);
        // 小程式碼新增到背景圖
        $qrcodeX = $this->config['qrcode']['left'] * 2;
        $qrcodeY = $this->config['qrcode']['top'] * 2;
        $editor->blend($backdropImage, $qrcodeImage, 'normal', 1.0, 'top-left', $qrcodeX, $qrcodeY);

        // 寫入使用者暱稱
        $fontSize = $this->config['nickName']['fontSize'] * 2;
        $fontX = $this->config['nickName']['left'] * 2;
        $fontY = $this->config['nickName']['top'] * 2;
        $Color = new Color($this->config['nickName']['color']);
        $fontPath = public_path() . '/static/' . 'st-heiti-light.ttc';
        $editor->text($backdropImage, $this->agent['user']['nickName'], $fontSize, $fontX, $fontY, $Color, $fontPath);

        // 儲存圖片
        $editor->save($backdropImage, $this->getPosterPath());
        return $this->getPosterUrl();
    }

    /**
     * 生成圓形圖片
     */
    private function circular($imgpath, $saveName = '')
    {
        $srcImg = imagecreatefromstring(file_get_contents($imgpath));
        $w = imagesx($srcImg);
        $h = imagesy($srcImg);
        $w = $h = min($w, $h);
        $newImg = imagecreatetruecolor($w, $h);
        // 這一句一定要有
        imagesavealpha($newImg, true);
        // 拾取一個完全透明的顏色,最後一個引數127為全透明
        $bg = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
        imagefill($newImg, 0, 0, $bg);
        $r = $w / 2; //圓半徑
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($srcImg, $x, $y);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($newImg, $x, $y, $rgbColor);
                }
            }
        }
        // 輸出圖片到檔案
        imagepng($newImg, $saveName);
        // 釋放空間
        imagedestroy($srcImg);
        imagedestroy($newImg);
    }

}