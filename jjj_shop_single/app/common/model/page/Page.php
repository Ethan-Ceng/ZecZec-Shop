<?php

namespace app\common\model\page;

use app\common\model\BaseModel;

/**
 * diy頁面模型
 */
class Page extends BaseModel
{
    protected $pk = 'page_id';
    protected $name = 'page';

    /**
     * 頁面標題欄預設資料
     * @return array
     */
    public function getDefaultPage()
    {
        static $defaultPage = [];
        if (!empty($defaultPage)) return $defaultPage;
        return [
            'type' => 'page',
            'name' => '頁面設定',
            'params' => [
                'name' => '頁面名稱',
                'title' => '頁面標題',
                'title_type' => 'text',//text文字 image圖片
                'share_title' => '分享標題',
                'share_img' => self::$base_url . 'image/diy/logo.png',
                'toplogo' => self::$base_url . 'image/diy/logo_top.png',
                'icon' => 'icon-biaoti',
            ],
            'style' => [
                'titleTextColor' => 'black',
                'titleBackgroundColor' => '#ffffff',
                'hide_search' => 0
            ],
            'category' => [
                'open' => 0,
                'color' => '#000000',
            ]
        ];
    }

    /**
     * 個人中心頁面diy元素預設資料
     * @return array[]
     */
    public function getCenterDefaultItems()
    {
        $data = [
            'base' => [
                'name' => '基礎資訊',
                'type' => 'base',
                'group' => 'page',
                'icon' => 'icon-jibenxinxi',
                'style' => [
                    'background' => '#ffffff',
                    'padding' => 48,
                    'paddingTop' => 0,
                    'paddingBottom' => 0,
                    'paddingLeft' => 0,
                    'bgcolor' => '#f2f2f2',
                    'type' => 1
                ],
            ],
            'order' => [
                'name' => '我的訂單',
                'type' => 'order',
                'group' => 'page',
                'icon' => 'icon-wodedingdan',
                'style' => [
                    'background' => '#ffffff',
                    'paddingTop' => 0,
                    'paddingBottom' => 0,
                    'paddingLeft' => 10,
                    'bgcolor' => '#f2f2f2',
                    'topRadio' => 0,
                    'bottomRadio' => 10,
                    'type' => 1
                ],
            ],
            'imageSingle' => [
                'name' => '單圖組',
                'type' => 'imageSingle',
                'group' => 'media',
                'icon' => 'icon-tupian111',
                'style' => [
                    'paddingTop' => 0,
                    'paddingLeft' => 0,
                    'background' => '#ffffff'
                ],
                'data' => [
                    [
                        'imgUrl' => self::$base_url . 'image/diy/banner/01.png',
                        'imgName' => 'image-1.jpg',
                        'linkUrl' => ''
                    ]
                ]
            ],
            'navBar' => [
                'name' => '導航組',
                'type' => 'navBar',
                'group' => 'media',
                'icon' => 'icon-mulu',
                'style' => [
                    'background' => '#ffffff',
                    'rowsNum' => 4
                ],
                'data' => [
                    [
                        'imgUrl' => self::$base_url . 'image/diy/navbar/01.png',
                        'imgName' => 'icon-1.png',
                        'linkUrl' => '',
                        'text' => '按鈕文字1',
                        'color' => '#666666'
                    ],
                    [
                        'imgUrl' => self::$base_url . 'image/diy/navbar/02.png',
                        'imgName' => 'icon-2.jpg',
                        'linkUrl' => '',
                        'text' => '按鈕文字2',
                        'color' => '#666666'
                    ],
                    [
                        'imgUrl' => self::$base_url . 'image/diy/navbar/03.png',
                        'imgName' => 'icon-3.jpg',
                        'linkUrl' => '',
                        'text' => '按鈕文字3',
                        'color' => '#666666'
                    ],
                    [
                        'imgUrl' => self::$base_url . 'image/diy/navbar/04.png',
                        'imgName' => 'icon-4.jpg',
                        'linkUrl' => '',
                        'text' => '按鈕文字4',
                        'color' => '#666666'
                    ]
                ]
            ],
            'title' => [
                'name' => '標題',
                'type' => 'title',
                'group' => 'media',
                'icon' => 'icon-biaoti',
                'style' => [
                    'paddingTop' => 0,
                    'paddingBottom' => 0,
                    'paddingLeft' => 0,
                    'topRadio' => 0,
                    'bottomRadio' => 0,
                    'bgcolor' => '#FFFFFF',
                    'textSize' => 20,
                    'weight' => 800,
                    'isLine' => 1,
                    'lineColor' => '#FF0000',
                    'isSub' => 1,
                    'subtextSize' => 14,
                    'subtextColor' => '#DDDDDD',
                    'subbackground' => '#FFCCCC',
                    'isMore' => 1,
                    'moretextColor' => '#FF0000',
                    'background' => '#F5F5F5',
                    'textColor' => '#FF0000',
                    'type' => '1'
                ],
                'params' => [
                    'title' => '標題名稱',
                    'subtitle' => '副標題名稱',
                    'moretitle' => '更多',
                    'show_icon' => 'yes',
                    'icon' => '',
                    'linkUrl' => '',
                    'sublinkUrl' => ''
                ]
            ],
            'blank' => [
                'name' => '輔助空白',
                'type' => 'blank',
                'group' => 'tools',
                'icon' => 'icon-kongbaiye',
                'style' => [
                    'height' => 20,
                    'background' => '#ffffff'
                ]
            ],
            'guide' => [
                'name' => '輔助線',
                'type' => 'guide',
                'group' => 'tools',
                'icon' => 'icon-fuzhuxian',
                'style' => [
                    'background' => '#ffffff',
                    'lineStyle' => 'solid',
                    'lineHeight' => '1',
                    'lineColor' => "#000000",
                    'paddingTop' => 10
                ]
            ],
            'richText' => [
                'name' => '富文字',
                'type' => 'richText',
                'group' => 'tools',
                'icon' => 'icon-fuwenben',
                'params' => [
                    'content' => '<p>這裡是文字的內容</p>'
                ],
                'style' => [
                    'paddingTop' => 0,
                    'paddingLeft' => 0,
                    'background' => '#ffffff'
                ]
            ],
            'service' => [
                'name' => '線上客服',
                'type' => 'service',
                'group' => 'tools',
                'icon' => 'icon-zaixiankefu',
                'params' => [
                    'type' => 'chat',// '客服型別' => chat線上聊天，phone撥打電話
                    'image' => self::$base_url . 'image/diy/service.png',
                    'phone_num' => ''
                ],
                'style' => [
                    'right' => '1',
                    'bottom' => '10',
                    'opacity' => '100'
                ]
            ],
            'product' => [
                'name' => '商品組',
                'type' => 'product',
                'group' => 'shop',
                'icon' => 'icon-shangping',
                'params' => [
                    'source' => 'auto', // choice; auto
                    'auto' => [
                        'category' => 0,
                        'productSort' => 'all', // all; sales; price
                        'showNum' => 6,
                        'productName' => 1,
                        'productPrice' => 1,
                    ]
                ],
                'style' => [
                    'background' => '#F6F6F6',
                    'display' => 'list', // list; slide
                    'column' => 2,
                    'show' => [
                        'productName' => 1,
                        'productPrice' => 1,
                        'linePrice' => 1,
                        'sellingPoint' => 0,
                        'productSales' => 0,
                        'paddingTop' => 0,
                        'paddingBottom' => 0,
                        'paddingLeft' => 10,
                    ]
                ],
                // '自動獲取' => 預設資料
                'defaultData' => [
                    [
                        'product_name' => '此處顯示商品名稱',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '99.00',
                        'line_price' => '139.00',
                        'selling_point' => '此款商品美觀大方 不容錯過',
                        'product_sales' => '100',
                    ],
                    [
                        'product_name' => '此處顯示商品名稱',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '99.00',
                        'line_price' => '139.00',
                        'selling_point' => '此款商品美觀大方 不容錯過',
                        'product_sales' => '100',
                    ],
                    [
                        'product_name' => '此處顯示商品名稱',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '99.00',
                        'line_price' => '139.00',
                        'selling_point' => '此款商品美觀大方 不容錯過',
                        'product_sales' => '100',
                    ],
                    [
                        'product_name' => '此處顯示商品名稱',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '99.00',
                        'line_price' => '139.00',
                        'selling_point' => '此款商品美觀大方 不容錯過',
                        'product_sales' => '100',
                    ]
                ],
                // '手動選擇' => 預設資料
                'data' => [
                    [
                        'product_name' => '此處顯示商品名稱',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '99.00',
                        'line_price' => '139.00',
                        'selling_point' => '此款商品美觀大方 不容錯過',
                        'product_sales' => '100',
                        'is_default' => true
                    ],
                    [
                        'product_name' => '此處顯示商品名稱',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '99.00',
                        'line_price' => '139.00',
                        'selling_point' => '此款商品美觀大方 不容錯過',
                        'product_sales' => '100',
                        'is_default' => true
                    ]
                ]
            ],
        ];
        return $data;
    }

