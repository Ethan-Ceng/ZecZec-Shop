import{E as e,d as o,e as s,f as a,c as t}from"./element-plus-84a27f94.js";import{S as l}from"./setting-db36ed28.js";import{_ as r}from"./index-5ae5860a.js";import{o as m,c as i,P as d,S as p,a as n,W as u}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const c={data:()=>({loading:!1,form:{name:"",phone:"",detail:"",sort:1}}),created(){},methods:{onSubmit(){let o=this,s=o.form;o.$refs.form.validate((a=>{a&&(o.loading=!0,l.addAddress(s,!0).then((s=>{o.loading=!1,e({message:"恭喜你，新增成功",type:"success"}),o.$router.push("/setting/address/index")})).catch((e=>{o.loading=!1})))}))}}},f={class:"product-add"},j=n("div",{class:"common-form"},"新增地址",-1),h=n("div",{class:"tips"},"數字越小越靠前",-1),b={class:"common-button-wrapper"};const g=r(c,[["render",function(e,l,r,c,g,v){const x=o,V=s,y=a,w=t;return m(),i("div",f,[d(w,{size:"small",ref:"form",model:g.form,"label-width":"200px"},{default:p((()=>[j,d(V,{label:"收貨人姓名",prop:"name",rules:[{required:!0,message:" "}]},{default:p((()=>[d(x,{type:"text",modelValue:g.form.name,"onUpdate:modelValue":l[0]||(l[0]=e=>g.form.name=e),modelModifiers:{trim:!0},class:"max-w460",prop:"name"},null,8,["modelValue"])])),_:1}),d(V,{label:"聯絡電話",prop:"phone",rules:[{required:!0,message:" "}]},{default:p((()=>[d(x,{type:"text",modelValue:g.form.phone,"onUpdate:modelValue":l[1]||(l[1]=e=>g.form.phone=e),modelModifiers:{trim:!0},class:"max-w460"},null,8,["modelValue"])])),_:1}),d(V,{label:"詳細地址",prop:"detail",rules:[{required:!0,message:" "}]},{default:p((()=>[d(x,{type:"text",modelValue:g.form.detail,"onUpdate:modelValue":l[2]||(l[2]=e=>g.form.detail=e),class:"max-w460"},null,8,["modelValue"])])),_:1}),d(V,{label:"排序"},{default:p((()=>[d(x,{modelValue:g.form.sort,"onUpdate:modelValue":l[3]||(l[3]=e=>g.form.sort=e),modelModifiers:{trim:!0},type:"number",class:"max-w460"},null,8,["modelValue"]),h])),_:1}),n("div",b,[d(y,{size:"small",type:"primary",onClick:l[4]||(l[4]=e=>v.onSubmit()),loading:g.loading},{default:p((()=>[u("提交")])),_:1},8,["loading"])])])),_:1},8,["model"])])}]]);export{g as default};
