<?php

namespace app\common\model\settings;

use app\common\enum\settings\SettingEnum;
use think\facade\Cache;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\model\BaseModel;
use think\Model;

/**
 * 系統設定模型
 */
class Setting extends BaseModel
{
    protected $name = 'setting';
    protected $createTime = false;

    /**
     * 獲取器: 轉義陣列格式
     */
    public function getValuesAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * 修改器: 轉義成json格式
     */
    public function setValuesAttr($value)
    {
        return json_encode($value);
    }

    /**
     * 獲取指定項設定
     */
    public static function getItem($key, $app_id = null)
    {
        $data = self::getAll($app_id);
        $data_key = $data[$key];
        if (isset($data_key)) {
            $data_key = $data[$key]['values'];
            jsonRecursive($data_key);
        } else {
            $data_key = [];
        }

        return $data_key;
    }

    /**
     * 獲取設定項資訊
     */
    public static function detail($key)
    {
        return (new static())->where('key', '=', $key)->find();
    }

    /**
     * 全域性快取: 系統設定
     */
    public static function getAll($app_id = null)
    {
        $static = new static;
        is_null($app_id) && $app_id = $static::$app_id;

        if (!$data = Cache::get('setting_' . $app_id)) {
            $setting = $static->where(compact('app_id'))->select();
            $data = empty($setting) ? [] : array_column($static->collection($setting)->toArray(), null, 'key');
            Cache::tag('cache')->set('setting_' . $app_id, $data);
        }
        return $static->getMergeData($data);
    }

    /**
     * 陣列轉換為資料集物件
     */
    public function collection($resultSet)
    {
        $item = current(get_mangled_object_vars($resultSet));
        if ($item instanceof Model) {
            return \think\model\Collection::make($resultSet);
        } else {
            return \think\Collection::make($resultSet);
        }
    }


    /**
     * 合併使用者設定與預設資料
     */
    private function getMergeData($userData)
    {
        $defaultData = $this->defaultData();
        // 商城設定：配送方式
        if (isset($userData['store']['values']['delivery_type'])) {
            unset($defaultData['store']['values']['delivery_type']);
        }
        if (isset($userData['nav']['values']['data']['list'])) {
            unset($defaultData['nav']['values']['data']['list']);
        }
        if (isset($userData['balance_cash']['values'])) {
            unset($defaultData['balance_cash']['values']);
        }
        return array_merge_multiple($defaultData, $userData);
    }

    /**
     * 獲取系統配置
     */
    public static function getSysConfig()
    {
        $model = new static;
        $result = $model->withoutGlobalScope()->where('key', '=', SettingEnum::SYS_CONFIG)->value('values');
        if (!$result) {
            $result = $model->defaultData()[SettingEnum::SYS_CONFIG]['values'];
        } else {
            $result = json_decode($result, true);
            $result = array_merge_multiple($model->defaultData()[SettingEnum::SYS_CONFIG]['values'], $result);
        }
        return $result;
    }

