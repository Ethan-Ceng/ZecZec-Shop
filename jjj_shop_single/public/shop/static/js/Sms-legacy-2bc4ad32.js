System.register(["./element-plus-legacy-4010b94c.js","./message-legacy-9d4f16ba.js","./index-legacy-a1d733aa.js","./@vue-legacy-a5eb5da2.js","./@vueuse-legacy-7e5b8ef2.js","./lodash-es-legacy-93596df5.js","./async-validator-legacy-aa1fd2de.js","./dayjs-legacy-4687536e.js","./call-bind-legacy-b90429f9.js","./get-intrinsic-legacy-4f34f38e.js","./has-symbols-legacy-afcc0593.js","./has-proto-legacy-d280ab1e.js","./function-bind-legacy-b76e1749.js","./has-legacy-06d86c07.js","./@element-plus-legacy-f19c70ad.js","./escape-html-legacy-ae962a8c.js","./normalize-wheel-es-legacy-13d39621.js","./@ctrl-legacy-33dbca08.js","./vue-router-legacy-b3eb717b.js","./pinia-legacy-9406b588.js","./vue-demi-legacy-97cfbb01.js","./axios-legacy-3cda2001.js","./qs-legacy-5fc46ed9.js","./side-channel-legacy-8f83241e.js","./object-inspect-legacy-2e2b0934.js","./vue-clipboard2-legacy-b2d9783e.js","./clipboard-legacy-b868140c.js","./vue-ueditor-wrap-legacy-53d1452b.js"],(function(e,l){"use strict";var t,n,a,i,s,o,d,u,c,r,g,f,m,_,p,h,y,v,j;return{setters:[function(e){t=e.E,n=e.d,a=e.e,i=e.l,s=e.i,o=e.c,d=e.f,u=e.q,c=e.v},function(e){r=e.M},function(e){g=e._},function(e){f=e.o,m=e.T,_=e.S,p=e.a,h=e.P,y=e.W,v=e.$,j=e.X},null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null],execute:function(){var l={data:function(){return{formLabelWidth:"120px",dialogVisible:!1,loading:!1,isupload:!1,fieldList:[],title:"設定簡訊模板",checkList:[],settings:{},template_id:""}},props:["open_sms","messageModel"],created:function(){this.dialogVisible=this.open_sms,this.title=this.title+"("+this.messageModel.message_name+")",this.getData()},methods:{getData:function(){var e=this;e.loading=!0,r.fieldList({message_id:e.messageModel.message_id,message_type:"sms"},!0).then((function(l){l.data.list.forEach((function(e){e.field_new_ename=e.field_ename,e.filed_new_value=e.filed_value})),e.fieldList=l.data.list,null==l.data.settings||0==l.data.settings.length?(e.settings={},e.template_id=""):(e.settings=l.data.settings,e.template_id=l.data.settings.template_id),e.loading=!1,e.$nextTick((function(){e.initChecked()}))})).catch((function(e){}))},saveTemplate:function(){var e=this;e.loading=!0,r.saveSettings({fieldList:e.checkList,message_id:e.messageModel.message_id,message_type:"sms",template_id:e.template_id}).then((function(l){e.loading=!1,t({message:"儲存成功",type:"success"}),e.dialogFormVisible(!0)})).catch((function(l){e.loading=!1}))},dialogFormVisible:function(e){e?this.$emit("closeDialog",{type:"success",openDialog:!1}):this.$emit("closeDialog",{type:"error",openDialog:!1})},handleSelectionChange:function(e){this.checkList=e},initChecked:function(){var e=this;"{}"!=JSON.stringify(e.settings)&&Object.keys(e.settings.var_data).forEach((function(l){e.fieldList.forEach((function(t){t.field_ename==l&&(e.$refs.fieldTable.toggleRowSelection(t,!0),t.field_new_ename=e.settings.var_data[l].field_name,t.filed_new_value=e.settings.var_data[l].filed_value)}))}))}}},b={class:"table-wrap"},w=p("div",{class:"operation-wrap"},[p("p",null," 配置說明："),p("p",null," 1、簡訊模板裡有的欄位才勾選，如果沒有請勿勾選。"),p("p",null," 2、模板變數替換成簡訊模板裡的欄位。")],-1),V=["textContent"],C={class:"dialog-footer"};e("default",g(l,[["render",function(e,l,t,r,g,k){var L=n,x=a,S=i,M=s,z=o,D=d,T=u,U=c;return f(),m(T,{title:g.title,modelValue:g.dialogVisible,"onUpdate:modelValue":l[1]||(l[1]=function(e){return g.dialogVisible=e}),onClose:k.dialogFormVisible,"close-on-click-modal":!1,"close-on-press-escape":!1,width:"600px"},{footer:_((function(){return[p("div",C,[h(D,{onClick:k.dialogFormVisible},{default:_((function(){return[y("取 消")]})),_:1},8,["onClick"]),h(D,{type:"primary",onClick:k.saveTemplate,loading:g.loading},{default:_((function(){return[y("確 定")]})),_:1},8,["onClick","loading"])])]})),default:_((function(){return[h(z,{size:"small"},{default:_((function(){return[p("div",b,[w,p("div",null,[h(x,{label:"模板id："},{default:_((function(){return[h(L,{size:"small",class:"max-w460",modelValue:g.template_id,"onUpdate:modelValue":l[0]||(l[0]=function(e){return g.template_id=e}),modelModifiers:{trim:!0},placeholder:"請填寫申請的簡訊模板code"},null,8,["modelValue"])]})),_:1})]),v((f(),m(M,{border:"",ref:"fieldTable",data:g.fieldList,onSelectionChange:k.handleSelectionChange},{default:_((function(){return[h(S,{type:"selection",width:"55"}),h(S,{label:"欄位名稱"},{default:_((function(e){return[p("label",{textContent:j(e.row.field_name)},null,8,V)]})),_:1}),h(S,{label:"模板變數名"},{default:_((function(e){return[h(L,{size:"small",prop:"field_new_ename",modelValue:e.row.field_new_ename,"onUpdate:modelValue":function(l){return e.row.field_new_ename=l},modelModifiers:{trim:!0}},null,8,["modelValue","onUpdate:modelValue"])]})),_:1}),h(S,{label:"模板內容"},{default:_((function(e){return[h(L,{size:"small",prop:"filed_new_value",disabled:1===e.row.is_var,modelValue:e.row.filed_new_value,"onUpdate:modelValue":function(l){return e.row.filed_new_value=l},modelModifiers:{trim:!0}},null,8,["disabled","modelValue","onUpdate:modelValue"])]})),_:1})]})),_:1},8,["data","onSelectionChange"])),[[U,g.loading]])])]})),_:1})]})),_:1},8,["title","modelValue","onClose"])}]]))}}}));
