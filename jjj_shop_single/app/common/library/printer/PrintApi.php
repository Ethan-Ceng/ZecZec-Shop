<?php

namespace app\common\library\printer;

use app\common\model\settings\Setting as SettingModel;
use app\shop\model\settings\DeliverySetting as DeliverySettingModel;
use app\shop\model\settings\DeliveryTemplate as DeliveryTemplateModel;
use app\shop\model\settings\ReturnAddress as ReturnAddressModel;

/**
 * 電子面單列印
 */
class PrintApi
{
    // 客戶授權key
    public $key = '';
    // 授權secret
    public $secret = '';
    // 列印方式
    public $label_print_type = '';
    // 印表機編碼
    public $siid = '';

    /**
     * 建構函式
     */
    public function __construct($app_id)
    {
        $setting = SettingModel::getItem('store', $app_id);
        $this->key = $setting['kuaidi100']['key'];
        $this->secret = $setting['kuaidi100']['secret'];
        $config = SettingModel::getItem('printer', $app_id);
        $this->label_print_type = $config['label_print_type'];
        $this->siid = $config['siid'];
    }

    //電子面單下單介面
    public function printOrder($data, $order)
    {
        $settingDetail = DeliverySettingModel::detail($data['setting_id'], ['express']);
        $TemplateDetail = DeliveryTemplateModel::detail($data['template_id']);
        $sendMan = ReturnAddressModel::detail($data['address_id']);
        list($msec, $sec) = explode(' ', microtime());
        $t = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);    // 當前時間戳
        $param = array(
            'printType' => $this->label_print_type == 1 ? 'CLOUD' : 'IMAGE',              // 列印型別，NON:只下單不列印（預設）；IMAGE:生成圖片短鏈；HTML:生成html短鏈；CLOUD:使用快遞100雲印表機列印
            'partnerId' => $settingDetail['partner_id'],                 // 電子面單客戶賬戶或月結賬號
            'partnerKey' => $settingDetail['partner_key'],                // 電子面單密碼
            'partnerSecret' => $settingDetail['partner_secret'],             // 電子面單金鑰
            'partnerName' => $settingDetail['partner_name'],               // 電子面單客戶賬戶名稱
            'net' => $settingDetail['net'],                       // 收件網點名稱,由快遞公司當地網點分配
            'code' => $settingDetail['code'],                      // 電子面單承載編號
            'checkMan' => $settingDetail['check_man'],                  // 電子面單承載快遞員名
            'tbNet' => '',                     // 在使用菜鳥/淘寶/拼多多授權電子面單時，若月結賬號下存在多個網點，則tbNet="網點名稱,網點編號" ，注意此處為英文逗號
            'kuaidicom' => $settingDetail['express']['express_code'],                 // 快遞公司的編碼：https://api.kuaidi100.com/document/5f0ff6e82977d50a94e10237.html
            'recMan' => array(
                'name' => $order['address']['name'],                  // 收件人姓名
                'mobile' => $order['address']['phone'],                // 收件人的手機號，手機號和電話號二者其一必填
                'tel' => '',                   // 收件人的電話號，手機號和電話號二者其一必填
                'printAddr' => $order['address'] ? $order['address']->getFullAddress() : '',             // 收件人地址
                'company' => ''                // 收件人公司名
            ),
            'sendMan' => array(
                'name' => $sendMan['name'],                  // 寄件人姓名
                'mobile' => $sendMan['phone'],              // 寄件人的手機號，手機號和電話號二者其一必填
                'tel' => '',                   // 寄件人的電話號，手機號和電話號二者其一必填
                'printAddr' => $sendMan['detail'],              // 寄件人地址
                'company' => ''                // 寄件人公司名
            ),
            'cargo' => '商品',                 // 物品名稱
            'count' => '1',                    // 物品總數量
            'weight' => '0.5',                 // 物品總重量KG
            'payType' => 'SHIPPER',            // 支付方式
            'expType' => '標準快遞',           // 快遞型別: 標準快遞（預設）、順豐特惠、EMS經濟
            'remark' => '測試',                // 備註
            'siid' => $this->label_print_type == 1 ? $this->siid : '',                      // 裝置編碼
            'direction' => '0',                // 列印方向，0：正方向（預設）； 1：反方向；只有printType為CLOUD時該引數生效
            'tempId' => $TemplateDetail['template_num'],                    // 主單模板：快遞公司模板V2連結：https://api.kuaidi100.com/manager/v2/shipping-label/template-shipping-label
            'childTempId' => '',               // 子單模板：快遞公司模板V2連結：https://api.kuaidi100.com/manager/v2/shipping-label/template-shipping-label
            'backTempId' => '',                // 回單模板：快遞公司模板V2連結：https://api.kuaidi100.com/manager/v2/shipping-label/template-shipping-label
            'valinsPay' => '',                 // 保價額度
            'collection' => '',                // 代收貨款額度
            'needChild' => '0',                // 是否需要子單
            'needBack' => '0',                 // 是否需要回單
            'orderId' => null,                 // 貴司內部自定義的訂單編號,需要保證唯一性
            'callBackUrl' => null,             // 列印狀態回撥地址，預設僅支援http
            'salt' => '',                      // 簽名用隨機字串
            'needSubscribe' => false,          // 是否開啟訂閱功能 false：不開啟(預設)；true：開啟
            'pollCallBackUrl' => null,         // 如果needSubscribe 設定為true時，pollCallBackUrl必須填入，用於跟蹤回撥
            'resultv2' => '0',                 // 新增此欄位表示開通行政區域解析或地圖軌跡功能
            'needDesensitization' => false,    // 是否脫敏 false：關閉（預設）；true：開啟
            'needLogo' => false,               // 面單是否需要logo false：關閉（預設）；true：開啟
            'thirdOrderId' => null,            // 平臺匯入返回的訂單id：如平臺類加密訂單，使用此下單為必填
            'oaid' => null,                    // 淘寶訂單收件人ID (Open Addressee ID)，長度不超過128個字元，淘寶訂單加密情況用於解密
            'thirdTemplateURL' => null,        // 第三方平臺面單基礎模板連結，如為第三方平臺匯入訂單選填，如不填寫，預設返回兩聯面單模板
            'thirdCustomTemplateUrl' => null,  // 第三方平臺自定義區域模板地址
            'customParam' => null,             // 面單自定義引數
            'needOcr' => false,                // 第三方平臺訂單是否需要開啟ocr，開啟後將會透過推送方式推送 false：關閉（預設）；true：開啟
            'ocrInclude' => null,              // orc需要檢測識別的面單元素
            'height' => null,                  // 列印紙的高度，以mm為單位
            'width' => null                    // 列印紙的寬度，以mm為單位
        );
        //請求引數
        $post_data = array();
        $post_data['param'] = json_encode($param, JSON_UNESCAPED_UNICODE);
        $post_data['key'] = $this->key;
        $post_data['t'] = $t;
        $sign = md5($post_data['param'] . $t . $this->key . $this->secret);
        $post_data['sign'] = strtoupper($sign);
        $url = 'https://api.kuaidi100.com/label/order?method=order';    // 電子面單下單介面請求地址
        //傳送post請求
        $result = $this->apiPost($url, $post_data);
        return $result;
    }

    //電子面單取消訂單
    public function cancelOrder($data, $kd_order_num,$express_no)
    {
        list($msec, $sec) = explode(' ', microtime());
        $t = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);    // 當前時間戳
        $param = array(
            'partnerId' => $data['partner_id'],                 // 電子面單客戶賬戶或月結賬號
            'partnerKey' => $data['partner_key'],                // 電子面單密碼
            'partnerSecret' => $data['partner_secret'],             // 電子面單金鑰
            'partnerName' => $data['partner_name'],               // 電子面單客戶賬戶名稱
            'net' => $data['net'],                       // 收件網點名稱,由快遞公司當地網點分配
            'code' => $data['code'],                      // 電子面單承載編號
            'kuaidicom' => $data['express']['express_code'],                 // 快遞公司的編碼：https://api.kuaidi100.com/document/5f0ff6e82977d50a94e10237.html
            'kuaidinum' => $express_no,                 // 快遞單號
            'orderId' => $kd_order_num,                   // 快遞公司訂單號，對應下單時返回的kdComOrderNum，如果下單時有返回該欄位，則取消時必填，否則可以不填
            'reason' => ''                     // 取消原因
        );
        // 請求引數
        $post_data = array();
        $post_data['param'] = json_encode($param, JSON_UNESCAPED_UNICODE);
        $post_data['key'] = $this->key;
        $post_data['t'] = $t;
        $sign = md5($post_data['param'] . $t . $this->key . $this->secret);
        $post_data['sign'] = strtoupper($sign);
        $url = 'https://poll.kuaidi100.com/eorderapi.do?method=cancel';    // 電子面單取消請求地址
        $result = $this->apiPost($url, $post_data);
        return $result;
    }

    //電子面單復打
    public function printOld($task_id)
    {
        list($msec, $sec) = explode(' ', microtime());
        $t = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);    // 當前時間戳
        $param = array(
            'taskId' => $task_id,                    // 任務ID
            'siid' => '',                      // 快遞100印表機,不填預設為下單時填入的siid
        );
        //請求引數
        $post_data = array();
        $post_data['param'] = json_encode($param, JSON_UNESCAPED_UNICODE);
        $post_data['key'] = $this->key;
        $post_data['t'] = $t;
        $sign = md5($post_data['param'] . $t . $this->key . $this->secret);
        $post_data['sign'] = strtoupper($sign);
        $url = 'https://api.kuaidi100.com/label/order?method=printOld';    // 電子面單復打介面請求地址
        $result = $this->apiPost($url, $post_data);
        return $result;

    }

    //傳送請求
    private function apiPost($url, $param)
    {
        //傳送post請求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        log_write($result);
        $data = json_decode($result, true);
        return $data;
    }
}
