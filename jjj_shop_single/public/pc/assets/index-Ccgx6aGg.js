/* empty css             *//* empty css                */import{d as T,e as g,g as $,o as p,c as u,W as v,m as t,a as e,a3 as D,G as o,b as a,w as r,h as i,F as w,D as L,i as B,N as C,r as M,Y as V,a6 as j,a7 as E,a4 as m,a5 as F}from"./index-BWcYl31F.js";import{u as H}from"./loading-DZi2qnJz.js";import{b as I}from"./product-Bc4eezU8.js";import{g as s}from"./get-Bnx5vrHs.js";const G={class:"border-t border-neutral-200 border-b"},P={class:"container cf"},R={class:"w-full flex"},W={class:"inline-block lg:float-left w-full lg:w-1/3"},Y={class:"py-2 lg:px-2 px-4 flex-1"},q={href:"/projects/in-z"},A={class:"text-base mb-0 inline-block mt-1 text-base font-bold"},J={class:"text-neutral-600 text-xs mb-4"},K={class:"font-bold"},O={class:"text-xs text-neutral-600"},Q={class:"container my-8"},U={class:"lg:-mx-4 flex"},X={class:"w-full px-4 mb-8"},Z={class:"text-center text-xs rounded bg-zinc-100 p-2 font-bold tracking-widest hidden xs:block"},S={class:"flex xs:flex-wrap flex-wrap-reverse xs:flex-nowrap xs:whitespace-nowrap xs:w-auto overflow-x-auto scrollbar-top"},ee=["data-src","src"],te={class:"text-gray-600 font-bold mt-4 mb-2"},se={class:"text-black font-bold text-xl flex items-center"},le=e("span",{class:"inline-block text-xs font-bold text-black bg-yellow-300 leading-relaxed px-2 ml-2 rounded-sm"},"69 折",-1),ae={class:"w-full text-gray-500 font-normal text-xs"},oe={class:"line-through"},ie={class:"text-xs my-2"},ne={class:"text-xs text-white px-2 py-1 bg-zec-red font-bold inline-block"},ce={class:"text-black px-2 py-1 bg-zinc-100 inline-block"},de={class:"font-bold"},re={class:"maxh5 maxh-none-ns overflow-y-auto break-all"},_e={class:"text-black text-sm mv-child-0 flex flex-col space-y-4 leading-relaxed"},xe=["innerHTML"],fe={class:"mt-4 pt-4 border-t text-gray-800 text-xs flex"},pe={class:"mr-2 -indent-4 pl-4 leading-relaxed"},ue={class:"mr-2 -indent-4 pl-4 leading-relaxed"},me={class:"mr-2 -indent-4 pl-4 leading-relaxed"},he={class:"mr-2 -indent-4 pl-4 leading-relaxed"},be={class:"text-center text-xs text-gray-600 pt-4 mt-4 border-t"},Te=T({__name:"index",setup(ge){const y=B(),{loading:h,setLoading:b}=H(!1),k=g(null),c=g({}),z=async _=>{if(_){b(!0);try{const{data:d}=await I(_);if(d&&d.detail){let n=s(d,"detail.product_multi_spec.spec_attr[0].spec_items",[])||[],f=d.detail.sku||[];f=f.map(x=>{let l=n.find(N=>N.item_id-0===x.spec_sku_id-0);return{...x,spec_item:l}}),c.value={...d.detail,sku:f.filter(x=>x.type-0===0)},console.log(c.value),isLike.value=d.is_fav}console.log(tableData.value)}catch{}finally{b(!1)}}};return $(()=>{const _=s(y,"params.id");_&&(k.value=_,z(_))}),(_,d)=>{const n=C,f=M("router-link"),x=V;return p(),u(w,null,[v((p(),u("div",G,[e("div",P,[e("div",R,[e("div",W,[e("div",{class:"aspect-ratio-project-cover bg-zinc-100 lg:mr-6",style:D(`background-image: url('${t(s)(c.value,"product_image","")}')`)},null,4)]),e("div",Y,[e("a",q,[e("h2",A,o(t(s)(c.value,"product_name","")),1)]),e("div",J,o(t(s)(c.value,"type_text","")),1),e("span",K,"NT$ "+o(t(s)(c.value,"total_money","")),1),e("span",O,"/ 目標 NT$ "+o(t(s)(c.value,"target_money","")),1)])])])])),[[x,t(h)]]),v((p(),u("div",Q,[e("div",U,[e("div",X,[e("div",Z,[a(n,null,{default:r(()=>[a(t(j))]),_:1}),i(" 左右捲動看看更多選項 "),a(n,null,{default:r(()=>[a(t(E))]),_:1})]),e("div",S,[(p(!0),u(w,null,L(c.value.sku,l=>(p(),u("div",{key:l.product_sku_id,class:"w-full xs:flex-none whitespace-normal xs:mr-4 xs:pt-4 xs:w-1/2 lg:w-3/10"},[a(f,{class:"p-4 border-2 border-inherit rounded text-neutral-200 mb-8 block border-rainbow",to:{path:`/order/${l.product_id}/${l.product_sku_id}`}},{default:r(()=>[e("img",{width:"600",height:"200",class:"lazy placeholder-3:1 w-full h-auto mb-2 round-s entered loaded","data-ll-status":"loaded","data-src":t(s)(l,"image.file_path"),src:t(s)(l,"image.file_path")},null,8,ee),e("div",te,o(t(s)(l,"spec_item.spec_value")),1),e("div",se,[i(" NT$ "+o(t(s)(l,"product_price"))+" ",1),le,e("p",ae,[i(" 預定售價 "),e("span",oe,"NT$ "+o(t(s)(l,"line_price")),1),i(" ，現省 NT$ "+o(t(s)(l,"line_price")-t(s)(l,"product_price")),1)])]),e("div",ie,[e("span",ne," 剩餘 "+o(t(s)(l,"stock_num"))+" 份 ",1),e("span",ce,[i("已被贊助 "),e("span",de,o(t(s)(l,"product_sales")),1),i("/ "+o(t(s)(l,"stock_num")+t(s)(l,"product_sales"))+" 次",1)])]),e("div",re,[e("div",_e,[e("div",{innerHTML:t(s)(l,"detail")},null,8,xe)])]),e("ul",fe,[e("li",pe,[a(n,{size:"14",color:"#229f2a"},{default:r(()=>[a(t(m))]),_:1}),i(" 可選擇加購商品 ")]),e("li",ue,[a(n,{size:"14",color:"#229f2a"},{default:r(()=>[a(t(m))]),_:1}),i(" 可選 7-11 取貨 ")]),e("li",me,[a(n,{size:"14",color:"#229f2a"},{default:r(()=>[a(t(m))]),_:1}),i(" 臺灣本島、離島免運 ")]),e("li",he,[a(n,{size:"14",color:"#229f2a"},{default:r(()=>[a(t(m))]),_:1}),i(" 可寄送至 香港、澳門、馬來西亞 等地區 ")])]),e("div",be,[a(n,{size:"14"},{default:r(()=>[a(t(F))]),_:1}),i(" 預計於 2025 年二月實現 ")])]),_:2},1032,["to"])]))),128))])])])])),[[x,t(h)]])],64)}}});export{Te as default};
