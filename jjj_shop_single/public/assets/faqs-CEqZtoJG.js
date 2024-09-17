import{g as d,i as L}from"./get-CAfd8AwD.js";import{b as N,c as B}from"./product-BXEgIn4-.js";import{d as C,e as u,N as D,t as F,o as m,c as _,a as e,y as a,i as v,b as p,w as f,s as V,F as g,x as $,g as i,S,r as Z}from"./index-CCr7jDqf.js";import{u as I}from"./loading-a_aMpdwj.js";const M={class:"border-t border-neutral-200"},P={class:"container cf"},A={class:"w-full flex"},E={class:"py-2 lg:px-2 px-4 flex-1"},R={href:"/projects/iris-6-0"},T={class:"text-base mb-0 inline-block mt-1 text-base font-bold"},G={class:"text-neutral-600 text-xs mb-4"},H={class:"px-4 lg:px-0 lg:mt-0 mt-6 top-0 bg-gray-50 sticky"},J={class:"container cf text-sm relative"},K={class:"-mx-4 flex"},O={class:"px-4 overflow-auto whitespace-nowrap flex flex-nowrap items-center space-x-8 my-1 tracking-widest lg:w-7/10"},Q={class:"text-xs font-bold ml-1 text-neutral-600"},U={class:"text-xs font-bold ml-1 text-neutral-600"},W={class:"container my-8"},X={class:"lg:-mx-4 flex"},Y={class:"w-full px-4 mb-8"},tt={class:"flex items-center justify-between py-1 cursor-pointer"},et={class:"w-full text-xs text-gray-400"},st={class:"flex-1 font-semibold pt-2"},ot={class:"text-gray-600 leading-relaxed text-sm mt-2 max-w-3xl pl-4 list-disc nested-media"},lt={class:"text-sm font-bold text-center mt-16"},ut=C({__name:"faqs",setup(at){const b=S(),{loading:rt,setLoading:n}=I(!1),w=u(null),o=u({}),y=u(!1),h=D(()=>{try{if(console.log(o.value.faq_ids),o.value.faq_ids){let s=o.value.faq_ids.split(",");return console.log(s),L(s)?s.length:0}}catch{return 0}}),k=async s=>{if(s){n(!0);try{const{data:t}=await N(s);if(t&&t.detail){let r=d(t,"detail.product_multi_spec.spec_attr[0].spec_items",[])||[],l=t.detail.sku||[];l=l.map(c=>{let j=r.find(z=>z.item_id-0===c.spec_sku_id-0);return{...c,spec_item:j}}),o.value={...t.detail,sku:l.filter(c=>c.type-0===0)},console.log(o.value),y.value=t.is_fav}}catch{}finally{n(!1)}}},x=u([]),q=async s=>{if(s){n(!0);try{const{data:t}=await B(s);t&&t.list&&(x.value=t.list||[])}catch{}finally{n(!1)}}};return F(()=>{const s=d(b,"params.id");s&&(w.value=s,k(s),q(s))}),(s,t)=>{const r=Z("router-link");return m(),_(g,null,[t[9]||(t[9]=e("div",{class:"container"},null,-1)),e("div",M,[e("div",P,[e("div",A,[t[0]||(t[0]=e("div",{class:"inline-block lg:float-left w-full lg:w-1/3"},[e("div",{class:"aspect-ratio-project-cover bg-zinc-100 lg:mr-6",style:{"background-image":"url('https://assets.zeczec.com/asset_817258_image_big.jpg?1717127997')"}})],-1)),e("div",E,[e("a",R,[e("h2",T,a(v(d)(o.value,"category.name")),1)]),e("div",G,a(o.value.type_text),1)])])])]),e("div",H,[e("div",J,[e("div",K,[e("div",O,[p(r,{class:"inline-block py-2 hover:border-zec-green lg:border-b-2 text-black border-transparent",to:`/project/${o.value.product_id}`},{default:f(()=>t[1]||(t[1]=[i("專案內容 ")])),_:1},8,["to"]),p(r,{class:"inline-block py-2 hover:border-zec-cyan lg:border-b-2 text-black border-transparent border-zec-cyan font-bold",to:`/project/${o.value.product_id}/faqs`},{default:f(()=>[t[2]||(t[2]=i("常見問答 ")),e("span",Q,a(h.value),1)]),_:1},8,["to"]),p(r,{class:"inline-block py-2 hover:border-zec-green lg:border-b-2 text-black border-transparent",to:`/project/${o.value.product_id}/comments`},{default:f(()=>[t[3]||(t[3]=i("留言 ")),e("span",U,a(v(d)(o.value,"comment_data_count")),1)]),_:1},8,["to"])]),t[4]||(t[4]=V('<div class="px-4 py-3 text-center w-full flex lg:static bg-gray-50 bottom-0 z-50 lg:w-3/10 fixed"><a class="p-2 inline-block flex-initial mr-2 transition-transform hover:scale-105 focus:scale-105 active:scale-90 text-zec-cyan border-2 border-current rounded tooltip tooltip-l" data-method="post" data-click-event="follow_project" aria-label="追蹤後會收到公開的專案更新和計畫結束提醒。" href="/projects/iris-6-0/follow"><svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48" class="w-6 h-6 fill-current"><path d="m479.435 948.718-48.609-43.978q-106.231-97.889-175.847-168.98-69.616-71.091-110.93-127.066-41.314-55.975-57.53-101.96-16.215-45.986-16.215-93.289 0-94.915 63.544-158.528 63.544-63.613 157.087-63.613 55.885 0 103.877 25.304t84.623 74.543q43.13-51.739 88.77-75.793 45.639-24.054 99.859-24.054 94.379 0 158.006 63.574 63.626 63.574 63.626 158.43 0 47.409-16.215 93.127-16.216 45.718-57.53 101.694-41.314 55.975-111.138 127.412-69.823 71.437-176.204 169.199l-49.174 43.978Zm-.283-100.936q100.045-92.612 164.566-157.708t102.206-113.998q37.685-48.902 52.369-87.12 14.685-38.218 14.685-75.34 0-63.355-40.649-104.475-40.649-41.119-103.649-41.119-50.349 0-92.851 31.783-42.503 31.782-70.503 88.717h-52.217q-26.859-56.5-70.188-88.5t-92.204-32q-62.394 0-102.762 40.599t-40.368 105.353q0 38.151 15.184 76.807 15.183 38.655 52.835 88.06 37.653 49.405 101.556 113.989 63.903 64.583 161.99 154.952Zm1.413-290.412Z"></path></svg></a><a class="js-back-project-now tracking-widest flex-1 border-zec-cyan bg-zec-cyan py-2 text-white inline-block w-full text-base transition-transform hover:scale-105 focus:scale-105 active:scale-90 rounded font-bold py-1" data-click-event="list_options" href="/projects/iris-6-0/orders/back_project">立即購買</a></div>',1))])])]),t[10]||(t[10]=e("div",{class:"container"},null,-1)),e("div",W,[e("div",X,[e("div",Y,[e("div",null,[(m(!0),_(g,null,$(x.value,l=>(m(),_("details",{key:l.faq_id,class:"group p-4 even:bg-slate-100 reset-details rounded",id:"faq-62888"},[e("summary",tt,[e("div",et," 更新於 "+a(l.update_time),1),e("h3",st,a(l.question),1),t[5]||(t[5]=e("svg",{"aria-hidden":"true",class:"flex-initial w-5 h-5 rotate-0 transform text-gray-500 stroke-2 group-open:rotate-180 transition-transform",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24",xmlns:"http://www.w3.org/2000/svg"},[e("path",{d:"M19 9l-7 7-7-7",strokelinecap:"round",strokelinejoin:"round"})],-1))]),e("div",ot,[e("p",null,a(l.answer),1)])]))),128))]),e("div",lt,[t[7]||(t[7]=e("i",{class:"icon-question-sign"},null,-1)),t[8]||(t[8]=i(" 還有其他問題嗎？ ")),p(r,{to:`/message/new?product_id=${o.value.product_id}`},{default:f(()=>t[6]||(t[6]=[i("直接問提案人！")])),_:1},8,["to"])])])])])],64)}}});export{ut as default};
