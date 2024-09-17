# 訊息

公眾號訊息分為 [**服務端被動回覆訊息**](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Passive_user_reply_message.html) 和 [**客服訊息**](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Service_Center_messages.html) 兩個場景。

需要注意的是兩個場景的訊息雖然類似，但是結構卻有些差異，比如服務端使用 XML 結構，而客服訊息使用 JSON 結構，且同樣類似的訊息型別，結構和名稱都有些許差異，在使用時請勿混淆。

## 服務端訊息結構

當你接收到使用者發來的訊息時，可能會提取訊息中的相關屬性，參考：

請求訊息基本屬性(以下所有訊息都有的基本屬性)：

```
  - `ToUserName`    接收方帳號（該公眾號 ID）
  - `FromUserName`  傳送方帳號（OpenID, 代表使用者的唯一標識）
  - `CreateTime`    訊息建立時間（時間戳）
  - `MsgId`        訊息 ID（64位整型）
```

### 文字

```
  - `MsgType`  text
  - `Content`  文字訊息內容
```

### 圖片

```
  - `MsgType`  image
  - `MediaId`  圖片訊息媒體id，可以呼叫多媒體檔案下載介面拉取資料。
  - `PicUrl`   圖片連結
```

### 語音

```
  - `MsgType`        voice
  - `MediaId`        語音訊息媒體id，可以呼叫多媒體檔案下載介面拉取資料。
  - `Format`         語音格式，如 amr，speex 等
  - `Recognition`  * 開通語音識別後才有
```

> 請注意，開通語音識別後，使用者每次傳送語音給公眾號時，微信會在推送的語音訊息 XML 資料包中，增加一個 `Recongnition` 欄位

### 影片

```
  - `MsgType`       video
  - `MediaId`       影片訊息媒體id，可以呼叫多媒體檔案下載介面拉取資料。
  - `ThumbMediaId`  影片訊息縮圖的媒體id，可以呼叫多媒體檔案下載介面拉取資料。
```

### 小影片

```
  - `MsgType`     shortvideo
  - `MediaId`     影片訊息媒體id，可以呼叫多媒體檔案下載介面拉取資料。
  - `ThumbMediaId`    影片訊息縮圖的媒體id，可以呼叫多媒體檔案下載介面拉取資料。
```

### 事件訊息

```
  - `MsgType`     event
  - `Event`       事件型別 （如：subscribe(訂閱)、unsubscribe(取消訂閱) ...， CLICK 等）
```

#### 掃描帶引數二維碼事件

```
  - `EventKey`    事件KEY值，比如：qrscene_123123，qrscene_為字首，後面為二維碼的引數值
  - `Ticket`      二維碼的 ticket，可用來換取二維碼圖片
```

#### 上報地理位置事件

```
  - `Latitude`    23.137466   地理位置緯度
  - `Longitude`   113.352425  地理位置經度
  - `Precision`   119.385040  地理位置精度
```

#### 自定義選單事件

```
  - `EventKey`    事件KEY值，與自定義選單介面中KEY值對應，如：CUSTOM_KEY_001, www.qq.com
```

### 地理位置

```
  - `MsgType`     location
  - `Location_X`  地理位置緯度
  - `Location_Y`  地理位置經度
  - `Scale`       地圖縮放大小
  - `Label`       地理位置資訊
```

### 連結

```
  - `MsgType`      link
  - `Title`        訊息標題
  - `Description`  訊息描述
  - `Url`          訊息連結
```

### 檔案

```
  - `MsgType`      file
  - `Title`        檔名
  - `Description`  檔案描述，可能為null
  - `FileKey`      檔案KEY
  - `FileMd5`      檔案MD5值
  - `FileTotalLen` 檔案大小，單位位元組
```

## 客服訊息結構

### 傳送文字訊息

```json
{
  "touser": "OPENID",
  "msgtype": "text",
  "text": {
    "content": "Hello World"
  }
}
```

### 圖片訊息

```json
{
  "touser": "OPENID",
  "msgtype": "image",
  "image": {
    "media_id": "MEDIA_ID"
  }
}
```

### 語音訊息

```json
{
  "touser": "OPENID",
  "msgtype": "voice",
  "voice": {
    "media_id": "MEDIA_ID"
  }
}
```

### 影片訊息

```json
{
  "touser": "OPENID",
  "msgtype": "video",
  "video": {
    "media_id": "MEDIA_ID",
    "thumb_media_id": "MEDIA_ID",
    "title": "TITLE",
    "description": "DESCRIPTION"
  }
}
```

### 音樂訊息

```json
{
  "touser": "OPENID",
  "msgtype": "music",
  "music": {
    "title": "MUSIC_TITLE",
    "description": "MUSIC_DESCRIPTION",
    "musicurl": "MUSIC_URL",
    "hqmusicurl": "HQ_MUSIC_URL",
    "thumb_media_id": "THUMB_MEDIA_ID"
  }
}
```

### 圖文訊息（點選跳轉到外鏈）

```json
{
  "touser": "OPENID",
  "msgtype": "news",
  "news": {
    "articles": [
      {
        "title": "Happy Day",
        "description": "Is Really A Happy Day",
        "url": "URL",
        "picurl": "PIC_URL"
      }
    ]
  }
}
```

### 圖文訊息（點選跳轉到圖文訊息頁面）

```json
{
  "touser": "OPENID",
  "msgtype": "mpnews",
  "mpnews": {
    "media_id": "MEDIA_ID"
  }
}
```

### 選單訊息

```json
{
  "touser": "OPENID",
  "msgtype": "msgmenu",
  "msgmenu": {
    "head_content": "您對本次服務是否滿意呢? "
    "list": [
      {
        "id": "101",
        "content": "滿意"
      },
      {
        "id": "102",
        "content": "不滿意"
      }
    ],
    "tail_content": "歡迎再次光臨"
  }
}
```

### 卡券訊息

```json
{
  "touser": "OPENID",
  "msgtype": "wxcard",
  "wxcard": {
    "card_id": "123dsdajkasd231jhksad"
  }
}
```

> 請以官方文件為準。
