<?php

namespace app\common\model\plus\agent;

use app\common\model\BaseModel;
use think\facade\Cache;

/**
 * 分銷商設定模型
 */
class Setting extends BaseModel
{
    protected $name = 'agent_setting';
    protected $createTime = false;

    /**
     * 轉義陣列格式
     * @param $value
     * @return mixed
     */
    public function getValuesAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * 轉義成json格式
     * @param $value
     * @return false|string
     */
    public function setValuesAttr($value)
    {
        return json_encode($value);
    }

    /**
     * 獲取指定項設定
     * @param $key
     * @param null $app_id
     * @return array|mixed
     */
    public static function getItem($key, $app_id = null)
    {
        $data = static::getAll($app_id);
        return isset($data[$key]) ? $data[$key]['values'] : [];
    }

    /**
     * 獲取分銷商設定
     * @param null $app_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getAll($app_id = null)
    {
        $self = new static;
        is_null($app_id) && $app_id = $self::$app_id;
        if (!$data = Cache::get('agent_setting_' . $app_id)) {
            $data = array_column($self->select()->toArray(), null, 'key');
            Cache::tag('cache')->set('agent_setting_' . $app_id, $data);
        }
        return array_merge_multiple($self->defaultData(), $data);
    }

    /**
     * 獲取設定項資訊
     * @param $key
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function detail($key)
    {
        return (new static())->find(compact('key'));
    }

    /**
     * 是否開啟分銷功能
     * @param null $app_id
     * @return mixed
     */
    public static function isOpen($app_id = null)
    {
        return static::getItem('basic', $app_id)['is_open'];
    }

    /**
     * 分銷中心頁面名稱
     * @param null $app_id
     * @return mixed
     */
    public static function getAgentTitle($app_id = null)
    {
        return static::getItem('words', $app_id)['index']['title']['value'];
    }

