System.register(["./element-plus-legacy-4010b94c.js","./setting-legacy-cd098a7e.js","./Area-legacy-f1e8a1e2.js","./index-legacy-a1d733aa.js","./@vue-legacy-a5eb5da2.js","./@vueuse-legacy-7e5b8ef2.js","./lodash-es-legacy-93596df5.js","./async-validator-legacy-aa1fd2de.js","./dayjs-legacy-4687536e.js","./call-bind-legacy-b90429f9.js","./get-intrinsic-legacy-4f34f38e.js","./has-symbols-legacy-afcc0593.js","./has-proto-legacy-d280ab1e.js","./function-bind-legacy-b76e1749.js","./has-legacy-06d86c07.js","./@element-plus-legacy-f19c70ad.js","./escape-html-legacy-ae962a8c.js","./normalize-wheel-es-legacy-13d39621.js","./@ctrl-legacy-33dbca08.js","./vue-router-legacy-b3eb717b.js","./pinia-legacy-9406b588.js","./vue-demi-legacy-97cfbb01.js","./axios-legacy-3cda2001.js","./qs-legacy-5fc46ed9.js","./side-channel-legacy-8f83241e.js","./object-inspect-legacy-2e2b0934.js","./vue-clipboard2-legacy-b2d9783e.js","./clipboard-legacy-b868140c.js","./vue-ueditor-wrap-legacy-53d1452b.js"],(function(e,a){"use strict";var t,l,n,r,i,o,u,d,s,c,f,m,h,p,y,g,v,_,w,b,j,x,k,V,C,M,A=document.createElement("style");return A.textContent=".el-table__header-wrapper[data-v-3c54159f]{line-height:23px}.el-table .area-list[data-v-3c54159f]{font-size:12px}.el-table .area-list .province[data-v-3c54159f]{padding-right:10px;font-weight:700}.el-table .area-list .city[data-v-3c54159f]{display:inline-block;white-space:nowrap;padding-right:4px;color:#999}\n",document.head.appendChild(A),{setters:[function(e){t=e.E,l=e.d,n=e.e,r=e.m,i=e.l,o=e.i,u=e.f,d=e.c},function(e){s=e.S},function(e){c=e.default},function(e){f=e._,m=e.d},function(e){h=e.ae,p=e.o,y=e.c,g=e.P,v=e.S,_=e.a,w=e.W,b=e.Q,j=e.a9,x=e.X,k=e.Y,V=e.T,C=e.bb,M=e.b9},null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null],execute:function(){var a={components:{Areas:c},data:function(){return{form:{delivery_id:0,method:"10",name:"",sort:1,radio:"10",rule:[]},formRules:{name:[{required:!0,message:" ",trigger:"blur"}]},loading:!1,options:[],optionsMap:{},areaModel:{index:0},show_area:!1,tableHeadName:{first:"首件(個)",first_fee:"運費(元)",additional:"續件(個)",additional_fee:"續費(元)"},cityCount:0,formData:[]}},watch:{"form.radio":function(e,a){this.tableHeadName="10"==e?{first:"首件(個)",first_fee:"運費(元)",additional:"續件(個)",additional_fee:"續費(元)"}:{first:"首重(Kg)",first_fee:"運費(元)",additional:"續重(Kg)",additional_fee:"續費(元)"}},"form.rule":{handler:function(e,a){},deep:!0}},created:function(){this.delivery_id=this.$route.query.delivery_id,this.getData()},methods:{getData:function(){var e=this;s.deliveryDetail({delivery_id:e.delivery_id},!0).then((function(a){if(e.delivery_id>0&&(e.form=a.data.detail,e.form.rule=[],e.form.radio=e.form.method.value.toString(),e.formData=a.data.formData),e.options=a.data.arr,e.cityCount=a.data.cityCount,e.options.forEach((function(a){e.optionsMap[a.value]=a})),!e.formData.length)return!1;e.formData.forEach((function(a){for(var t in a.citys)a.citys.hasOwnProperty(t)&&(a.citys[t]=parseInt(a.citys[t]));a.showData=e.getShowData({province:a.province,citys:a.citys}),e.form.rule.push(a)}))})).catch((function(e){}))},getShowData:function(e){var a=this,t={};return e.province.forEach((function(l){var n=a.optionsMap[l],r=[],i=0;for(var o in n.children)if(n.children.hasOwnProperty(o)){var u=n.children[o];a.inArray(u.value,e.citys)&&r.push({id:u.value,name:u.label}),i++}t[n.value]={id:n.value,name:n.label,citys:r,isAllCitys:r.length===i}})),t},inArray:function(e,a){return a.indexOf(e)>-1},addAreaClick:function(e){this.areaModel.index=this.form.rule.length,this.areaModel.type="add",this.show_area=!0},editAreaClick:function(e){this.areaModel.index=e,this.areaModel.type="edit",this.show_area=!0},closeAreaFunc:function(e){var a=this;if(this.show_area=e.statu,"confirm"==e.type){this.options=e.this_area;var t=[],l=[];e.this_area.forEach((function(e,n){1!=e.checked&&1!=e.indeterminate||-1==e.index.indexOf(a.areaModel.index)||(t.push(e.value),e.children.forEach((function(e){1==e.checked&&e.index==a.areaModel.index&&l.push(e.value)})))})),""==t&&""==l||("add"==this.areaModel.type&&this.form.rule.push({first:"",first_fee:"",additional:"",additional_fee:"",citys:l,showData:this.getShowData({province:t,citys:l})}),"edit"==this.areaModel.type&&(this.form.rule[this.areaModel.index].showData=this.getShowData({province:t,citys:l}),this.form.rule[this.areaModel.index].citys=l),this.form.rule.push([]),this.form.rule.pop())}},deleteAreaClick:function(e){var a=this;a.options.forEach((function(t){a.form.rule[e].province&&a.inArray(t.value,a.form.rule[e].province)&&(t.checked=!1,t.index=null),t.children.forEach((function(t){a.inArray(t.value,a.form.rule[e].citys)&&(t.checked=!1,t.index=null)}))})),a.form.rule.splice(e,1)},onSubmit:function(){var e=this,a=m(e.form);a.rule.forEach((function(e){delete e.showData})),e.$refs.form.validate((function(l){if(l){if(0==a.rule.length)return t({message:"請新增配送區域和運費",type:"error"}),!1;for(var n in a.rule){if(!(a.rule[n].hasOwnProperty("first")&&a.rule[n].first>0))return t({message:"首件/首重不能為空",type:"error"}),!1;if(!a.rule[n].hasOwnProperty("first_fee")||""==a.rule[n].first_fee)return t({message:"運費不能為空",type:"error"}),!1;if(!(a.rule[n].hasOwnProperty("additional")&&a.rule[n].additional>0))return t({message:"續件/續重不能為空",type:"error"}),!1;if(!a.rule[n].hasOwnProperty("additional_fee")||""==a.rule[n].additional_fee)return t({message:"續費不能為空",type:"error"}),!1}e.loading=!0,s.editDelivery(a,!0).then((function(a){e.loading=!1,t({message:"恭喜你，修改成功",type:"success"}),e.$router.push("/setting/delivery/index")})).catch((function(a){e.loading=!1}))}}))},cancelFunc:function(){this.$router.back(-1)}}},A=function(e){return C("data-v-3c54159f"),e=e(),M(),e},D={class:"product-add"},U=A((function(){return _("div",{class:"common-form"},"修改運費模版",-1)})),E={class:"area-list"},S={key:0},O={class:"am-link-muted gray9"},P={key:0},z=["onClick"],H=["onClick"],N=A((function(){return _("div",{class:"tips"},"數字越小越靠前",-1)})),$={class:"common-button-wrapper"};e("default",f(a,[["render",function(e,a,t,s,c,f){var m=l,C=n,M=r,A=i,F=o,q=u,I=d,K=h("Areas");return p(),y("div",D,[g(I,{size:"small",ref:"form",model:c.form,rules:c.formRules,"label-width":"200px"},{default:v((function(){return[U,g(C,{label:"模版名稱",prop:"name"},{default:v((function(){return[g(m,{modelValue:c.form.name,"onUpdate:modelValue":a[0]||(a[0]=function(e){return c.form.name=e}),modelModifiers:{trim:!0},class:"max-w460"},null,8,["modelValue"])]})),_:1}),g(C,{label:"計費方式"},{default:v((function(){return[_("div",null,[g(M,{modelValue:c.form.radio,"onUpdate:modelValue":a[1]||(a[1]=function(e){return c.form.radio=e}),label:"10"},{default:v((function(){return[w("按件數")]})),_:1},8,["modelValue"]),g(M,{modelValue:c.form.radio,"onUpdate:modelValue":a[2]||(a[2]=function(e){return c.form.radio=e}),label:"20"},{default:v((function(){return[w("按重量")]})),_:1},8,["modelValue"])])]})),_:1}),g(C,{label:"配送區域及運費"},{default:v((function(){return[g(F,{data:c.form.rule,border:"",style:{width:"100%"}},{default:v((function(){return[g(A,{label:"可配送區域"},{default:v((function(a){return[_("div",E,[a.row.citys.length==c.cityCount?(p(),y("span",S,"全國")):(p(!0),y(b,{key:1},j(a.row.showData,(function(e,a){return p(),y("span",{key:a,class:"pr16"},[w(x(e.name)+" ",1),e.isAllCitys?k("",!0):(p(),y(b,{key:0},[w(" ( "),_("span",O,[(p(!0),y(b,null,j(e.citys,(function(a,t){return p(),y("em",{key:t},[_("span",null,x(a.name),1),t+1<e.citys.length?(p(),y("span",P,"、")):k("",!0)])})),128))]),w(" ) ")],64))])})),128))]),_("a",{href:"javascript:void(0);",onClick:function(t){return e.el=f.editAreaClick(a.$index)}},"編輯",8,z)]})),_:1}),g(A,{prop:"first",label:c.tableHeadName.first,width:"100px"},{default:v((function(e){return[g(m,{modelValue:e.row.first,"onUpdate:modelValue":function(a){return e.row.first=a},modelModifiers:{trim:!0}},null,8,["modelValue","onUpdate:modelValue"])]})),_:1},8,["label"]),g(A,{prop:"first_fee",label:c.tableHeadName.first_fee,width:"100px"},{default:v((function(e){return[g(m,{modelValue:e.row.first_fee,"onUpdate:modelValue":function(a){return e.row.first_fee=a},modelModifiers:{trim:!0}},null,8,["modelValue","onUpdate:modelValue"])]})),_:1},8,["label"]),g(A,{prop:"additional",label:c.tableHeadName.additional,width:"100px"},{default:v((function(e){return[g(m,{modelValue:e.row.additional,"onUpdate:modelValue":function(a){return e.row.additional=a},modelModifiers:{trim:!0}},null,8,["modelValue","onUpdate:modelValue"])]})),_:1},8,["label"]),g(A,{prop:"additional_fee",label:c.tableHeadName.additional_fee,width:"100px"},{default:v((function(e){return[g(m,{modelValue:e.row.additional_fee,"onUpdate:modelValue":function(a){return e.row.additional_fee=a},modelModifiers:{trim:!0}},null,8,["modelValue","onUpdate:modelValue"])]})),_:1},8,["label"]),g(A,{label:"操作",width:"60px"},{default:v((function(e){return[_("a",{href:"javascript:void(0);",onClick:function(a){return f.deleteAreaClick(e.$index)}},"刪除",8,H)]})),_:1})]})),_:1},8,["data"]),_("div",null,[g(q,{onClick:f.addAreaClick,type:"text",size:"small"},{default:v((function(){return[w("+點選新增可配送區域和運費")]})),_:1},8,["onClick"])])]})),_:1}),g(C,{label:"排序"},{default:v((function(){return[g(m,{modelValue:c.form.sort,"onUpdate:modelValue":a[3]||(a[3]=function(e){return c.form.sort=e}),modelModifiers:{trim:!0},type:"number",class:"max-w460"},null,8,["modelValue"]),N]})),_:1}),_("div",$,[g(q,{size:"small",type:"info",onClick:f.cancelFunc},{default:v((function(){return[w("取消")]})),_:1},8,["onClick"]),g(q,{type:"primary",onClick:f.onSubmit,loading:c.loading},{default:v((function(){return[w("提交")]})),_:1},8,["onClick","loading"])])]})),_:1},8,["model","rules"]),c.show_area?(p(),V(K,{key:0,show_area:c.show_area,areaModel:c.areaModel,areas:c.options,onCloseArea:f.closeAreaFunc},null,8,["show_area","areaModel","areas","onCloseArea"])):k("",!0)])}],["__scopeId","data-v-3c54159f"]]))}}}));
