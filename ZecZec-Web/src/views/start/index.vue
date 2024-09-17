<script setup lang="ts">
import BgImg from '@/assets/image/start.jpg'
import {Check, Comment} from "@element-plus/icons-vue";
</script>

<template>
  <div class="mx-auto mb-16 flex container flex-col">
    <div class="py-5 relative px-4 lg:px-0">
      <img width="1600" height="900" class="object-cover rounded w-full aspect-[4/3] md:aspect-video" :src="BgImg">
      <div class="absolute top-0 left-0 right-0 bottom-0 flex items-center justify-center">
        <div class="p-2 md:p-5 rounded bg-stone-50 shadow text-stone-700">
          <h2 class="text-2xl md:text-3xl font-semibold p-5 md:py-8 md:px-16 rounded text-center border-2 border-current">
            讓美好的事物發生
            <span class="text-lg mt-2 block">全臺最大的群眾集資平臺</span>
          </h2>
        </div>
      </div>
    </div>
    <div class="text-center">
      <h2 class="text-2xl font-bold my-16 inline-block after:h-1 after:block after:bg-emerald-500 after:rounded after:mt-1">
        關於嘖嘖
      </h2>
      <div class="flex text-xl font-semibold">
        <section class="mb-16 w-full sm:w-1/2 md:w-1/4">
          <h3 class="mb-4">集資累積金額</h3>
          <h4 class="text-4xl text-center text-zec-green">100 億 +</h4>
        </section>
        <section class="mb-16 w-full sm:w-1/2 md:w-1/4">
          <h3 class="mb-4">總成功計畫數</h3>
          <h4 class="text-4xl text-center text-zec-green">5,600 +</h4>
        </section>
        <section class="mb-16 w-full sm:w-1/2 md:w-1/4">
          <h3 class="mb-4">會員數</h3>
          <h4 class="text-4xl text-center text-zec-green">290 萬 +</h4>
        </section>
        <section class="mb-16 w-full sm:w-1/2 md:w-1/4">
          <h3 class="mb-4">月均瀏覽</h3>
          <h4 class="text-4xl text-center text-zec-green">950 萬 +</h4>
        </section>
      </div>
    </div>
    <div class="text-center">
      <h2 class="text-2xl font-bold mt-4 mb-16 inline-block after:h-1 after:block after:bg-emerald-500 after:rounded after:mt-1">
        提案類別
      </h2>
      <div class="flex items-stretch">
        <section class="px-4 w-full mb-8 md:w-1/3">
          <a class="block rounded-md border-2 border-zec-green pt-6 pb-4 h-full flex flex-col group hover:shadow" href="?type=crowdfunding"><h3 class="text-xl font-bold mb-3">群眾集資</h3>
            <h4 class="font-semibold text-zec-green mb-6">
              在一定期限內
              <br>
              募集完成計畫所需的資金
            </h4>
            <div class="bg-gray-50 text-left mx-4 px-4 rounded p-4 mt-auto">
              <ul class="text-gray-600 leading-relaxed mb-8">
                <li class="mb-5 flex items-center">
                  <el-icon class="material-icons text-2xl align-middle text-zec-green flex-initial mr-2"><Check /></el-icon>
                  <span class="flex-1">一次型專案</span>
                </li>
                <li class="mb-5 flex items-center">
                  <el-icon class="material-icons text-2xl align-middle text-zec-green flex-initial mr-2"><Check /></el-icon>
                  <span class="flex-1">平均集資時程 45 - 60 天</span>
                </li>
                <li class="mb-5 flex items-center">
                  <el-icon class="material-icons text-2xl align-middle text-zec-green flex-initial mr-2"><Check /></el-icon>
                  <span class="flex-1">世界首發專案，尚未於全球通路官方上市販售</span>
                </li>
                <li class="mb-5 flex items-center">
                  <el-icon class="material-icons text-2xl align-middle text-zec-green flex-initial mr-2"><Check /></el-icon>
                  <span class="flex-1">專案達成後，一次性回饋贊助者</span>
                </li>
              </ul>
              <span class="block text-white bg-zec-green py-2 rounded font-bold text-center group-hover:brightness-90">
