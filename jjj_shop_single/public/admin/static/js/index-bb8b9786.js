function e(){import.meta.url,import("_").catch((()=>1))}import{E as t,J as n,K as r,o,S as s,R as i,O as a,ad as l,am as m}from"./@vue-5c89b57d.js";import{c,a as u}from"./vue-router-feb6ca35.js";import{d as p,c as d}from"./pinia-3964e703.js";import{E as f,a as _,z as h}from"./element-plus-4e26fc63.js";import{E as g}from"./@element-plus-9b3bb84c.js";import"./vue-demi-71ba0ef2.js";import"./lodash-es-493ac664.js";import"./async-validator-cf877c1f.js";import"./@vueuse-e57ebffb.js";import"./dayjs-342c85a3.js";import"./call-bind-0966096f.js";import"./get-intrinsic-ccd8a43d.js";import"./has-symbols-456daba2.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";!function(){const e=document.createElement("link").relList;if(!(e&&e.supports&&e.supports("modulepreload"))){for(const e of document.querySelectorAll('link[rel="modulepreload"]'))t(e);new MutationObserver((e=>{for(const n of e)if("childList"===n.type)for(const e of n.addedNodes)"LINK"===e.tagName&&"modulepreload"===e.rel&&t(e)})).observe(document,{childList:!0,subtree:!0})}function t(e){if(e.ep)return;e.ep=!0;const t=function(e){const t={};return e.integrity&&(t.integrity=e.integrity),e.referrerPolicy&&(t.referrerPolicy=e.referrerPolicy),"use-credentials"===e.crossOrigin?t.credentials="include":"anonymous"===e.crossOrigin?t.credentials="omit":t.credentials="same-origin",t}(e);fetch(e.href,t)}}();const I={},j=function(e,t,n){if(!t||0===t.length)return e();const r=document.getElementsByTagName("link");return Promise.all(t.map((e=>{if(e=function(e,t){return new URL(e,t).href}(e,n),e in I)return;I[e]=!0;const t=e.endsWith(".css"),o=t?'[rel="stylesheet"]':"";if(!!n)for(let n=r.length-1;n>=0;n--){const o=r[n];if(o.href===e&&(!t||"stylesheet"===o.rel))return}else if(document.querySelector(`link[href="${e}"]${o}`))return;const s=document.createElement("link");return s.rel=t?"stylesheet":"modulepreload",t||(s.as="script",s.crossOrigin=""),s.href=e,document.head.appendChild(s),t?new Promise(((t,n)=>{s.addEventListener("load",t),s.addEventListener("error",(()=>n(new Error(`Unable to preload CSS for ${e}`))))})):void 0}))).then((()=>e()))},E=e=>!!sessionStorage.hasOwnProperty(e)&&JSON.parse(sessionStorage.getItem(e)),y=(e,t)=>{if(sessionStorage.hasOwnProperty(e)){let n=JSON.parse(sessionStorage.getItem(e)),r=Object.assign(n,t);sessionStorage.setItem(e,JSON.stringify(r))}else sessionStorage.setItem(e,JSON.stringify(t))},x=e=>{var t=Array.isArray(e)?[]:{};if(e&&"object"==typeof e)for(var n in e)e.hasOwnProperty(n)&&(e[n]&&"object"==typeof e[n]?t[n]=x(e[n]):t[n]=e[n]);return t},b=(e,t)=>{for(var n in e)t&&void 0!==t[n]&&(e[n]&&"[object Object]"===Object.prototype.toString.call(e[n])?b(e[n],t[n]):e[n]=t[n]);return e};let O={...{baseURL:"/index.php",tokenName:"token",strongToken:"jjjSingleAdminToken",isDev:!1,contentType:"application/x-www-form-urlencoded;charset=UTF-8",requestTimeout:5e4,statusName:"code",messageName:"msg",withCredentials:!0,responseType:"json"}},{strongToken:v}=O;const T=p("main",{state:()=>({token:E(v),userInfo:E("userInfo"),list:{}}),getters:{},actions:{bus_on(e,t){let n=this;n.list[e]=n.list[e]||[],n.list[e].push(t)},bus_emit(e,t){let n=this;n.list[e]&&n.list[e].forEach((e=>{e(t)}))},bus_off(e){let t=this;t.list[e]&&delete t.list[e]},async afterLogin(e){this.userInfo=this.userInfo||{};const{data:{token:t,user_name:n}}=e;this.userInfo.userName=n,this.token=t,y(v,t),y("userInfo",this.userInfo)},afterLogout(){sessionStorage.clear(),this.token=null,this.menus=null}}});const S=[{path:"/login",name:"login",meta:{title:"登入"},component:()=>j((()=>import("./index-164465fd.js")),["./index-164465fd.js","./element-plus-4e26fc63.js","./@vue-5c89b57d.js","./lodash-es-493ac664.js","./async-validator-cf877c1f.js","./@vueuse-e57ebffb.js","./dayjs-342c85a3.js","./call-bind-0966096f.js","./get-intrinsic-ccd8a43d.js","./has-symbols-456daba2.js","./function-bind-72d06d3b.js","./has-885c3436.js","./@element-plus-9b3bb84c.js","./escape-html-1935ddb3.js","./normalize-wheel-es-3222b0a2.js","./@ctrl-91de2ec7.js","..\\css\\element-plus-7d37d56a.css","./user-e81d40bc.js","./request-58949a77.js","./axios-85bcd05e.js","./qs-9a0be292.js","./side-channel-7d553c0c.js","./object-inspect-1e1e9601.js","./vue-router-feb6ca35.js","./pinia-3964e703.js","./vue-demi-71ba0ef2.js","..\\css\\index-8bd3b42a.css"],import.meta.url)},{path:"/",name:"Main",meta:{title:"母版"},component:()=>j((()=>import("./Main-bae4f461.js")),["./Main-bae4f461.js","./vue-router-feb6ca35.js","./@vue-5c89b57d.js","./element-plus-4e26fc63.js","./lodash-es-493ac664.js","./async-validator-cf877c1f.js","./@vueuse-e57ebffb.js","./dayjs-342c85a3.js","./call-bind-0966096f.js","./get-intrinsic-ccd8a43d.js","./has-symbols-456daba2.js","./function-bind-72d06d3b.js","./has-885c3436.js","./@element-plus-9b3bb84c.js","./escape-html-1935ddb3.js","./normalize-wheel-es-3222b0a2.js","./@ctrl-91de2ec7.js","..\\css\\element-plus-7d37d56a.css","./pinia-3964e703.js","./vue-demi-71ba0ef2.js","..\\css\\Main-90ee51de.css"],import.meta.url),children:[{path:"/Home",name:"Home",meta:{title:"首頁",topTree:"/Home"},component:()=>j((()=>import("./Home-3961234e.js")),["./Home-3961234e.js","./user-e81d40bc.js","./request-58949a77.js","./axios-85bcd05e.js","./qs-9a0be292.js","./side-channel-7d553c0c.js","./get-intrinsic-ccd8a43d.js","./has-symbols-456daba2.js","./function-bind-72d06d3b.js","./has-885c3436.js","./call-bind-0966096f.js","./object-inspect-1e1e9601.js","./element-plus-4e26fc63.js","./@vue-5c89b57d.js","./lodash-es-493ac664.js","./async-validator-cf877c1f.js","./@vueuse-e57ebffb.js","./dayjs-342c85a3.js","./@element-plus-9b3bb84c.js","./escape-html-1935ddb3.js","./normalize-wheel-es-3222b0a2.js","./@ctrl-91de2ec7.js","..\\css\\element-plus-7d37d56a.css","./vue-router-feb6ca35.js","./pinia-3964e703.js","./vue-demi-71ba0ef2.js","..\\css\\Home-ca442aee.css"],import.meta.url)},{path:"/plugs/plugs/Index",name:"plugs_index",meta:{title:"外掛管理"},component:()=>j((()=>import("./Index-531cf6ab.js")),["./Index-531cf6ab.js","./element-plus-4e26fc63.js","./@vue-5c89b57d.js","./lodash-es-493ac664.js","./async-validator-cf877c1f.js","./@vueuse-e57ebffb.js","./dayjs-342c85a3.js","./call-bind-0966096f.js","./get-intrinsic-ccd8a43d.js","./has-symbols-456daba2.js","./function-bind-72d06d3b.js","./has-885c3436.js","./@element-plus-9b3bb84c.js","./escape-html-1935ddb3.js","./normalize-wheel-es-3222b0a2.js","./@ctrl-91de2ec7.js","..\\css\\element-plus-7d37d56a.css","./request-58949a77.js","./axios-85bcd05e.js","./qs-9a0be292.js","./side-channel-7d553c0c.js","./object-inspect-1e1e9601.js","./vue-router-feb6ca35.js","./pinia-3964e703.js","./vue-demi-71ba0ef2.js","..\\css\\Index-0ccb814e.css"],import.meta.url)},{path:"/access/Index",name:"access_Index",meta:{title:"許可權管理"},component:()=>j((()=>import("./Index-f31a1388.js")),["./Index-f31a1388.js","./element-plus-4e26fc63.js","./@vue-5c89b57d.js","./lodash-es-493ac664.js","./async-validator-cf877c1f.js","./@vueuse-e57ebffb.js","./dayjs-342c85a3.js","./call-bind-0966096f.js","./get-intrinsic-ccd8a43d.js","./has-symbols-456daba2.js","./function-bind-72d06d3b.js","./has-885c3436.js","./@element-plus-9b3bb84c.js","./escape-html-1935ddb3.js","./normalize-wheel-es-3222b0a2.js","./@ctrl-91de2ec7.js","..\\css\\element-plus-7d37d56a.css","./request-58949a77.js","./axios-85bcd05e.js","./qs-9a0be292.js","./side-channel-7d553c0c.js","./object-inspect-1e1e9601.js","./vue-router-feb6ca35.js","./pinia-3964e703.js","./vue-demi-71ba0ef2.js"],import.meta.url)},{path:"/shop/Index",name:"shop_Index",meta:{title:"商城"},component:()=>j((()=>import("./Index-2a758f49.js")),["./Index-2a758f49.js","./element-plus-4e26fc63.js","./@vue-5c89b57d.js","./lodash-es-493ac664.js","./async-validator-cf877c1f.js","./@vueuse-e57ebffb.js","./dayjs-342c85a3.js","./call-bind-0966096f.js","./get-intrinsic-ccd8a43d.js","./has-symbols-456daba2.js","./function-bind-72d06d3b.js","./has-885c3436.js","./@element-plus-9b3bb84c.js","./escape-html-1935ddb3.js","./normalize-wheel-es-3222b0a2.js","./@ctrl-91de2ec7.js","..\\css\\element-plus-7d37d56a.css","./request-58949a77.js","./axios-85bcd05e.js","./qs-9a0be292.js","./side-channel-7d553c0c.js","./object-inspect-1e1e9601.js","./vue-router-feb6ca35.js","./pinia-3964e703.js","./vue-demi-71ba0ef2.js","..\\css\\Index-9e19b0cc.css"],import.meta.url)},{path:"/password/Index",name:"password_Index",meta:{title:"修改密碼"},component:()=>j((()=>import("./Index-26c77642.js")),["./Index-26c77642.js","./element-plus-4e26fc63.js","./@vue-5c89b57d.js","./lodash-es-493ac664.js","./async-validator-cf877c1f.js","./@vueuse-e57ebffb.js","./dayjs-342c85a3.js","./call-bind-0966096f.js","./get-intrinsic-ccd8a43d.js","./has-symbols-456daba2.js","./function-bind-72d06d3b.js","./has-885c3436.js","./@element-plus-9b3bb84c.js","./escape-html-1935ddb3.js","./normalize-wheel-es-3222b0a2.js","./@ctrl-91de2ec7.js","..\\css\\element-plus-7d37d56a.css","./user-e81d40bc.js","./request-58949a77.js","./axios-85bcd05e.js","./qs-9a0be292.js","./side-channel-7d553c0c.js","./object-inspect-1e1e9601.js","./vue-router-feb6ca35.js","./pinia-3964e703.js","./vue-demi-71ba0ef2.js"],import.meta.url)},{path:"/message/Index",name:"message_Index",meta:{title:"訊息設定"},component:()=>j((()=>import("./Index-40cfcee9.js")),["./Index-40cfcee9.js","./element-plus-4e26fc63.js","./@vue-5c89b57d.js","./lodash-es-493ac664.js","./async-validator-cf877c1f.js","./@vueuse-e57ebffb.js","./dayjs-342c85a3.js","./call-bind-0966096f.js","./get-intrinsic-ccd8a43d.js","./has-symbols-456daba2.js","./function-bind-72d06d3b.js","./has-885c3436.js","./@element-plus-9b3bb84c.js","./escape-html-1935ddb3.js","./normalize-wheel-es-3222b0a2.js","./@ctrl-91de2ec7.js","..\\css\\element-plus-7d37d56a.css","./request-58949a77.js","./axios-85bcd05e.js","./qs-9a0be292.js","./side-channel-7d553c0c.js","./object-inspect-1e1e9601.js","./vue-router-feb6ca35.js","./pinia-3964e703.js","./vue-demi-71ba0ef2.js"],import.meta.url)},{path:"/region/Index",name:"region_Index",meta:{title:"地區列表"},component:()=>j((()=>import("./Index-90f159bc.js")),["./Index-90f159bc.js","./element-plus-4e26fc63.js","./@vue-5c89b57d.js","./lodash-es-493ac664.js","./async-validator-cf877c1f.js","./@vueuse-e57ebffb.js","./dayjs-342c85a3.js","./call-bind-0966096f.js","./get-intrinsic-ccd8a43d.js","./has-symbols-456daba2.js","./function-bind-72d06d3b.js","./has-885c3436.js","./@element-plus-9b3bb84c.js","./escape-html-1935ddb3.js","./normalize-wheel-es-3222b0a2.js","./@ctrl-91de2ec7.js","..\\css\\element-plus-7d37d56a.css","./region-ca2c0c1a.js","./request-58949a77.js","./axios-85bcd05e.js","./qs-9a0be292.js","./side-channel-7d553c0c.js","./object-inspect-1e1e9601.js","./vue-router-feb6ca35.js","./pinia-3964e703.js","./vue-demi-71ba0ef2.js"],import.meta.url)},{path:"/region/add",name:"region_add",meta:{title:"地區新增"},component:()=>j((()=>import("./add-988d09f8.js")),["./add-988d09f8.js","./element-plus-4e26fc63.js","./@vue-5c89b57d.js","./lodash-es-493ac664.js","./async-validator-cf877c1f.js","./@vueuse-e57ebffb.js","./dayjs-342c85a3.js","./call-bind-0966096f.js","./get-intrinsic-ccd8a43d.js","./has-symbols-456daba2.js","./function-bind-72d06d3b.js","./has-885c3436.js","./@element-plus-9b3bb84c.js","./escape-html-1935ddb3.js","./normalize-wheel-es-3222b0a2.js","./@ctrl-91de2ec7.js","..\\css\\element-plus-7d37d56a.css","./region-ca2c0c1a.js","./request-58949a77.js","./axios-85bcd05e.js","./qs-9a0be292.js","./side-channel-7d553c0c.js","./object-inspect-1e1e9601.js","./vue-router-feb6ca35.js","./pinia-3964e703.js","./vue-demi-71ba0ef2.js","..\\css\\add-b6e7e198.css"],import.meta.url)},{path:"/region/edit",name:"region_edit",meta:{title:"地區修改"},component:()=>j((()=>import("./edit-2791f532.js")),["./edit-2791f532.js","./element-plus-4e26fc63.js","./@vue-5c89b57d.js","./lodash-es-493ac664.js","./async-validator-cf877c1f.js","./@vueuse-e57ebffb.js","./dayjs-342c85a3.js","./call-bind-0966096f.js","./get-intrinsic-ccd8a43d.js","./has-symbols-456daba2.js","./function-bind-72d06d3b.js","./has-885c3436.js","./@element-plus-9b3bb84c.js","./escape-html-1935ddb3.js","./normalize-wheel-es-3222b0a2.js","./@ctrl-91de2ec7.js","..\\css\\element-plus-7d37d56a.css","./region-ca2c0c1a.js","./request-58949a77.js","./axios-85bcd05e.js","./qs-9a0be292.js","./side-channel-7d553c0c.js","./object-inspect-1e1e9601.js","./vue-router-feb6ca35.js","./pinia-3964e703.js","./vue-demi-71ba0ef2.js","..\\css\\add-b6e7e198.css"],import.meta.url)},{path:"/setting/Index",name:"setting_Index",meta:{title:"系統設定"},component:()=>j((()=>import("./index-2f92253e.js")),["./index-2f92253e.js","./element-plus-4e26fc63.js","./@vue-5c89b57d.js","./lodash-es-493ac664.js","./async-validator-cf877c1f.js","./@vueuse-e57ebffb.js","./dayjs-342c85a3.js","./call-bind-0966096f.js","./get-intrinsic-ccd8a43d.js","./has-symbols-456daba2.js","./function-bind-72d06d3b.js","./has-885c3436.js","./@element-plus-9b3bb84c.js","./escape-html-1935ddb3.js","./normalize-wheel-es-3222b0a2.js","./@ctrl-91de2ec7.js","..\\css\\element-plus-7d37d56a.css","./request-58949a77.js","./axios-85bcd05e.js","./qs-9a0be292.js","./side-channel-7d553c0c.js","./object-inspect-1e1e9601.js","./vue-router-feb6ca35.js","./pinia-3964e703.js","./vue-demi-71ba0ef2.js","..\\css\\index-2b74e4dd.css"],import.meta.url)},{path:"/SystemPicture/Index",name:"SystemPicture_Index",meta:{title:"系統圖標"},component:()=>j((()=>import("./Index-c9497b18.js")),["./Index-c9497b18.js","./element-plus-4e26fc63.js","./@vue-5c89b57d.js","./lodash-es-493ac664.js","./async-validator-cf877c1f.js","./@vueuse-e57ebffb.js","./dayjs-342c85a3.js","./call-bind-0966096f.js","./get-intrinsic-ccd8a43d.js","./has-symbols-456daba2.js","./function-bind-72d06d3b.js","./has-885c3436.js","./@element-plus-9b3bb84c.js","./escape-html-1935ddb3.js","./normalize-wheel-es-3222b0a2.js","./@ctrl-91de2ec7.js","..\\css\\element-plus-7d37d56a.css","./request-58949a77.js","./axios-85bcd05e.js","./qs-9a0be292.js","./side-channel-7d553c0c.js","./object-inspect-1e1e9601.js","./vue-router-feb6ca35.js","./pinia-3964e703.js","./vue-demi-71ba0ef2.js","..\\css\\Index-4ce71f29.css"],import.meta.url)}]}],k=c({history:u(),routes:S});const P=(e,t)=>{const n=e.__vccOpts||e;for(const[r,o]of t)n[r]=o;return n};const L=P(t({components:{[_.name]:_},setup(){let e=h;const t=n({});return{...r(t),locale:e}}}),[["render",function(e,t,n,r,m,c){const u=l("router-view"),p=_;return o(),s(p,{locale:e.locale},{default:i((()=>[a(u)])),_:1},8,["locale"])}]]),w={testFilter:function(e){return"vip"+e},isNull:function(e){return null==e||null==e||""===e||"null"===e||"undefined"==e?"-":e},returnPercentage:function(e){if(null!==e&&""!==e&&void 0!==e){return(100*e).toFixed(2)+"%"}return"-"},returnToFixed:function(e,t){if(null!=e){return e.toFixed(t)}return null!=e&&""!==e?e:"-"},tenThousand:function(e){if(null!=e&&""!=e){var t=(e/1e4).toFixed(2);return(Math.round(100*t)/100).toString()}return"-"},numTabWeek:function(e){let t="";switch(e){case 1:t="一";break;case 2:t="二";break;case 3:t="三";break;case 4:t="四";break;case 5:t="五";break;case 6:t="六";break;case 7:t="日"}return t},convertSex:function(e){let t="";switch(e){case 0:t="女";break;case 1:t="男";break;default:t="其他"}return t},replaceSpace:function(e){return null!=e?e.replace(/\s*/g,""):""},hasSpace:function(e){if(null!=e){return/\s/g.test(e)}return!1},passwordForm:e=>!!new RegExp(/^(?![^a-zA-Z]+$)(?!\D+$)/).test(e),isAllSpace:e=>!!e.match(/^[ ]*$/)},A=d(),R=m(L);for(const[N,V]of Object.entries(g))R.component(N,V);var D;R.config.globalProperties.$filter=w,R.use(A),R.use(k),R.mount("#app"),D=R,async function(e){let t=0;e.beforeEach((async(e,n,r)=>{const{token:o}=T(),s=["/login"];if(o){if("/login"==e.path)return void r({path:"/Home"});if(0==t)return t++,void r({...e,replace:!0});r()}else{if(s.includes(e.path))return void r();r("/login")}}))}(k),D.use(k),function(e){e.provide("$baseConfirm",((e,t=undefined,n=undefined,r=undefined,o="確定",s="取消")=>{f.confirm(e,t||"溫馨提示",{confirmButtonText:o,cancelButtonText:s,closeOnClickModal:!1,type:"warning",lockScroll:!1,dangerouslyUseHTMLString:!0}).then((()=>{n&&n()})).catch((()=>{r&&r()}))})),e.provide("$testhh",(()=>"我是elementplus的test"))}(R);export{P as _,e as __vite_legacy_guard,x as d,b as f,O as o,k as r,T as u};
