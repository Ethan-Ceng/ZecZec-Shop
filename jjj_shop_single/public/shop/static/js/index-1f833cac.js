import{E as e,m as l,e as t,s as o,t as a,n as i,d as r,f as s,c as d}from"./element-plus-84a27f94.js";import{S as m}from"./setting-db36ed28.js";import{_ as n}from"./index-5ae5860a.js";import{o as p,c as u,P as f,S as _,a as c,W as h,Q as j,a9 as b,T as V,Y as v}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const g={data:()=>({form:{is_open:"",printer_id:"",order_status:[],label_print_type:"",siid:""},checked:!1,printerList:[],loading:!1}),created(){this.getData()},methods:{getData(){let e=this;m.printingDetail({},!0).then((l=>{let t=l.data.vars.values;e.form.is_open=t.is_open,e.form.printer_id=""+t.printer_id,e.form.order_status=t.order_status,e.form.label_print_type=t.label_print_type,e.form.siid=t.siid,e.printerList=l.data.vars.printerList,null!=t.order_status&&20==t.order_status[0]&&(e.checked=!0)})).catch((e=>{}))},onSubmit(){let l=this,t=this.form;l.loading=!0,m.editPrinting(t,!0).then((t=>{l.loading=!1,e({message:"恭喜你，列印設定成功",type:"success"})})).catch((e=>{l.loading=!1}))},handleCheckedCitiesChange(e){let l=this;e?l.form.order_status[0]=20:l.form.order_status=[]}}},y={class:"product-add"},k=c("div",{class:"common-form"},"小票列印設定",-1),C=c("div",{class:"common-form"},"電子面單列印設定",-1),M=c("div",{class:"tips"},"如需使用快遞100雲列印功能,請購買快遞100雲印表機,並在快遞100印表機管理繫結該裝置,快遞100印表機編碼檢視貼在硬體上的標籤SIID裝置碼",-1),U={class:"common-button-wrapper"};const w=n(g,[["render",function(e,m,n,g,w,x){const S=l,D=t,L=o,z=a,I=i,P=r,q=s,A=d;return p(),u("div",y,[f(A,{size:"small",ref:"form",model:w.form,"label-width":"200px"},{default:_((()=>[k,f(D,{label:"是否開啟小票列印"},{default:_((()=>[c("div",null,[f(S,{modelValue:w.form.is_open,"onUpdate:modelValue":m[0]||(m[0]=e=>w.form.is_open=e),modelModifiers:{trim:!0},label:1},{default:_((()=>[h("開啟")])),_:1},8,["modelValue"]),f(S,{modelValue:w.form.is_open,"onUpdate:modelValue":m[1]||(m[1]=e=>w.form.is_open=e),modelModifiers:{trim:!0},label:0},{default:_((()=>[h("關閉")])),_:1},8,["modelValue"])])])),_:1}),f(D,{label:"選擇訂單印表機"},{default:_((()=>[f(z,{modelValue:w.form.printer_id,"onUpdate:modelValue":m[2]||(m[2]=e=>w.form.printer_id=e),modelModifiers:{trim:!0},placeholder:"請選擇",style:{width:"460px"}},{default:_((()=>[(p(!0),u(j,null,b(w.printerList,((e,l)=>(p(),V(L,{key:l,label:e.printer_name,value:e.printer_id+""},null,8,["label","value"])))),128))])),_:1},8,["modelValue"])])),_:1}),f(D,{label:"訂單列印方式"},{default:_((()=>[f(I,{modelValue:w.checked,"onUpdate:modelValue":m[3]||(m[3]=e=>w.checked=e),modelModifiers:{trim:!0},onChange:x.handleCheckedCitiesChange},{default:_((()=>[h("訂單付款時")])),_:1},8,["modelValue","onChange"])])),_:1}),C,f(D,{label:"電子面單列印方式"},{default:_((()=>[c("div",null,[f(S,{modelValue:w.form.label_print_type,"onUpdate:modelValue":m[4]||(m[4]=e=>w.form.label_print_type=e),modelModifiers:{trim:!0},label:"1"},{default:_((()=>[h("快遞100雲列印")])),_:1},8,["modelValue"]),f(S,{modelValue:w.form.label_print_type,"onUpdate:modelValue":m[5]||(m[5]=e=>w.form.label_print_type=e),modelModifiers:{trim:!0},label:"0"},{default:_((()=>[h("本地列印")])),_:1},8,["modelValue"])])])),_:1}),"1"==w.form.label_print_type?(p(),V(D,{key:0,label:"快遞100雲印表機編碼",prop:"siid"},{default:_((()=>[f(P,{modelValue:w.form.siid,"onUpdate:modelValue":m[6]||(m[6]=e=>w.form.siid=e),modelModifiers:{trim:!0},placeholder:"",class:"max-w460"},null,8,["modelValue"]),M])),_:1})):v("",!0),c("div",U,[f(q,{type:"primary",onClick:x.onSubmit,loading:w.loading},{default:_((()=>[h("提交")])),_:1},8,["onClick","loading"])])])),_:1},8,["model"])])}]]);export{w as default};
