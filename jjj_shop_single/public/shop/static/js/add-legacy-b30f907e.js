System.register(["./element-plus-legacy-4010b94c.js","./surface-legacy-34b520d2.js","./index-legacy-a1d733aa.js","./@vue-legacy-a5eb5da2.js","./@vueuse-legacy-7e5b8ef2.js","./lodash-es-legacy-93596df5.js","./async-validator-legacy-aa1fd2de.js","./dayjs-legacy-4687536e.js","./call-bind-legacy-b90429f9.js","./get-intrinsic-legacy-4f34f38e.js","./has-symbols-legacy-afcc0593.js","./has-proto-legacy-d280ab1e.js","./function-bind-legacy-b76e1749.js","./has-legacy-06d86c07.js","./@element-plus-legacy-f19c70ad.js","./escape-html-legacy-ae962a8c.js","./normalize-wheel-es-legacy-13d39621.js","./@ctrl-legacy-33dbca08.js","./vue-router-legacy-b3eb717b.js","./pinia-legacy-9406b588.js","./vue-demi-legacy-97cfbb01.js","./axios-legacy-3cda2001.js","./qs-legacy-5fc46ed9.js","./side-channel-legacy-8f83241e.js","./object-inspect-legacy-2e2b0934.js","./vue-clipboard2-legacy-b2d9783e.js","./clipboard-legacy-b868140c.js","./vue-ueditor-wrap-legacy-53d1452b.js"],(function(e,l){"use strict";var n,a,t,u,s,c,o,r,i,m,d,f,p,g=document.createElement("style");return g.textContent=".tips{color:#ccc}\n",document.head.appendChild(g),{setters:[function(e){n=e.E,a=e.d,t=e.e,u=e.f,s=e.c},function(e){c=e.S},function(e){o=e._},function(e){r=e.o,i=e.c,m=e.P,d=e.S,f=e.a,p=e.W},null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null],execute:function(){var l={data:function(){return{loading:!1,form:{template_name:"",template_num:"",sort:1}}},created:function(){},methods:{onSubmit:function(){var e=this,l=this.form;e.$refs.form.validate((function(a){a&&(e.loading=!0,c.addtemplate(l,!0).then((function(l){e.loading=!1,n({message:"恭喜你，新增成功",type:"success"}),e.$router.push("/plus/surface/index")})).catch((function(l){e.loading=!1})))}))},cancelFunc:function(){this.$router.push({path:"/plus/surface/index"})}}},g={class:"product-add"},y=f("div",{class:"common-form"},"新增電子面單",-1),j=f("div",{class:"tips"},"用於快遞100電子面單列印，務必填寫正確",-1),h=f("div",{class:"tips"},"數字越小越靠前",-1),v={class:"common-button-wrapper"};e("default",o(l,[["render",function(e,l,n,c,o,b){var _=a,x=t,V=u,w=s;return r(),i("div",g,[m(w,{size:"small",ref:"form",model:o.form,"label-width":"200px"},{default:d((function(){return[y,m(x,{label:"電子面單模板名稱 ",prop:"template_name",rules:[{required:!0,message:" "}]},{default:d((function(){return[m(_,{modelValue:o.form.template_name,"onUpdate:modelValue":l[0]||(l[0]=function(e){return o.form.template_name=e}),class:"max-w460"},null,8,["modelValue"])]})),_:1}),m(x,{label:"電子面單模板編碼 ",prop:"template_num",rules:[{required:!0,message:" "}]},{default:d((function(){return[m(_,{modelValue:o.form.template_num,"onUpdate:modelValue":l[1]||(l[1]=function(e){return o.form.template_num=e}),class:"max-w460"},null,8,["modelValue"]),j]})),_:1}),m(x,{label:"排序"},{default:d((function(){return[m(_,{modelValue:o.form.sort,"onUpdate:modelValue":l[2]||(l[2]=function(e){return o.form.sort=e}),type:"number",class:"max-w460"},null,8,["modelValue"]),h]})),_:1}),f("div",v,[m(V,{size:"small",type:"info",onClick:b.cancelFunc},{default:d((function(){return[p("取消")]})),_:1},8,["onClick"]),m(V,{type:"primary",onClick:b.onSubmit,loading:o.loading},{default:d((function(){return[p("提交")]})),_:1},8,["onClick","loading"])])]})),_:1},8,["model"])])}]]))}}}));
