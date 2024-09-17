import{P as e}from"./Product-14aa5af4.js";import{E as o,m as a,e as t,f as s,k as l,C as i,c as m}from"./element-plus-84a27f94.js";import{P as r}from"./plus-c5329662.js";import{_ as d}from"./index-5ae5860a.js";import{ae as p,o as c,c as u,P as n,S as _,a as f,W as b,Q as j,T as h,a9 as g,X as y,Y as v,bb as V,b9 as w}from"./@vue-8fe4574d.js";import"./product-6ff3546d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const k={components:{Product:e},data:()=>({form:{},isproduct:!1,loading:!1}),props:{settingData:Object},created(){this.form=this.settingData.data.condition.values,this.form.product_image||(this.form.product_image=[])},methods:{onSubmit(){let e=this;e.loading=!0;let a=this.form;r.condition(a,!0).then((a=>{e.loading=!1,o({message:"恭喜你，設定成功",type:"success"})})).catch((o=>{e.loading=!1}))},deleteFunc(e){this.form.become__buy_product_ids.splice(e,1),this.form.product_image.splice(e,1)},openProduct(){this.isproduct=!0},closeDialogFunc(e){this.isproduct=e.openDialog,"success"==e.type&&(-1==this.form.become__buy_product_ids.indexOf(e.params.product_id)?(this.form.become__buy_product_ids.push(e.params.product_id),this.form.product_image.push({product_id:e.params.product_id,image:e.params.image,product_name:e.params.product_name})):o({message:"已選擇該商品",type:"warning"}))}}},C={class:"product-add mt30"},P=(e=>(V("data-v-0ac3e603"),e=e(),w(),e))((()=>f("div",{class:"tips"},"購買指定商品付款後自動成為分銷商，無需後臺稽核",-1))),x={class:"ww100"},D=["src"],F=["onClick"],U={class:"text-ellipsis"},z={class:"common-button-wrapper"};const S=d(k,[["render",function(o,r,d,V,w,k){const S=a,I=t,O=s,q=p("CircleCloseFilled"),A=l,E=i,J=m,N=e;return c(),u("div",C,[n(J,{size:"small",ref:"form",model:w.form,"label-width":"200px"},{default:_((()=>[n(I,{label:"成為分銷商條件"},{default:_((()=>[f("div",null,[n(S,{modelValue:w.form.become,"onUpdate:modelValue":r[0]||(r[0]=e=>w.form.become=e),label:"10"},{default:_((()=>[b("需後臺稽核")])),_:1},8,["modelValue"]),n(S,{modelValue:w.form.become,"onUpdate:modelValue":r[1]||(r[1]=e=>w.form.become=e),label:"20"},{default:_((()=>[b("無需稽核")])),_:1},8,["modelValue"])])])),_:1}),n(I,{label:"購買指定商品成為分銷商"},{default:_((()=>[n(S,{modelValue:w.form.become__buy_product,"onUpdate:modelValue":r[2]||(r[2]=e=>w.form.become__buy_product=e),label:"0"},{default:_((()=>[b("關閉")])),_:1},8,["modelValue"]),n(S,{modelValue:w.form.become__buy_product,"onUpdate:modelValue":r[3]||(r[3]=e=>w.form.become__buy_product=e),label:"1"},{default:_((()=>[b("開啟")])),_:1},8,["modelValue"]),P,1==w.form.become__buy_product?(c(),u(j,{key:0},[f("div",x,[n(O,{type:"primary",onClick:k.openProduct,class:"mb16"},{default:_((()=>[b("選擇商品")])),_:1},8,["onClick"])]),w.form.product_image&&w.form.product_image.length>0?(c(),h(E,{key:0,class:"ww100"},{default:_((()=>[(c(!0),u(j,null,g(w.form.product_image,((e,o)=>(c(),u("div",{key:o,class:"imgItem pr mr10"},[f("img",{src:e.image,width:"100",height:"100"},null,8,D),f("a",{href:"javascript:void(0)",class:"delete-btn",onClick:e=>k.deleteFunc(o)},[n(A,null,{default:_((()=>[n(q)])),_:1})],8,F),f("p",U,y(e.product_name),1)])))),128))])),_:1})):v("",!0)],64)):v("",!0)])),_:1}),n(I,{label:"成為下線條件"},{default:_((()=>[f("div",null,[n(S,{modelValue:w.form.downline,"onUpdate:modelValue":r[4]||(r[4]=e=>w.form.downline=e),label:"10"},{default:_((()=>[b("首次點選分享連結")])),_:1},8,["modelValue"])])])),_:1}),f("div",z,[n(O,{size:"small",type:"primary",onClick:k.onSubmit,loading:w.loading},{default:_((()=>[b("提交")])),_:1},8,["onClick","loading"])])])),_:1},8,["model"]),n(N,{isproduct:w.isproduct,onCloseDialog:r[5]||(r[5]=e=>k.closeDialogFunc(e))},{default:_((()=>[b("產品列表彈出層")])),_:1},8,["isproduct"])])}],["__scopeId","data-v-0ac3e603"]]);export{S as default};