開始提案
</span>
            </div>
          </a></section>
        <section class="px-4 w-full mb-8 md:w-1/3">
          <a class="block rounded-md border-2 border-zec-blue pt-6 pb-4 h-full flex flex-col group hover:shadow" href="?type=presale"><h3 class="text-xl font-bold mb-3">預購專案</h3>
            <h4 class="font-semibold text-zec-blue mb-6">
              讓你的新產品在上市前
              <br>
              進行集客預購
            </h4>
            <div class="bg-gray-50 text-left mx-4 px-4 rounded p-4 mt-auto">
              <ul class="text-gray-600 leading-relaxed mb-8">
                <li class="mb-5 flex items-center">
                  <el-icon class="text-2xl align-middle text-zec-blue flex-initial mr-2"><Check /></el-icon>
                  <span class="flex-1">一次型專案</span>
                </li>
                <li class="mb-5 flex items-center">
                  <el-icon class="text-2xl align-middle text-zec-blue flex-initial mr-2"><Check /></el-icon>
                  <span class="flex-1">平均集資時程 45 - 60 天</span>
                </li>
                <li class="mb-5 flex items-center">
                  <el-icon class="text-2xl align-middle text-zec-blue flex-initial mr-2"><Check /></el-icon>
                  <span class="flex-1">臺灣首發專案，尚未於臺灣通路官方上市販售</span>
                </li>
                <li class="mb-5 flex items-center">
                  <el-icon class="text-2xl align-middle text-zec-blue flex-initial mr-2"><Check /></el-icon>
                  <span class="flex-1">專案達成後，一次性回饋贊助者</span>
                </li>
              </ul>
              <span class="block text-white bg-zec-blue py-2 rounded font-bold text-center group-hover:brightness-90">
開始提案
</span>
            </div>
          </a></section>
        <section class="w-full mb-8 px-4 md:w-1/3">
          <a class="block rounded-md border-2 border-zec-purple pt-6 pb-4 h-full flex flex-col group hover:shadow hover:outline-2" href="?type=subscription"><h3 class="text-xl font-bold mb-3">訂閱專案</h3>
            <h4 class="font-semibold text-zec-purple mb-6">
              用持續性的計畫
              <br>
              累積每月可獲得的贊助
            </h4>
            <div class="bg-gray-50 text-left mx-4 px-4 rounded p-4 mt-auto">
              <ul class="text-gray-600 leading-relaxed mb-8">
                <li class="mb-5 flex items-center">
                  <el-icon class="text-2xl align-middle text-zec-purple flex-initial mr-2"><Check /></el-icon>
                  <span class="flex-1">長期型專案</span>
                </li>
                <li class="mb-5 flex items-center">
                  <el-icon class="text-2xl align-middle text-zec-purple flex-initial mr-2"><Check /></el-icon>
                  <span class="flex-1">可持續集資至專案結束</span>
                </li>
                <li class="mb-5 flex items-center">
                  <el-icon class="text-2xl align-middle text-zec-purple flex-initial mr-2"><Check /></el-icon>
                  <span class="flex-1">尚未於其他平臺提供相同主題的訂閱計畫</span>
                </li>
                <li class="mb-5 flex items-center">
                  <el-icon class="text-2xl align-middle text-zec-purple flex-initial mr-2"><Check /></el-icon>
                  <span class="flex-1">專案進行期間，持續回饋贊助者</span>
                </li>
              </ul>
              <span class="block text-white bg-zec-purple py-2 rounded font-bold text-center group-hover:brightness-90">