    /**
     * 預設配置
     */
    public function defaultData($storeName = null)
    {
        return [
            SettingEnum::STORE => [
                'key' => 'store',
                'describe' => '商城設定',
                'values' => [
                    // 商城名稱
                    'name' => $storeName ?: '玖玖珈商城',
                    // 配送方式
                    'delivery_type' => array_keys(DeliveryTypeEnum::data()),
                    // 快遞100
                    'kuaidi100' => [
                        'customer' => '',
                        'secret' => '',
                        'key' => '',
                    ],
                    //預設頭像
                    'avatarUrl' => base_url() . 'image/user/avatarUrl.png',
                    // 是否記錄日誌
                    'is_get_log' => true,
                    // 是否向小程式傳送物流
                    'is_send_wx' => false,
                    //商城logo
                    'logoUrl' => base_url() . 'image/diy/logo.png',
                    // H5註冊是否開啟簡訊驗證
                    'sms_open' => true,
                    // 小程式是否開啟微信授權
                    'wx_open' => true,
                    // 小程式是否開啟手機授權
                    'wx_phone' => true,
                    // 公眾號是否開啟微信授權
                    'mp_open' => true,
                    // 騰訊地圖key
                    'tx_key' => '',
                    // 預設暱稱字首
                    'user_name' => '會員',
                    // 登入logo
                    'login_logo' => base_url() . 'image/user/login_logo.png',
                    // 登入描述
                    'login_desc' => '成為會員，立享更多優惠福利',
                    // 公眾號是否開啟手機繫結
                    'mp_phone' => true,
                ],
            ],
            SettingEnum::MP_SERVICE => [
                'key' => 'mp_service',
                'describe' => '公眾號客服設定',
                'values' => [
                    // qq
                    'qq' => '',
                    // 微信
                    'wechat' => '',
                    // 微信公眾號圖片
                    'mp_image' => '',
                ],
            ],
            SettingEnum::TRADE => [
                'key' => 'trade',
                'describe' => '交易設定',
                'values' => [
                    'order' => [
                        'close_days' => '3',
                        'receive_days' => '10',
                        'refund_days' => '7'
                    ],
                    'freight_rule' => '10',
                ]
            ],
            SettingEnum::STORAGE => [
                'key' => 'storage',
                'describe' => '上傳設定',
                'values' => [
                    'default' => 'local',
                    'max_image' => '2',
                    'max_video' => '20',
                    'engine' => [
                        'local' => [],
                        'qiniu' => [
                            'bucket' => '',
                            'access_key' => '',
                            'secret_key' => '',
                            'domain' => 'http://'
                        ],
                        'aliyun' => [
                            'bucket' => '',
                            'access_key_id' => '',
                            'access_key_secret' => '',
                            'domain' => 'http://'
                        ],
                        'qcloud' => [
                            'bucket' => '',
                            'region' => '',
                            'secret_id' => '',
                            'secret_key' => '',
                            'domain' => 'http://'
                        ],
                    ]
                ],
            ],
            SettingEnum::SMS => [
                'key' => 'sms',
                'describe' => '簡訊通知',
                'values' => [
                    'default' => 'aliyun',
                    'accept_phone' => '',
                    'engine' => [
                        'aliyun' => [
                            'AccessKeyId' => '',
                            'AccessKeySecret' => '',
                            'sign' => '',
                            'template_code' => ''
                        ],
                        'qcloud' => [
                            'AccessKeyId' => '',
                            'AccessKeySecret' => '',
                            'sign' => '',
                            'template_code' => ''
                        ],
                        'hwcloud' => [
                            'AccessKeyId' => '',
                            'AccessKeySecret' => '',
                            'sign' => '',
                            'sender' => '',
                            'template_code' => '',
                            'url' => ''
                        ],
                    ],
                ],
            ],
            SettingEnum::TPL_MSG => [
                'key' => 'tplMsg',
                'describe' => '模板訊息',
                'values' => [
                    'payment' => [
                        'is_enable' => '0',
                        'template_id' => '',
                    ],
                    'delivery' => [
                        'is_enable' => '0',
                        'template_id' => '',
                    ],
                    'refund' => [
                        'is_enable' => '0',
                        'template_id' => '',
                    ],
                ],
            ],
            SettingEnum::PRINTER => [
                'key' => 'printer',
                'describe' => '小票印表機設定',
                'values' => [
                    'is_open' => '0',   // 是否開啟列印
                    'printer_id' => '', // 印表機id
                    'order_status' => [], // 訂單型別 10下單列印 20付款列印 30確認收貨列印
                    'label_print_type' => '0',
                    'siid' => ''
                ],
            ],
            SettingEnum::FULL_FREE => [
                'key' => 'full_free',
                'describe' => '滿額包郵設定',
                'values' => [
                    'is_open' => '0',   // 是否開啟滿額包郵
                    'money' => '',      // 單筆訂單額度
                ],
            ],
            SettingEnum::POINTS => [
                'key' => 'points',
                'describe' => '積分設定',
                'values' => [
                    'points_name' => '積分',         // 積分名稱自定義
                    'register_points' => '0',            // 註冊贈送積分
                    'is_shopping_gift' => '0',      // 是否開啟購物送積分
                    'gift_ratio' => '100',            // 是否開啟購物送積分
                    'is_shopping_discount' => '0',    // 是否允許下單使用積分抵扣
                    'is_trans_balance' => '0',    // 是否允許轉換餘額
                    'discount' => [     // 積分抵扣
                        'discount_ratio' => '0.01',       // 積分抵扣比例
                        'full_order_price' => '100.00',       // 訂單滿[?]元
                        'max_money_ratio' => '10',             // 最高可抵扣訂單額百分比
                    ],
                    // 充值說明
                    'describe' => "a) 積分不可兌現、不可轉讓,僅可在本平臺使用;\n" .
                        "b) 您在本平臺參加特定活動也可使用積分,詳細使用規則以具體活動時的規則為準;\n" .
                        "c) 積分的數值精確到個位(小數點後全部捨棄,不進行四捨五入)\n" .
                        "d) 買家在完成該筆交易(訂單狀態為“已簽收”)後才能得到此筆交易的相應積分,如購買商品參加店鋪其他優惠,則優惠的金額部分不享受積分獲取;",
                ],
            ],
            SettingEnum::OFFICIA => [
                'key' => 'officia',
                'describe' => '公眾號關注',
                'values' => [
                    'status' => 0
                ],
            ],
            SettingEnum::COLLECTION => [
                'key' => 'collection',
                'describe' => '引導收藏',
                'values' => [
                    'status' => 0
                ],
            ],
            SettingEnum::RECOMMEND => [
                'key' => 'recommend',
                'describe' => '商品推薦',
                'values' => [
                    'is_recommend' => '0',
                    'location' => [],
                    'choice' => '0',
                    'type' => '10',
                    'num' => '20',
                    'product' => []
                ],
            ],
            SettingEnum::HOMEPUSH => [
                'key' => 'homepush',
                'describe' => '首頁推送',
                'values' => [
                    // 是否開啟
                    'is_open' => 0,
                ]
            ],
            SettingEnum::POINTSMALL => [
                'key' => 'pointsmall',
                'describe' => '積分商城',
                'values' => [
                    // 是否開啟
                    'is_open' => false,
                    // 是否使用優惠券
                    'is_coupon' => false,
                    // 是否分銷
                    'is_agent' => false,
                ]
            ],
            SettingEnum::BARGAIN => [
                'key' => 'bargain',
                'describe' => '限時砍價',
                'values' => [
                    // 是否使用優惠券
                    'is_coupon' => false,
                    // 是否分銷
                    'is_agent' => false,
                    // 是否開啟積分
                    'is_point' => false,
                    // 規則
                    'bargain_rules' => ''
                ]
            ],
            SettingEnum::SIGN => [
                'key' => 'sign',
                'describe' => '簽到有禮',
                'values' => [
                    // 是否開啟
                    'is_open' => false
                ]
            ],
            SettingEnum::SECKILL => [
                'key' => 'seckill',
                'describe' => '限時秒殺',
                'values' => [
                    // 是否開啟積分
                    'is_point' => false,
                    // 是否開啟分銷
                    'is_agent' => false,
                    //未付款訂單自動關閉時間,分鐘
                    'order_close' => 10,
                    // 是否開啟優惠券
                    'is_coupon' => false,
                ]
            ],
            SettingEnum::ASSEMBLE => [
                'key' => 'assemble',
                'describe' => '限時拼團',
                'values' => [
                    // 是否開啟
                    'is_open' => false,
                    // 是否開啟積分
                    'is_point' => false,
                    // 是否開啟分銷
                    'is_agent' => false,
                    // 是否使用優惠券
                    'is_coupon' => false,
                ]
            ],
            SettingEnum::BALANCE => [
                'key' => 'balance',
                'describe' => '充值設定',
                'values' => [
                    // 是否開啟
                    'is_open' => 0,
                    // 是否可以自定義
                    'is_plan' => 1,
                    // 最低充值金額
                    'min_money' => 1,
                    // 充值說明
                    'describe' => "a) 賬戶充值僅限線上方式支付，充值金額即時到賬；\n" .
                        "b) 有問題請聯絡客服;\n",
                ]
            ],
            SettingEnum::INVITATION => [
                'key' => 'invitation',
                'describe' => '邀請好友',
                'values' => [
                    // 是否開啟
                    'is_open' => false,
                ]
            ],
            SettingEnum::APPSHARE => [
                'key' => 'appshare',
                'describe' => 'app分享',
                'values' => [
                    // 分享型別 1公眾號/h5 2小程式 3下載頁
                    'type' => 1,
                    // 公眾號、h5地址
                    'open_site' => '',
                    // 小程式原始id
                    'gh_id' => '',
                    // 跳轉網頁
                    'web_url' => '',
                    // 下載頁
                    'down_url' => '',
                    // 繫結型別
                    'bind_type' => 1
                ]
            ],
            SettingEnum::H5ALIPAY => [
                'key' => 'h5Alipay',
                'describe' => 'h5支付寶支付',
                'values' => [
                    // 是否開啟
                    'is_open' => false,
                    // 支付寶app_id
                    'app_id' => '',
                    // 支付寶公鑰
                    'publicKey' => '',
                    // 應用私鑰
                    'privateKey' => ''
                ]
            ],
            SettingEnum::LIVE => [
                'key' => 'live',
                'describe' => '直播設定',
                'values' => [
                    // 自動同步
                    'auto_syn' => false,
                ],
            ],
            SettingEnum::SYS_CONFIG => [
                'key' => 'sys_config',
                'describe' => '系統設定',
                'values' => [
                    'admin_name' => '商城運營管理系統',
                    'admin_bg_img' => '',
                    'shop_name' => '商城管理系統',
                    'shop_bg_img' => '',
                    // 微信服務商支付
                    'weixin_service' => [
                        'is_open' => 0,
                        'app_id' => '',
                        'mch_id' => '',
                        'apikey' => '',
                        'cert_pem' => '',
                        'key_pem' => '',
                        'serial_no' => ''
                    ],
                    'storage' => [
                        'default' => 'local',
                        'max_image' => '2',
                        'max_video' => '20',
                        'engine' => [
                            'local' => [],
                            'qiniu' => [
                                'bucket' => '',
                                'access_key' => '',
                                'secret_key' => '',
                                'domain' => 'http://'
                            ],
                            'aliyun' => [
                                'bucket' => '',
                                'access_key_id' => '',
                                'access_key_secret' => '',
                                'domain' => 'http://'
                            ],
                            'qcloud' => [
                                'bucket' => '',
                                'region' => '',
                                'secret_id' => '',
                                'secret_key' => '',
                                'domain' => 'http://'
                            ],
                        ]]
                ]
            ],
            SettingEnum::THEME => [
                'key' => 'theme',
                'describe' => '主題設定',
                'values' => [
                    'theme' => '0',//主題設定
                ],
            ],
            SettingEnum::NAV => [
                'key' => 'nav',
                'describe' => '底部導航',
                'values' => [
                    'data' => [
                        'is_auto' => "0",
                        "type" => "0",
                        "backgroundColor" => "#FFFFFF",
                        "textColor" => "#000000",
                        "textHoverColor" => "#E2231A",
                        "bulge" => "true",
                        "list" => [
                            [
                                "text" => "首頁",
                                "iconPath" => base_url() . "image/tabbar/home.png",
                                "selectedIconPath" => base_url() . "image/tabbar/home_active.png",
                                "link_url" => "/pages/index/index"
                            ],
                            [
                                "text" => "分類",
                                "iconPath" => base_url() . "image/tabbar/category.png",
                                "selectedIconPath" => base_url() . "image/tabbar/category_active.png",
                                "link_url" => "/pages/product/category"
                            ],
                            [
                                "text" => "購物車",
                                "iconPath" => base_url() . "image/tabbar/cart.png",
                                "selectedIconPath" => base_url() . "image/tabbar/cart_active.png",
                                "link_url" => "/pages/cart/cart"
                            ],
                            [
                                "text" => "我的",
                                "iconPath" => base_url() . "image/tabbar/user.png",
                                "selectedIconPath" => base_url() . "image/tabbar/user_active.png",
                                "link_url" => "/pages/user/index/index"
                            ]
                        ]
                    ]
                ]
            ],
            SettingEnum::CARD => [
                'key' => 'card',
                'describe' => '卡券設定',
                'values' => [
                    // 背景圖
                    'image' => '',
                ],
            ],
            SettingEnum::ADVANCE => [
                'key' => 'advance',
                'describe' => '預售設定',
                'values' => [
                    // banner圖
                    'image' => [],
                    // 預售未付款關閉訂單時間
                    'end_time' => '10',//分鐘
                    // 支付定金預售結束後未付款結束時間
                    'pay_time' => '24',//小時
                    // 未支付尾款是否退定金
                    'money_return' => '0',//0否1是
                    // 是否開啟優惠券抵扣
                    'is_coupon' => '0',//0否1是
                    // 是否開啟分銷
                    'is_agent' => '0',//0否1是
                    // 是否開啟積分抵扣
                    'is_point' => '0',//0否1是
                    //是否開啟會員折扣
                    'is_user_grade' => '0',//0否1是
                ],
            ],
            SettingEnum::BALANCE_CASH => [
                'key' => 'balance_cash',
                'describe' => '餘額提現設定',
                'values' => [
                    // 是否開啟
                    'is_open' => '0',
                    // 最低提現金額
                    'min_money' => 1,
                    // 提現比例
                    'cash_ratio' => 100,
                    // 提現方式
                    'pay_type' => ['10', '20', '30'],   // 引數值：10微信支付 20支付寶支付 30銀行卡支付
                ]
            ],
            SettingEnum::TASK => [
                'key' => 'task',
                'describe' => '任務中心',
                'values' => [
                    //任務背景圖
                    'back_image' => base_url() . 'image/task/task.png',
                    //成長任務
                    'grow_task' => [
                        [
                            'name' => '上傳頭像',
                            'image' => base_url() . 'image/task/image.png',
                            'is_open' => '1',
                            'task_type' => 'image',
                            'rule' => '首次修改頭像獎勵5積分',
                            'points' => 5,
                            'status' => 0
                        ],
                        [
                            'name' => '完善基本資料',
                            'image' => base_url() . 'image/task/base.png',
                            'is_open' => '1',
                            'task_type' => 'base',
                            'rule' => '首次修改暱稱獎勵5積分',
                            'points' => 5,
                            'status' => 0
                        ],
                    ],
                    //日常任務
                    'day_task' => [
                        [
                            'name' => '分享商品',
                            'image' => base_url() . 'image/task/product.png',
                            'is_open' => '1',
                            'task_type' => 'product',
                            'rule' => '當日分享商品給好友獎勵5積分',
                            'points' => 5,
                            'status' => 0
                        ],
                        [
                            'name' => '分享文章',
                            'image' => base_url() . 'image/task/article.png',
                            'is_open' => '1',
                            'task_type' => 'article',
                            'rule' => '當日分享文章給好友獎勵5積分',
                            'points' => 5,
                            'status' => 0
                        ],
                    ],
                ]
            ],
            SettingEnum::SERVICE => [
                'key' => 'service',
                'describe' => '使用者協議',
                'values' => [
                    'service' => '',
                ]
            ],
            SettingEnum::PRIVACY => [
                'key' => 'privacy',
                'describe' => '隱私協議',
                'values' => [
                    'privacy' => '',
                ]
            ],
        ];
    }
}
