import{J as e,e as l,d as o,m as a,g as s}from"./element-plus-84a27f94.js";import{_ as m}from"./index-5ae5860a.js";import{o as r,c as t,P as i,S as u,a as p,W as d}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const n={inject:["form"]},j=p("div",{class:"common-form"},"其它",-1);const c=m(n,[["render",function(m,n,c,f,b,V){const _=e,v=l,y=o,h=a,g=s;return r(),t("div",null,[j,i(v,{label:"活動時間",rules:[{required:!0,message:"請選擇活動時間"}],prop:"active_time"},{default:u((()=>[p("div",null,[i(_,{modelValue:V.form.active_time,"onUpdate:modelValue":n[0]||(n[0]=e=>V.form.active_time=e),type:"datetimerange","range-separator":"至","start-placeholder":"開始日期","end-placeholder":"結束日期"},null,8,["modelValue"])])])),_:1}),i(v,{label:"定金",prop:"money",rules:[{required:!0,message:"請填寫定金"}]},{default:u((()=>[i(y,{type:"number",modelValue:V.form.money,"onUpdate:modelValue":n[1]||(n[1]=e=>V.form.money=e),class:"max-w460"},null,8,["modelValue"])])),_:1}),i(v,{label:"限購數量",prop:"limit_num",rules:[{required:!0,message:"請填寫限購數量"}]},{default:u((()=>[i(y,{type:"number",modelValue:V.form.limit_num,"onUpdate:modelValue":n[2]||(n[2]=e=>V.form.limit_num=e),class:"max-w460"},null,8,["modelValue"])])),_:1}),i(v,{label:"排序",prop:"sort",rules:[{required:!0,message:"請填寫排序"}]},{default:u((()=>[i(y,{type:"number",modelValue:V.form.sort,"onUpdate:modelValue":n[3]||(n[3]=e=>V.form.sort=e),class:"max-w460"},null,8,["modelValue"])])),_:1}),i(v,{label:"商品狀態"},{default:u((()=>[i(g,{modelValue:V.form.status,"onUpdate:modelValue":n[4]||(n[4]=e=>V.form.status=e)},{default:u((()=>[i(h,{label:10},{default:u((()=>[d("上架")])),_:1}),i(h,{label:20},{default:u((()=>[d("下架")])),_:1})])),_:1},8,["modelValue"])])),_:1})])}]]);export{c as default};