開始提案
</span>
            </div>
          </a></section>
      </div>
      <div class="text-center w-full leading-relaxed text-gray-600 border-t border-neutral-200 pt-8 mt-8">
        <div class="block xs:inline-block">
          <el-icon class="material-icons align-middle text-2xl mr-2"><Comment /></el-icon>不確定怎麼準備專案？
      </div>現在就加入<a class="mt-4 xs:mt-0" href="https://www.facebook.com/groups/172950163462473/">嘖嘖提案人前哨站</a>與大家切磋討論。
      </div>
    </div>
    <section class="text-center">
      <h2 class="text-2xl font-bold my-16 inline-block after:h-1 after:block after:bg-emerald-500 after:rounded after:mt-1">
        常見問答
      </h2>
      <div class="container px-4 flex flex-col text-left">
        <details class="group p-4 border-y border-gray-200 reset-details">
          <summary class="flex items-center justify-between py-1 cursor-pointer font-semibold">
            <h3 class="flex-1">我想要提案，但是我的內容還沒有準備好，可以先和你們討論嗎？</h3>
            <svg aria-hidden="true" class="flex-initial w-5 h-5 rotate-0 transform text-gray-500 stroke-2 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 9l-7 7-7-7" strokelinecap="round" strokelinejoin="round"></path>
            </svg>
          </summary>
          <p class="text-gray-600 leading-loose text-sm mt-2 max-w-3xl">
            在嘖嘖上提案後至正式上線前，您都可以繼續修改計畫內容，所以不用擔心資料還沒準備完善就提案，之後有的是時間修改！但請注意還是必須提供足夠的計畫內容以供嘖嘖專案部評估。
            <br>
            專案透過審核後，您會被指派一位專屬的專案經理協助您後續的專案執行，若在提案前有其他進一步問題，可以加入「
            <a href="https://www.facebook.com/groups/172950163462473/">嘖嘖提案人前哨站</a>
            」Facebook 粉絲團與大家切磋討論，或透過客服信箱 (<a href="mailto:support@zeczec.com">support@zeczec.com</a>) 聯絡我們。
          </p>
        </details>
        <details class="group p-4 border-b border-gray-200 reset-details">
          <summary class="flex items-center justify-between py-1 cursor-pointer font-semibold">
            <h3 class="flex-1">發起募資專案有什麼身份資格限制嗎？</h3>
            <svg aria-hidden="true" class="flex-initial w-5 h-5 rotate-0 transform text-gray-500 stroke-2 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 9l-7 7-7-7" strokelinecap="round" strokelinejoin="round"></path>
            </svg>
          </summary>
          <p class="text-gray-600 leading-loose text-sm mt-2 max-w-3xl">
            為符合本國法規，提案人必須是本國的自然人、或是在臺灣註冊的法人或機關團體。若您是外國人，可以透過在臺灣註冊的法人或其他代理人來負責發起募資計畫。
          </p>
        </details>
        <details class="group p-4 border-b border-gray-200 reset-details">
          <summary class="flex items-center justify-between py-1 cursor-pointer font-semibold">
            <h3 class="flex-1">集資目標有限制嗎？</h3>
            <svg aria-hidden="true" class="flex-initial w-5 h-5 rotate-0 transform text-gray-500 stroke-2 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 9l-7 7-7-7" strokelinecap="round" strokelinejoin="round"></path>
            </svg>
          </summary>
          <p class="text-gray-600 leading-loose text-sm mt-2 max-w-3xl">
            若您是以「自然人」發起計畫，任意連續三十天內集資金額上限為新臺幣 100 萬；以「法人」發起計畫，第一階段上限為新臺幣 1,500 萬，接近上限金額時，可與嘖嘖平臺討論並申請開放第二階段額度。
          </p>
        </details>
        <details class="group p-4 border-b border-gray-200 reset-details">
          <summary class="flex items-center justify-between py-1 cursor-pointer font-semibold">
            <h3 class="flex-1">集資時間有限制嗎？</h3>
            <svg aria-hidden="true" class="flex-initial w-5 h-5 rotate-0 transform text-gray-500 stroke-2 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 9l-7 7-7-7" strokelinecap="round" strokelinejoin="round"></path>
            </svg>
          </summary>
          <p class="text-gray-600 leading-loose text-sm mt-2 max-w-3xl">
            「一次性計畫」嘖嘖平臺上發起最短的計畫為一天；最長的計畫為五個月。我們通常建議集資計畫在 35-45 天是最理想的。計畫時間過短或太長，都對計畫集資幫助不大。
            <br>
            <br>
            「訂閱專案」只要持續向贊助人交付回饋，則沒有執行天期的限制。
          </p>
        </details>
        <details class="group p-4 border-b border-gray-200 reset-details">
          <summary class="flex items-center justify-between py-1 cursor-pointer font-semibold">
            <h3 class="flex-1">為什麼計畫要經過審核？</h3>
            <svg aria-hidden="true" class="flex-initial w-5 h-5 rotate-0 transform text-gray-500 stroke-2 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 9l-7 7-7-7" strokelinecap="round" strokelinejoin="round"></path>
            </svg>
          </summary>
          <p class="text-gray-600 leading-loose text-sm mt-2 max-w-3xl">
            「群眾集資」即意味著將計畫交到贊助人眼前，由他們決定計畫是否成功，嘖嘖作為媒介平臺，不介入專案計畫及執行，但為了維護產業生態的健康，您發起計畫後，將經過以下四個階段的審核：
            <br>
            <br>
            <strong>請留意，審核透過不代表任何法律或道德上的背書，您仍需為您發起的專案負擔所有責任。</strong>
          </p><h4 class="text-sm text-gray-600 font-semibold mt-4">（1）提案後：我們會初步評估計畫應是合理且合法的，包括但不限於：</h4>
          <ul class="list-disc text-sm text-gray-600 pl-8 leading-relaxed">
            <li>合法性檢查：計畫應確保未違反法律在地及國際法規。</li>
            <li>計畫合理性：提案應包括說明計畫執行方式及如何使用集資資金、計畫的時間表，這有助幫助贊助人確認計畫是具體的，且有可能成功實踐。</li>
            <li>相關檔案：我們會希望您出示計畫中的必要的合法檔案，如檢驗證明、專利證明、報價單等。</li>
          </ul>
          <h4 class="text-sm text-gray-600 font-semibold mt-4">（2）初審後：身份及金流確認，包括但不限於：</h4>
          <ul class="list-disc text-sm text-gray-600 pl-8 leading-relaxed">
            <li>身份驗證：無論是以自然人或法人發起專案，均需提供不同的檔案，以確認是合法存在的實體。</li>
          </ul>
          <h4 class="text-sm text-gray-600 font-semibold mt-4">（3）上架後：提案上架後，我們仍會持續確保計畫的合法性和品質，包括但不限於：</h4>
          <ul class="list-disc text-sm text-gray-600 pl-8 leading-relaxed">
            <li>保護專利及智慧產財權：發起專案後，若存在侵權或盜用他人設計之風險，必要時，我們會要求提案者提供進一步的權利證明檔案。</li>
            <li>緊急應對：若計畫集資過程中衍生高度消費爭議，我們會在謹慎評估後暫停或下架計畫，以保護贊助者和合法提案者的權益。</li>
          </ul>
          <h4 class="text-sm text-gray-600 font-semibold mt-4">（4）執行後：實踐及貫徹專案的能力，有無和贊助者溝通的誠意，包括但不限於：</h4>
          <ul class="list-disc text-sm text-gray-600 pl-8 leading-relaxed">
            <li>溝通和透明度：提案者應定期向贊助者提供計畫進展報告，確保計畫透明度和溝通，並及時解決問題。</li>
            <li>風險管理：提案者應對執行中的風險，例如生產延遲或預算超支，並找到解決方案。</li>
            <li>計畫成果：若您的曾有計畫在交付集資金額後執行失敗，未能成功交付回饋品的記錄，會降低您下一次計畫的審核機率。</li>
          </ul>
          <p></p>
        </details>
        <details class="group p-4 border-b border-gray-200 reset-details">
          <summary class="flex items-center justify-between py-1 cursor-pointer font-semibold">
            <h3 class="flex-1">發起計畫需要付錢嗎？</h3>
            <svg aria-hidden="true" class="flex-initial w-5 h-5 rotate-0 transform text-gray-500 stroke-2 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 9l-7 7-7-7" strokelinecap="round" strokelinejoin="round"></path>
            </svg>
          </summary>
          <p class="text-gray-600 leading-loose text-sm mt-2 max-w-3xl">
            發起計畫不需支付任何費用，只有集資成功的專案需付平臺手續費及金流費用。專案成功後，嘖嘖將收取您計畫最後集資總金額的 2.5%* 金流基本服務費及 5.5% 嘖嘖手續費。嘖嘖收取手續費將用於網站維護及資安保護等，確保平臺能長久經營，幫助更多的創作人。
            <br>
            <br>
            <strong>*依不同的付款方式可能會有不同的金流費用比例，詳見提案契約。</strong>
          </p>
        </details>
        <details class="group p-4 border-b border-gray-200 reset-details">
          <summary class="flex items-center justify-between py-1 cursor-pointer font-semibold">
            <h3 class="flex-1">我發起的計畫集資成功了，我該怎麼聯絡贊助人？</h3>
            <svg aria-hidden="true" class="flex-initial w-5 h-5 rotate-0 transform text-gray-500 stroke-2 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 9l-7 7-7-7" strokelinecap="round" strokelinejoin="round"></path>
            </svg>
          </summary>
          <p class="text-gray-600 leading-loose text-sm mt-2 max-w-3xl">
            計畫成功結束後，提案人的可以在計畫頁面工具箱中找到「贊助人列表」，其中包含所有贊助人的聯絡資料和贊助細節。
          </p>
        </details>
        <details class="group p-4 border-b border-gray-200 reset-details">
          <summary class="flex items-center justify-between py-1 cursor-pointer font-semibold">
            <h3 class="flex-1">我發起的計畫集資成功了，什麼時候才能拿到贊助的款項呢？</h3>
            <svg aria-hidden="true" class="flex-initial w-5 h-5 rotate-0 transform text-gray-500 stroke-2 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 9l-7 7-7-7" strokelinecap="round" strokelinejoin="round"></path>
            </svg>
          </summary>
          <p class="text-gray-600 leading-loose text-sm mt-2 max-w-3xl">
            計畫結束後，嘖嘖的金流信託公司將扣除交付款項所需之金流及手續費共計 8%* 後，於次月十三日前，將剩餘之項給付給提案人。
            <br>
            <br>
            <strong>*依不同的付款方式可能會有不同的金流費用比例，詳見提案契約。</strong>
          </p>
        </details>
        <details class="group p-4 border-b border-gray-200 reset-details">
          <summary class="flex items-center justify-between py-1 cursor-pointer font-semibold">
            <h3 class="flex-1">我可以在嘖嘖上發起公益計畫嗎？</h3>
            <svg aria-hidden="true" class="flex-initial w-5 h-5 rotate-0 transform text-gray-500 stroke-2 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 9l-7 7-7-7" strokelinecap="round" strokelinejoin="round"></path>
            </svg>
          </summary>
          <p class="text-gray-600 leading-loose text-sm mt-2 max-w-3xl">
            在嘖嘖可以發起公益計畫，但需符合《公益勸募條例》規範，包括第 5 條對於發起計畫身份之確認，並需具有該年度的公益勸募許可文號，此外，您的計畫中若有純捐款，需提供贊助者可抵稅之捐款收據。
            <br>
            <br>
            《公益勸募條例》第 5 條，本條例所稱勸募團體如下
            <br>
            一、公立學校
            <br>
            二、行政法人
            <br>
            三、公益性社團法人
            <br>
            四、財團法人
            <br>
            <br>
            若無法判斷欲發起計畫是否受《公益勸募條例》規範，可聯絡衛福部公益勸募相關法令釋疑專線：02-8590-6651
          </p>
        </details>
        <details class="group p-4 border-b border-gray-200 reset-details">
          <summary class="flex items-center justify-between py-1 cursor-pointer font-semibold">
            <h3 class="flex-1">我有提供回饋品給贊助者，是否就不受《公益勸募條例》規範，即不需勸募字號也可以上架公益計畫？</h3>
            <svg aria-hidden="true" class="flex-initial w-5 h-5 rotate-0 transform text-gray-500 stroke-2 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 9l-7 7-7-7" strokelinecap="round" strokelinejoin="round"></path>
            </svg>
          </summary>
          <p class="text-gray-600 leading-loose text-sm mt-2 max-w-3xl">
            不是的，根據《公益勸募條例》規範，只要文案中宣稱集資金額將使「不特定多數人」受惠，即屬「公益」範疇，不論是否有提供回饋品，均需符合規範。提醒您，若經判定為違法之公益勸募計畫，主管機關有權要求計畫將勸募所得財物返還捐贈人。
          </p>
        </details>
        <details class="group p-4 border-b border-gray-200 reset-details">
          <summary class="flex items-center justify-between py-1 cursor-pointer font-semibold">
            <h3 class="flex-1">我想在嘖嘖發起計畫來販售產品，需要注意什麼法律問題嗎？</h3>
            <svg aria-hidden="true" class="flex-initial w-5 h-5 rotate-0 transform text-gray-500 stroke-2 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 9l-7 7-7-7" strokelinecap="round" strokelinejoin="round"></path>
            </svg>
          </summary>
          <p class="text-gray-600 leading-loose text-sm mt-2 max-w-3xl">
            在嘖嘖上發起計畫集資，用於研發、預購預售產品等，均需留意產品屬性有關之相關法律規範，如進口具有藍芽功能等產品，根據電信法及國家電訊傳播委員會（NCC）規範，商品須先完成 NCC 檢驗，並在上架嘖嘖時於集資頁面載明 NCC 認證號碼及 BSMI 相關認證，始得集資。如發起計畫集資販售食品、食物容器等，依衛福部規範，應先向主管機關申請登入業者身分等。
          </p>
        </details>
      </div>
    </section>
  </div>
</template>

<style scoped lang="scss">

</style>