    /**
     * 預設配置
     * @return array[]
     */
    public function defaultData()
    {
        return [
            'basic' => [
                'key' => 'basic',
                'describe' => '基礎設定',
                'values' => [
                    // 是否開啟分銷功能
                    'is_open' => '0',   // 引數值：1開啟 0關閉
                    // 分銷層級
                    'level' => '3', // 引數值：1一級 2二級 3三級
                    // 分銷商內購
                    'self_buy' => '0'   // 引數值：1開啟 0關閉
                ],
            ],
            'condition' => [
                'key' => 'condition',
                'describe' => '分銷商條件',
                'values' => [
                    // 成為分銷商條件
                    'become' => '10',   // 引數值：10填寫申請資訊(需後臺稽核) 20填寫申請資訊(無需稽核)
                    // 購買指定商品成為分銷商 0關閉 1開啟
                    'become__buy_product' => '0',
                    // 購買指定商品的id集
                    'become__buy_product_ids' => [],
                    // 成為下線條件
                    'downline' => '10',  // 引數值：10首次點選分享連結 20首次下單 30首次付款
                ]
            ],
            'commission' => [
                'key' => 'commission',
                'describe' => '佣金設定',
                'values' => [
                    // 一級佣金
                    'first_money' => '0',
                    // 一級佣金
                    'second_money' => '0',
                    // 一級佣金
                    'third_money' => '0',
                ]
            ],
            'settlement' => [
                'key' => 'settlement',
                'describe' => '結算',
                'values' => [
                    // 提現方式
                    'pay_type' => ['10', '20', '30'],   // 引數值：10微信支付 20支付寶支付 30銀行卡支付
                    // 微信支付自動打款
                    'wechat_pay_auto' => '0',       // 微信支付自動打款：1開啟 0關閉
                    // 最低提現額度
                    'min_money' => '10.00',
                    // 佣金結算天數
                    'settle_days' => '10',
                ]
            ],
            'words' => [
                'key' => 'words',
                'describe' => '自定義文字',
                'values' => [
                    'index' => [
                        'title' => [
                            'default' => '分銷中心',
                            'value' => '分銷中心'
                        ],
                        'words' => [
                            'not_agent' => [
                                'default' => '您還不是分銷商，請先提交申請',
                                'value' => '您還不是分銷商，請先提交申請'
                            ],
                            'apply_now' => [
                                'default' => '立即加入',
                                'value' => '立即加入'
                            ],
                            'referee' => [
                                'default' => '推薦人',
                                'value' => '推薦人'
                            ],
                            'money' => [
                                'default' => '可提現佣金',
                                'value' => '可提現'
                            ],
                            'freeze_money' => [
                                'default' => '待提現佣金',
                                'value' => '待提現'
                            ],
                            'total_money' => [
                                'default' => '已提現金額',
                                'value' => '已提現金額'
                            ],
                            'cash' => [
                                'default' => '去提現',
                                'value' => '去提現'
                            ],
                        ]
                    ],
                    'apply' => [
                        'title' => [
                            'default' => '申請成為分銷商',
                            'value' => '申請成為分銷商'
                        ],
                        'words' => [
                            'title' => [
                                'default' => '請填寫申請資訊',
                                'value' => '請填寫申請資訊'
                            ],
                            'license' => [
                                'default' => '分銷商申請協議',
                                'value' => '分銷商申請協議'
                            ],
                            'submit' => [
                                'default' => '申請成為經銷商',
                                'value' => '申請成為經銷商'
                            ],
                            'wait_audit' => [
                                'default' => '您的申請已受理，正在進行資訊核驗，請耐心等待。',
                                'value' => '您的申請已受理，正在進行資訊核驗，請耐心等待。'
                            ],
                            'goto_mall' => [
                                'default' => '去商城逛逛',
                                'value' => '去商城逛逛'
                            ],
                        ]
                    ],
                    'order' => [
                        'title' => [
                            'default' => '分銷訂單',
                            'value' => '分銷訂單'
                        ],
                        'words' => [
                            'all' => [
                                'default' => '全部',
                                'value' => '全部'
                            ],
                            'unsettled' => [
                                'default' => '未結算',
                                'value' => '未結算'
                            ],
                            'settled' => [
                                'default' => '已結算',
                                'value' => '已結算'
                            ],
                        ]
                    ],
                    'team' => [
                        'title' => [
                            'default' => '我的團隊',
                            'value' => '我的團隊'
                        ],
                        'words' => [
                            'total_team' => [
                                'default' => '團隊總人數',
                                'value' => '團隊總人數'
                            ],
                            'first' => [
                                'default' => '一級團隊',
                                'value' => '一級團隊'
                            ],
                            'second' => [
                                'default' => '二級團隊',
                                'value' => '二級團隊'
                            ],
                            'third' => [
                                'default' => '三級團隊',
                                'value' => '三級團隊'
                            ],
                        ]
                    ],
                    'cash_list' => [
                        'title' => [
                            'default' => '提現明細',
                            'value' => '提現明細'
                        ],
                        'words' => [
                            'all' => [
                                'default' => '全部',
                                'value' => '全部'
                            ],
                            'apply_10' => [
                                'default' => '稽核中',
                                'value' => '稽核中'
                            ],
                            'apply_20' => [
                                'default' => '稽核透過',
                                'value' => '稽核透過'
                            ],
                            'apply_40' => [
                                'default' => '已打款',
                                'value' => '已打款'
                            ],
                            'apply_30' => [
                                'default' => '駁回',
                                'value' => '駁回'
                            ],
                        ]
                    ],
                    'cash_apply' => [
                        'title' => [
                            'default' => '申請提現',
                            'value' => '申請提現'
                        ],
                        'words' => [
                            'capital' => [
                                'default' => '可提現佣金',
                                'value' => '可提現佣金'
                            ],
                            'money' => [
                                'default' => '提現金額',
                                'value' => '提現金額'
                            ],
                            'money_placeholder' => [
                                'default' => '請輸入要提取的金額',
                                'value' => '請輸入要提取的金額'
                            ],
                            'min_money' => [
                                'default' => '最低提現佣金',
                                'value' => '最低提現佣金'
                            ],
                            'submit' => [
                                'default' => '提交申請',
                                'value' => '提交申請'
                            ],
                        ]
                    ],
                    'qrcode' => [
                        'title' => [
                            'default' => '推廣二維碼',
                            'value' => '推廣二維碼'
                        ]
                    ],
                ]
            ],
            'license' => [
                'key' => 'license',
                'describe' => '申請協議',
                'values' => [
                    'license' => ''
                ]
            ],
            'background' => [
                'key' => 'background',
                'describe' => '頁面背景圖',
                'values' => [
                    // 分銷中心首頁
                    'index' => self::$base_url . 'image/agent/agent-bg.jpg',
                    // 申請成為分銷商頁
                    'apply' => self::$base_url . 'image/agent/agent-bg.jpg',
                    // 申請提現頁
                    'cash_apply' => self::$base_url . 'image/agent/agent-bg.jpg',
                ],
            ],
            'template_msg' => [
                'key' => 'template_msg',
                'describe' => '模板訊息',
                'values' => [
                    'apply_tpl' => '',    // 分銷商稽核通知
                    'cash_tpl' => '',    // 提現狀態通知
                ]
            ],
            'qrcode' => [
                'key' => 'template_msg',
                'describe' => '分銷海報',
                'values' => [
                    'backdrop' => [
                        'src' => self::$base_url . 'image/agent/backdrop.jpg',
                    ],
                    'nickName' => [
                        'fontSize' => 14,
                        'color' => '#000000',
                        'left' => 118,
                        'top' => 550
                    ],
                    'avatar' => [
                        'width' => 70,
                        'style' => 'circle',
                        'left' => 25,
                        'top' => 510
                    ],
                    'qrcode' => [
                        'width' => 100,
                        'style' => 'circle',
                        'left' => 253,
                        'top' => 522
                    ]
                ],
            ]
        ];
    }
}