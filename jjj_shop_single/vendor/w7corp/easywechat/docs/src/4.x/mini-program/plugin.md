# 外掛管理

> 微信文件：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/plugin-management/pluginManager.applyPlugin.html

## 申請使用外掛

```php
$pluginAppId = 'xxxxxxxxx';

$app->plugin->apply($pluginAppId);
```

## 刪除已新增的外掛

```php
$pluginAppId = 'xxxxxxxxx';

$app->plugin->unbind($pluginAppId);
```

## 查詢已新增的外掛

```php
$app->plugin->list();
```

## 獲取當前所有外掛使用方

```php
$page = 1;
$size = 10;

$app->plugin_dev->getUsers($page, $size);
```

## 同意外掛使用申請

```php
$appId = 'wxxxxxxxxxxxxxx';

$app->plugin_dev->agree($appId);
```

## 拒絕外掛使用申請

```php
$app->plugin_dev->refuse('拒絕理由');
```

## 刪除已拒絕的申請者

```php
$app->plugin_dev->delete();
```
