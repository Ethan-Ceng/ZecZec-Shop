# 使用者

使用者資訊的獲取是微信開發中比較常用的一個功能了，以下所有的使用者資訊的獲取與更新，都是**基於微信的 `openid` 的，並且是已關注當前賬號的**，其它情況可能無法正常使用。

## 獲取使用者資訊

獲取單個：

```php
$user = $app->user->get($openId);
```

獲取多個：

```php
$users = $app->user->select([$openId1, $openId2, ...]);
```

## 獲取使用者列表

```php
$app->user->list($nextOpenId = null);  // $nextOpenId 可選
```

示例：

```php
 $users = $app->user->list();

// result
 {
  "total": 2,
  "count": 2,
  "data": {
    "openid": [
      "OPENID1",
      "OPENID2"
    ]
  },
  "next_openid": "NEXT_OPENID"
}
```

## 修改使用者備註

```php
$app->user->remark($openId, $remark); // 成功返回boolean
```

示例：

```php
$app->user->remark($openId, "殭屍粉");
```

## 拉黑使用者

```php
$app->user->block('openidxxxxx');
// 或者多個使用者
$app->user->block(['openid1', 'openid2', 'openid3', ...]);
```

## 取消拉黑使用者

```php
$app->user->unblock('openidxxxxx');
// 或者多個使用者
$app->user->unblock(['openid1', 'openid2', 'openid3', ...]);
```

## 獲取黑名單

```php
$app->user->blacklist($beginOpenid = null); // $beginOpenid 可選
```

## 賬號遷移 openid 轉換

賬號遷移請從這裡瞭解：https://kf.qq.com/product/weixinmp.html#hid=2488

微信使用者關注不同的公眾號，對應的 OpenID 是不一樣的，遷移成功後，粉絲的 OpenID 以目標帳號（即新公眾號）對應的 OpenID 為準。但開發者可以透過開發介面轉換 OpenID，開發文件可以參考：
提供一個 openid 轉換的 API 介面，當帳號遷移後，可以透過該介面：

1. 將原帳號粉絲的 openid 轉換為新帳號的 openid。
2. 將有授權關係使用者的 openid 轉換為新帳號的 openid。
3. 將卡券關聯使用者的 openid 轉換為新帳號的 openid。

> - ◆ 原帳號：準備要遷移的帳號，當稽核完成且管理員確認後即被回收。
> - ◆ 新帳號：用來接納粉絲的帳號。新帳號在整個流程中均能正常使用。

一定要按照下面的步驟來操作。

1. 一定要在原帳號被凍結之前，最好是準備提交稽核前，獲取原帳號的使用者列表。如果沒有原帳號的使用者列表，用不了轉換工具。如果原賬號被回收，這時候也沒辦法呼叫介面獲取使用者列表。

如何獲取使用者列表見這裡：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140840

2. 轉換 openid 的 API 介面如下，可在帳號遷移稽核完成後開始呼叫，並最多保留 15 天。若帳號遷移沒完成，呼叫時無返回結果或報錯。帳號遷移 15 天后，該轉換介面將會失效、無法拉取到資料。

```php
$app->user->changeOpenid($oldAppId, $openidList);
```

返回值樣例：

```json
{
  "errcode": 0,
  "errmsg": "ok",
  "result_list": [
    {
      "ori_openid": "oEmYbwN-n24jxvk4Sox81qedINkQ",
      "new_openid": "o2FwqwI9xCsVadFah_HtpPfaR-X4",
      "err_msg": "ok"
    },
    {
      "ori_openid": "oEmYbwH9uVd4RKJk7ZZg6SzL6tTo",
      "err_msg": "ori_openid error"
    }
  ]
}
```
