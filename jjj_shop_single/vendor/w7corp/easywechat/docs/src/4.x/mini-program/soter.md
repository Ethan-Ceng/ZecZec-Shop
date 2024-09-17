# 生物認證

## 生物認證秘鑰簽名驗證

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/soter/soter.verifySignature.html

```php
$app->soter->verifySignature($openid, $json, $signature);
```

返回值示例:
```json
{
    "is_ok": true
}
```

引數說明:

> - string $openid - 使用者 openid
> - string $json - 透過 [wx.startSoterAuthentication](https://developers.weixin.qq.com/miniprogram/dev/api/open-api/soter/wx.startSoterAuthentication.html) 成功回撥獲得的 resultJSON 欄位
> - string $signature - 透過 [wx.startSoterAuthentication](https://developers.weixin.qq.com/miniprogram/dev/api/open-api/soter/wx.startSoterAuthentication.html) 成功回撥獲得的 resultJSONSignature 欄位