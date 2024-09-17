import{E as e,s as l,t as o,e as a,d as i,f as s,C as t,c as r,q as d}from"./element-plus-84a27f94.js";import{_ as m}from"./Upload-71ca9325.js";import{P as p}from"./product-6ff3546d.js";import{_ as u}from"./index-5ae5860a.js";import{o as n,T as f,S as c,a as g,P as h,W as b,c as _,Q as j,a9 as y,Y as V,bb as v,b9 as w}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./file-b7a04c7e.js";/* empty css                                                               */import"./AddCategory-69aab672.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const k={components:{Upload:m},data:()=>({form:{parent_id:"0",name:"",sort:100,image_id:""},formRules:{name:[{required:!0,message:"請輸入分類名稱",trigger:"blur"}],image_id:[{required:!0,message:"請上傳分類圖片",trigger:"blur"}],sort:[{required:!0,message:"分類排序不能為空"},{type:"number",message:"分類排序必須為數字"}]},formLabelWidth:"120px",dialogVisible:!1,loading:!1,isupload:!1}),props:["open_add","addform"],created(){this.dialogVisible=this.open_add},methods:{addUser(){let l=this,o=l.form;l.$refs.form.validate((a=>{a&&(l.loading=!0,p.catAdd(o).then((o=>{l.loading=!1,e({message:"新增成功",type:"success"}),l.dialogFormVisible(!0)})).catch((e=>{l.loading=!1})))}))},dialogFormVisible(e){e?this.$emit("closeDialog",{type:"success",openDialog:!1}):this.$emit("closeDialog",{type:"error",openDialog:!1})},openUpload(e){this.type=e,this.isupload=!0},returnImgsFunc(e){null!=e&&e.length>0&&(this.file_path=e[0].file_path,this.form.image_id=e[0].file_id),this.isupload=!1}}},C={key:0,class:"img"},U=["src"],x=(e=>(v("data-v-241d9e42"),e=e(),w(),e))((()=>g("div",{class:"gray"},"建議圖片上傳尺寸為100px*100px",-1))),F={class:"dialog-footer"};const L=u(k,[["render",function(e,p,u,v,w,k){const L=l,W=o,q=a,I=i,z=s,D=t,R=r,A=m,$=d;return n(),f($,{title:"新增分類",modelValue:w.dialogVisible,"onUpdate:modelValue":p[3]||(p[3]=e=>w.dialogVisible=e),onClose:k.dialogFormVisible,"close-on-click-modal":!1,"close-on-press-escape":!1,width:"600px"},{footer:c((()=>[g("div",F,[h(z,{onClick:k.dialogFormVisible},{default:c((()=>[b("取 消")])),_:1},8,["onClick"]),h(z,{type:"primary",onClick:k.addUser},{default:c((()=>[b("確 定")])),_:1},8,["onClick"])])])),default:c((()=>[h(R,{size:"small",model:w.form,rules:w.formRules,ref:"form"},{default:c((()=>[h(q,{label:"所屬分類","label-width":w.formLabelWidth},{default:c((()=>[h(W,{modelValue:w.form.parent_id,"onUpdate:modelValue":p[0]||(p[0]=e=>w.form.parent_id=e),style:{width:"100%"}},{default:c((()=>[h(L,{label:"頂級分類",value:"0"}),(n(!0),_(j,null,y(u.addform.catList,(e=>(n(),f(L,{value:e.category_id,label:e.name,key:e.category_id},null,8,["value","label"])))),128))])),_:1},8,["modelValue"])])),_:1},8,["label-width"]),h(q,{label:"分類名稱",prop:"name","label-width":w.formLabelWidth},{default:c((()=>[h(I,{modelValue:w.form.name,"onUpdate:modelValue":p[1]||(p[1]=e=>w.form.name=e),autocomplete:"off"},null,8,["modelValue"])])),_:1},8,["label-width"]),h(q,{label:"分類圖片",prop:"image_id","label-width":w.formLabelWidth},{default:c((()=>[h(D,{class:"d-c"},{default:c((()=>[h(z,{type:"primary",onClick:k.openUpload,style:{width:"92px"}},{default:c((()=>[b("選擇圖片")])),_:1},8,["onClick"]),""!=w.form.image_id?(n(),_("div",C,[g("img",{src:e.file_path,width:"100",height:"100"},null,8,U)])):V("",!0),x])),_:1})])),_:1},8,["label-width"]),h(q,{label:"分類排序",prop:"sort","label-width":w.formLabelWidth},{default:c((()=>[h(I,{modelValue:w.form.sort,"onUpdate:modelValue":p[2]||(p[2]=e=>w.form.sort=e),modelModifiers:{number:!0},autocomplete:"off"},null,8,["modelValue"])])),_:1},8,["label-width"])])),_:1},8,["model","rules"]),w.isupload?(n(),f(A,{key:0,isupload:w.isupload,type:e.type,onReturnImgs:k.returnImgsFunc},{default:c((()=>[b("上傳圖片")])),_:1},8,["isupload","type","onReturnImgs"])):V("",!0)])),_:1},8,["modelValue","onClose"])}],["__scopeId","data-v-241d9e42"]]);export{L as default};
