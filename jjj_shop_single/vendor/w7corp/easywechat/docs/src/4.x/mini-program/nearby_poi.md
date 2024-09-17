# 附近的小程式

> 微信文件：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/nearby-poi/nearbyPoi.add.html

## 新增地點

```php
$params = [
    'kf_info' => '{"open_kf":true,"kf_headimg":"http://mmbiz.qpic.cn/mmbiz_jpg/kKMgNtnEfQzDKpLXYhgo3W3Gndl34gITqmP914zSwhajIEJzUPpx40P7R8fRe1QmicneQMhFzpZNhSLjrvU1pIA/0?wx_fmt=jpeg","kf_name":"Harden"}',
    'pic_list' => '{"list":["http://mmbiz.qpic.cn/mmbiz_jpg/kKMgNtnEfQzDKpLXYhgo3W3Gndl34gITqmP914zSwhajIEJzUPpx40P7R8fRe1QmicneQMhFzpZNhSLjrvU1pIA/0?wx_fmt=jpeg","http://mmbiz.qpic.cn/mmbiz_jpg/kKMgNtnEfQzDKpLXYhgo3W3Gndl34gITRneE5FS9uYruXGMmrtmhsBySwddEWUGOibG8Ze2NT5E3Dyt79I0htNg/0?wx_fmt=jpeg"]}',
    'service_infos' => '{"service_infos":[{"id":2,"type":1,"name":"快遞","appid":"wx1373169e494e0c39","path":"index"},{"id":0,"type":2,"name":"自定義","appid":"wx1373169e494e0c39","path":"index"}]}',
    'store_name' => '羊村小馬燒烤',
    'contract_phone' => '111111111',
    'hour' => '00:00-11:11',
    'company_name' => '深圳市騰訊計算機系統有限公司',
    'credential' => '156718193518281',
    'address' => '新疆維吾爾自治區克拉瑪依市克拉瑪依區碧水路15-1-8號(碧水雲天廣場)',
    'qualification_list' => '3LaLzqiTrQcD20DlX_o-OV1-nlYMu7sdVAL7SV2PrxVyjZFZZmB3O6LPGaYXlZWq',
];

$app->nearby_poi->add($params);
```

## 更新地點

```php
$poiId = 'xxxxxxxx';

$params = [
    'kf_info' => '{"open_kf":true,"kf_headimg":"http://mmbiz.qpic.cn/mmbiz_jpg/kKMgNtnEfQzDKpLXYhgo3W3Gndl34gITqmP914zSwhajIEJzUPpx40P7R8fRe1QmicneQMhFzpZNhSLjrvU1pIA/0?wx_fmt=jpeg","kf_name":"Harden"}',
    'pic_list' => '{"list":["http://mmbiz.qpic.cn/mmbiz_jpg/kKMgNtnEfQzDKpLXYhgo3W3Gndl34gITqmP914zSwhajIEJzUPpx40P7R8fRe1QmicneQMhFzpZNhSLjrvU1pIA/0?wx_fmt=jpeg","http://mmbiz.qpic.cn/mmbiz_jpg/kKMgNtnEfQzDKpLXYhgo3W3Gndl34gITRneE5FS9uYruXGMmrtmhsBySwddEWUGOibG8Ze2NT5E3Dyt79I0htNg/0?wx_fmt=jpeg"]}',
    'service_infos' => '{"service_infos":[{"id":2,"type":1,"name":"快遞","appid":"wx1373169e494e0c39","path":"index"},{"id":0,"type":2,"name":"自定義","appid":"wx1373169e494e0c39","path":"index"}]}',
    'contract_phone' => '111111111',
    'hour' => '00:00-11:11',
    'company_name' => '深圳市騰訊計算機系統有限公司',
    'credential' => '156718193518281',
    'address' => '新疆維吾爾自治區克拉瑪依市克拉瑪依區碧水路15-1-8號(碧水雲天廣場)',
    'qualification_list' => '3LaLzqiTrQcD20DlX_o-OV1-nlYMu7sdVAL7SV2PrxVyjZFZZmB3O6LPGaYXlZWq',
];

$app->nearby_poi->update($poiId, $params);
```

## 刪除地點

```php
$poiId = 'xxxxxxxx';

$app->nearby_poi->delete($poiId);
```

## 地點列表

```php
$page = 1;
$pageRows = 10;

$app->nearby_poi->list($page, $pageRows);
```

## 設定地點展示狀態

```php
$poiId = 'xxxxxxxx';
$status = 0; // 0: 不展示，1：展示

$app->nearby_poi->setVisibility($poiId, $status);
```
