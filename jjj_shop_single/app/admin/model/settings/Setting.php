<?php

namespace app\admin\model\settings;

use app\common\enum\settings\SettingEnum;
use app\common\library\easywechat\AesUtil;
use app\common\model\settings\Setting as SettingModel;
use EasyWeChat\Pay\Application;
use WeChatPay\Util\PemUtil;

class Setting extends SettingModel
{

    /**
     * 新增
     */
    public function add($data)
    {
        $service = $this->where(['key' => SettingEnum::SYS_CONFIG])->find();
        if ($data['weixin_service']['is_open']) {
            $data['weixin_service']['serial_no'] = $this->writeCertPemFiles($data, $data['weixin_service']['cert_pem'], $data['weixin_service']['key_pem']);
        }
        if (!$service) {
            $add['key'] = SettingEnum::SYS_CONFIG;
            $add['describe'] = '系統設定';
            $add['values'] = $data;
            return $this->save($add);
        } else {
            return $service->save(['values' => $data]);
        }
    }

    private function writeCertPemFiles($data, $cert_pem = '', $key_pem = '')
    {
        if (empty($cert_pem) || empty($key_pem)) {
            return false;
        }
        // 證書目錄
        $filePath = root_path() . 'runtime/cert/appwx/10000/';
        // 目錄不存在則自動建立
        if (!is_dir($filePath)) {
            mkdir($filePath, 0755, true);
        }
        // 寫入cert.pem檔案
        if (!empty($cert_pem)) {
            file_put_contents($filePath . 'cert.pem', $cert_pem);
        }
        // 寫入key.pem檔案
        if (!empty($key_pem)) {
            file_put_contents($filePath . 'key.pem', $key_pem);
        }
        $this->createCert($data);
        $filePath = root_path() . 'runtime/cert/appwx/10000/';
        // 從本地檔案中載入「微信支付平臺證書」，用來驗證微信支付應答的簽名
        $platformCertificateFilePath = "file://" . $filePath . 'platform.pem';
        // 從「微信支付平臺證書」中獲取「證書序列號」
        return PemUtil::parseCertificateSerialNo($platformCertificateFilePath);
    }

    public function createCert($data)
    {
        // cert目錄
        $filePath = root_path() . 'runtime/cert/appwx/10000/';
        $config = [
            'mch_id' => $data['weixin_service']['mch_id'],
            // 商戶證書
            'certificate' => $filePath . 'cert.pem',
            'private_key' => $filePath . 'key.pem',
            // v3 API 秘鑰
            'secret_key' => $data['weixin_service']['apikey'],
            'http' => [
                'throw' => true,
                'timeout' => 5.0,
            ],
        ];
        $app = new Application($config);
        $api = $app->getClient();
        $response = $api->get('/v3/certificates');
        $response = $response->toArray();
        //獲得加密證書資訊
        $cert = end($response['data']);
        $tool = new AesUtil($config['secret_key']);
        $res = $tool->decryptToString($cert['encrypt_certificate']['associated_data'], $cert['encrypt_certificate']['nonce'], $cert['encrypt_certificate']['ciphertext']);
        // 證書目錄
        $filePath = root_path() . 'runtime/cert/appwx/10000/';
        file_put_contents($filePath . 'platform.pem', $res);
        return $res;

    }
}
