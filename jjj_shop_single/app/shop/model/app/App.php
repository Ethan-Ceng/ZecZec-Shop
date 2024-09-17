<?php

namespace app\shop\model\app;

use app\common\library\easywechat\AesUtil;
use app\common\model\app\App as AppModel;
use EasyWeChat\Pay\Application;
use think\facade\Cache;
use WeChatPay\Util\PemUtil;

/**
 * 應用模型
 */
class App extends AppModel
{
    /**
     * 更新應用設定
     */
    public function edit($data)
    {
        $this->startTrans();
        try {
            // 刪除app快取
            self::deleteCache();
            $where['app_id'] = self::$app_id;

            $count = $this->count($where);
            // 更新小程式設定
            if ($count > 0) {
                self::update($data, $where);
            }
            if ($count == 0) {
                $data['app_id'] = self::$app_id;
                self::create($data);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    public function count($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 刪除app快取
     */
    public static function deleteCache()
    {
        return Cache::delete('app_' . self::$app_id);
    }


    public function editPay($data)
    {
        $this->startTrans();
        try {
            foreach ($data['pay_type'] as &$item) {
                if ($item['value'] == 'wx' || $item['value'] == 'mp') {
                    $pay_type = [];
                    foreach ($item['pay_type'] as &$value) {
                        if ($value != 30) {
                            $pay_type[] = $value;
                        }
                    }
                    $item['pay_type'] = $pay_type;
                }
            }
            $data['pay_type'] = json_encode($data['pay_type']);
            if (!empty($data['cert_pem']) && !empty($data['key_pem'])) {
                // 寫入微信支付證書檔案
                $this->writeCertPemFiles($data['cert_pem'], $data['key_pem']);
                $platform_pem = $this->createCert($data);
                if ($platform_pem) {
                    $data['platform_pem'] = $platform_pem;
                }
                $serial_no = $this->createSerial();
                if ($serial_no) {
                    $data['serial_no'] = $serial_no;
                }
            }
            $this->save($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 寫入cert證書檔案
     */
    private function writeCertPemFiles($cert_pem = '', $key_pem = '')
    {
        // 證書目錄
        $filePath = root_path() . 'runtime/cert/app/' . self::$app_id . '/';
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
        return true;
    }

    //生成平臺證書
    public function createCert($data)
    {
        // cert目錄
        $filePath = root_path() . 'runtime/cert/app/' . self::$app_id . '/';
        $config = [
            'mch_id' => $data['mchid'],
            // 商戶證書
            'certificate' => $filePath . 'cert.pem',
            'private_key' => $filePath . 'key.pem',
            // v3 API 秘鑰
            'secret_key' => $data['apikey'],
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
        $filePath = root_path() . 'runtime/cert/app/' . self::$app_id . '/';
        file_put_contents($filePath . 'platform.pem', $res);
        return $res;

    }

    //生成證書序列號
    public function createSerial()
    {
        $filePath = root_path() . 'runtime/cert/app/' . self::$app_id . '/';
        // 從本地檔案中載入「微信支付平臺證書」，用來驗證微信支付應答的簽名
        $platformCertificateFilePath = "file://" . $filePath . 'platform.pem';
        // 從「微信支付平臺證書」中獲取「證書序列號」
        return PemUtil::parseCertificateSerialNo($platformCertificateFilePath);
    }
}
