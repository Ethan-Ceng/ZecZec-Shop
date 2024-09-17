<?php

namespace app\common\library\easywechat;

use app\admin\model\settings\Setting as SettingModel;
use app\common\model\app\App as AppModel;
use app\common\exception\BaseException;
use app\common\model\app\AppWx as AppWxModel;
use EasyWeChat\MiniApp\Application;
use EasyWeChat\Pay\Application as payApp;

/**
 * 微信小程式
 */
class AppWx
{
    public static function getApp($app_id = null)
    {
        // 獲取當前小程式資訊
        $wxConfig = AppWxModel::getAppWxCache($app_id);
        // 驗證appid和appsecret是否填寫
        if (empty($wxConfig['wxapp_id']) || empty($wxConfig['wxapp_secret'])) {
            throw new BaseException(['msg' => '請到 [後臺-應用-小程式設定] 填寫appid 和 appsecret']);
        }
        $config = [
            'app_id' => $wxConfig['wxapp_id'],
            'secret' => $wxConfig['wxapp_secret'],
            'response_type' => 'array',
        ];
        $app = new Application($config);
        return $app;
    }

    public static function getWxPayApp($app_id)
    {
        // 獲取當前小程式資訊
        $wxConfig = AppWxModel::getAppWxCache($app_id);
        // 驗證appid和appsecret是否填寫
        if (empty($wxConfig['wxapp_id']) || empty($wxConfig['wxapp_secret'])) {
            throw new BaseException(['msg' => '請到 [後臺-應用-小程式設定] 填寫appid 和 appsecret']);
        }

        $app = AppModel::detail($app_id);
        $sysConfig = SettingModel::getSysConfig();
        $is_service_pay = false;
        if ($sysConfig['weixin_service']['is_open'] == 1 && $app['weixin_service'] == 1) {
            $is_service_pay = true;
        }
        if (empty($app['cert_pem']) || empty($app['key_pem'])) {
            if (!$is_service_pay) {
                throw new BaseException(['msg' => '請先到後臺[應用->支付設定]填寫微信支付證書檔案']);
            }
        }
        // cert目錄
        $filePath = root_path() . 'runtime/cert/app/' . $wxConfig['app_id'] . '/';

        $config = [
            'app_id' => $wxConfig['wxapp_id'],
            'secret' => $wxConfig['wxapp_secret'],
            'mch_id' => $app['mchid'],
            'secret_key' => $app['apikey'],   // API 金鑰
            // 如需使用敏感介面（如退款、傳送紅包等）需要配置 API 證書路徑(登入商戶平臺下載 API 證書)
            'certificate' => $filePath . 'cert.pem',
            'private_key' => $filePath . 'key.pem',
            'platform_certs' => [
                $filePath . 'platform.pem',
            ],
            'http' => [
                'throw' => true, // 狀態碼非 200、300 時是否丟擲異常，預設為開啟
                'timeout' => 5.0,
            ],
        ];
        if ($is_service_pay) {
            $config['sp_appid'] = $sysConfig['weixin_service']['app_id'];
            $config['sp_mchid'] = $sysConfig['weixin_service']['mch_id'];
            $config['sub_appid'] = $wxConfig['wxapp_id'];
            $config['sub_mch_id'] = $app['mchid'];
            $config['secret_key'] = $sysConfig['weixin_service']['apikey'];
            $filePath = root_path() . 'runtime/cert/appwx/10000/';
            $config['certificate'] = $filePath . 'cert.pem';
            $config['private_key'] = $filePath . 'key.pem';
            $config['mch_id'] = $sysConfig['weixin_service']['mch_id'];
            $config['platform_certs'] = $filePath . 'platform.pem';
        }
        $payApp = new payApp($config);
        return $payApp;
    }

    /**
     * 獲取session_key
     * @param $code
     * @return array|mixed
     */
    public static function sessionKey($app, $code)
    {
        $utils = $app->getUtils();
        return $utils->codeToSession($code);
    }
}
