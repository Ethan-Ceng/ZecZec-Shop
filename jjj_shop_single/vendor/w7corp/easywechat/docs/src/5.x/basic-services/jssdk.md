# JSSDK

微信 JSSDK 官方文件：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141115

## API

#### 獲取JSSDK的配置陣列

```php
$app->jssdk->buildConfig(array $APIs, $debug = false, $beta = false, $json = true, array $openTagList = []);
```

預設返回 JSON 字串，當 `$json` 為 `false` 時返回陣列，你可以直接使用到網頁中。

#### 設定當前URL

```php
$app->jssdk->setUrl($url)
```
如果不想用預設讀取的URL，可以使用此方法手動設定，通常不需要。


#### 示例

我們可以生成js配置檔案：

```js
<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
    wx.config(<?php echo $app->jssdk->buildConfig(array('updateAppMessageShareData', 'updateTimelineShareData'), true) ?>);
</script>
```
結果如下：


```js
<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
wx.config({
    debug: true, // 請在上線前刪除它
    appId: 'wx3cf0f39249eb0e60',
    timestamp: 1430009304,
    nonceStr: 'qey94m021ik',
    signature: '4F76593A4245644FAE4E1BC940F6422A0C3EC03E',
    jsApiList: ['updateAppMessageShareData', 'updateTimelineShareData']
});
</script>
```

