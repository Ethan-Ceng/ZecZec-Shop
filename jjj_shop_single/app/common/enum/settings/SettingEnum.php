<?php

namespace app\common\enum\settings;

use MyCLabs\Enum\Enum;

/**
 * 商城設定列舉類
 */
class SettingEnum extends Enum
{
    // 商城設定
    const STORE = 'store';
    // 商城設定
    const MP_SERVICE = 'mp_service';
    // 交易設定
    const TRADE = 'trade';
    // 簡訊通知
    const SMS = 'sms';
    // 模板訊息
    const TPL_MSG = 'tplMsg';
    // 上傳設定
    const STORAGE = 'storage';
    // 小票列印
    const PRINTER = 'printer';
    // 滿額包郵設定
    const FULL_FREE = 'full_free';
    // 充值設定
    const RECHARGE = 'recharge';
    // 積分設定
    const POINTS = 'points';
    // 公眾號設定
    const OFFICIA = 'officia';
    // 商品推薦
    const RECOMMEND = 'recommend';
    // 簽到有禮
    const SIGN = 'sign';
    // 首頁推送
    const HOMEPUSH = 'homepush';
    // 引導收藏
    const COLLECTION = 'collection';
    // 好物圈
    const BASIC = 'basic';
    // 積分商城
    const POINTSMALL = 'pointsmall';
    // 限時秒殺
    const SECKILL = 'seckill';
    // 限時拼團
    const ASSEMBLE = 'assemble';
    // 限時砍價
    const BARGAIN = 'bargain';
    // 充值設定
    const BALANCE = 'balance';
    // 邀請好友設定
    const INVITATION = 'invitation';
    // 邀請好友設定
    const APPSHARE = 'appshare';
    // h5支付寶支付設定
    const H5ALIPAY = 'h5Alipay';
    // 直播設定
    const LIVE = 'live';
    // 系統配置
    const SYS_CONFIG = 'sys_config';
    // 主題設定
    const THEME = 'theme';
    // 底部導航設定
    const NAV = 'nav';
    // 卡券設定
    const CARD = 'card';
    // 預售設定
    const ADVANCE = 'advance';
    // 餘額提現設定
    const BALANCE_CASH = 'balance_cash';
    // 日常任務
    const TASK = 'task';
    // 使用者協議
    const SERVICE = 'service';
    // 隱私協議
    const PRIVACY = 'privacy';

    /**
     * 獲取訂單型別值
     */
    public static function data()
    {
        return [
            self::STORE => [
                'value' => self::STORE,
                'describe' => '商城設定',
            ],
            self::MP_SERVICE => [
                'value' => self::MP_SERVICE,
                'describe' => '客服設定',
            ],
            self::TRADE => [
                'value' => self::TRADE,
                'describe' => '交易設定',
            ],
            self::SMS => [
                'value' => self::SMS,
                'describe' => '簡訊通知',
            ],
            self::TPL_MSG => [
                'value' => self::TPL_MSG,
                'describe' => '模板訊息',
            ],
            self::STORAGE => [
                'value' => self::STORAGE,
                'describe' => '上傳設定',
            ],
            self::PRINTER => [
                'value' => self::PRINTER,
                'describe' => '小票列印',
            ],
            self::FULL_FREE => [
                'value' => self::FULL_FREE,
                'describe' => '滿額包郵設定',
            ],
            self::RECHARGE => [
                'value' => self::RECHARGE,
                'describe' => '充值設定',
            ],
            self::POINTS => [
                'value' => self::POINTS,
                'describe' => '積分設定',
            ],
            self::OFFICIA => [
                'value' => self::OFFICIA,
                'describe' => '公眾號設定',
            ],
            self::RECOMMEND => [
                'value' => self::RECOMMEND,
                'describe' => '商品推薦',
            ],
            self::SIGN => [
                'value' => self::SIGN,
                'describe' => '簽到有禮',
            ],
            self::HOMEPUSH => [
                'value' => self::HOMEPUSH,
                'describe' => '首頁推送',
            ],
            self::COLLECTION => [
                'value' => self::COLLECTION,
                'describe' => '引導收藏',
            ],
            self::BASIC => [
                'value' => self::BASIC,
                'describe' => '好物圈',
            ],
            self::POINTSMALL => [
                'value' => self::POINTSMALL,
                'describe' => '積分商城',
            ],
            self::SECKILL => [
                'value' => self::SECKILL,
                'describe' => '限時秒殺',
            ],
            self::ASSEMBLE => [
                'value' => self::ASSEMBLE,
                'describe' => '限時拼團',
            ],
            self::BARGAIN => [
                'value' => self::BARGAIN,
                'describe' => '限時砍價',
            ],
            self::BALANCE => [
                'value' => self::BALANCE,
                'describe' => '充值設定',
            ],
            self::INVITATION => [
                'value' => self::INVITATION,
                'describe' => '邀請好友設定',
            ],
            self::APPSHARE => [
                'value' => self::APPSHARE,
                'describe' => 'app分享',
            ],
            self::H5ALIPAY => [
                'value' => self::H5ALIPAY,
                'describe' => 'h5支付寶支付',
            ],
            self::LIVE => [
                'value' => self::LIVE,
                'describe' => '直播設定',
            ],
            self::SYS_CONFIG => [
                'value' => self::SYS_CONFIG,
                'describe' => '系統設定',
            ],
            self::THEME => [
                'value' => self::THEME,
                'describe' => '主題設定',
            ],
            self::NAV => [
                'value' => self::NAV,
                'describe' => '底部導航',
            ],
            self::CARD => [
                'value' => self::CARD,
                'describe' => '卡券設定',
            ],
            self::ADVANCE => [
                'value' => self::ADVANCE,
                'describe' => '預售設定',
            ],
            self::BALANCE_CASH => [
                'value' => self::BALANCE_CASH,
                'describe' => '餘額提現設定',
            ],
            self::TASK => [
                'value' => self::TASK,
                'describe' => '任務中心',
            ],
            self::SERVICE => [
                'value' => self::SERVICE,
                'describe' => '使用者協議',
            ],
            self::PRIVACY => [
                'value' => self::PRIVACY,
                'describe' => '隱私協議',
            ],
        ];
    }

}