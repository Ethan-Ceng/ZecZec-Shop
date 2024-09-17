# 基礎能力 Base
## 使用者授權 OAuth
### 獲取授權訪問令牌
* API宣告

getToken(code: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| code | string | 是  |  授權碼，使用者對應用授權後得到  |

* 出參說明

可前往[alipay.system.oauth.token](https://docs.open.alipay.com/api_9/alipay.system.oauth.token)檢視更加詳細的引數說明。

### 重新整理授權訪問令牌
* API宣告

refreshToken(refreshToken: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| refreshToken | string | 是  |  重新整理令牌，上次換取訪問令牌時得到，見出參的refresh_token欄位  |

* 出參說明

可前往[alipay.system.oauth.token](https://docs.open.alipay.com/api_9/alipay.system.oauth.token)檢視更加詳細的引數說明。

---

## 小程式二維碼 Qrcode
### 建立小程式二維碼
* API宣告

create(urlParam: string, queryParam: string, describe: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| urlParam | string | 是  |  小程式中能訪問到的頁面路徑，例如：page/component/component-pages/view/view  |
| queryParam | string | 是  |  小程式的啟動引數，開啟小程式的query ，在小程式 onLaunch的方法中獲取  |
| describe | string | 是  |  二維碼描述  |

* 出參說明

可前往[alipay.open.app.qrcode.create](https://docs.open.alipay.com/api_5/alipay.open.app.qrcode.create)檢視更加詳細的引數說明。

---

## 圖片 Image
### 上傳圖片
* API宣告

upload(imageName: string, imageFilePath: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| imageName | string | 是  |  圖片名稱  |
| imageFilePath | string | 是  |  待上傳的本地圖片檔案路徑 |

* 出參說明

可前往[alipay.offline.material.image.upload](https://docs.open.alipay.com/api_3/alipay.offline.material.image.upload)檢視更加詳細的引數說明。

---

## 影片 Video
### 上傳影片
* API宣告

upload(videoName: string, videoFilePath: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| videoName | string | 是  |  影片名稱  |
| videoFilePath | string | 是  |  待上傳的本地影片檔案路徑 |

* 出參說明

可前往[alipay.offline.material.image.upload](https://docs.open.alipay.com/api_3/alipay.offline.material.image.upload)檢視更加詳細的引數說明。

---

# 營銷能力 Marketing
## 生活號 OpenLife
### 建立圖文訊息內容
* API宣告

createImageTextContent(title: string, cover: string, content: string, contentComment: string, ctype: string, benefit: string, extTags: string, loginIds: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| title | string | 是  |  標題  |
| cover | string | 是  | 封面圖URL, 尺寸為996*450，最大不超過3M，支援.jpg、.png格式，請先呼叫上傳圖片介面獲得圖片URL  |
| content | string | 是  |  訊息正文（支援富文字）  |
| contentComment | string | 否  |  是否允許評論，T：允許，F：不允許，預設不允許  |
| ctype | string | 否  |  圖文型別：填activity表示活動圖文，不填預設普通圖文  |
| benefit | string | 否  |  活動利益點，圖文型別ctype為activity型別時才需要傳，最多10個字元  |
| extTags | string | 否  |  關鍵詞列表，英文逗號分隔，最多不超過5個  |
| loginIds | string | 否  |  可預覽支付寶賬號列表，需要預覽時才填寫， 英文逗號分隔，最多不超過10個  |

* 出參說明

可前往[alipay.open.public.message.content.create](https://docs.open.alipay.com/api_6/alipay.open.public.message.content.create)檢視更加詳細的引數說明。

### 更新圖文訊息內容
* API宣告

modifyImageTextContent(contentId: string, title: string, cover: string, content: string, couldComment: string, ctype: string, benefit: string, extTags: string, loginIds: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| contentId | string | 是  |  內容ID，透過建立圖文內容訊息介面返回  |
| title | string | 是  |  標題  |
| cover | string | 是  | 封面圖URL, 尺寸為996*450，最大不超過3M，支援.jpg、.png格式，請先呼叫上傳圖片介面獲得圖片URL  |
| content | string | 是  |  訊息正文（支援富文字）  |
| contentComment | string | 否  |  是否允許評論，T：允許，F：不允許，預設不允許  |
| ctype | string | 否  |  圖文型別：填activity表示活動圖文，不填預設普通圖文  |
| benefit | string | 否  |  活動利益點，圖文型別ctype為activity型別時才需要傳，最多10個字元  |
| extTags | string | 否  |  關鍵詞列表，英文逗號分隔，最多不超過5個  |
| loginIds | string | 否  |  可預覽支付寶賬號列表，需要預覽時才填寫， 英文逗號分隔，最多不超過10個  |

* 出參說明

可前往[alipay.open.public.message.content.modify](https://docs.open.alipay.com/api_6/alipay.open.public.message.content.modify)檢視更加詳細的引數說明。

### 群發本文訊息
* API宣告

sendText(text: string)
			
* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| text | string | 是  |  文字訊息內容  |

* 出參說明

可前往[alipay.open.public.message.total.send](https://docs.open.alipay.com/api_6/alipay.open.public.message.total.send)檢視更加詳細的引數說明。

### 群發圖文訊息
* API宣告

sendImageText(articles: [ Article ])
			
* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| articles | Article陣列 | 是  |  圖文訊息內容  |

Article物件說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| title | string | 否  |   圖文訊息標題 |
| desc | string | 是  |  圖文訊息描述   |
| imageUrl | string | 特殊可選  |  圖片連結，對於多條圖文訊息的第一條訊息，該欄位不能為空，請先呼叫上傳圖片介面獲得圖片URL  |
| url | string | 是  |  點選圖文訊息跳轉的連結  |
| actionName | string | 否  |  連結文字   |

* 出參說明

可前往[alipay.open.public.message.total.send](https://docs.open.alipay.com/api_6/alipay.open.public.message.total.send)檢視更加詳細的引數說明。

### 單發模板訊息
* API宣告

sendSingleMessage(toUserId: string, template: Template)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| toUserId | string | 是  |  訊息接收使用者的UserId  |
| template | Template | 是  |  訊息接收使用者的UserId  |

Template物件說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| templateId | string | 是  |  訊息模板ID |
| context | Context | 是  |  訊息模板上下文，即模板中定義的引數及引數值 |

Context物件說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| headColor | string | 是  |  頂部色條的色值，比如#85be53 |
| url | string | 是  |  點選訊息後承接頁的地址 |
| actionName | string | 是 |  底部連結描述文字，如：“檢視詳情”，最多能傳8個漢字或16個英文字元 |
| keyword1 | Keyword | 否  |  模板中佔位符的值及文字顏色 |
| keyword2 | Keyword | 否  |  模板中佔位符的值及文字顏色 |
| first | Keyword | 否  |  模板中佔位符的值及文字顏色  |
| remark | Keyword | 否  |  模板中佔位符的值及文字顏色 |

Keyword物件說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| color | string | 是  | 當前文字顏色，比如#85be53 |
| value | string | 是  |  模板中佔位符的值  |

* 出參說明

可前往[alipay.open.public.message.single.send](https://docs.open.alipay.com/api_6/alipay.open.public.message.single.send)檢視更加詳細的引數說明。

### 生活號訊息撤回
* API宣告

recallMessage(messageId: string)
			
* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| messageId | string | 是  |  訊息ID  |

* 出參說明

可前往[alipay.open.public.life.msg.recall](https://docs.open.alipay.com/api_6/alipay.open.public.life.msg.recall)檢視更加詳細的引數說明。

### 模板訊息行業設定
* API宣告

setIndustry(primaryIndustryCode: string, primaryIndustryName: string, secondaryIndustryCode: string, secondaryIndustryName: string)
			
* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| primaryIndustryCode | string | 是  |  服務窗訊息模板所屬主行業一級編碼，檢視[行業資訊](https://alipay.open.taobao.com/doc2/detail?treeId=197&docType=1&articleId=105043)  |
| primaryIndustryName | string | 是  |  服務窗訊息模板所屬主行業一級名稱  |
| secondaryIndustryCode | string | 是  |  服務窗訊息模板所屬主行業二級編碼 |
| secondaryIndustryName | string | 是  |  服務窗訊息模板所屬主行業二級名稱  |

* 出參說明

可前往[alipay.open.public.template.message.industry.modify](https://docs.open.alipay.com/api_6/alipay.open.public.template.message.industry.modify)檢視更加詳細的引數說明。

### 生活號查詢行業設定
* API宣告

getIndustry()
			
* 入參說明

無

* 出參說明

可前往[alipay.open.public.setting.category.query](https://docs.open.alipay.com/api_6/alipay.open.public.setting.category.query)檢視更加詳細的引數說明。

---


## 支付寶卡包 Pass
### 卡券模板建立
* API宣告

createTemplate(uniqueId: string, tplContent: string)
			
* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| uniqueId | string | 是  |  商戶用於控制模版的唯一性（可以使用時間戳保證唯一性）  |
| tplContent | string | 是  |  模板內容資訊，遵循JSON規範，詳情參見tpl_content[引數說明](https://doc.open.alipay.com/doc2/detail.htm?treeId=193&articleId=105249&docType=1#tpl_content)   |

* 出參說明

可前往[alipay.pass.template.add](https://docs.open.alipay.com/api_24/alipay.pass.template.add)檢視更加詳細的引數說明。

### 卡券模板更新
* API宣告

updateTemplate(uniqueId: string, tplContent: string)
			
* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| uniqueId | string | 是  |  商戶用於控制模版的唯一性（可以使用時間戳保證唯一性）  |
| tplContent | string | 是  |  模板內容資訊，遵循JSON規範，詳情參見tpl_content[引數說明](https://doc.open.alipay.com/doc2/detail.htm?treeId=193&articleId=105249&docType=1#tpl_content)   |

* 出參說明

可前往[alipay.pass.template.update](https://docs.open.alipay.com/api_24/alipay.pass.template.update)檢視更加詳細的引數說明。

### 卡券例項發放
* API宣告

addInstance(tplId: string, tplParams: string, recognitionType: string, recognitionInfo: string)
			
* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| tplId | string | 是  |  支付寶pass模版ID，即呼叫模板建立介面時返回的tpl_id  |
| tplParams | string | 是  |  模版動態引數資訊，對應模板中$變數名$的動態引數，見模板建立介面返回值中的tpl_params欄位。示例：  |
| recognitionType | string | 是  |  Alipass新增物件識別型別，填寫“1”表示訂單資訊  |
| recognitionInfo | string | 是  |  支付寶使用者識別資訊，參見[UID發券元件對接文件](https://docs.open.alipay.com/199/sy3hs4 ) |

* 出參說明

可前往[alipay.pass.instance.add](https://docs.open.alipay.com/api_24/alipay.pass.instance.add)檢視更加詳細的引數說明。

### 卡券例項更新
* API宣告

updateInstance(serialNumber: string, channelId: string, tplParams: string, status: string, verifyCode: string, verifyType: string)
			
* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| serialNumber | string | 是  |  商戶指定卡券唯一值，卡券JSON模板中fileInfo->serialNumber欄位對應的值  |
| channelId | string | 是  |  代理商代替商戶發放卡券後，再代替商戶更新卡券時，此值為商戶的PID/AppID  |
| tplParams | string | 否  |  Alipass新增物件識別型別，填寫“1”表示訂單資訊  |
| status | string | 否  |  券狀態，支援更新為USED、CLOSED兩種狀態 |
| verifyCode | string | 否  |  核銷碼串值（當狀態變更為USED時，建議傳），該值正常為模板中核銷區域（Operation）對應的message值 |
| verifyType | string | 否  |  核銷方式，該值正常為模板中核銷區域（Operation）對應的format值，verifyCode和verifyType需同時傳入 |

* 出參說明

可前往[alipay.pass.instance.update](https://docs.open.alipay.com/api_24/alipay.pass.instance.update)檢視更加詳細的引數說明。

---


## 小程式模板訊息 TemplateMessage
### 傳送模板訊息
* API宣告

send(toUserId: string, formId: string, userTemplateId: string, page: string, data: string)
			
* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| toUserId | string | 是  |  傳送訊息的支付寶賬號  |
| formId | string | 是  |  使用者發生的交易行為的交易號，或者使用者在小程式產生表單提交的表單號，用於資訊傳送的校驗  |
| userTemplateId | string | 是  |  使用者申請的模板id號，固定的模板id會發送固定的訊息  |
| page | string | 是  |  小程式的跳轉頁面，用於訊息中心使用者點選之後詳細跳轉的小程式頁面，例如：page/component/index |
| data | string | 是  |  開發者需要傳送模板訊息中的自定義部分來替換模板的佔位符，例如：{"keyword1": {"value" : "12:00"},"keyword2": {"value" : "20180808"},"keyword3": {"value" : "支付寶"}}  |

* 出參說明

可前往[alipay.open.app.mini.templatemessage.send](https://docs.open.alipay.com/api_5/alipay.open.app.mini.templatemessage.send)檢視更加詳細的引數說明。

---


# 會員能力 Member
## 支付寶身份認證 Identification
### 身份認證初始化
* API宣告

init(outerOrderNo: string, bizCode: string, identityParam: IdentityParam, merchantConfig: MerchantConfig)
			
* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| outerOrderNo | string | 是  |  商戶請求的唯一標識，商戶要保證其唯一性，值為32位長度的字母數字組合，建議前面幾位字元是商戶自定義的簡稱，中間可以使用一段時間，後段可以使用一個隨機或遞增序列  |
| bizCode | string | 是  |  認證場景碼，入參支援的認證場景碼和商戶簽約的認證場景相關，可選值有如下，FACE：多因子人臉認證；CERT_PHOTO：多因子證照認證；CERT_PHOTO_FACE：多因子證照和人臉認證；SMART_FACE：多因子快捷認證  |
| identityParam | IdentityParam | 是  |   需要驗證的身份資訊引數  |
| merchantConfig | MerchantConfig | 是  |  商戶個性化配置  |

IdentityParam物件說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| identityType | string | 是  |  身份資訊引數型別，必須傳入CERT_INFO  |
| certType | string | 是  |  證件型別，當前支援身份證，必須傳入IDENTITY_CARD  |
| certName | string | 是  |  真實姓名 |
| certNo | string | 是  |  證件號碼  |

MerchantConfig物件說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| returnUrl | string | 是  |  需要回跳的目標URL地址，一般指定為商戶業務頁面  |

* 出參說明

可前往[alipay.user.certify.open.initialize](https://docs.open.alipay.com/api_2/alipay.user.certify.open.initialize)檢視更加詳細的引數說明。

### 生成認證連結
* API宣告

certify(certifyId: string)
			
* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| certifyId | string | 是  |  本次申請操作的唯一標識，由身份認證初始化介面呼叫後生成，後續的操作都需要用到  |

* 出參說明

可前往[alipay.user.certify.open.certify](https://docs.open.alipay.com/api_2/alipay.user.certify.open.certify)檢視更加詳細的引數說明。

### 身份認證記錄查詢
* API宣告

query(certifyId: string)
			
* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| certifyId | string | 是  |  身份認證操作的唯一標識，由身份認證初始化介面呼叫後生成  |

* 出參說明

可前往[alipay.user.certify.open.query](https://docs.open.alipay.com/api_2/alipay.user.certify.open.query)檢視更加詳細的引數說明。

---


# 支付能力 Payment
## 通用介面 Common
### 建立交易
* API宣告

create(subject: string, outTradeNo: string, totalAmount: string, buyerId: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| subject | string | 是  |  訂單標題  |
| outTradeNo | string | 是  | 商戶訂單號，64個字元以內，可包含字母、數字、下劃線，需保證在商戶端不重複  |
| totalAmount | string | 是  | 訂單總金額，單位為元，精確到小數點後兩位，取值範圍[0.01,100000000]  |
| buyerId | string | 是 | 買家的支付寶唯一使用者號（2088開頭的16位純數字）  |

* 出參說明

可前往[alipay.trade.create](https://docs.open.alipay.com/api_1/alipay.trade.create)檢視更加詳細的引數說明。

### 查詢交易
* API宣告

query(outTradeNo: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| outTradeNo | string | 是  |  交易建立時傳入的商戶訂單號  |

* 出參說明

可前往[alipay.trade.query](https://docs.open.alipay.com/api_1/alipay.trade.query)檢視更加詳細的引數說明。

### 交易退款
* API宣告

refund(outTradeNo: string, refundAmount: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| outTradeNo | string | 是  |  交易建立時傳入的商戶訂單號  |
| refundAmount | string | 是  |  需要退款的金額，該金額不能大於訂單金額，單位為元，支援兩位小數  |

* 出參說明

可前往[alipay.trade.refund](https://docs.open.alipay.com/api_1/alipay.trade.refund)檢視更加詳細的引數說明。

### 關閉交易
* API宣告

close(outTradeNo: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| outTradeNo | string | 是  |  交易建立時傳入的商戶訂單號  |

* 出參說明

可前往[alipay.trade.close](https://docs.open.alipay.com/api_1/alipay.trade.close)檢視更加詳細的引數說明。

### 撤銷交易
* API宣告

cancel(outTradeNo: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| outTradeNo | string | 是  |  交易建立時傳入的商戶訂單號  |

* 出參說明

可前往[alipay.trade.cancel](https://docs.open.alipay.com/api_1/alipay.trade.cancel)檢視更加詳細的引數說明。

### 交易退款查詢
* API宣告

queryRefund(outTradeNo: string, outRequestNo: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| outTradeNo | string | 是  |  交易建立時傳入的商戶訂單號  |
| outRequestNo | string | 是  |  請求退款介面時，傳入的退款請求號，如果在退款請求時未傳入，則該值為建立交易時的外部交易號  |

* 出參說明

可前往[alipay.trade.fastpay.refund.query](https://opendocs.alipay.com/apis/api_1/alipay.trade.fastpay.refund.query)檢視更加詳細的引數說明。


### 查詢對賬單下載地址
* API宣告

downloadBill(billType: string, billDate: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| billType | string | 是  |  賬單型別，商戶透過介面或商戶經開放平臺授權後其所屬服務商透過介面可以獲取以下賬單型別：trade、signcustomer；trade指商戶基於支付寶交易收單的業務賬單；signcustomer是指基於商戶支付寶餘額收入及支出等資金變動的帳務賬單  |
| billDate | string | 是  |  賬單時間：日賬單格式為yyyy-MM-dd，最早可下載2016年1月1日開始的日賬單；月賬單格式為yyyy-MM，最早可下載2016年1月開始的月賬單  |

* 出參說明

可前往[alipay.data.dataservice.bill.downloadurl.query](https://opendocs.alipay.com/apis/api_15/alipay.data.dataservice.bill.downloadurl.query)檢視更加詳細的引數說明。


### 非同步通知驗籤
* API宣告

verifyNotify(parameters: map[string]string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| parameters | map[string]string | 是  |  非同步通知中收到的待驗籤的所有引數 |

---

## 花唄分期 Huabei
### 建立花唄分期交易
* API宣告

create(subject: string, outTradeNo: string, totalAmount: string, buyerId: string, extendParams: HuabeiConfig)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| subject | string | 是  |  訂單標題  |
| outTradeNo | string | 是  | 商戶訂單號，64個字元以內，可包含字母、數字、下劃線，需保證在商戶端不重複  |
| totalAmount | string | 是  | 訂單總金額，單位為元，精確到小數點後兩位，取值範圍[0.01,100000000]  |
| buyerId | string | 是 | 買家的支付寶使用者ID，如果為空，會從傳入的碼值資訊中獲取買家ID  |
| extendParams | HuabeiConfig | 是  |  花唄交易擴充套件引數  |

HuabeiConfig物件說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| hbFqNum | string | 是  | 花唄分期數，僅支援傳入3、6、12  |
| hbFqSellerPercent | string | 是  | 代表賣家承擔收費比例，商家承擔手續費傳入100，使用者承擔手續費傳入0，僅支援傳入100、0兩種  |


* 出參說明

可前往[alipay.trade.create](https://docs.open.alipay.com/api_1/alipay.trade.create)檢視更加詳細的引數說明。

--- 

<a name="faceToFace"/>

## 當面付 FaceToFace
### 當面付交易付款
* API宣告

pay(subject: string, outTradeNo: string, totalAmount: string, authCode: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| subject | string | 是  |  訂單標題  |
| outTradeNo | string | 是  |  交易建立時傳入的商戶訂單號  |
| totalAmount | string | 是  |  訂單總金額，單位為元，精確到小數點後兩位，取值範圍[0.01,100000000]  |
| authCode | string | 是  |  支付授權碼，即買家的付款碼數字  |

* 出參說明

可前往[alipay.trade.pay](https://docs.open.alipay.com/api_1/alipay.trade.pay)檢視更加詳細的引數說明。

* 返佣說明

ISV對接當面付產品需涉及返佣時，請先閱讀[政策詳情](https://opendocs.alipay.com/p/00fc2g)與[合作攻略](https://opendocs.alipay.com/open/300/taphxd)。


**對接時必須在支付介面的extend_params引數中設定sys_service_provider_id返佣引數 ，引數值為簽約返佣協議的PID**，示例程式碼如下（Java為例）：

```java
Map<String, String> extendParams = new HashMap<>();
extendParams.put("sys_service_provider_id", "<--請填寫ISV簽約協議的PID，比如：2088511833207846-->");
AlipayTradePayResponse response = Factory.Payment.FaceToFace()
	.agent("<--請填寫商戶應用授權後獲取到的app_auth_token，比如：ca34ea491e7146cc87d25fca24c4cD11-->")
	.optional("extend_params", extendParams)
	.pay("iPhone6 16G", "64628156-f784-4572-9540-485b7c91b850", "0.01", "289821051157962364");
```

--- 
### 交易預建立，生成正掃二維碼
* API宣告

precreate(subject: string, outTradeNo: string, totalAmount: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| subject | string | 是  |  訂單標題  |
| outTradeNo | string | 是  |  交易建立時傳入的商戶訂單號  |
| totalAmount | string | 是  |  訂單總金額，單位為元，精確到小數點後兩位，取值範圍[0.01,100000000]  |

* 出參說明

可前往[alipay.trade.precreate](https://docs.open.alipay.com/api_1/alipay.trade.precreate)檢視更加詳細的引數說明。

* 返佣說明

ISV對接當面付產品需涉及返佣時，請先閱讀[政策詳情](https://opendocs.alipay.com/p/00fc2g)與[合作攻略](https://opendocs.alipay.com/open/300/taphxd)。


**對接時必須在支付介面的extend_params引數中設定sys_service_provider_id返佣引數 ，引數值為簽約返佣協議的PID**，示例程式碼如下（Java為例）：

```java
Map<String, String> extendParams = new HashMap<>();
extendParams.put("sys_service_provider_id", "<--請填寫ISV簽約協議的PID，比如：2088511833207846-->");
AlipayTradePrecreateResponse response = Payment.FaceToFace()
	.agent("<--請填寫商戶應用授權後獲取到的app_auth_token，比如：ca34ea491e7146cc87d25fca24c4cD11-->")
	.optional("extend_params", extendParams)
	.preCreate("iPhone6 16G", "64628156-f784-4572-9540-485b7c91b850", "0.01");
```

--- 
## 電腦網站 Page
### 電腦網站支付
* API宣告

pay(subject: string, outTradeNo: string, totalAmount: string, returnUrl: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| subject | string | 是  |  訂單標題  |
| outTradeNo | string | 是  |  交易建立時傳入的商戶訂單號  |
| totalAmount | string | 是  |  訂單總金額，單位為元，精確到小數點後兩位，取值範圍[0.01,100000000]  |
| returnUrl | string | 否  |  支付成功後同步跳轉的頁面，是一個http/https開頭的字串  |

* 出參說明

可前往[alipay.trade.page.pay](https://docs.open.alipay.com/api_1/alipay.trade.page.pay)檢視更加詳細的引數說明。

--- 

## 手機網站 Wap
### 手機網站支付
* API宣告

pay(subject: string, outTradeNo: string, totalAmount: string, quitUrl: string, returnUrl: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| subject | string | 是  |  訂單標題  |
| outTradeNo | string | 是  |  交易建立時傳入的商戶訂單號  |
| totalAmount | string | 是  |  訂單總金額，單位為元，精確到小數點後兩位，取值範圍[0.01,100000000]  |
| quitUrl | string | 是  |  使用者付款中途退出返回商戶網站的地址  |
| returnUrl | string | 否 |  支付成功後同步跳轉的頁面，是一個http/https開頭的字串  |

* 出參說明

可前往[alipay.trade.wap.pay](https://docs.open.alipay.com/api_1/alipay.trade.wap.pay)檢視更加詳細的引數說明。

--- 

## App支付 App
### 手機APP支付
* API宣告

pay(subject: string, outTradeNo: string, totalAmount: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| subject | string | 是  |  訂單標題  |
| outTradeNo | string | 是  |  交易建立時傳入的商戶訂單號  |
| totalAmount | string | 是  |  訂單總金額，單位為元，精確到小數點後兩位，取值範圍[0.01,100000000]  |

* 出參說明

可前往[alipay.trade.app.pay](https://docs.open.alipay.com/api_1/alipay.trade.app.pay)檢視更加詳細的引數說明。

---

# 安全能力 Security
## 文字風險識別 TextRisk
### 檢測內容風險
* API宣告

detect(content: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| content | string | 是  |  待檢測的文字內容 |

* 出參說明

可前往[alipay.security.risk.content.detect](https://docs.open.alipay.com/api_49/alipay.security.risk.content.detect)檢視更加詳細的引數說明。

---

# 輔助工具 Util


## 加解密 AES
### AES解密（常用於會員手機號解密）
* API宣告

decrypt(cipherText: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| cipherText | string | 是  |  密文 |

* 出參說明

| 型別     |  說明 |
|------|----|
| string | 明文|

### AES加密
* API宣告

encrypt(plainText: string)

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| plainText | string | 是  |  明文 |

* 出參說明

| 型別     |  說明 |
|------|----|
| string | 密文|


<a name="generic"/>

## 通用介面 Generic
### 執行OpenAPI呼叫
* API宣告

execute(method: string, textParams: map[string]string, bizParams: map[string]any): AlipayOpenApiGenericResponse

* 介面說明

對於Alipay Easy SDK尚未支援的Open API，開發者可以透過呼叫此方法，透過自行拼裝請求引數，完成大部分OpenAPI的呼叫，且呼叫時可按需設定所有可選引數。本介面同樣會自動為您完成請求的加簽和響應的驗籤工作。
注：本介面不支援檔案型欄位的上傳。

* 入參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| method | string | 是  |  OpenAPI的名稱，例如：alipay.trade.pay |
| textParams | map[string]string | 否  |  **沒有**包裝在`biz_content`下的請求引數集合，例如`app_auth_token`等引數 |
| bizParams | map[string]any | 否  |  被包裝在`biz_content`下的請求引數集合 |

* 出參說明

| 欄位名  | 型別     | 必填 | 說明 |
|------|--------|----|----|
| httpBody | string | 是  |  閘道器返回的HTTP響應，是一個JSON格式的字串，開發者可按需從中解析出響應引數，響應示例：{"alipay_trade_create_response":{"code":"10000","msg":"Success","out_trade_no":"4ac9eac...","trade_no":"202003..."},"sign":"AUumfYgGSe7...02MA=="} |
| code | string | 是  |  [閘道器返回碼](https://docs.open.alipay.com/common/105806) |
| msg | string | 是  |  [閘道器返回碼描述](https://docs.open.alipay.com/common/105806) |
| subCode | string | 否  |  業務返回碼，參見具體的API介面文件 |
| subMsg | string | 否  |  業務返回碼描述，參見具體的API介面文件 |

---




