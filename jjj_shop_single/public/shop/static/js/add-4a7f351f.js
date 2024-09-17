import{E as e,d as l,e as r,s as a,t as o,f as s,c as m}from"./element-plus-84a27f94.js";import{S as i}from"./setting-db36ed28.js";import{_ as t}from"./index-5ae5860a.js";import{o as d,c as p,P as u,S as n,Q as _,a9 as f,T as E,Y as c,a as U,W as N}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const V={data:()=>({form:{printer_name:"",printer_type:"",sort:1,print_times:"",FEI_E_YUN:{USER:"",UKEY:"",SN:""},PRINT_CENTER:{deviceNo:"",key:""},XP_YUN:{USER:"",UKEY:"",SN:""}},loading:!1,type:[]}),created(){this.getData()},methods:{getData(){i.printerType({},!0).then((e=>{this.type=e.data.printerType})).catch((e=>{}))},onSubmit(){let l=this,r=l.form;l.$refs.form.validate((a=>{a&&(l.loading=!0,i.addPrinter(r,!0).then((r=>{l.loading=!1,e({message:"恭喜你，新增成功",type:"success"}),l.$router.push("/setting/printer/index")})).catch((e=>{l.loading=!1})))}))}}},Y={class:"product-add"},j=U("div",{class:"common-form"},"新增小票印表機",-1),v=U("div",{class:"tips"},"目前支援 飛鵝印表機、365雲列印",-1),y={key:0},b=U("div",{class:"tips"},"飛鵝雲後臺註冊使用者名稱",-1),R=U("div",{class:"tips"},"飛鵝雲後臺登入生成的UKEY",-1),g=U("div",{class:"tips"},"印表機編號為9位數字，檢視飛鵝印表機底部貼紙上面的編號",-1),S={key:1},P={key:2},h=U("div",{class:"tips"},"芯燁雲開放平臺開發者ID",-1),I=U("div",{class:"tips"},"芯燁雲開放平臺生成的開發者秘鑰",-1),T=U("div",{class:"tips"}," 檢視芯燁雲印表機背面二維碼下的編號 ",-1),x=U("div",{class:"tips"},"同一訂單，列印的次數",-1),w=U("div",{class:"tips"},"數字越小越靠前",-1),q={class:"common-button-wrapper"};const M=t(V,[["render",function(e,i,t,V,M,X){const k=l,F=r,K=a,C=o,z=s,D=m;return d(),p("div",Y,[u(D,{size:"small",ref:"form",model:M.form,"label-width":"200px"},{default:n((()=>[j,u(F,{label:"印表機名稱 ",prop:"printer_name",rules:[{required:!0,message:" "}]},{default:n((()=>[u(k,{modelValue:M.form.printer_name,"onUpdate:modelValue":i[0]||(i[0]=e=>M.form.printer_name=e),modelModifiers:{trim:!0},class:"max-w460"},null,8,["modelValue"])])),_:1}),u(F,{label:"印表機型別 ",prop:"printer_type",rules:[{required:!0,message:" "}]},{default:n((()=>[u(C,{modelValue:M.form.printer_type,"onUpdate:modelValue":i[1]||(i[1]=e=>M.form.printer_type=e),modelModifiers:{trim:!0},placeholder:"請選擇",style:{width:"460px"}},{default:n((()=>[(d(!0),p(_,null,f(M.type,((e,l)=>(d(),E(K,{key:l,label:e,value:l},null,8,["label","value"])))),128))])),_:1},8,["modelValue"]),v])),_:1}),"FEI_E_YUN"==M.form.printer_type?(d(),p("div",y,[u(F,{label:"USER",prop:"FEI_E_YUN.USER",rules:[{required:!0,message:" "}]},{default:n((()=>[u(k,{modelValue:M.form.FEI_E_YUN.USER,"onUpdate:modelValue":i[2]||(i[2]=e=>M.form.FEI_E_YUN.USER=e),modelModifiers:{trim:!0},class:"max-w460"},null,8,["modelValue"]),b])),_:1}),u(F,{label:"UKEY",prop:"FEI_E_YUN.UKEY",rules:[{required:!0,message:" "}]},{default:n((()=>[u(k,{modelValue:M.form.FEI_E_YUN.UKEY,"onUpdate:modelValue":i[3]||(i[3]=e=>M.form.FEI_E_YUN.UKEY=e),modelModifiers:{trim:!0},class:"max-w460"},null,8,["modelValue"]),R])),_:1}),u(F,{label:"印表機編號",prop:"FEI_E_YUN.SN",rules:[{required:!0,message:" "}]},{default:n((()=>[u(k,{modelValue:M.form.FEI_E_YUN.SN,"onUpdate:modelValue":i[4]||(i[4]=e=>M.form.FEI_E_YUN.SN=e),modelModifiers:{trim:!0},class:"max-w460"},null,8,["modelValue"]),g])),_:1})])):c("",!0),"PRINT_CENTER"==M.form.printer_type?(d(),p("div",S,[u(F,{label:"印表機編號 ",prop:"PRINT_CENTER.deviceNo",rules:[{required:!0,message:" "}]},{default:n((()=>[u(k,{modelValue:M.form.PRINT_CENTER.deviceNo,"onUpdate:modelValue":i[5]||(i[5]=e=>M.form.PRINT_CENTER.deviceNo=e),modelModifiers:{trim:!0},class:"max-w460"},null,8,["modelValue"])])),_:1}),u(F,{label:"印表機秘鑰",prop:"PRINT_CENTER.key",rules:[{required:!0,message:" "}]},{default:n((()=>[u(k,{modelValue:M.form.PRINT_CENTER.key,"onUpdate:modelValue":i[6]||(i[6]=e=>M.form.PRINT_CENTER.key=e),modelModifiers:{trim:!0},class:"max-w460"},null,8,["modelValue"])])),_:1})])):c("",!0),"XP_YUN"==M.form.printer_type?(d(),p("div",P,[u(F,{label:"USER",prop:"XP_YUN.USER",rules:[{required:!0,message:" "}]},{default:n((()=>[u(k,{modelValue:M.form.XP_YUN.USER,"onUpdate:modelValue":i[7]||(i[7]=e=>M.form.XP_YUN.USER=e),modelModifiers:{trim:!0},class:"max-w460"},null,8,["modelValue"]),h])),_:1}),u(F,{label:"UKEY",prop:"XP_YUN.UKEY",rules:[{required:!0,message:" "}]},{default:n((()=>[u(k,{modelValue:M.form.XP_YUN.UKEY,"onUpdate:modelValue":i[8]||(i[8]=e=>M.form.XP_YUN.UKEY=e),modelModifiers:{trim:!0},class:"max-w460"},null,8,["modelValue"]),I])),_:1}),u(F,{label:"印表機編號",prop:"XP_YUN.SN",rules:[{required:!0,message:" "}]},{default:n((()=>[u(k,{modelValue:M.form.XP_YUN.SN,"onUpdate:modelValue":i[9]||(i[9]=e=>M.form.XP_YUN.SN=e),modelModifiers:{trim:!0},class:"max-w460"},null,8,["modelValue"]),T])),_:1})])):c("",!0),u(F,{label:"列印聯數",prop:"print_times",rules:[{required:!0,message:" "}]},{default:n((()=>[u(k,{modelValue:M.form.print_times,"onUpdate:modelValue":i[10]||(i[10]=e=>M.form.print_times=e),modelModifiers:{trim:!0},type:"number",class:"max-w460"},null,8,["modelValue"]),x])),_:1}),u(F,{label:"排序"},{default:n((()=>[u(k,{modelValue:M.form.sort,"onUpdate:modelValue":i[11]||(i[11]=e=>M.form.sort=e),modelModifiers:{trim:!0},type:"number",class:"max-w460"},null,8,["modelValue"]),w])),_:1}),U("div",q,[u(z,{type:"primary",onClick:X.onSubmit,loading:M.loading},{default:n((()=>[N("提交")])),_:1},8,["onClick","loading"])])])),_:1},8,["model"])])}]]);export{M as default};