    /**
     * 頁面diy元素預設資料
     * @return array[]
     */
    public function getDefaultItems()
    {
        return [
            'banner' => [
                'name' => '圖片輪播',
                'type' => 'banner',
                'group' => 'media',
                'icon' => 'icon-lunbotu',
                'style' => [
                    'paddingTop' => 0,
                    'paddingBottom' => 0,
                    'paddingLeft' => 0,
                    'topRadio' => 0,
                    'bottomRadio' => 0,
                    'btnColor' => '#ffffff',
                    'background' => '#ffffff',
                    'btnShape' => 'round',//rectangle 長方形，round圓形, square正方形
                    'height' => 340,
                ],
                'data' => [
                    [
                        'imgUrl' => self::$base_url . 'image/diy/banner/01.png',
                        'linkUrl' => ''
                    ],
                    [
                        'imgUrl' => self::$base_url . 'image/diy/banner/01.png',
                        'linkUrl' => ''
                    ]
                ]
            ],
            'imageSingle' => [
                'name' => '單圖組',
                'type' => 'imageSingle',
                'group' => 'media',
                'icon' => 'icon-tupian111',
                'style' => [
                    'paddingTop' => 0,
                    'paddingBottom' => 10,
                    'paddingLeft' => 10,
                    'background' => '#F2F2F2',
                    'topRadio' => 5,
                    'bottomRadio' => 5,
                ],
                'data' => [
                    [
                        'imgUrl' => self::$base_url . 'image/diy/banner/01.png',
                        'imgName' => 'image-1.jpg',
                        'linkUrl' => ''
                    ]
                ]
            ],
            'navBar' => [
                'name' => '導航組',
                'type' => 'navBar',
                'group' => 'media',
                'icon' => 'icon-mulu',
                'style' => [
                    'background' => '#ffffff',
                    'rowsNum' => 4,
                    "bgcolor" => "#f2f2f2",
                    "paddingTop" => 10,
                    "paddingBottom" => 10,
                    "paddingLeft" => 10,
                    "topRadio" => 5,
                    "bottomRadio" => 5
                ],
                'data' => [
                    [
                        'imgUrl' => self::$base_url . 'image/diy/navbar/01.png',
                        'imgName' => 'icon-1.png',
                        'linkUrl' => '',
                        'text' => '按鈕文字1',
                        'color' => '#666666'
                    ],
                    [
                        'imgUrl' => self::$base_url . 'image/diy/navbar/02.png',
                        'imgName' => 'icon-2.jpg',
                        'linkUrl' => '',
                        'text' => '按鈕文字2',
                        'color' => '#666666'
                    ],
                    [
                        'imgUrl' => self::$base_url . 'image/diy/navbar/03.png',
                        'imgName' => 'icon-3.jpg',
                        'linkUrl' => '',
                        'text' => '按鈕文字3',
                        'color' => '#666666'
                    ],
                    [
                        'imgUrl' => self::$base_url . 'image/diy/navbar/04.png',
                        'imgName' => 'icon-4.jpg',
                        'linkUrl' => '',
                        'text' => '按鈕文字4',
                        'color' => '#666666'
                    ]
                ]
            ],
            'blank' => [
                'name' => '輔助空白',
                'type' => 'blank',
                'group' => 'tools',
                'icon' => 'icon-kongbaiye',
                'style' => [
                    'height' => '',
                    'paddingTop' => '',
                    'paddingBottom' => '',
                    'paddingLeft' => '',
                    'topRadio' => '',
                    'bottomRadio' => '',
                    'bgcolor' => '',
                    'background' => '',
                ]
            ],
            'guide' => [
                'name' => '輔助線',
                'type' => 'guide',
                'group' => 'tools',
                'icon' => 'icon-fuzhuxian',
                'style' => [
                    'background' => '#f2f2f2',
                    'lineStyle' => 'solid',
                    'lineHeight' => 1,
                    'lineColor' => "#eeeeee",
                    'paddingTop' => 10,
                    'paddingLeft' => 10,
                    'paddingBottom' => 0,
                ]
            ],
            'video' => [
                'name' => '影片組',
                'type' => 'video',
                'group' => 'media',
                'icon' => 'icon-shipin',
                'params' => [
                    'videoUrl' => 'http://wxsnsdy.tc.qq.com/105/20210/snsdyvideodownload?filekey=30280201010421301f0201690402534804102ca905ce620b1241b726bc41dcff44e00204012882540400',
                    'poster' => self::$base_url . 'image/diy/video_poster.png',
                    'autoplay' => 0
                ],
                'style' => [
                    'paddingTop' => 10,
                    'height' => 190,
                    'paddingBottom' => 10,
                    'paddingLeft' => 10,
                    'topRadio' => 5,
                    'bottomRadio' => 5,
                    'bgcolor' => '#f2f2f2',
                ]
            ],
            'article' => [
                'name' => '文章組',
                'type' => 'article',
                'group' => 'media',
                'icon' => 'icon-wenzhangguanli',
                'params' => [
                    'source' => 'auto', // choice; auto
                    'auto' => [
                        'category' => 0,
                        'showNum' => 2
                    ],
                ],
                'style' => [
                    'display' => 10,
                    'background' => '#FFFFFF',
                    'bgcolor' => '#F2F2F2',
                    'paddingTop' => '',
                    'paddingBottom' => 10,
                    'paddingLeft' => 10,
                    'topRadio' => 5,
                    'bottomRadio' => 5,
                ],
                // '自動獲取' => 預設資料
                'defaultData' => [
                    [
                        'article_title' => '此處顯示文章標題',
                        'show_type' => 10,
                        'image' => self::$base_url . 'image/diy/article/01.png',
                        'views_num' => 309
                    ],
                    [
                        'article_title' => '此處顯示文章標題',
                        'show_type' => 10,
                        'image' => self::$base_url . 'image/diy/article/01.png',
                        'views_num' => 309
                    ]
                ],
                // '手動選擇' => 預設資料
                'data' => []
            ],
            'special' => [
                'name' => '頭條快報',
                'type' => 'special',
                'group' => 'media',
                'icon' => 'icon-gonggao',
                'params' => [
                    'source' => 'auto', // choice; auto
                    'auto' => [
                        'category' => 0,
                        'showNum' => 6
                    ]
                ],
                'style' => [
                    'display' => 1,
                    'image' => self::$base_url . 'image/diy/special.png',
                    'background' => '#ffffff',
                    'bgcolor' => '#f2f2f2',
                    'paddingTop' => '',
                    'paddingBottom' => '',
                    'paddingLeft' => 10,
                    'topRadio' => 5,
                    'bottomRadio' => 5
                ],
                // '自動獲取' => 預設資料
                'defaultData' => [
                    [
                        'article_title' => '此處顯示頭條快報標題'
                    ]
                ],
                // '手動選擇' => 預設資料
                'data' => []
            ],
            'notice' => [
                'name' => '公告組',
                'type' => 'notice',
                'group' => 'media',
                'icon' => 'icon-gonggao1',
                'params' => [
                    'text' => '這裡是第一條自定義公告的標題',
                    'icon' => self::$base_url . 'image/diy/notice.png'
                ],
                'style' => [
                    'padding' => 4,
                    'paddingTop' => 0,
                    'paddingBottom' => 0,
                    'paddingLeft' => 10,
                    'topRadio' => 5,
                    'bottomRadio' => 5,
                    'bgcolor' => '',
                    'background' => '#ffffff',
                    'textColor' => '#000000',
                ]
            ],
            'richText' => [
                'name' => '富文字',
                'type' => 'richText',
                'group' => 'tools',
                'icon' => 'icon-fuwenben',
                'params' => [
                    'content' => '<p>這裡是文字的內容</p>'
                ],
                'style' => [
                    'paddingTop' => 10,
                    'paddingLeft' => 10,
                    'background' => '#ffffff'
                ]
            ],
            'window' => [
                'name' => '圖片櫥窗',
                'type' => 'window',
                'group' => 'media',
                'icon' => 'icon-tupian11',
                'style' => [
                    'paddingTop' => 0,
                    'paddingLeft' => 10,
                    'paddingBottom' => 10,
                    'background' => '#f2f2f2',
                    'layout' => 4
                ],
                'data' => [
                    [
                        'imgUrl' => self::$base_url . 'image/diy/window/01.jpg',
                        'linkUrl' => ''
                    ],
                    [
                        'imgUrl' => self::$base_url . 'image/diy/window/02.jpg',
                        'linkUrl' => ''
                    ],
                    [
                        'imgUrl' => self::$base_url . 'image/diy/window/03.jpg',
                        'linkUrl' => ''
                    ],
                    [
                        'imgUrl' => self::$base_url . 'image/diy/window/04.jpg',
                        'linkUrl' => ''
                    ]
                ],
                'dataNum' => 4
            ],
            'product' => [
                'name' => '商品組',
                'type' => 'product',
                'group' => 'shop',
                'icon' => 'icon-shangping',
                'params' => [
                    'source' => 'auto', // choice; auto
                    'auto' => [
                        'category' => 0,
                        'productSort' => 'all', // all; sales; price
                        'showNum' => 6
                    ],
                    'column' => 2,
                    'display' => 'list',
                    'productName' => 1,
                    'productPrice' => 1,
                    'linePrice' => 1,
                ],
                'style' => [
                    'paddingTop' => '',
                    'paddingBottom' => 10,
                    'paddingLeft' => 10,
                    'topRadio' => 5,
                    'bottomRadio' => 5,
                    'bgcolor' => '#F2f2f2',
                    'background' => '#Ffffff',
                    'display' => 'list', // list; slide
                    'column' => 2,
                    'product_name_color' => '#333333',
                    'product_price_color' => '#FF4C01',
                    'line_price_color' => '#999999',
                    'show' => [
                        'productName' => 1,
                        'productPrice' => 1,
                        'linePrice' => 1,
                        'sellingPoint' => 0,
                        'productSales' => 0,
                    ],
                ],
                // '自動獲取' => 預設資料
                'defaultData' => [
                    [
                        'product_name' => '此處顯示商品名稱',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '99.00',
                        'line_price' => '139.00',
                        'selling_point' => '此款商品美觀大方 不容錯過',
                        'product_sales' => '100',
                    ],
                    [
                        'product_name' => '此處顯示商品名稱',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '99.00',
                        'line_price' => '139.00',
                        'selling_point' => '此款商品美觀大方 不容錯過',
                        'product_sales' => '100',
                    ],
                    [
                        'product_name' => '此處顯示商品名稱',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '99.00',
                        'line_price' => '139.00',
                        'selling_point' => '此款商品美觀大方 不容錯過',
                        'product_sales' => '100',
                    ],
                    [
                        'product_name' => '此處顯示商品名稱',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '99.00',
                        'line_price' => '139.00',
                        'selling_point' => '此款商品美觀大方 不容錯過',
                        'product_sales' => '100',
                    ]
                ],
                // '手動選擇' => 預設資料
                'data' => [
                    [
                        'product_name' => '此處顯示商品名稱',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '99.00',
                        'line_price' => '139.00',
                        'selling_point' => '此款商品美觀大方 不容錯過',
                        'product_sales' => '100',
                        'is_default' => true
                    ],
                    [
                        'product_name' => '此處顯示商品名稱',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '99.00',
                        'line_price' => '139.00',
                        'selling_point' => '此款商品美觀大方 不容錯過',
                        'product_sales' => '100',
                        'is_default' => true
                    ]
                ]
            ],
            'coupon' => [
                'name' => '優惠券組',
                'type' => 'coupon',
                'group' => 'shop',
                'icon' => 'icon-hongbao',
                'style' => [
                    'paddingTop' => 10,
                    'paddingBottom' => 10,
                    'paddingLeft' => 10,
                    'topRadio' => 5,
                    'bottomRadio' => 5,
                    'bgcolor' => '#f2f2f2',
                    'background' => '#ff4c01',
                    'descolor' => '#666666',
                    'pricecolor' => '#ff4c01',
                    'cillcolor' => '#ff4c01',
                    'btncolor' => '#ff4c01',
                    'btnTxtcolor' => '#FFFFFF',
                    'btnRadio' => 24,
                    'bgtype' => 2,
                    'bgimage' => self::$base_url . 'image/diy/active/coupon.png',
                ],
                'params' => [
                    'btntext' => '立即領取',
                    'limit' => 5
                ],
                'data' => [
                    [
                        'color' => 'red',
                        'reduce_price' => '10',
                        'min_price' => '100.00'
                    ],
                    [
                        'color' => 'violet',
                        'reduce_price' => '10',
                        'min_price' => '100.00'
                    ]
                ]
            ],
            'assembleProduct' => [
                'name' => '拼團商品組',
                'type' => 'assembleProduct',
                'group' => 'shop',
                'icon' => 'icon-pintuangou',
                'params' => [
                    'showNum' => 3,
                    'title' => '標題',
                    'more' => '更多',
                    'btntext' => '去開團'
                ],
                'style' => [
                    'paddingTop' => '',
                    'paddingBottom' => 10,
                    'paddingLeft' => 10,
                    'topRadio' => 5,
                    'bottomRadio' => 5,
                    'bgcolor' => '#f2f2f2',
                    'background' => '',
                    'titleType' => 2,
                    'moreSize' => 12,
                    'moreColor' => '#fff',
                    'product_name' => 1,
                    'product_price' => 1,
                    'product_lineprice' => 1,
                    'product_btn' => 1,
                    'product_numberbtn' => 1,
                    'productLine_btnBackground' => '#ff4c01',
                    'productLine_btnRadius' => 30,
                    'title_color1' => '#fc4528',
                    'title_color2' => '#fc7639',
                    'number_color' => '#FFFFFF',
                    'title_image' => self::$base_url . 'image/diy/active/assemble.png',
                    'bgimage' => self::$base_url . 'image/diy/active/assemble_bgimage.png',
                    'product_imgRadio' => 0,
                    'product_topRadio' => 5,
                    'product_bottomRadio' => 5,
                    'productName_color' => '#333333',
                    'productPrice_color' => '#ff4c01',
                    'productBg_color' => '#ffffff',
                    'titleColor' => '#333333',
                    'titleSize' => 14,
                    "productLine_btnColor" => "#ffffff",
                    "productLine_color" => "#999999",
                ],
                // '自動獲取' => 預設資料
                'defaultData' => [
                    [
                        'product_name' => '此處是拼團商品',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'selling_point' => '此款商品美觀大方 價效比較高 不容錯過',
                        'assemble_price' => '99.00',
                        'line_price' => '139.00',
                    ],
                    [
                        'product_name' => '此處是拼團商品',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'selling_point' => '此款商品美觀大方 價效比較高 不容錯過',
                        'assemble_price' => '99.00',
                        'line_price' => '139.00',
                    ],
                    [
                        'product_name' => '此處是拼團商品',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'selling_point' => '此款商品美觀大方 價效比較高 不容錯過',
                        'assemble_price' => '99.00',
                        'line_price' => '139.00',
                    ],
                    [
                        'product_name' => '此處是拼團商品',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'selling_point' => '此款商品美觀大方 價效比較高 不容錯過',
                        'assemble_price' => '99.00',
                        'line_price' => '139.00',
                    ]
                ],
                // '手動選擇' => 預設資料
                'data' => [
                    [
                        'product_name' => '此處是拼團商品',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'selling_point' => '此款商品美觀大方 價效比較高 不容錯過',
                        'assemble_price' => '99.00',
                        'line_price' => '139.00',
                        'is_default' => true
                    ],
                    [
                        'product_name' => '此處是拼團商品',
                        'image' => self::$base_url . 'image/diy/product/01.png',
                        'selling_point' => '此款商品美觀大方 價效比較高 不容錯過',
                        'assemble_price' => '99.00',
                        'line_price' => '139.00',
                        'is_default' => true
                    ]
                ]
            ],
            'bargainProduct' => [
                'name' => '砍價商品組',
                'type' => 'bargainProduct',
                'group' => 'shop',
                'icon' => 'icon-kanjia1',
                'params' => [
                    'showNum' => 4,
                    'title' => '標題',
                    'more' => '更多',
                ],
                'style' => [
                    'paddingTop' => '',
                    'paddingBottom' => 10,
                    'paddingLeft' => 10,
                    'topRadio' => 5,
                    'bottomRadio' => 5,
                    'bgcolor' => '#f2f2f2',
                    'background' => '#ffffff',
                    'titleType' => 2,
                    'title_image' => self::$base_url . 'image/diy/active/bargain.png',
                    'bgimage' => self::$base_url . 'image/diy/active/bargain_bgimage.png',
                    'moreSize' => 12,
                    'moreColor' => '#ffffff',
                    'product_name' => 1,
                    'product_price' => 1,
                    'product_lineprice' => 1,
                    'product_btn' => 1,
                    'product_numberbtn' => 1,
                    'productLine_btnBackground' => '',
                    'productLine_btnRadius' => '',
                    'product_sales' => 1,
                    'title_color1' => '',
                    'title_color2' => '',
                    'number_color' => '',
                    'product_imgRadio' => 5,
                    'productBg_color' => '#ffffff',
                    'product_topRadio' => 5,
                    'product_bottomRadio' => 5,
                    'productName_color' => '#333333',
                    'productPrice_color' => '#ff4c01',
                    'titleColor' => '#333333',
                    'titleSize' => '14',
                    'total_sales' => 1,
                    'salesColor' => '#ffffff',
                    'bgSales' => '#ff6417'
                ],
                // '自動獲取' => 預設資料
                'defaultData' => [
                    [
                        'product_name' => '此處是砍價商品',
                        'product_image' => self::$base_url . 'image/diy/product/01.png',
                        'floor_price' => '0.01',
                        'original_price' => '139.00',
                    ],
                    [
                        'product_name' => '此處是砍價商品',
                        'product_image' => self::$base_url . 'image/diy/product/01.png',
                        'floor_price' => '0.01',
                        'original_price' => '139.00',
                    ],
                ],
                // '手動選擇' => 預設資料
                'data' => [
                    [
                        'product_name' => '此處是砍價商品',
                        'product_image' => self::$base_url . 'image/diy/product/01.png',
                        'floor_price' => '0.01',
                        'original_price' => '139.00',
                    ],
                    [
                        'product_name' => '此處是砍價商品',
                        'product_image' => self::$base_url . 'image/diy/product/01.png',
                        'floor_price' => '0.01',
                        'original_price' => '139.00',
                    ],
                ]
            ],
            'seckillProduct' => [
                'name' => '秒殺商品組',
                'type' => 'seckillProduct',
                'group' => 'shop',
                'icon' => 'icon-miaosha11',
                'params' => [
                    'showNum' => 3,
                    'title' => '限時秒殺',
                    'more' => '更多',
                    'btntext' => "去搶購"
                ],
                'style' => [
                    'paddingTop' => '',
                    'paddingBottom' => 10,
                    'paddingLeft' => 10,
                    'topRadio' => 5,
                    'bottomRadio' => 5,
                    'bgcolor' => '#f2f2f2',
                    'background' => '#ffffff',
                    'titleType' => 2,
                    'title_image' => self::$base_url . 'image/diy/active/seckill.png',
                    'bgimage' => self::$base_url . 'image/diy/active/seckill_bgimage.png',
                    'moreSize' => 12,
                    'moreColor' => '#FFFFFF',
                    'product_name' => 1,
                    'product_price' => 1,
                    'product_lineprice' => 1,
                    'product_imgRadio' => 5,
                    'productBg_color' => '#ffffff',
                    'product_topRadio' => 0,
                    'product_bottomRadio' => '',
                    'productName_color' => '#333',
                    'productPrice_color' => '#ff4c01',
                    'titleColor' => '#333333',
                    'titleSize' => '14',
                    "product_schedule" => 1,
                    "product_btn" => 1,
                    "title_color1" => "#ffffff",
                    "number_color" => "#ff4c01",
                    "productLine_color" => "#999999",
                    "productLine_btnBackground" => "#ff4c01",
                    "productLine_btnColor" => "#ffffff",
                    "productLine_btnRadius" => 30,
                    "productSlider_color" => "#ff4c01"
                ],
                // '手動選擇' => 預設資料
                'data' => [
                    [
                        'product_name' => '此處是秒殺商品',
                        'product_image' => self::$base_url . 'image/diy/product/01.png',
                        'seckill_price' => '69.00',
                        'original_price' => '139.00',
                    ],
                    [
                        'product_name' => '此處是秒殺商品',
                        'product_image' => self::$base_url . 'image/diy/product/01.png',
                        'seckill_price' => '69.00',
                        'original_price' => '139.00',
                    ],
                    [
                        'product_name' => '此處是秒殺商品',
                        'product_image' => self::$base_url . 'image/diy/product/01.png',
                        'seckill_price' => '69.00',
                        'original_price' => '139.00',
                    ],
                ]
            ],
            'previewProduct' => [
                'name' => '預告商品組',
                'type' => 'previewProduct',
                'group' => 'shop',
                'icon' => 'icon-yushoucuifu',
                'params' => [
                    'showNum' => 1
                ],
                'style' => [
                    'paddingTop' => '',
                    'paddingBottom' => 10,
                    'paddingLeft' => 10,
                    'topRadio' => 5,
                    'bottomRadio' => 5,
                    'bgcolor' => '#f2f2f2',
                    'background' => '#ffffff',
                    'titleType' => 2,
                    'title_image' => self::$base_url . 'image/diy/active/preview.png',
                    'bgimage' => self::$base_url . 'image/diy/active/preview_bg.png',
                    'moreSize' => 12,
                    'moreColor' => '#ffffff',
                    'product_name' => 1,
                    'product_price' => 1,
                    'product_lineprice' => 1,
                    'product_btn' => 1,
                    'product_numberbtn' => 1,
                    'productLine_btnBackground' => '',
                    'productLine_btnRadius' => '',
                    'product_tag' => 1,
                    'title_color1' => '',
                    'title_color2' => '',
                    'number_color' => '',
                    'product_imgRadio' => 5,
                    'productBg_color' => '#ffffff',
                    'product_topRadio' => 5,
                    'product_bottomRadio' => 5,
                    'productName_color' => '#333333',
                    'productPrice_color' => '#ff4c01',
                    'titleColor' => '#333333',
                    'titleSize' => '14',
                    'total_sales' => 1,
                    'tagColor' => '#ffffff',
                    'bgTag' => '#ff6417'
                ],
                // 預設資料
                'data' => [
                    [
                        'product_name' => '此處是預告商品',
                        'product_image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '69.00',
                        'original_price' => '139.00',
                    ],
                    [
                        'product_name' => '此處是預告商品',
                        'product_image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '69.00',
                        'original_price' => '139.00',
                    ],
                    [
                        'product_name' => '此處是預告商品',
                        'product_image' => self::$base_url . 'image/diy/product/01.png',
                        'product_price' => '69.00',
                        'original_price' => '139.00',
                    ],
                ]
            ],
            'store' => [
                'name' => '線下門店',
                'type' => 'store',
                'group' => 'shop',
                'icon' => 'icon-stores',
                'params' => [
                    'source' => 'auto', // choice; auto
                    'auto' => [
                        'showNum' => 6
                    ]
                ],
                'style' => [
                    'background' => '#FFFFFF',
                    'bgcolor' => '#f2f2f2',
                    'paddingTop' => 0,
                    'paddingBottom' => 10,
                    'paddingLeft' => 10,
                    'topRadio' => 5,
                    'bottomRadio' => 5
                ],
                // '自動獲取' => 預設資料
                'defaultData' => [
                    [
                        'shop_name' => '此處顯示門店名稱',
                        'logo_image' => self::$base_url . 'image/diy/circular.png',
                        'phone' => '010-6666666',
                        'region' => [
                            'province' => 'xx省',
                            'city' => 'xx市',
                            'region' => 'xx區'
                        ],
                        'address' => 'xx街道',
                    ],
                    [
                        'shop_name' => '此處顯示門店名稱',
                        'logo_image' => self::$base_url . 'image/diy/circular.png',
                        'phone' => '010-6666666',
                        'region' => [
                            'province' => 'xx省',
                            'city' => 'xx市',
                            'region' => 'xx區'
                        ],
                        'address' => 'xx街道',
                    ],
                ],
                // '手動選擇' => 預設資料
                'data' => [
                    [
                        'shop_name' => '此處顯示門店名稱',
                        'logo_image' => self::$base_url . 'image/diy/circular.png',
                        'phone' => '010-6666666',
                        'region' => [
                            'province' => 'xx省',
                            'city' => 'xx市',
                            'region' => 'xx區'
                        ],
                        'address' => 'xx街道',
                    ],
                ]
            ],
            'wxlive' => [
                'name' => '微信直播',
                'type' => 'wxlive',
                'group' => 'shop',
                'icon' => 'icon-shipinbofang',
                'params' => [
                    'source' => 'auto', // choice; auto
                    'showNum' => 2
                ],
                'style' => [
                    'background_image' => self::$base_url . 'image/diy/active/live.png',
                    'color' => '#700505',
                    'background' => '#FFFFFF',
                    'bgcolor' => '#f2f2f2',
                    'paddingTop' => 10,
                    'paddingBottom' => 10,
                    'paddingLeft' => 10,
                    'topRadio' => 5,
                    'bottomRadio' => 5
                ],
                // '自動獲取' => 預設資料
                'defaultData' => [
                    [
                        'shop_name' => '直播間名稱',
                        'logo_image' => self::$base_url . 'image/diy/circular.png',
                        'name' => '主播暱稱',
                    ],
                    [
                        'shop_name' => '直播間名稱',
                        'logo_image' => self::$base_url . 'image/diy/circular.png',
                        'name' => '主播暱稱',
                    ],
                ],
                // '手動選擇' => 預設資料
                'data' => [
                    [
                        'shop_name' => '直播間名稱',
                        'logo_image' => self::$base_url . 'image/diy/circular.png',
                        'name' => '主播暱稱',
                    ],
                    [
                        'shop_name' => '直播間名稱',
                        'logo_image' => self::$base_url . 'image/diy/circular.png',
                        'name' => '主播暱稱',
                    ],
                ]
            ],
            'service' => [
                'name' => '線上客服',
                'type' => 'service',
                'group' => 'tools',
                'icon' => 'icon-zaixiankefu',
                'params' => [
                    'type' => 'chat',     // '客服型別' => chat線上聊天，phone撥打電話
                    'image' => self::$base_url . 'image/diy/service.png',
                    'phone_num' => ''
                ],
                'style' => [
                    'right' => 1,
                    'bottom' => 10,
                    'opacity' => 100
                ]
            ],
            'title' => [
                'name' => '標題',
                'type' => 'title',
                'group' => 'media',
                'icon' => 'icon-biaoti',
                'style' => [
                    'paddingTop' => 0,
                    'paddingBottom' => 0,
                    'paddingLeft' => 0,
                    'topRadio' => 0,
                    'bottomRadio' => 0,
                    'bgcolor' => '#FFFFFF',
                    'textSize' => 20,
                    'weight' => 800,
                    'isLine' => 1,
                    'lineColor' => '#ff4c01',
                    'isSub' => 1,
                    'subtextSize' => 14,
                    'subtextColor' => '#DDDDDD',
                    'subbackground' => '#FFCCCC',
                    'isMore' => 1,
                    'moretextColor' => '#FF0000',
                    'background' => '#F5F5F5',
                    'textColor' => '#ff4c01',
                    'type' => '1'
                ],
                'params' => [
                    'title' => '標題名稱',
                    'subtitle' => '副標題名稱',
                    'moretitle' => '更多',
                    'show_icon' => 'yes',
                    'icon' => '',
                    'linkUrl' => '',
                    'sublinkUrl' => ''
                ]
            ],
            'videoLive' => [
                'name' => '影片號直播',
                'type' => 'videoLive',
                'group' => 'tools',
                'icon' => 'icon-shipinbofang',
                'style' => [
                    'right' => 1,
                    'bottom' => 60,
                    'opacity' => 100,
                ],
                'params' => [
                    'finderUserName' => '',
                    'image' => self::$base_url . 'image/diy/videoLive.png',
                ],
            ]
        ];
    }

