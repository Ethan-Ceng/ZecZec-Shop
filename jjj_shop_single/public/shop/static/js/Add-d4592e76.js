import{E as e,d as o,e as i,c as r,f as t,q as s}from"./element-plus-84a27f94.js";import{O as a}from"./order-10f620ac.js";import{_ as l}from"./index-5ae5860a.js";import{o as d,T as p,S as m,a as u,P as n,W as c}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const j={data:()=>({order_id:0,loading:!1,formLabelWidth:"100px",dialogVisible:!0,order:{update_price:0,update_express_price:0}}),props:["open_edit"],created(){this.order_id=this.$route.query.order_id,this.getData()},methods:{getData(){let e=this;a.orderdetail({order_id:this.order_id},!0).then((o=>{e.loading=!1,e.order.update_price=o.data.detail.pay_price})).catch((o=>{e.loading=!1}))},submitFunc(o){let i=this,r=this.order;i.$refs.order.validate((o=>{o&&(i.loading=!0,a.updatePrice({order_id:this.order_id,order:r},!0).then((o=>{i.loading=!1,e({message:"修改成功",type:"success"}),i.dialogFormVisible(!0)})).catch((e=>{i.loading=!1})))}))},dialogFormVisible(){this.$emit("close",{openDialog:!1})}}},h=u("p",null,"最終付款價 = 訂單金額 + 運費金額",-1),_={class:"dialog-footer"};const b=l(j,[["render",function(e,a,l,j,b,f){const g=o,V=i,y=r,v=t,x=s;return d(),p(x,{title:"訂單價格修改",modelValue:b.dialogVisible,"onUpdate:modelValue":a[2]||(a[2]=e=>b.dialogVisible=e),onClose:f.dialogFormVisible,"close-on-click-modal":!1,"close-on-press-escape":!1,width:"30%"},{footer:m((()=>[u("div",_,[n(v,{onClick:f.dialogFormVisible},{default:m((()=>[c("取 消")])),_:1},8,["onClick"]),n(v,{type:"primary",onClick:f.submitFunc,loading:b.loading},{default:m((()=>[c("確 定")])),_:1},8,["onClick","loading"])])])),default:m((()=>[n(y,{size:"small",model:b.order,ref:"order"},{default:m((()=>[n(V,{label:"訂單金額","label-width":b.formLabelWidth,prop:"update_price",rules:[{required:!0,message:" "}]},{default:m((()=>[n(g,{type:"number",modelValue:b.order.update_price,"onUpdate:modelValue":a[0]||(a[0]=e=>b.order.update_price=e),autocomplete:"off"},null,8,["modelValue"]),h])),_:1},8,["label-width"]),n(V,{label:"運費金額","label-width":b.formLabelWidth,prop:"update_express_price",rules:[{required:!0,message:" "}]},{default:m((()=>[n(g,{type:"number",modelValue:b.order.update_express_price,"onUpdate:modelValue":a[1]||(a[1]=e=>b.order.update_express_price=e),autocomplete:"off"},null,8,["modelValue"])])),_:1},8,["label-width"])])),_:1},8,["model"])])),_:1},8,["modelValue","onClose"])}]]);export{b as default};
