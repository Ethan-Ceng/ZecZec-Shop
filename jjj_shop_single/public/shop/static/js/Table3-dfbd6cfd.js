import{E as e,m as s,g as t,e as o,d as a,f as l,c as r}from"./element-plus-84a27f94.js";import{P as i}from"./push-49f3a20d.js";import{_ as m}from"./index-5ae5860a.js";import{o as p,c as u,P as d,S as n,W as j,a as c}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const f={data:()=>({form:{status:0}}),methods:{onSubmit(){let s=this,t=this.form;i.shipping(t,!0).then((t=>{1==t.code?(e({message:"恭喜你，新增成功",type:"success"}),s.$router.push("/plus/article/Index")):e.error("錯了哦，這是一條錯誤訊息")})).catch((e=>{}))}}},h={class:"user"},b=c("div",{class:"common-form"},"滿額包郵設定",-1),_={style:{width:"500px"}},y=c("p",null,"如果開啟滿額包郵，設定0為全場包郵",-1);const v=m(f,[["render",function(e,i,m,f,v,x){const V=s,g=t,w=o,k=a,S=l,z=r;return p(),u("div",h,[d(z,{ref:"form",model:v.form,"label-width":"150px"},{default:n((()=>[b,d(w,{label:"是否開啟滿額包郵"},{default:n((()=>[d(g,{modelValue:v.form.status,"onUpdate:modelValue":i[0]||(i[0]=e=>v.form.status=e)},{default:n((()=>[d(V,{label:0},{default:n((()=>[j("關")])),_:1}),d(V,{label:1},{default:n((()=>[j("開")])),_:1})])),_:1},8,["modelValue"])])),_:1}),d(w,{label:" 單筆訂單滿 "},{default:n((()=>[c("div",_,[d(k,{placeholder:"請輸入金額",modelValue:e.input2,"onUpdate:modelValue":i[1]||(i[1]=s=>e.input2=s)},{append:n((()=>[j("元")])),_:1},8,["modelValue"]),y])])),_:1}),d(w,{label:"不參與包郵的商品"},{default:n((()=>[d(S,{type:"primary"},{default:n((()=>[j("請選擇商品")])),_:1})])),_:1}),d(w,{label:"不參與包郵的地區"},{default:n((()=>[d(S,{type:"primary"},{default:n((()=>[j("請選擇地區")])),_:1})])),_:1}),d(w,null,{default:n((()=>[d(S,{type:"primary",onClick:x.onSubmit},{default:n((()=>[j("提交")])),_:1},8,["onClick"])])),_:1})])),_:1},8,["model"])])}]]);export{v as default};
