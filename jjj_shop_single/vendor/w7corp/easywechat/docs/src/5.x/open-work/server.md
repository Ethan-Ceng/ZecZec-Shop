# 服務端

## 企業微信第三方回撥協議

SDK 預設會處理事件 `suite_ticket` ，並會快取 `suite_ticket`

> 需要注意的是：授權成功、變更授權、取消授權通知時間的響應必須在 1000ms 內完成，以保證使用者安裝應用的體驗。建議在接收到此事件時 立即回應企業微信，之後再做相關業務的處理。

```php
$server = $app->server;

$server->push(function ($message) {
    //指令回撥
    if (isset($message['InfoType'])) {
        switch ($message['InfoType']) {
            //推送suite_ticket
            case 'suite_ticket':
                break;
            //授權成功通知
            case 'create_auth':
                break;
            //變更授權通知
            case 'cancel_auth':
                break;
            //通訊錄事件通知
            case 'change_contact':
                switch ($message['ChangeType']) {
                    case 'create_user':
                        return '新增成員事件';
                        break;
                    case 'update_user':
                        return '更新成員事件';
                        break;
                    case 'delete_user':
                        return '刪除成員事件';
                        break;
                    case 'create_party':
                        return '新增部門事件';
                        break;
                    case 'update_party':
                        return '更新部門事件';
                        break;
                    case 'delete_party':
                        return '刪除部門事件';
                        break;
                    case 'update_tag':
                        return '標籤成員變更事件';
                        break;
                }
                break;
            default:
                return 'fail';
                break;
        }
    }

    //資料回撥
    if(isset($message['MsgType'])){
        switch ($message['MsgType']) {
            case 'event':
                return '事件訊息';//詳情 https://work.weixin.qq.com/api/doc/90001/90143/90376#%E5%88%A0%E9%99%A4%E6%88%90%E5%91%98%E4%BA%8B%E4%BB%B6
                break;
            case 'text':
                return '文字訊息';//詳情 https://work.weixin.qq.com/api/doc/90001/90143/90375#%E5%9B%BE%E7%89%87%E6%B6%88%E6%81%AF
                break;
            case 'image':
                return '圖片訊息';
                break;
                //等等...不再一一舉例
            default:
                return '其他訊息';
                break;
        }
    }

});
$response = $server->serve();
$response->send();
```