    /**
     * 格式化頁面資料
     * @param $json
     * @return mixed
     */
    public function getPageDataAttr($json)
    {
        // 舊版資料轉義
        $array = $this->_transferToNewData($json);
        // 合併預設資料
        return $this->_mergeDefaultData($array);
    }

    /**
     * 自動轉換data為json格式
     * @param $value
     * @return false|string
     */
    public function setPageDataAttr($value)
    {
        return json_encode($value ?: ['items' => []]);
    }

    /**
     * diy頁面詳情
     */
    public static function detail($page_id)
    {
        return (new static())->find($page_id);
    }

    /**
     * diy頁面詳情
     */
    public static function getHomePage()
    {
        return (new static())->where('page_type', '10')->find();
    }

    /**
     * 舊版資料轉義為新版格式
     * @param $json
     * @return array
     */
    private function _transferToNewData($json)
    {
        $array = json_decode($json, true);
        $items = $array['items'];
        if (isset($items['page'])) {
            unset($items['page']);
        }
        foreach ($items as &$item) {
            isset($item['data']) && $item['data'] = array_values($item['data']);
        }
        return [
            'page' => isset($array['page']) ? $array['page'] : $array['items']['page'],
            'items' => array_values(array_filter($items))
        ];
    }

    /**
     * 合併預設資料
     * @param $array
     * @return mixed
     */
    private function _mergeDefaultData($array)
    {
        $array['page'] = array_merge_multiple($this->getDefaultPage(), $array['page']);
        $defaultItems = $this->getDefaultItems();
        foreach ($array['items'] as &$item) {
            if (isset($defaultItems[$item['type']])) {
                array_key_exists('data', $item) && $defaultItems[$item['type']]['data'] = [];
                $item = array_merge_multiple($defaultItems[$item['type']], $item);
            }
        }
        return $array;
    }

