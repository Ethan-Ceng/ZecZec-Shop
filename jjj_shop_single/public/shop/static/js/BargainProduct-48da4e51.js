import{k as t}from"./element-plus-84a27f94.js";import{ae as e,ap as s,o as i,c as o,a as l,V as a,X as r,Y as d,W as p,P as m,S as c,Q as n,a9 as y,$ as u,a1 as g,M as b}from"./@vue-8fe4574d.js";import{_ as j}from"./index-5ae5860a.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const h={class:"left d-s-c"},x=["src"],f={class:"product-cover pr"},v={class:"product-info p-0-10"},R={class:"price ww100 f12 tc"},_={class:"btn-edit-del"};const k=j({data:()=>({tableData:[],category_id:0}),created(){},props:["item","index","selectedIndex"],methods:{getUlwidth(t){if("slide"==t.style.display){let e=0;e="choice"==t.params.source?t.data.length:t.defaultData.length;let s=0;return s=2==t.style.column?150*e:100*e,"width:"+s+"px;"}}}},[["render",function(j,k,w,L,T,z){const B=e("ArrowRight"),I=t,S=s("img-url");return i(),o("div",{class:b(["drag optional",{selected:w.index===w.selectedIndex}]),style:a({background:w.item.style.bgcolor,paddingLeft:w.item.style.paddingLeft+"px",paddingRight:w.item.style.paddingLeft+"px",paddingTop:w.item.style.paddingTop+"px",paddingBottom:w.item.style.paddingBottom+"px"}),onClick:k[1]||(k[1]=g((t=>j.$parent.$parent.onEditer(w.index)),["stop"]))},[l("div",{class:"diy-sharpproduct",style:a({background:w.item.style.background,borderTopLeftRadius:w.item.style.topRadio+"px",borderTopRightRadius:w.item.style.topRadio+"px",borderBottomLeftRadius:w.item.style.bottomRadio+"px",borderBottomRightRadius:w.item.style.bottomRadio+"px"})},[l("div",{class:"sharpproduct-head d-b-c",style:a({backgroundImage:w.item.style.bgimage?"url("+w.item.style.bgimage+")":""})},[l("div",h,[1==w.item.style.titleType?(i(),o("div",{key:0,style:a({color:w.item.style.titleColor,fontSize:w.item.style.titleSize+"px"}),class:"name"},r(w.item.params.title),5)):d("",!0),2==w.item.style.titleType?(i(),o("img",{key:1,class:"titleImg",src:w.item.style.title_image,alt:""},null,8,x)):d("",!0)]),l("div",{class:"right white d-c-c",style:a([{"line-height":"1"},{color:w.item.style.moreColor,fontSize:w.item.style.moreSize+"px"}])},[p(r(w.item.params.more)+" ",1),m(I,{size:"14px"},{default:c((()=>[m(B)])),_:1})],4)],4),l("ul",{class:"product-list column__3",style:a(z.getUlwidth(w.item))},[(i(!0),o(n,null,y(w.item.data,((t,e)=>(i(),o("li",{class:"product-item",style:a({background:w.item.style.productBg_color,borderBottomLeftRadius:w.item.style.product_bottomRadio+"px",borderBottomRightRadius:w.item.style.product_bottomRadio+"px",borderTopLeftRadius:w.item.style.product_topRadio+"px",borderTopRightRadius:w.item.style.product_topRadio+"px"}),key:e},[l("div",f,[u(l("img",{style:a({borderRadius:w.item.style.product_imgRadio+"px"})},null,4),[[S,t.image]]),1==w.item.style.product_sales?(i(),o("div",{key:0,style:a({color:w.item.style.salesColor,background:w.item.style.bgSales}),class:"product-sales"}," 已砍13人 ",4)):d("",!0)]),l("div",v,[l("div",R,[1==w.item.style.product_name?(i(),o("div",{key:0,class:"f12 tc text-ellipsis",style:a({color:w.item.style.productName_color})}," 商品名稱 ",4)):d("",!0),1==w.item.style.product_price?(i(),o("div",{key:1,class:"f14 tc",style:a({color:w.item.style.productPrice_color})}," ¥120 ",4)):d("",!0),1==w.item.style.product_lineprice?(i(),o("div",{key:2,style:a({color:w.item.style.productLine_color}),class:"f12 text-d-line"}," ¥233 ",4)):d("",!0)])])],4)))),128))],4)],4),l("div",_,[l("div",{class:"btn-del",onClick:k[0]||(k[0]=g((t=>j.$parent.$parent.onDeleleItem(w.index)),["stop"]))},"刪除")])],6)}],["__scopeId","data-v-74b3a9ef"]]);export{k as default};
