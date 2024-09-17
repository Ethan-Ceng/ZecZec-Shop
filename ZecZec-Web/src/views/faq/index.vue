<script setup lang="ts">
import Items from './items.vue'
</script>

<template>
  <div class="max-w-3xl mx-auto py-16 px-4">
    <div class="cf">
      <h2 class="text-2xl font-bold mt-0 xs:float-left">
        常見問答
      </h2>
    </div>
    <h3 class="text-xl font-bold mt-8">發起計畫</h3>

    <Items />
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        集資的時間有限制嗎？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>嘖嘖平臺上發起最短的計畫為一天；最長的計畫為五個月。我們通常建議集資計畫在 35-45 天是最理想的。計畫時間過短或太長，都對計畫集資幫助不大。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        集資目標有限制嗎？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>若您是以「自然人」發起計畫，任意連續三十天內集資金額上限為新臺幣 100 萬；以「法人」發起計畫，第一階段上限為新臺幣 1,500 萬，接近上限金額時，可與嘖嘖平臺討論並申請開放第二階段額度。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        發起計畫需要付錢嗎？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>發起計畫不需支付任何費用，只有集資成功的專案需付平臺手續費及金流費用。專案成功後，嘖嘖將收取您計畫最後集資總金額的 2.5%* 金流基本服務費及 5.5% 嘖嘖手續費。嘖嘖收取手續費將用於網站維護及資安保護等，確保平臺能長久經營，幫助更多的創作人。<br><br> *依不同的付款方式可能會有不同的金流費用比例，詳見提案契約。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        我發起的計畫集資成功了，什麼時候才能拿到贊助的款項呢？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>計畫結束後，嘖嘖的金流信託公司將扣除交付款項所需之金流及手續費共計 8%* 後，於次月十三日前，將剩餘之項給付給提案人。<br><br> *依不同的付款方式可能會有不同的金流費用比例，詳見提案契約。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        我發起的計畫集資成功了，我該怎麼聯絡贊助人？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>計畫成功結束後，提案人的可以在計畫頁面工具箱中找到「贊助人列表」，其中包含所有贊助人的聯絡資料和贊助細節。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        我可以在嘖嘖上發起公益計畫嗎？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>在嘖嘖可以發起公益計畫，但需符合《公益勸募條例》規範，包括第 5 條對於發起計畫身份之確認，並需具有該年度的公益勸募許可文號，此外，您的計畫中若有純捐款，需提供贊助者可抵稅之捐款收據。<br><br>
          《公益勸募條例》第 5 條，本條例所稱勸募團體如下：<br>
          一、公立學校<br>
          二、行政法人<br>
          三、公益性社團法人<br>
          四、財團法人<br><br>
          若無法判斷欲發起計畫是否受《公益勸募條例》規範，可聯絡衛福部公益勸募相關法令釋疑專線：02-8590-6651</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        我有提供回饋品給贊助者，是否就不受《公益勸募條例》規範，即不需勸募字號也可以上架公益計畫？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>不是的，根據《公益勸募條例》規範，只要文案中宣稱集資金額將使「不特定多數人」受惠，即屬「公益」範疇，不論是否有提供回饋品，均需符合規範。提醒您，若經判定為違法之公益勸募計畫，主管機關有權要求計畫將勸募所得財物返還捐贈人。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        我想要提案，但是我的內容還沒有準備好，可以先和您們討論嗎？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>在嘖嘖上提案後至正式上線前，您都可以繼續修改計畫內容，所以不用擔心資料還沒準備完善就提案，之後有的是時間修改！但請注意還是必須提供足夠的計畫內容以供嘖嘖專案部評估。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        為什麼計畫要經過審核？審核什麼部分？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>「群眾集資」即意味著將計畫交到贊助人眼前，由他們決定計畫是否成功，嘖嘖作為媒介平臺，不介入專案計畫及執行，但為了維護產業生態的健康，您發起計畫後，將經過以下四個階段的審核：<br><br>
          請留意，審核透過不代表任何法律或道德上的背書，您仍需為您發起的專案負擔所有責任。<br><br>
          （1）提案後：我們會初步評估計畫應是合理且合法的，包括但不限於：<br>
          - 合法性檢查：計畫應確保未違反法律在地及國際法規。<br>
          - 計畫合理性：提案應包括說明計畫執行方式及如何使用集資資金、計畫的時間表，這有助幫助贊助人確認計畫是具體的，且有可能成功實踐。<br>
          - 相關檔案：我們會希望您出示計畫中的必要的合法檔案，如檢驗證明、專利證明、報價單等。<br><br>
          （2）初審後：身份及金流確認，包括但不限於：<br>
          - 身份驗證：無論是以自然人或法人發起專案，均需提供不同的檔案，以確認是合法存在的實體。<br><br>
          （3）上架後：提案上架後，我們仍會持續確保計畫的合法性和品質，包括但不限於：<br>
          - 保護專利及智慧產財權：發起專案後，若存在侵權或盜用他人設計之風險，必要時，我們會要求提案者提供進一步的權利證明檔案。<br>
          - 緊急應對：若計畫集資過程中衍生高度消費爭議，我們會在謹慎評估後暫停或下架計畫，以保護贊助者和合法提案者的權益。<br><br>
          （4）執行後：實踐及貫徹專案的能力，有無和贊助者溝通的誠意，包括但不限於：<br>
          - 溝通和透明度：提案者應定期向贊助者提供計畫進展報告，確保計畫透明度和溝通，並及時解決問題。<br>
          - 風險管理：提案者應對執行中的風險，例如生產延遲或預算超支，並找到解決方案。<br>
          - 計畫成果：若您的曾有計畫在交付集資金額後執行失敗，未能成功交付回饋品的記錄，會降低您下一次計畫的審核機率。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        我想在嘖嘖發起計畫來販售產品，需要注意什麼法律問題嗎？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>在嘖嘖上發起計畫集資，用於研發、預購預售產品等，均需留意產品屬性有關之相關法律規範，如進口具有藍芽功能等產品，根據電信法及國家電訊傳播委員會（NCC）規範，商品須先完成 NCC 檢驗，並在上架嘖嘖時於集資頁面載明 NCC 認證號碼及 BSMI 相關認證，始得集資。如發起計畫集資販售食品、食物容器等，依衛福部規範，應先向主管機關申請登入業者身分等。</p>
      </div>
    </div>
    <h3 class="text-xl font-bold mt-8">贊助計畫</h3>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        我人不在臺灣/不是臺灣人可以支援計畫嗎？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>可以，但是不是所有計畫的回饋品都會運送到臺灣以外的地區。建議您在贊助計畫前使用計畫頁面上的聯絡提案人功能來和提案者確認。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        贊助計畫以後什麼時候才會收到回饋？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>每一個計畫回饋的選項不一樣，在回饋選項說明下方都會標示預計送出日期，但因為選項的預計送出日期都是估計，所以不一定會準確。請密切注意提案人的更新、動態來確定時間。如果有疑惑請使用計畫頁面上的聯絡提案人功能來和提案者確認。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        我支援的計畫沒有成功 / 失敗了的話，我贊助的錢跑去哪裡？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>如果計畫期限到了仍然沒有達到目標，嘖嘖會透過您贊助時的原付款方式退還您所支援的金額（不包含虛擬帳號轉帳交易手續費）。您將會收到金流公司所寄出的退款信件，請根據信中的指示完成退款步驟。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        我想取消贊助或退款，該怎麼做？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>如果您所支援的計畫仍在計畫集資期間尚未結束，您可以透過贊助細節頁面的「取消贊助並退款」按鈕來申請，我們會透過您贊助時的原付款方式退還您所支援的金額（不包含虛擬帳號轉帳交易手續費）。如果該計畫已經集資結束，您則需要與提案人聯絡。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        我想要贊助計畫，可以透過哪些方式呢？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>嘖嘖目前有提供國內外信用卡刷卡以及實體 ATM 付款。礙於若計畫失敗可能需要退款的狀況，嘖嘖暫時不提供超商繳費的功能。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        我還沒有收到贊助回饋，怎麼辦？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>在您贊助的計畫成功後，建議您密切留意「專案更新」瞭解計畫執行進度。若有疑問，可使用計畫頁面上的聯絡提案人功能來和提案者確認。也可直接透過專案頁面展示的提案團隊客服聯絡方式，到提案計畫粉絲專頁、網站、e-mail 或電話等資訊等向提案者詢問。如未能獲得提案團隊的回覆，請來信 <a href="mailto:support@zeczec.com">support@zeczec.com</a> 告知狀況，我們將聯絡提案團隊儘速處理。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        贊助計畫會有收據嗎？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>贊助嘖嘖的計畫後，您會收到由嘖嘖合作的金流公司所寄出的 Email 收款證明，此證明並非電子發票，各專案若有發票或收據，將由提案團隊開立給贊助人。贊助回饋的發票或收據，在計畫成功達標結束後，將由計畫提案人負責處理。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        我使用實體 ATM 轉帳了，為什麼遲遲沒有收到贊助成功確認信件？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>若在 24 小時後仍未收到確認信件，也確認金額有離開您的銀行帳戶，請聯絡嘖嘖 <a href="mailto:support@zeczec.com">support@zeczec.com</a> ，並提供您的訂單編號。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        如何訂閱？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>訂閱式贊助提供提案團隊長期的贊助，多數的訂閱是透過約定授扣完成。約定授扣在贊助人同意後，會於每月月初進行扣款。部份專案也提供一次給付多期金額的付款方式。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        如何停止訂閱、取消授扣？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>贊助人可透過「贊助記錄」，在「修改 / 檢視記錄」頁面中，透過「停止訂閱」功能按鍵停止訂閱授扣。<br>
          若為一次給付多期的訂閱，依該筆款項狀況可能由提案團隊或嘖嘖客服執行退款，詳細情況請提供贊助編號來信 <a href="mailto:support@zeczec.com">support@zeczec.com</a> 詢問。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        請問嘖嘖處理爭議的流程是什麼？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>(1) 確認是您自身下單的贊助 / 預購，或是遭到他人盜刷<br>
          - 若遭到他人盜刷，請發現盜刷後立即向發卡銀行通報。<br><br>
          (2) 贊助的回饋已經超過預計寄送時間<br>
          - 首先請至贊助記錄頁面檢視您的贊助預計送出日期。<br>
          - 如已逾回饋預計送出日期，請至該專案頁面檢視「專案更新」是否有回饋品項寄送相關資訊。<br>
          - 若以上資訊仍無法讓您確認出貨日期，或出貨日期明顯已過，您可以透過各專案內容中的「客服聯絡方式」與提案團隊直接聯絡。<br>
          - 與提案團隊聯絡時，請附贊助編號及收件資訊，以利讓提案團隊確認資訊、縮短處理時間，以便盡快為您提供協助。<br>
          - 如未能獲得提案團隊的回覆，請來信 <a href="mailto:support@zeczec.com">support@zeczec.com</a> 告知狀況，我們將聯絡提案團隊儘速處理。<br><br>
          (3) 提案團隊未如預期更新進度<br>
          - 請透過各專案內容中的「客服聯絡方式」與提案團隊就更新進度事宜直接聯絡。<br>
          - 與提案團隊聯絡時，請附贊助編號，以利提案團隊確認資訊、縮短處理時間，以便盡快為您提供協助<br><br>
          (4) 回饋品項不如預期<br>
          - 在臺灣目前群眾集資平臺的贊助行為仍適用於《消保法》第 19 條通訊交易範疇，提案團隊須提供贊助者 7 天鑑賞期，且不得要求贊助者承擔退貨運費或其他營運成本。<br>
          - 請透過各專案內容中的「客服聯絡方式」與提案團隊就回饋品狀況直接聯絡。<br>
          - 與提案團隊聯絡時，請附贊助編號和實品圖片，並充份敘述與預期不符之處。以利提案團隊確認資訊、縮短處理時間、以利盡快為您提供協助。<br>
          - 若提案團隊未及時回覆或擱置您的退款請求，請來信 <a href="mailto:support@zeczec.com">support@zeczec.com</a> 告知，我們將協助聯絡提案團隊儘速處理。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        請問當我覺得在贊助過程中權益受損時，我可以遵循什麼法規或條例向嘖嘖申訴呢？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>您可先參考嘖嘖的提案人合約，若發起計畫的提案人有任何違反合約的行為，在您自行溝通未果後，均可來信 <a href="mailto:support@zeczec.com">support@zeczec.com</a> 告知，我們將聯絡提案團隊儘速處理。<br><br>
          另外群眾集資平臺業者與數位發展部共同研擬的「集資平臺之贊助者保護措施自律規範」，也具體闡述了嘖嘖平臺為保護贊助者權益的機制。<br><br>
          *<a href="https://drive.google.com/file/d/1TbMrzKf_zV5dFjgeSkL6dvToTTfa2cCL/view?usp=sharing">集資平臺之贊助者保護措施自律規範｜完整條文</a></p>
      </div>
    </div>
    <h3 class="text-xl font-bold mt-8">其他</h3>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        我想要採訪嘖嘖，怎麼聯絡您們？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>請寄信到 <a href="mailto:support+press@zeczec.com">support+press@zeczec.com</a> 。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        您們有在接受合作提案嗎？要跟誰聯絡？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>嘖嘖一直都很希望能參與鼓勵創意人的合作，舉凡講座、座談會、創意市集、表演、聚會活動嘖嘖都很有興趣參與。請寄信到 <a href="support+collab@zeczec.com">support+collab@zeczec.com</a> 。如果您是想和嘖嘖上面的計畫提案人合作，請使用計畫頁面上的聯絡提案人功能來連絡提案人。</p>
      </div>
    </div>
    <div class="js-details block bg-zinc-100 border border-transparent mb-4 rounded hover:border-neutral-600 mt-4">
      <div class="js-summary font-bold p-4">
        請問可以刪除個人帳號嗎？
      </div>
      <div class="bg-white mv-child-0 p-4 rounded-sm mb-4 mx-4 hidden">
        <p>請來信 <a href="support+collab@zeczec.com">support+collab@zeczec.com</a> 並附上您的帳號信箱。</p>
      </div>
    </div>
    <div class="mt-8 text-sm text-center pt-8 border-t border-neutral-200">
      還是沒有你要的答案？<a href="mailto:support@zeczec.com">聯絡我們</a>。
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
