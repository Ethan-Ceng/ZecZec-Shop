GatewayWorker 
=================

GatewayWorker基於[Workerman](https://github.com/walkor/Workerman)開發的一個專案框架，用於快速開發長連線應用，例如app推送服務端、即時IM服務端、遊戲服務端、物聯網、智慧家居等等。

GatewayWorker使用經典的Gateway和Worker程序模型。Gateway程序負責維持客戶端連線，並轉發客戶端的資料給Worker程序處理；Worker程序負責處理實際的業務邏輯，並將結果推送給對應的客戶端。Gateway服務和Worker服務可以分開部署在不同的伺服器上，實現分散式叢集。

GatewayWorker提供非常方便的API，可以全域性廣播資料、可以向某個群體廣播資料、也可以向某個特定客戶端推送資料。配合Workerman的定時器，也可以定時推送資料。

快速開始
======
開發者可以從一個簡單的demo開始(demo中包含了GatewayWorker核心，以及start_gateway.php start_business.php等啟動入口檔案)<br>
[點選這裡下載demo](http://www.workerman.net/download/GatewayWorker.zip)。<br>
demo說明見原始碼readme。

手冊
=======
http://www.workerman.net/gatewaydoc/

安裝核心
=======

只安裝GatewayWorker核心檔案（不包含start_gateway.php start_businessworker.php等啟動入口檔案）
```
composer require workerman/gateway-worker
```

使用GatewayWorker開發的專案
=======
## [tadpole](http://kedou.workerman.net/)  
[Live demo](http://kedou.workerman.net/)  
[Source code](https://github.com/walkor/workerman)  
![workerman todpole](http://www.workerman.net/img/workerman-todpole.png)   

## [chat room](http://chat.workerman.net/)  
[Live demo](http://chat.workerman.net/)  
[Source code](https://github.com/walkor/workerman-chat)  
![workerman-chat](http://www.workerman.net/img/workerman-chat.png)  
