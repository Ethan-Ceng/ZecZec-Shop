System.register(["./element-plus-legacy-4010b94c.js","./@vue-legacy-a5eb5da2.js","./index-legacy-a1d733aa.js","./@vueuse-legacy-7e5b8ef2.js","./lodash-es-legacy-93596df5.js","./async-validator-legacy-aa1fd2de.js","./dayjs-legacy-4687536e.js","./call-bind-legacy-b90429f9.js","./get-intrinsic-legacy-4f34f38e.js","./has-symbols-legacy-afcc0593.js","./has-proto-legacy-d280ab1e.js","./function-bind-legacy-b76e1749.js","./has-legacy-06d86c07.js","./@element-plus-legacy-f19c70ad.js","./escape-html-legacy-ae962a8c.js","./normalize-wheel-es-legacy-13d39621.js","./@ctrl-legacy-33dbca08.js","./vue-router-legacy-b3eb717b.js","./pinia-legacy-9406b588.js","./vue-demi-legacy-97cfbb01.js","./axios-legacy-3cda2001.js","./qs-legacy-5fc46ed9.js","./side-channel-legacy-8f83241e.js","./object-inspect-legacy-2e2b0934.js","./vue-clipboard2-legacy-b2d9783e.js","./clipboard-legacy-b868140c.js","./vue-ueditor-wrap-legacy-53d1452b.js"],(function(l,e){"use strict";var t,s,u,n,o,a,c,i,r,m,d,p,f,y;return{setters:[function(l){t=l.N,s=l.M,u=l.d,n=l.f,o=l.c},function(l){a=l.o,c=l.c,i=l.a,r=l.X,m=l.P,d=l.S,p=l.a1,f=l.W},function(l){y=l._},null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null],execute:function(){var e={data:function(){return{}},props:["curItem","selectedIndex","opts"],created:function(){this.curItem.style.height=parseInt(this.curItem.style.height)},methods:{}},g={class:"common-form"},h=i("div",{class:"f16 gray3 form-subtitle"},"樣式設定",-1),v={class:"form-item mt10"},j=i("div",{class:"form-label"},"元件高度：",-1),V={class:"form-item"},b=i("div",{class:"form-label"},"上邊距：",-1),I={class:"form-item"},z=i("div",{class:"form-label"},"下邊距：",-1),w={class:"form-item"},x=i("div",{class:"form-label"},"左右邊距：",-1),k={class:"form-item"},U=i("div",{class:"form-label"},"上圓角：",-1),R={class:"form-item"},C=i("div",{class:"form-label"},"下圓角：",-1),_={class:"form-item"},B=i("div",{class:"form-label"},"底部背景：",-1),E={class:"flex-1 d-s-c",style:{height:"36px"}},L={class:"form-item"},S=i("div",{class:"form-label"},"元件背景：",-1),T={class:"flex-1 d-s-c",style:{height:"36px"}};l("default",y(e,[["render",function(l,e,y,$,q,A){var M=t,N=s,P=u,W=n,X=o;return a(),c("div",null,[i("div",g,[i("span",null,r(y.curItem.name),1)]),m(X,{size:"small",model:y.curItem,"label-width":"100px"},{default:d((function(){return[h,i("div",v,[j,m(M,{modelValue:y.curItem.style.height,"onUpdate:modelValue":e[0]||(e[0]=function(l){return y.curItem.style.height=l}),size:"small","show-input":"","show-input-controls":!1,"input-size":"small"},null,8,["modelValue"])]),i("div",V,[b,m(M,{modelValue:y.curItem.style.paddingTop,"onUpdate:modelValue":e[1]||(e[1]=function(l){return y.curItem.style.paddingTop=l}),size:"small","show-input":"","show-input-controls":!1,"input-size":"small"},null,8,["modelValue"])]),i("div",I,[z,m(M,{modelValue:y.curItem.style.paddingBottom,"onUpdate:modelValue":e[2]||(e[2]=function(l){return y.curItem.style.paddingBottom=l}),size:"small","show-input":"","show-input-controls":!1,"input-size":"small"},null,8,["modelValue"])]),i("div",w,[x,m(M,{modelValue:y.curItem.style.paddingLeft,"onUpdate:modelValue":e[3]||(e[3]=function(l){return y.curItem.style.paddingLeft=l}),size:"small","show-input":"","show-input-controls":!1,"input-size":"small"},null,8,["modelValue"])]),i("div",k,[U,m(M,{modelValue:y.curItem.style.topRadio,"onUpdate:modelValue":e[4]||(e[4]=function(l){return y.curItem.style.topRadio=l}),size:"small","show-input":"","show-input-controls":!1,"input-size":"small"},null,8,["modelValue"])]),i("div",R,[C,m(M,{modelValue:y.curItem.style.bottomRadio,"onUpdate:modelValue":e[5]||(e[5]=function(l){return y.curItem.style.bottomRadio=l}),size:"small","show-input":"","show-input-controls":!1,"input-size":"small"},null,8,["modelValue"])]),i("div",_,[B,i("div",E,[m(N,{size:"default",modelValue:y.curItem.style.bgcolor,"onUpdate:modelValue":e[6]||(e[6]=function(l){return y.curItem.style.bgcolor=l})},null,8,["modelValue"]),m(P,{class:"ml10",modelValue:y.curItem.style.bgcolor,"onUpdate:modelValue":e[7]||(e[7]=function(l){return y.curItem.style.bgcolor=l}),placeholder:"透明"},null,8,["modelValue"]),m(W,{style:{"margin-left":"10px"},onClick:e[8]||(e[8]=p((function(e){return l.$parent.onEditorResetColor(y.curItem.style,"bgcolor","#f2f2f2")}),["stop"])),type:"primary",link:""},{default:d((function(){return[f("重置")]})),_:1})])]),i("div",L,[S,i("div",T,[m(N,{size:"default",modelValue:y.curItem.style.background,"onUpdate:modelValue":e[9]||(e[9]=function(l){return y.curItem.style.background=l})},null,8,["modelValue"]),m(P,{class:"ml10",modelValue:y.curItem.style.background,"onUpdate:modelValue":e[10]||(e[10]=function(l){return y.curItem.style.background=l}),placeholder:"透明"},null,8,["modelValue"]),m(W,{style:{"margin-left":"10px"},onClick:e[11]||(e[11]=p((function(e){return l.$parent.onEditorResetColor(y.curItem.style,"background","#ffffff")}),["stop"])),type:"primary",link:""},{default:d((function(){return[f("重置")]})),_:1})])])]})),_:1},8,["model"])])}]]))}}}));
