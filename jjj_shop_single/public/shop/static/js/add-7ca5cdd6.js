import{_ as e}from"./category-c676c94f.js";import{P as l}from"./Product-14aa5af4.js";import{E as a,x as o,y as t,d,e as r,m as s,g as i,J as u,f as m,l as p,i as c,K as n,c as _}from"./element-plus-84a27f94.js";import{C as f}from"./coupon-7cc893e2.js";import{_ as y}from"./index-5ae5860a.js";import{ap as b,o as g,c as h,P as V,S as v,Q as j,W as k,Y as x,T as C,$ as U,a as w,a9 as z,X as D,bb as I,b9 as q}from"./@vue-8fe4574d.js";import"./product-6ff3546d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const F={components:{Product:l,Category:e},data:()=>({activeName:"1",form:{color:"10",coupon_type:"10",expire_type:"10",sort:1,active_time:"",show_center:1,free_limit:0,apply_range:10,max_price:""},loading:!1,pickerOptions0:{disabledDate:e=>e.getTime()<Date.now()-864e5},is_category:!1,is_product:!1,exclude_ids:[],product_list:[],category_list:{second:[],first:[]},category_ids:[]}),created(){},methods:{hasImages:e=>e||"",onSubmit(){let e=this,l=e.form;l.product_ids=e.exclude_ids,l.category_list=e.category_list,e.$refs.form.validate((o=>{o&&(e.loading=!0,f.addCoupon(l,!0).then((l=>{e.loading=!1,a({message:"恭喜你，新增成功",type:"success"}),e.$router.push("/plus/coupon/index")})).catch((l=>{e.loading=!1})))}))},closeCategoryFunc(e){let l=this;l.is_category=!1,e&&(l.category_list=e)},closeProductFunc(e){let l=this;l.is_product=e.openDialog,"success"==e.type&&e.params.forEach(((e,a)=>{let o={product_id:e.product_id,product_name:e.product_name,product_image:e.product_image};l.exclude_ids.push(o.product_id),l.product_list.push(o)}))},cancelFunc(){this.$router.push({path:"/plus/coupon/index"})},deleteClick(e){this.exclude_ids.splice(e,1),this.product_list.splice(e,1)}}},P=e=>(I("data-v-53ba55c3"),e=e(),q(),e),N={class:"product-add"},Y=P((()=>w("div",{class:"common-form"},"新增優惠券",-1))),$=P((()=>w("div",{class:"tips"},"例如：滿100減10",-1))),S={key:0},E={key:1},J=P((()=>w("div",{class:"tips"},"折扣率範圍0-10，9.5代表9.5折，0代表不折扣",-1))),M=P((()=>w("div",{class:"tips"},"最大抵扣金額不能超出此金額，0代表不限制",-1))),O={key:2},T={key:3},W=P((()=>w("div",{class:"tips"},"限制領取的優惠券數量，-1為不限制",-1))),A=P((()=>w("div",{class:"tips"},"促銷是指滿減，等級優惠券值商品的會員等級折扣",-1))),K=P((()=>w("div",{class:"common-form"},"適用商品",-1))),Q=P((()=>w("span",null,"指定商品",-1))),X=P((()=>w("span",null,"指定分類",-1))),B={alt:"",width:50},G={class:"common-button-wrapper"};const H=y(F,[["render",function(a,f,y,I,q,F){const P=o,H=t,L=d,R=r,Z=s,ee=i,le=u,ae=m,oe=p,te=c,de=n,re=_,se=l,ie=e,ue=b("img-url");return g(),h("div",N,[V(re,{size:"small",ref:"form",model:q.form,"label-width":"200px"},{default:v((()=>[V(H,{modelValue:q.activeName,"onUpdate:modelValue":f[0]||(f[0]=e=>q.activeName=e),type:"card"},{default:v((()=>[V(P,{label:"基本資訊",name:"1"}),V(P,{label:"適用商品",name:"2"})])),_:1},8,["modelValue"]),"1"==q.activeName?(g(),h(j,{key:0},[Y,V(R,{label:"優惠券名稱",prop:"name",rules:[{required:!0,message:" "}]},{default:v((()=>[V(L,{modelValue:q.form.name,"onUpdate:modelValue":f[1]||(f[1]=e=>q.form.name=e),placeholder:"請輸入優惠券名稱"},null,8,["modelValue"]),$])),_:1}),V(R,{label:"優惠券顏色"},{default:v((()=>[V(ee,{modelValue:q.form.color,"onUpdate:modelValue":f[2]||(f[2]=e=>q.form.color=e)},{default:v((()=>[V(Z,{label:"10"},{default:v((()=>[k("藍色")])),_:1}),V(Z,{label:"20"},{default:v((()=>[k("紅色")])),_:1}),V(Z,{label:"30"},{default:v((()=>[k("紫色")])),_:1}),V(Z,{label:"40"},{default:v((()=>[k("黃色")])),_:1})])),_:1},8,["modelValue"])])),_:1}),V(R,{label:"優惠券型別"},{default:v((()=>[V(ee,{modelValue:q.form.coupon_type,"onUpdate:modelValue":f[3]||(f[3]=e=>q.form.coupon_type=e)},{default:v((()=>[V(Z,{label:"10"},{default:v((()=>[k("滿減券")])),_:1}),V(Z,{label:"20"},{default:v((()=>[k("折扣券")])),_:1})])),_:1},8,["modelValue"])])),_:1}),10==q.form.coupon_type?(g(),h("div",S,[V(R,{label:"減免金額",prop:"reduce_price",rules:[{required:!0,message:" "}]},{default:v((()=>[V(L,{modelValue:q.form.reduce_price,"onUpdate:modelValue":f[4]||(f[4]=e=>q.form.reduce_price=e),placeholder:"請輸入減免金額",type:"number"},null,8,["modelValue"])])),_:1})])):(g(),h("div",E,[V(R,{label:"折扣率 ",prop:"discount",rules:[{required:!0,message:" "}]},{default:v((()=>[V(L,{modelValue:q.form.discount,"onUpdate:modelValue":f[5]||(f[5]=e=>q.form.discount=e),placeholder:"請輸入折扣率",type:"number"},null,8,["modelValue"]),J])),_:1}),V(R,{label:"最多優惠金額",prop:"max_price",rules:[{required:!0,message:" "}]},{default:v((()=>[V(L,{modelValue:q.form.max_price,"onUpdate:modelValue":f[6]||(f[6]=e=>q.form.max_price=e),placeholder:"請輸入最多優惠金額",type:"number"},null,8,["modelValue"]),M])),_:1})])),V(R,{label:"最低消費金額",prop:"min_price",rules:[{required:!0,message:" "}]},{default:v((()=>[V(L,{modelValue:q.form.min_price,"onUpdate:modelValue":f[7]||(f[7]=e=>q.form.min_price=e),placeholder:"請輸入最低消費金額",type:"number"},null,8,["modelValue"])])),_:1}),V(R,{label:"到期型別"},{default:v((()=>[V(ee,{modelValue:q.form.expire_type,"onUpdate:modelValue":f[8]||(f[8]=e=>q.form.expire_type=e)},{default:v((()=>[V(Z,{label:"10"},{default:v((()=>[k("領取後生效")])),_:1}),V(Z,{label:"20"},{default:v((()=>[k("固定時間")])),_:1})])),_:1},8,["modelValue"])])),_:1}),10==q.form.expire_type?(g(),h("div",O,[V(R,{label:"有效天數"},{default:v((()=>[V(L,{modelValue:q.form.expire_day,"onUpdate:modelValue":f[9]||(f[9]=e=>q.form.expire_day=e),placeholder:"請輸入有效天數",type:"number"},null,8,["modelValue"])])),_:1})])):(g(),h("div",T,[V(R,{label:"有效時間"},{default:v((()=>[V(le,{modelValue:q.form.active_time,"onUpdate:modelValue":f[10]||(f[10]=e=>q.form.active_time=e),type:"daterange",align:"right","unlink-panels":"","value-format":"YYYY-MM-DD","range-separator":"至","start-placeholder":"開始日期","end-placeholder":"結束日期","picker-options":q.pickerOptions0},null,8,["modelValue","picker-options"])])),_:1})])),V(R,{label:"發放總數量 ",prop:"total_num",rules:[{required:!0,message:" "}]},{default:v((()=>[V(L,{modelValue:q.form.total_num,"onUpdate:modelValue":f[11]||(f[11]=e=>q.form.total_num=e),placeholder:"請輸入發放總數量",type:"number"},null,8,["modelValue"]),W])),_:1}),V(R,{label:"是否顯示在領券中心"},{default:v((()=>[V(ee,{modelValue:q.form.show_center,"onUpdate:modelValue":f[12]||(f[12]=e=>q.form.show_center=e)},{default:v((()=>[V(Z,{label:1},{default:v((()=>[k("顯示")])),_:1}),V(Z,{label:0},{default:v((()=>[k("不顯示")])),_:1})])),_:1},8,["modelValue"])])),_:1}),V(R,{label:"使用條件"},{default:v((()=>[V(ee,{modelValue:q.form.free_limit,"onUpdate:modelValue":f[13]||(f[13]=e=>q.form.free_limit=e)},{default:v((()=>[V(Z,{label:0},{default:v((()=>[k("不限制")])),_:1}),V(Z,{label:1},{default:v((()=>[k("不可與促銷同時使用")])),_:1}),V(Z,{label:2},{default:v((()=>[k("不可與等級優惠同時使用")])),_:1}),V(Z,{label:3},{default:v((()=>[k("不可於促銷和等級優惠同時使用")])),_:1})])),_:1},8,["modelValue"]),A])),_:1}),V(R,{label:"排序"},{default:v((()=>[V(L,{type:"number",modelValue:q.form.sort,"onUpdate:modelValue":f[14]||(f[14]=e=>q.form.sort=e),placeholder:"請輸入排序"},null,8,["modelValue"])])),_:1})],64)):x("",!0),"2"==q.activeName?(g(),h(j,{key:1},[K,V(R,{label:"選擇"},{default:v((()=>[V(ee,{modelValue:q.form.apply_range,"onUpdate:modelValue":f[15]||(f[15]=e=>q.form.apply_range=e)},{default:v((()=>[V(Z,{label:10},{default:v((()=>[k("全部商品")])),_:1}),V(Z,{label:20},{default:v((()=>[Q])),_:1}),V(Z,{label:30},{default:v((()=>[X])),_:1})])),_:1},8,["modelValue"])])),_:1}),20==q.form.apply_range?(g(),C(R,{key:0},{default:v((()=>[V(ae,{class:"mb10",onClick:f[16]||(f[16]=e=>q.is_product=!0),type:"primary",plain:""},{default:v((()=>[k("新增商品 ")])),_:1}),q.product_list.length>0?(g(),C(te,{key:0,data:q.product_list,"max-height":"400",border:"",style:{width:"100%"}},{default:v((()=>[V(oe,{prop:"product_id",label:"ID",width:"180"}),V(oe,{prop:"product_name",label:"商品名稱",width:"180"}),V(oe,{prop:"image",label:"圖片"},{default:v((e=>[U(w("img",B,null,512),[[ue,F.hasImages(e.row.product_image)]])])),_:1}),V(oe,{label:"操作"},{default:v((e=>[V(ae,{onClick:l=>F.deleteClick(e.$index),type:"text",size:"small"},{default:v((()=>[k("刪除")])),_:2},1032,["onClick"])])),_:1})])),_:1},8,["data"])):x("",!0)])),_:1})):x("",!0),30==q.form.apply_range?(g(),C(R,{key:1,label:""},{default:v((()=>[V(ae,{class:"mb10",onClick:f[17]||(f[17]=e=>q.is_category=!0),type:"primary",plain:""},{default:v((()=>[k("新增分類 ")])),_:1}),w("div",null,[q.category_list.first.length>0?(g(!0),h(j,{key:0},z(q.category_list.first,((e,l)=>(g(),h("div",{key:e.category_id,class:"mr10 mb10",style:{display:"inline-block"}},[V(de,{size:"large",type:"info"},{default:v((()=>[k(D(e.parent?e.parent+"→"+e.name:e.name),1)])),_:2},1024)])))),128)):x("",!0),q.category_list.second.length>0?(g(!0),h(j,{key:1},z(q.category_list.second,((e,l)=>(g(),h("div",{key:e.category_id,class:"mr10 mb10",style:{display:"inline-block"}},[V(de,{size:"large",class:"mr10 mb10",type:"info"},{default:v((()=>[k(D(e.parent?e.parent+"→"+e.name:e.name),1)])),_:2},1024)])))),128)):x("",!0)])])),_:1})):x("",!0)],64)):x("",!0),w("div",G,[V(ae,{type:"info",size:"small",onClick:F.cancelFunc,loading:q.loading},{default:v((()=>[k("取消")])),_:1},8,["onClick","loading"]),V(ae,{type:"primary",size:"small",onClick:F.onSubmit,loading:q.loading},{default:v((()=>[k("提交")])),_:1},8,["onClick","loading"])])])),_:1},8,["model"]),V(se,{isproduct:q.is_product,excludeIds:q.exclude_ids,islist:!0,onCloseDialog:F.closeProductFunc},null,8,["isproduct","excludeIds","onCloseDialog"]),V(ie,{is_category:q.is_category,excludeIds:q.category_ids,onClose:F.closeCategoryFunc},null,8,["is_category","excludeIds","onClose"])])}],["__scopeId","data-v-53ba55c3"]]);export{H as default};
