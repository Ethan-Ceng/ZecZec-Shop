# JSSDK

企業微信 JSSDK 官方文件：https://open.work.weixin.qq.com/api/doc/90000/90136/90514

## API

### 獲取config介面配置

```php
$app->jssdk->buildConfig(array $APIs, $debug = false, $beta = false, $json = true, array $openTagList = []);
```

預設返回 JSON 字串，當 `$json` 為 `false` 時返回陣列，你可以直接使用到網頁中。

- 設定當前URL

```php
$app->jssdk->setUrl($url);
$app->jssdk->buildConfig(array $APIs, $debug = false, $beta = false, $json = true, array $openTagList = []);
```
如果不想用預設讀取的URL，可以使用此方法手動設定，通常不需要。


- 示例

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

### 獲取agentConfig介面配置

呼叫wx.agentConfig之前，必須確保先成功呼叫wx.config. 注意：從企業微信3.0.24及以後版本（可透過企業微信UA判斷版本號），無須先呼叫wx.config，可直接wx.agentConfig.

```php
$app->jssdk->buildAgentConfig(
        array $jsApiList, // 需要檢測的JS介面列表
        $agentId, //應用id
        bool $debug = false,
        bool $beta = false,
        bool $json = true,
        array $openTagList = [],
        string $url = null //設定當前URL
    );
```

- 前端示例

```js
<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
<script src="https://open.work.weixin.qq.com/wwopen/js/jwxwork-1.0.0.js"></script>
<script type="text/javascript" charset="utf-8">
wx.config({
    debug: true, // 請在上線前刪除它
    appId: 'wx3cf0f39249eb0e60',
    timestamp: 1430009304,
    nonceStr: 'qey94m021ik',
    signature: '4F76593A4245644FAE4E1BC940F6422A0C3EC03E',
    jsApiList: ['updateAppMessageShareData', 'updateTimelineShareData']
});
wx.ready(function(){
    wx.agentConfig({ //呼叫agentConfig
        corpid: '', 
        agentid: '', 
        timestamp: '', 
        nonceStr: '', 
        signature: '',
        jsApiList: ['selectExternalContact'],
        success: function(res) {
            // 回撥
        },
        fail: function(res) {
            if(res.errMsg.indexOf('function not exist') > -1){
                alert('版本過低請升級')
            }
        }
    });
});
wx.error(function(res){
    console.log('失敗');  
});
</script>
```

