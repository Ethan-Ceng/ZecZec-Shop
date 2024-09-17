# 工具類 <version-tag>6.3.0+</version-tag>

為了簡化大家對於一些高頻基礎的操作，我們提供了一些簡單的工具類。你可以透過下面的方法獲取工具類：

```php
$utils = $app->getUtils();
```

## 工具方法

### code2session

```php
$response = $utils->codeToSession($code);

// {
//     "openid": "o6_bmjrPTlm6_2sgVt7hMZOPxxxx",
//     "session_key": "tiihtNczf5v6AKRyjwExxxx=",
//     "unionid": "o6_bmasdasdsad6_2sgVt7hMZOxxxx",
//     "errcode": 0,
//     "errmsg": "ok"
//}
```

### 解密會話資訊

```php
$session = $utils->decryptSession($sessionKey, $iv, $encryptedData);

//{
//    "openId": "oGZUI0egBJY1zhBYw2KhdUfwVJJE",
//    "nickName": "Band",
//    "gender": 1,
//    "language": "zh_CN",
//    "city": "Guangzhou",
//    "province": "Guangdong",
//    "country": "CN",
//    "avatarUrl": "http://wx.qlogo.cn/mmopen/vi_32/aSKcBBPpibyKNicHNTMM0qJVh8Kjgiak2AHWr8MHM4WgMEm7GFhsf8OYrySdbvAMvTsw3mo8ibKicsnfN5pRjl1p8HQ/0",
//    "unionId": "ocMvos6NjeKLIBqg5Mr9QjxrP1FA",
//    "watermark": {
//        "timestamp": 1477314187,
//        "appid": "wx4f4bc4dec97d474b"
//    }
//}
```
