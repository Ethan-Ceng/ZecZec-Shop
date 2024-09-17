<?php


namespace Alipay\EasySDK\Kernel;


use Alipay\EasySDK\Kernel\Util\AntCertificationUtil;
use http\Exception\RuntimeException;

class CertEnvironment
{
    private $rootCertSN;

    private $merchantCertSN;

    private $cachedAlipayPublicKey;

    /**
     * 構造證書執行環境
     * @param $merchantCertPath    string 商戶公鑰證書路徑
     * @param $alipayCertPath      string 支付寶公鑰證書路徑
     * @param $alipayRootCertPath  string 支付寶根證書路徑
     */
    public function certEnvironment($merchantCertPath, $alipayCertPath, $alipayRootCertPath)
    {
        if (empty($merchantCertPath) || empty($alipayCertPath) || empty($alipayRootCertPath)) {
            throw new RuntimeException("證書引數merchantCertPath、alipayCertPath或alipayRootCertPath設定不完整。");
        }
        $antCertificationUtil = new AntCertificationUtil();
        $this->rootCertSN = $antCertificationUtil->getRootCertSN($alipayRootCertPath);
        $this->merchantCertSN = $antCertificationUtil->getCertSN($merchantCertPath);
        $this->cachedAlipayPublicKey = $antCertificationUtil->getPublicKey($alipayCertPath);
    }

    /**
     * @return mixed
     */
    public function getRootCertSN()
    {
        return $this->rootCertSN;
    }

    /**
     * @return mixed
     */
    public function getMerchantCertSN()
    {
        return $this->merchantCertSN;
    }

    /**
     * @return mixed
     */
    public function getCachedAlipayPublicKey()
    {
        return $this->cachedAlipayPublicKey;
    }


}