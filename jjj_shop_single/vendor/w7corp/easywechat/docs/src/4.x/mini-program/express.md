# 物流助手 電子面單

## 獲取支援的快遞公司列表

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.getAllDelivery.html

```php

$app->express->listProviders();

{
  "count": 8,
  "data": [
    {
      "delivery_id": "BEST",
      "delivery_name": "百世快遞"
    },
    ...
  ]
}

```

## 生成運單

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.addOrder.html

```php

$app->express->createWaybill($data);


// 成功返回

{
  "order_id": "01234567890123456789",
  "waybill_id": "123456789",
  "waybill_data": [
    {
      "key": "SF_bagAddr",
      "value": "廣州"
    },
    {
      "key": "SF_mark",
      "value": "101- 07-03 509"
    }
  ]
}

// 失敗返回

{
  "errcode": 9300501,
  "errmsg": "delivery logic fail",
  "delivery_resultcode": 10002,
  "delivery_resultmsg": "客戶密碼不正確"
}

```

## 取消運單

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.cancelOrder.html

```php
$app->express->deleteWaybill($data);

```

## 獲取運單資料

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.getOrder.html

```php
$app->express->getWaybill($data);

```

## 查詢運單軌跡

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.getPath.html

```php
$app->express->getWaybillTrack($data);

```

## 獲取電子面單餘額。

僅在使用加盟類快遞公司時，才可以呼叫。

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.getQuota.html

```php

$app->express->getBalance($deliveryId, $bizId);

// 例如：

$app->express->getBalance('YTO', 'xyz');
```

## 繫結列印員

若需要使用微信打單 PC 軟體，才需要呼叫。

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.updatePrinter.html

```php
$app->express->bindPrinter($openid);
```

## 解綁列印員

若需要使用微信打單 PC 軟體，才需要呼叫。

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.updatePrinter.html

```php
$app->express->unbindPrinter($openid);
```
