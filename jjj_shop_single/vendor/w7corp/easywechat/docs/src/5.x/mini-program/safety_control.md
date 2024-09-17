# 安全風控

> 微信文件：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/safety-control-capability/riskControl.getUserRiskRank.html

> tips: 根據提交的使用者資訊資料獲取使用者的安全等級 risk_rank，無需使用者授權。

## 獲取使用者的安全等級

```php
$app->risk_control->getUserRiskRank([
	'appid' => 'wx311232323',
	'openid' => 'oahdg535ON6vtkUXLdaLVKvzJdmM',
	'scene' => 1,
	'client_ip' => '12.234.134.2',
]);
```