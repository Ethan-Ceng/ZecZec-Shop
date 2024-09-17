# 對賬單

## 下載對賬單

> 呼叫引數正確會返回一個 `EasyWeChat\Kernel\Http\StreamResponse` 物件，否則會返回相應錯誤資訊

Example:

```php
$bill = $app->bill->get('20140603'); // type: ALL
// or
$bill = $app->bill->get('20140603', 'SUCCESS'); // type: SUCCESS

// 呼叫正確，`$bill` 為 csv 格式的內容，儲存為檔案：
$bill->saveAs('your/path/to', 'file-20140603.csv');
```

第二個引數為賬單型別，參考：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_6 中 `bill_type`，預設為 `ALL`
