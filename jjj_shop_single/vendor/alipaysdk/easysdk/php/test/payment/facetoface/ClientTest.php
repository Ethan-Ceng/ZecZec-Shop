<?php


namespace Alipay\EasySDK\Test\payment\facetoface;


use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Test\TestAccount;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $account = new TestAccount();
        Factory::setOptions($account->getTestAccount());
    }

    public function testPay()
    {
        $create =Factory::payment()->common()->create("Iphone6 16G",
            microtime(), "88.88", "2088002656718920");

        $result = Factory::payment()->faceToFace()->pay("Iphone6 16G", $create->outTradeNo, "0.10",
            "1234567890");
        $this->assertEquals('40004', $result->code);
        $this->assertEquals('Business Failed', $result->msg);
        $this->assertEquals('ACQ.PAYMENT_AUTH_CODE_INVALID', $result->subCode);
        $this->assertEquals('支付失敗，獲取顧客賬戶資訊失敗，請顧客重新整理付款碼後重新收款，如再次收款失敗，請聯絡管理員處理。[SOUNDWAVE_PARSER_FAIL]', $result->subMsg);
    }

    public function testPrecreate(){
        $create =Factory::payment()->common()->create("Iphone6 16G",
            microtime(), "88.88", "2088002656718920");
        $result = Factory::payment()->faceToFace()->precreate("Iphone6 16G", $create->outTradeNo, "0.10");

        $this->assertEquals('10000', $result->code);
        $this->assertEquals('Success', $result->msg);
    }

}