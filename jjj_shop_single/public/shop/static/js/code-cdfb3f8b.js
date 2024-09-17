import{E as e,e as o,d as l,J as a,f as t,c as s,v as r}from"./element-plus-84a27f94.js";import{C as d}from"./card-8cac6905.js";import{_ as m}from"./Upload-71ca9325.js";import{_ as i}from"./index-5ae5860a.js";import{$ as p,o as u,c as n,P as c,S as f,W as _,X as j,a as h}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./file-b7a04c7e.js";/* empty css                                                               */import"./AddCategory-69aab672.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const g={components:{Upload:m},data:()=>({loading:!0,isupload:!1,form:{card_id:0,prefix:"",start_num:"",code_len:6,code_count:"",start_time:"",end_time:""},model:{},category:[],product:[]}),created(){this.getDetail()},methods:{openUpload(){this.isupload=!0},returnImgsFunc(e){null!=e&&(this.form.image_id=e[0].file_id,this.form.image.file_path=e[0].file_path),this.isupload=!1},getDetail(){let e=this;const o=e.$route.query.card_id;d.toCodeCard({card_id:o},!0).then((o=>{e.model=o.data.model,e.loading=!1})).catch((e=>{}))},onSubmit(){let o=this,l=o.form;l.card_id=o.$route.query.card_id,d.codeCard(l,!0).then((l=>{e({message:l.msg,type:"success"}),o.cancelFunc()})).catch((e=>{}))},cancelFunc(){this.$router.back(-1)}}},b={class:"product-add pb50"},V=h("div",{class:"common-form"},"提貨碼",-1),y=h("span",null,null,-1),v={class:"common-button-wrapper"};const x=i(g,[["render",function(e,d,m,i,g,x){const U=o,w=l,C=a,Y=t,q=s,k=r;return p((u(),n("div",b,[c(q,{size:"small",model:g.form,ref:"form","label-width":"100px"},{default:f((()=>[V,c(U,{label:"卡券名稱",prop:"card_title"},{default:f((()=>[_(j(g.model.card_title),1)])),_:1}),c(U,{label:"字首"},{default:f((()=>[c(w,{modelValue:g.form.prefix,"onUpdate:modelValue":d[0]||(d[0]=e=>g.form.prefix=e),placeholder:"",class:"max-w460"},null,8,["modelValue"])])),_:1}),c(U,{label:"起始數字",rules:[{required:!0,message:" "}],prop:"start_num"},{default:f((()=>[c(w,{modelValue:g.form.start_num,"onUpdate:modelValue":d[1]||(d[1]=e=>g.form.start_num=e),placeholder:"",class:"max-w460"},null,8,["modelValue"])])),_:1}),c(U,{label:"密碼位數",rules:[{required:!0,message:" "}],prop:"code_len"},{default:f((()=>[c(w,{modelValue:g.form.code_len,"onUpdate:modelValue":d[2]||(d[2]=e=>g.form.code_len=e),placeholder:"",class:"max-w460"},null,8,["modelValue"])])),_:1}),c(U,{label:"生成數量",rules:[{required:!0,message:" "}],prop:"code_count"},{default:f((()=>[c(w,{modelValue:g.form.code_count,"onUpdate:modelValue":d[3]||(d[3]=e=>g.form.code_count=e),placeholder:"",class:"max-w460"},null,8,["modelValue"])])),_:1}),c(U,{label:"提貨時間",rules:[{required:!0,message:" "}],prop:"start_time"},{default:f((()=>[c(C,{modelValue:g.form.start_time,"onUpdate:modelValue":d[4]||(d[4]=e=>g.form.start_time=e),type:"date","value-format":"YYYY-MM-DD",placeholder:"選擇開始時間"},null,8,["modelValue"]),y,c(C,{modelValue:g.form.end_time,"onUpdate:modelValue":d[5]||(d[5]=e=>g.form.end_time=e),type:"date","value-format":"YYYY-MM-DD",placeholder:"選擇結束時間"},null,8,["modelValue"])])),_:1}),h("div",v,[c(Y,{size:"small",type:"info",onClick:x.cancelFunc,loading:g.loading},{default:f((()=>[_("取消")])),_:1},8,["onClick","loading"]),c(Y,{size:"small",type:"primary",onClick:x.onSubmit,loading:g.loading},{default:f((()=>[_("提交")])),_:1},8,["onClick","loading"])])])),_:1},8,["model"])])),[[k,g.loading]])}]]);export{x as default};