    /**
     * 首頁預設設定
     */
    public static function getDefault($page_type = 10)
    {
        $detail = (new static())->where('is_delete', 0)->where('page_type', $page_type)->order('is_default desc,page_id desc')->find();
        if (!$detail) {
            self::addDefault($page_type, self::$app_id);
            $detail = (new static())->where('is_delete', 0)
                ->where('page_type', $page_type)
                ->order('is_default desc,page_id desc')
                ->find();
        }
        return $detail;
    }

    /**
     * 新增預設首頁和個人中心
     */
    public static function addDefault($page_type, $app_id)
    {
        if ($page_type == 10) {
            $page_data = '{"page":{"type":"page","name":"\u9875\u9762\u8bbe\u7f6e","params":{"name":"\u9996\u9875\u88c5\u4fee","title":"\u9875\u9762\u6807\u9898","title_type":"image","share_title":"\u5206\u4eab\u6807\u9898","share_img":"https:\/\/business3.jjjshop.net\/image\/diy\/logo.png","toplogo":"https:\/\/business3.jjjshop.net\/uploads\/20231009\/cbf03fea50cd173627f85f215f7cba48.png","icon":"icon-biaoti"},"style":{"titleTextColor":"black","titleBackgroundColor":"#ff4c01"},"category":{"open":1,"color":"#FFFFFF"}},"items":[{"name":"\u56fe\u7247\u8f6e\u64ad","type":"banner","group":"media","icon":"icon-lunbotu","style":{"paddingTop":10,"paddingBottom":0,"paddingLeft":10,"topRadio":10,"bottomRadio":10,"btnColor":"#FF5B5B","background":"#f2f2f2","btnShape":"round","height":340,"imgShape":"round"},"data":[{"imgUrl":"https:\/\/business3.jjjshop.net\/uploads\/20231008\/f2033d3904916bc881609c0e1ccc08fc.png","linkUrl":"pages\/product\/category","name":"\u94fe\u63a5\u5230 \u9875\u9762 \u5206\u7c7b"},{"imgUrl":"https:\/\/business3.jjjshop.net\/uploads\/20231008\/f2033d3904916bc881609c0e1ccc08fc.png","linkUrl":""},{"imgUrl":"https:\/\/business3.jjjshop.net\/uploads\/20231008\/f2033d3904916bc881609c0e1ccc08fc.png","linkUrl":""}]},{"name":"\u5bfc\u822a\u7ec4","type":"navBar","group":"media","icon":"icon-mulu","style":{"background":"#ffffff","rowsNum":5,"bgcolor":"#f2f2f2","paddingTop":10,"paddingBottom":0,"paddingLeft":10,"topRadio":8,"bottomRadio":8},"data":[{"imgUrl":"https:\/\/business3.jjjshop.net\/uploads\/20231008\/58632a19182264edfaa91ff6b2f0807d.png","imgName":"icon-1.png","linkUrl":"pages\/index\/index","text":"\u5bb6\u7528\u7535\u5668","color":"#666666","name":"\u94fe\u63a5\u5230 \u9875\u9762 \u9996\u9875"},{"imgUrl":"https:\/\/business3.jjjshop.net\/uploads\/20231008\/f5df0b0d8e81dba16fc750a17160ea99.png","imgName":"icon-2.jpg","linkUrl":"pages\/index\/index","text":"\u6570\u7801\u4ea7\u54c1","color":"#666666","name":"\u94fe\u63a5\u5230 \u9875\u9762 \u9996\u9875"},{"imgUrl":"https:\/\/business3.jjjshop.net\/uploads\/20231008\/6e586e48a7a7adf9d927de3cbed14829.png","imgName":"icon-3.jpg","linkUrl":"pages\/index\/index","text":"\u6444\u5f71\u8bbe\u5907","color":"#666666","name":"\u94fe\u63a5\u5230 \u9875\u9762 \u9996\u9875"},{"imgUrl":"https:\/\/business3.jjjshop.net\/uploads\/20231008\/681f185e9410e17d2c83cd7c6e7c79eb.png","imgName":"icon-4.jpg","linkUrl":"pages\/index\/index","text":"\u8f6c\u76d8\u62bd\u5956","color":"#666666","name":"\u94fe\u63a5\u5230 \u9875\u9762 \u9996\u9875"},{"imgUrl":"https:\/\/business3.jjjshop.net\/uploads\/20231008\/2555256dbbafab207983ea786de4fa06.png","imgName":"icon-1.png","linkUrl":"pages\/index\/index","text":"\u9886\u5238\u4e2d\u5fc3","color":"#666666","name":"\u94fe\u63a5\u5230 \u9875\u9762 \u9996\u9875"},{"imgUrl":"https:\/\/business3.jjjshop.net\/uploads\/20231008\/afa436d9fadcaddd417e0a65c8d92659.png","imgName":"icon-1.png","linkUrl":"pages\/plus\/signin\/signin","text":"\u7b7e\u5230\u6709\u793c","color":"#666666","name":"\u94fe\u63a5\u5230 \u8425\u9500 \u7b7e\u5230"},{"imgUrl":"https:\/\/business3.jjjshop.net\/uploads\/20231008\/de2d771042702ac5d237aa1410b07345.png","imgName":"icon-1.png","linkUrl":"pages\/index\/index","text":"\u4e07\u80fd\u8868\u5355","color":"#666666","name":"\u94fe\u63a5\u5230 \u9875\u9762 \u9996\u9875"},{"imgUrl":"https:\/\/business3.jjjshop.net\/uploads\/20231008\/d74cff55c4cd51009d9dfa9b52795d1e.png","imgName":"icon-1.png","linkUrl":"pages\/index\/index","text":"\u793c\u5305\u597d\u7269","color":"#666666","name":"\u94fe\u63a5\u5230 \u9875\u9762 \u9996\u9875"},{"imgUrl":"https:\/\/business3.jjjshop.net\/uploads\/20231008\/dafe789cb255290b83e0218cdb413d53.png","imgName":"icon-1.png","linkUrl":"pages\/index\/index","text":"\u751f\u6d3b\u7f8e\u5c45","color":"#666666","name":"\u94fe\u63a5\u5230 \u9875\u9762 \u9996\u9875"},{"imgUrl":"https:\/\/business3.jjjshop.net\/uploads\/20231009\/ea2bced3789bfd17995d548eda403961.png","imgName":"icon-1.png","linkUrl":"pages\/index\/index","text":"\u6700\u65b0\u8d44\u8baf","color":"#666666","name":"\u94fe\u63a5\u5230 \u9875\u9762 \u9996\u9875"}]},{"name":"\u516c\u544a\u7ec4","type":"notice","group":"media","icon":"icon-gonggao1","params":{"text":"\u6b22\u8fce\u9009\u8d2d\uff01\u53cc\u8282\u540c\u5e86\u4f18\u60e0\u6b63\u5f53\u65f6\uff01","icon":"https:\/\/business3.jjjshop.net\/uploads\/20231011\/8b7c191e1b878247efd33aafee569b90.png"},"style":{"padding":6,"paddingTop":10,"paddingBottom":0,"paddingLeft":10,"topRadio":5,"bottomRadio":5,"bgcolor":"#F2F2F2","background":"#Ffffff","textColor":"#333333"}},{"name":"\u4f18\u60e0\u5238\u7ec4","type":"coupon","group":"shop","icon":"icon-31hongbao","style":{"paddingTop":10,"paddingBottom":0,"paddingLeft":10,"topRadio":8,"bottomRadio":8,"bgcolor":"#f2f2f2","background":"#FCF5F0","descolor":"#666666","pricecolor":"#ff4c01","cillcolor":"#ff4c01","btncolor":"#FF4C16","btnTxtcolor":"#ffffff","btnRadio":24,"bgtype":1,"bgimage":"https:\/\/business3.jjjshop.net\/image\/diy\/active\/coupon.png"},"params":{"btntext":"\u7acb\u5373\u9886\u53d6","limit":8},"data":[{"color":"red","reduce_price":"10","min_price":"100.00"},{"color":"violet","reduce_price":"10","min_price":"100.00"}]},{"name":"\u79d2\u6740\u5546\u54c1\u7ec4","type":"seckillProduct","group":"shop","icon":"icon-miaosha1","params":{"showNum":4,"title":"\u9650\u65f6\u79d2\u6740","more":"\u66f4\u591a","btntext":"\u53bb\u62a2\u8d2d"},"style":{"paddingTop":10,"paddingBottom":0,"paddingLeft":10,"topRadio":8,"bottomRadio":8,"bgcolor":"#f2f2f2","background":"#ffffff","titleType":2,"title_image":"https:\/\/business3.jjjshop.net\/uploads\/20231010\/867a8542324d00b6bbaeb178eda70ef3.png","bgimage":"https:\/\/business3.jjjshop.net\/image\/diy\/active\/seckill_bgimage.png","moreSize":12,"moreColor":"#FFFFFF","product_name":1,"product_price":1,"product_lineprice":1,"product_imgRadio":5,"productBg_color":"","product_topRadio":0,"product_bottomRadio":"","productName_color":"#333","productPrice_color":"#ff4c01","titleColor":"#333333","titleSize":"14","product_schedule":1,"product_btn":1,"title_color1":"#FFFFFF","number_color":"#ff4c01","productLine_color":"#999999","productLine_btnBackground":"#ff4c01","productLine_btnColor":"#ffffff","productLine_btnRadius":30,"productSlider_color":"#ff4c01","color":"#ffffff","title_color2":"#FFFFFF"},"data":[{"product_name":"\u6b64\u5904\u662f\u79d2\u6740\u5546\u54c1","product_image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","seckill_price":"69.00","original_price":"139.00"},{"product_name":"\u6b64\u5904\u662f\u79d2\u6740\u5546\u54c1","product_image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","seckill_price":"69.00","original_price":"139.00"},{"product_name":"\u6b64\u5904\u662f\u79d2\u6740\u5546\u54c1","product_image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","seckill_price":"69.00","original_price":"139.00"}]},{"name":"\u62fc\u56e2\u5546\u54c1\u7ec4","type":"assembleProduct","group":"shop","icon":"icon-pintuangou","params":{"showNum":10,"title":"\u6807\u9898","more":"\u66f4\u591a","btntext":"\u4e00\u8d77\u56e2"},"style":{"paddingTop":10,"paddingBottom":0,"paddingLeft":10,"topRadio":8,"bottomRadio":8,"bgcolor":"#f2f2f2","background":"#ffffff","titleType":2,"moreSize":12,"moreColor":"#fff","product_name":1,"product_price":1,"product_lineprice":1,"product_btn":1,"product_numberbtn":1,"productLine_btnBackground":"#ff4c01","productLine_btnRadius":30,"title_color1":"#fc4528","title_color2":"#fc7639","number_color":"#FFFFFF","title_image":"https:\/\/business3.jjjshop.net\/uploads\/20231010\/5c26bbd81d72db26a8e3c4aa55ea0e74.png","bgimage":"https:\/\/business3.jjjshop.net\/image\/diy\/active\/assemble_bgimage.png","product_imgRadio":5,"product_topRadio":0,"product_bottomRadio":0,"productName_color":"#333333","productPrice_color":"#ff4c01","productBg_color":"#FFFFFF","titleColor":"#333333","titleSize":14,"productLine_btnColor":"#ffffff","productLine_color":"#999999"},"defaultData":[{"product_name":"\u6b64\u5904\u662f\u62fc\u56e2\u5546\u54c1","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u6027\u4ef7\u6bd4\u8f83\u9ad8 \u4e0d\u5bb9\u9519\u8fc7","assemble_price":"99.00","line_price":"139.00"},{"product_name":"\u6b64\u5904\u662f\u62fc\u56e2\u5546\u54c1","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u6027\u4ef7\u6bd4\u8f83\u9ad8 \u4e0d\u5bb9\u9519\u8fc7","assemble_price":"99.00","line_price":"139.00"},{"product_name":"\u6b64\u5904\u662f\u62fc\u56e2\u5546\u54c1","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u6027\u4ef7\u6bd4\u8f83\u9ad8 \u4e0d\u5bb9\u9519\u8fc7","assemble_price":"99.00","line_price":"139.00"},{"product_name":"\u6b64\u5904\u662f\u62fc\u56e2\u5546\u54c1","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u6027\u4ef7\u6bd4\u8f83\u9ad8 \u4e0d\u5bb9\u9519\u8fc7","assemble_price":"99.00","line_price":"139.00"}],"data":[{"product_name":"\u6b64\u5904\u662f\u62fc\u56e2\u5546\u54c1","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u6027\u4ef7\u6bd4\u8f83\u9ad8 \u4e0d\u5bb9\u9519\u8fc7","assemble_price":"99.00","line_price":"139.00","is_default":true},{"product_name":"\u6b64\u5904\u662f\u62fc\u56e2\u5546\u54c1","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u6027\u4ef7\u6bd4\u8f83\u9ad8 \u4e0d\u5bb9\u9519\u8fc7","assemble_price":"99.00","line_price":"139.00","is_default":true}]},{"name":"\u780d\u4ef7\u5546\u54c1\u7ec4","type":"bargainProduct","group":"shop","icon":"icon-kanjia1","params":{"showNum":4,"title":"\u6807\u9898","more":"\u66f4\u591a"},"style":{"paddingTop":10,"paddingBottom":0,"paddingLeft":10,"topRadio":8,"bottomRadio":8,"bgcolor":"#f2f2f2","background":"#ffffff","titleType":2,"title_image":"https:\/\/business3.jjjshop.net\/uploads\/20231010\/57b7d662a9c468b68187321e87efc0f5.png","bgimage":"https:\/\/business3.jjjshop.net\/image\/diy\/active\/bargain_bgimage.png","moreSize":12,"moreColor":"#ffffff","product_name":1,"product_price":1,"product_lineprice":1,"product_btn":1,"product_numberbtn":1,"productLine_btnBackground":"","productLine_btnRadius":"","product_sales":1,"title_color1":"","title_color2":"","number_color":"","product_imgRadio":5,"productBg_color":"#fff","product_topRadio":0,"product_bottomRadio":0,"productName_color":"#333333","productPrice_color":"#ff4c01","titleColor":"#333333","titleSize":"14","total_sales":1,"salesColor":"#FFFFFF","bgSales":"#ff4c01","productLine_color":"#999999"},"defaultData":[{"product_name":"\u6b64\u5904\u662f\u780d\u4ef7\u5546\u54c1","product_image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","floor_price":"0.01","original_price":"139.00"},{"product_name":"\u6b64\u5904\u662f\u780d\u4ef7\u5546\u54c1","product_image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","floor_price":"0.01","original_price":"139.00"}],"data":[{"product_name":"\u6b64\u5904\u662f\u780d\u4ef7\u5546\u54c1","product_image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","floor_price":"0.01","original_price":"139.00"},{"product_name":"\u6b64\u5904\u662f\u780d\u4ef7\u5546\u54c1","product_image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","floor_price":"0.01","original_price":"139.00"}]},{"name":"\u9884\u544a\u5546\u54c1\u7ec4","type":"previewProduct","group":"shop","icon":"icon-yushoucuifu","params":{"showNum":"4","more":"\u66f4\u591a"},"style":{"paddingTop":10,"paddingBottom":0,"paddingLeft":10,"topRadio":10,"bottomRadio":10,"bgcolor":"#f2f2f2","background":"#ffffff","titleType":2,"title_image":"https:\/\/business3.jjjshop.net\/uploads\/20231010\/0fd656a475f3fb01e67fbd0f359ced71.png","bgimage":"https:\/\/business3.jjjshop.net\/uploads\/20231010\/8740daa2d6172cba431e11db8a9f0b33.png","moreSize":12,"moreColor":"#ffffff","product_name":1,"product_price":1,"product_lineprice":1,"product_btn":1,"product_numberbtn":1,"productLine_btnBackground":"","productLine_btnRadius":"","product_tag":1,"title_color1":"","title_color2":"","number_color":"","product_imgRadio":5,"productBg_color":"#ffffff","product_topRadio":5,"product_bottomRadio":5,"productName_color":"#333333","productPrice_color":"#ff4c01","titleColor":"#333333","titleSize":"14","total_sales":1,"tagColor":"#ffffff","bgTag":"#ff6417","color":"#ff4c01","countdown_color":"#ff4c01","countdown_back_color":"#fef7e4","background_color":"#ffffff","top_image":"https:\/\/business3.jjjshop.net\/image\/diy\/active\/preview_top.png","background_image":"https:\/\/business3.jjjshop.net\/image\/diy\/active\/preview.png"},"data":[{"product_name":"\u6b64\u5904\u662f\u9884\u544a\u5546\u54c1","product_image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"69.00","original_price":"139.00"},{"product_name":"\u6b64\u5904\u662f\u9884\u544a\u5546\u54c1","product_image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"69.00","original_price":"139.00"},{"product_name":"\u6b64\u5904\u662f\u9884\u544a\u5546\u54c1","product_image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"69.00","original_price":"139.00"}]},{"name":"\u5728\u7ebf\u5ba2\u670d","type":"service","group":"tools","icon":"icon-zaixiankefu","params":{"type":"chat","image":"https:\/\/business3.jjjshop.net\/image\/diy\/service.png","phone_num":""},"style":{"right":0,"bottom":10,"opacity":75}},{"name":"\u5fae\u4fe1\u76f4\u64ad","type":"wxlive","group":"shop","icon":"icon-shipinbofang","params":{"source":"auto","showNum":2},"style":{"background_image":"https:\/\/business3.jjjshop.net\/uploads\/20231010\/b001caaf6571897358c54702753e510c.png","color":"#8C1B1B","background":"#ffffff","bgcolor":"#f2f2f2","paddingTop":10,"paddingBottom":0,"paddingLeft":10,"topRadio":8,"bottomRadio":8},"defaultData":[{"shop_name":"\u76f4\u64ad\u95f4\u540d\u79f0","logo_image":"https:\/\/business3.jjjshop.net\/image\/diy\/circular.png","name":"\u4e3b\u64ad\u6635\u79f0"},{"shop_name":"\u76f4\u64ad\u95f4\u540d\u79f0","logo_image":"https:\/\/business3.jjjshop.net\/image\/diy\/circular.png","name":"\u4e3b\u64ad\u6635\u79f0"}],"data":[{"shop_name":"\u76f4\u64ad\u95f4\u540d\u79f0","logo_image":"https:\/\/business3.jjjshop.net\/image\/diy\/circular.png","name":"\u4e3b\u64ad\u6635\u79f0"},{"shop_name":"\u76f4\u64ad\u95f4\u540d\u79f0","logo_image":"https:\/\/business3.jjjshop.net\/image\/diy\/circular.png","name":"\u4e3b\u64ad\u6635\u79f0"}]},{"name":"\u6807\u9898","type":"title","group":"media","icon":"icon-biaoti","style":{"paddingTop":0,"paddingBottom":0,"paddingLeft":0,"topRadio":0,"bottomRadio":0,"bgcolor":"#FFFFFF","textSize":16,"weight":800,"isLine":1,"lineColor":"#ff4c01","isSub":1,"subtextSize":14,"subtextColor":"#FF0000","subbackground":"#FFCCCC","isMore":1,"moretextColor":"#FF0000","background":"#F5F5F5","textColor":"#ff4c01","type":1},"params":{"title":"\u5546\u54c1\u63a8\u8350","subtitle":"\u526f\u6807\u9898\u540d\u79f0","moretitle":"\u66f4\u591a","show_icon":"yes","icon":"","linkUrl":"","sublinkUrl":""}},{"name":"\u5546\u54c1\u7ec4","type":"product","group":"shop","icon":"icon-shangping","params":{"source":"auto","auto":{"category":6,"productSort":"all","showNum":6}},"style":{"paddingTop":"","paddingBottom":10,"paddingLeft":10,"topRadio":5,"bottomRadio":5,"bgcolor":"#F2f2f2","background":"#Ffffff","display":"list","column":2,"show":{"productName":1,"productPrice":1,"linePrice":1,"sellingPoint":0,"productSales":0}},"defaultData":[{"product_name":"\u6b64\u5904\u663e\u793a\u5546\u54c1\u540d\u79f0","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"99.00","line_price":"139.00","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u4e0d\u5bb9\u9519\u8fc7","product_sales":"100"},{"product_name":"\u6b64\u5904\u663e\u793a\u5546\u54c1\u540d\u79f0","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"99.00","line_price":"139.00","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u4e0d\u5bb9\u9519\u8fc7","product_sales":"100"},{"product_name":"\u6b64\u5904\u663e\u793a\u5546\u54c1\u540d\u79f0","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"99.00","line_price":"139.00","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u4e0d\u5bb9\u9519\u8fc7","product_sales":"100"},{"product_name":"\u6b64\u5904\u663e\u793a\u5546\u54c1\u540d\u79f0","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"99.00","line_price":"139.00","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u4e0d\u5bb9\u9519\u8fc7","product_sales":"100"}],"data":[{"product_name":"\u6b64\u5904\u663e\u793a\u5546\u54c1\u540d\u79f0","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"99.00","line_price":"139.00","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u4e0d\u5bb9\u9519\u8fc7","product_sales":"100","is_default":true},{"product_name":"\u6b64\u5904\u663e\u793a\u5546\u54c1\u540d\u79f0","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"99.00","line_price":"139.00","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u4e0d\u5bb9\u9519\u8fc7","product_sales":"100","is_default":true}]},{"name":"\u6807\u9898","type":"title","group":"media","icon":"icon-biaoti","style":{"paddingTop":0,"paddingBottom":0,"paddingLeft":0,"topRadio":0,"bottomRadio":0,"bgcolor":"#FFFFFF","textSize":16,"weight":800,"isLine":1,"lineColor":"#ff4c01","isSub":1,"subtextSize":14,"subtextColor":"#DDDDDD","subbackground":"#FFCCCC","isMore":1,"moretextColor":"#FF0000","background":"#F2f2f2","textColor":"#ff4c01","type":"1"},"params":{"title":"\u70ed\u95e8\u65b0\u95fb","subtitle":"\u526f\u6807\u9898\u540d\u79f0","moretitle":"\u66f4\u591a","show_icon":"yes","icon":"","linkUrl":"","sublinkUrl":""}},{"name":"\u6587\u7ae0\u7ec4","type":"article","group":"media","icon":"icon-wenzhangguanli","params":{"source":"auto","auto":{"category":0,"showNum":2}},"style":{"display":10,"background":"#FFFFFF","bgcolor":"#F2F2F2","paddingTop":"","paddingBottom":10,"paddingLeft":10,"topRadio":5,"bottomRadio":5},"defaultData":[{"article_title":"\u6b64\u5904\u663e\u793a\u6587\u7ae0\u6807\u9898","show_type":10,"image":"https:\/\/business3.jjjshop.net\/image\/diy\/article\/01.png","views_num":309},{"article_title":"\u6b64\u5904\u663e\u793a\u6587\u7ae0\u6807\u9898","show_type":10,"image":"https:\/\/business3.jjjshop.net\/image\/diy\/article\/01.png","views_num":309}],"data":[]},{"name":"\u7ebf\u4e0b\u95e8\u5e97","type":"store","group":"shop","icon":"icon-stores","params":{"source":"auto","auto":{"showNum":6}},"style":{"background":"#FFFFFF","bgcolor":"#f2f2f2","paddingTop":0,"paddingBottom":10,"paddingLeft":10,"topRadio":5,"bottomRadio":5},"defaultData":[{"shop_name":"\u6b64\u5904\u663e\u793a\u95e8\u5e97\u540d\u79f0","logo_image":"https:\/\/business3.jjjshop.net\/image\/diy\/circular.png","phone":"010-6666666","region":{"province":"xx\u7701","city":"xx\u5e02","region":"xx\u533a"},"address":"xx\u8857\u9053"},{"shop_name":"\u6b64\u5904\u663e\u793a\u95e8\u5e97\u540d\u79f0","logo_image":"https:\/\/business3.jjjshop.net\/image\/diy\/circular.png","phone":"010-6666666","region":{"province":"xx\u7701","city":"xx\u5e02","region":"xx\u533a"},"address":"xx\u8857\u9053"}],"data":[{"shop_name":"\u6b64\u5904\u663e\u793a\u95e8\u5e97\u540d\u79f0","logo_image":"https:\/\/business3.jjjshop.net\/image\/diy\/circular.png","phone":"010-6666666","region":{"province":"xx\u7701","city":"xx\u5e02","region":"xx\u533a"},"address":"xx\u8857\u9053"}]}]}';
            $data = [
                'page_type' => 10,
                'page_name' => '首頁裝修',
                'page_data' => json_decode($page_data, 1),
                'is_default' => 1,
                'app_id' => $app_id
            ];
            return (new static())->save($data);
        } elseif ($page_type == 30) {
            $page_data = '{"page":{"type":"page","name":"\u9875\u9762\u8bbe\u7f6e","params":{"name":"\u4e2a\u4eba\u4e2d\u5fc3","title":"\u9875\u9762\u6807\u9898","title_type":"text","share_title":"\u5206\u4eab\u6807\u9898","share_img":"http:\/\/www.jjj-shop-v3.com\/image\/diy\/logo.png","toplogo":"http:\/\/www.jjj-shop-v3.com\/image\/diy\/logo_top.png","icon":"icon-biaoti"},"style":{"titleTextColor":"black","titleBackgroundColor":"#ffffff"},"category":{"open":0,"color":"#000000"}},"items":[{"name":"\u57fa\u7840\u4fe1\u606f","type":"base","group":"page","style":{"background":"#ffffff","padding":48,"paddingTop":0,"paddingBottom":4,"paddingLeft":0,"bgcolor":"#f2f2f2","type":1}},{"name":"\u6807\u9898","type":"title","group":"media","icon":"icon-biaoti","style":{"paddingTop":10,"paddingBottom":0,"paddingLeft":10,"topRadio":5,"bottomRadio":0,"bgcolor":"#f2f2f2","textSize":16,"weight":800,"isLine":0,"lineColor":"#FF0000","isSub":0,"subtextSize":12,"subtextColor":"#DDDDDD","subbackground":"#FFCCCC","isMore":1,"moretextColor":"#999999","background":"#ffffff","textColor":"#282828","type":8},"params":{"title":"\u6211\u7684\u8ba2\u5355","subtitle":"","moretitle":"\u66f4\u591a\uff1e","show_icon":"yes","icon":"","linkUrl":"","sublinkUrl":"pages\/cart\/cart","morelinkUrl":"pages\/order\/myorder"}},{"name":"\u8f85\u52a9\u7ebf","type":"guide","group":"tools","icon":"icon-fuzhuxian","style":{"background":"#f2f2f2","lineStyle":"solid","lineHeight":1,"lineColor":"#eeeeee","paddingTop":0,"paddingLeft":10,"paddingBottom":0}},{"name":"\u6211\u7684\u8ba2\u5355","type":"order","group":"page","style":{"background":"#ffffff","paddingTop":0,"paddingBottom":0,"paddingLeft":10,"bgcolor":"#f2f2f2","topRadio":0,"bottomRadio":10,"type":1}},{"name":"\u6807\u9898","type":"title","group":"media","icon":"icon-biaoti","style":{"paddingTop":10,"paddingBottom":0,"paddingLeft":10,"topRadio":5,"bottomRadio":0,"bgcolor":"#f2f2f2","textSize":16,"weight":800,"isLine":0,"lineColor":"#FF0000","isSub":0,"subtextSize":14,"subtextColor":"#FF0000","subbackground":"#FFCCCC","isMore":1,"moretextColor":"#FF0000","background":"#FFFFFF","textColor":"#282828","type":8},"params":{"title":"\u6211\u7684\u670d\u52a1","subtitle":"","moretitle":"","show_icon":"yes","icon":"","linkUrl":"","sublinkUrl":""}},{"name":"\u8f85\u52a9\u7ebf","type":"guide","group":"tools","icon":"icon-fuzhuxian","style":{"background":"#f2f2f2","lineStyle":"solid","lineHeight":1,"lineColor":"#eeeeee","paddingTop":0,"paddingLeft":10,"paddingBottom":0}},{"name":"\u5bfc\u822a\u7ec4","type":"navBar","group":"media","icon":"icon-mulu","style":{"background":"#ffffff","rowsNum":5,"bgcolor":"#f2f2f2","paddingTop":0,"paddingBottom":14,"paddingLeft":10,"topRadio":5,"bottomRadio":5},"data":[{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/20230920170725067060469.png","imgName":"icon-1.png","linkUrl":"\/pages\/user\/address\/address","text":"\u6536\u8d27\u5730\u5740","color":"#666666","name":"\u94fe\u63a5\u5230 \u83dc\u5355 \u6536\u8d27\u5730\u5740"},{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/202309201707241df798556.png","imgName":"icon-2.jpg","linkUrl":"\/pages\/coupon\/coupon","text":"\u9886\u5238\u4e2d\u5fc3","color":"#666666","name":"\u94fe\u63a5\u5230 \u83dc\u5355 \u9886\u5238\u4e2d\u5fc3"},{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/20230920172034762a69130.png","imgName":"icon-3.jpg","linkUrl":"\/pages\/user\/my-coupon\/my-coupon","text":"\u6211\u7684\u4f18\u60e0\u5238","color":"#666666","name":"\u94fe\u63a5\u5230 \u83dc\u5355 \u6211\u7684\u4f18\u60e0\u5238"},{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/20230920170725ab9484348.png","imgName":"icon-4.jpg","linkUrl":"\/pages\/agent\/index\/index","text":"\u5206\u9500\u4e2d\u5fc3","color":"#666666","name":"\u94fe\u63a5\u5230 \u83dc\u5355 \u5206\u9500\u4e2d\u5fc3"},{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/2023092017072545b6c8596.png","imgName":"icon-1.png","linkUrl":"\/pages\/user\/my-bargain\/my-bargain","text":"\u6211\u7684\u780d\u4ef7","color":"#666666","name":"\u94fe\u63a5\u5230 \u83dc\u5355 \u6211\u7684\u780d\u4ef7"},{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/20230920170725ed4075790.png","imgName":"icon-1.png","linkUrl":"pages\/plus\/signin\/signin","text":"\u7b7e\u5230","color":"#666666","name":"\u94fe\u63a5\u5230 \u8425\u9500 \u7b7e\u5230"},{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/20230920170725878430048.png","imgName":"icon-1.png","linkUrl":"\/pages\/plus\/giftpackage\/giftlist","text":"\u6211\u7684\u793c\u5305","color":"#666666","name":"\u94fe\u63a5\u5230 \u83dc\u5355 \u793c\u5305\u8d2d-\u6211\u7684\u793c\u5305"},{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/202309201707253b6c48952.png","imgName":"icon-1.png","linkUrl":"\/pages\/user\/set\/set","text":"\u8bbe\u7f6e","color":"#666666","name":"\u94fe\u63a5\u5230 \u83dc\u5355 \u8bbe\u7f6e"},{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/202309201720129f0a06574.png","imgName":"icon-1.png","linkUrl":"\/pages\/user\/favorite\/favorite","text":"\u6211\u7684\u6536\u85cf","color":"#666666","name":"\u94fe\u63a5\u5230 \u83dc\u5355 \u6211\u7684\u6536\u85cf"},{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/202309201726209941f5527.png","imgName":"icon-1.png","linkUrl":"pages\/plus\/lottery\/lottery","text":"\u6211\u7684\u62bd\u5956","color":"#666666","name":"\u94fe\u63a5\u5230 \u8425\u9500 \u5e78\u8fd0\u8f6c\u76d8"},{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/2023092017261980f186766.png","imgName":"icon-1.png","linkUrl":"pages\/order\/codeorder","text":"\u63d0\u8d27\u8ba2\u5355","color":"#666666","name":"\u94fe\u63a5\u5230 \u9875\u9762 \u63d0\u8d27\u8ba2\u5355"},{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/2023092017261992e286923.png","imgName":"icon-1.png","linkUrl":"\/pages\/task\/index","text":"\u4efb\u52a1\u4e2d\u5fc3","color":"#666666","name":"\u94fe\u63a5\u5230 \u83dc\u5355 \u4efb\u52a1\u4e2d\u5fc3"},{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/2023092017261980cbe8260.png","imgName":"icon-1.png","linkUrl":"\/pages\/order\/assemble-order","text":"\u6211\u7684\u62fc\u56e2","color":"#666666","name":"\u94fe\u63a5\u5230 \u83dc\u5355 \u6211\u7684\u62fc\u56e2"},{"imgUrl":"https:\/\/qn-cdn.jjjshop.net\/20230920170724d69ec2178.png","imgName":"icon-1.png","linkUrl":"\/pages\/user\/evaluate\/list","text":"\u6211\u7684\u8bc4\u4ef7","color":"#666666","name":"\u94fe\u63a5\u5230 \u83dc\u5355 \u6211\u7684\u8bc4\u4ef7"}]},{"name":"\u6807\u9898","type":"title","group":"media","icon":"icon-biaoti","style":{"paddingTop":0,"paddingBottom":10,"paddingLeft":10,"topRadio":5,"bottomRadio":0,"bgcolor":"#f2f2f2","textSize":16,"weight":800,"isLine":1,"lineColor":"#FC7A0F","isSub":1,"subtextSize":14,"subtextColor":"#DDDDDD","subbackground":"#FFCCCC","isMore":1,"moretextColor":"#FF0000","background":"#f2f2f2","textColor":"#FF800A","type":1},"params":{"title":"\u63a8\u8350\u5546\u54c1","subtitle":"\u526f\u6807\u9898\u540d\u79f0","moretitle":"\u66f4\u591a","show_icon":"yes","icon":"","linkUrl":"","sublinkUrl":"pages\/product\/list\/list?category_id=6"}},{"name":"\u5546\u54c1\u7ec4","type":"product","group":"shop","icon":"icon-shangping","params":{"source":"auto","auto":{"category":6,"productSort":"all","showNum":6}},"style":{"background":"#F6F6F6","display":"list","column":2,"show":{"productName":1,"productPrice":1,"linePrice":1,"sellingPoint":0,"productSales":0,"paddingTop":0,"paddingBottom":0,"paddingLeft":10}},"defaultData":[{"product_name":"\u6b64\u5904\u663e\u793a\u5546\u54c1\u540d\u79f0","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"99.00","line_price":"139.00","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u4e0d\u5bb9\u9519\u8fc7","product_sales":"100"},{"product_name":"\u6b64\u5904\u663e\u793a\u5546\u54c1\u540d\u79f0","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"99.00","line_price":"139.00","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u4e0d\u5bb9\u9519\u8fc7","product_sales":"100"},{"product_name":"\u6b64\u5904\u663e\u793a\u5546\u54c1\u540d\u79f0","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"99.00","line_price":"139.00","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u4e0d\u5bb9\u9519\u8fc7","product_sales":"100"},{"product_name":"\u6b64\u5904\u663e\u793a\u5546\u54c1\u540d\u79f0","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"99.00","line_price":"139.00","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u4e0d\u5bb9\u9519\u8fc7","product_sales":"100"}],"data":[{"product_name":"\u6b64\u5904\u663e\u793a\u5546\u54c1\u540d\u79f0","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"99.00","line_price":"139.00","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u4e0d\u5bb9\u9519\u8fc7","product_sales":"100","is_default":true},{"product_name":"\u6b64\u5904\u663e\u793a\u5546\u54c1\u540d\u79f0","image":"https:\/\/business3.jjjshop.net\/image\/diy\/product\/01.png","product_price":"99.00","line_price":"139.00","selling_point":"\u6b64\u6b3e\u5546\u54c1\u7f8e\u89c2\u5927\u65b9 \u4e0d\u5bb9\u9519\u8fc7","product_sales":"100","is_default":true}]}]}';
            $data = [
                'page_type' => 30,
                'page_name' => '個人中心',
                'page_data' => json_decode($page_data, 1),
                'is_default' => 1,
                'app_id' => $app_id
            ];
            return (new static())->save($data);
        }
    }

}
