System.register(["./element-plus-legacy-cad8c7ae.js","./region-legacy-75c4d95d.js","./index-legacy-fa2084e1.js","./@vue-legacy-3d9ca20c.js","./lodash-es-legacy-74aa31b9.js","./async-validator-legacy-aa1fd2de.js","./@vueuse-legacy-3a183765.js","./dayjs-legacy-6e85b031.js","./call-bind-legacy-1e89bf8a.js","./get-intrinsic-legacy-bfdfe19a.js","./has-symbols-legacy-afcc0593.js","./function-bind-legacy-b76e1749.js","./has-legacy-06d86c07.js","./@element-plus-legacy-4f22db85.js","./escape-html-legacy-ae962a8c.js","./normalize-wheel-es-legacy-13d39621.js","./@ctrl-legacy-33dbca08.js","./request-legacy-2cc17f9e.js","./axios-legacy-40880ebd.js","./qs-legacy-31069618.js","./side-channel-legacy-27799042.js","./object-inspect-legacy-742bf942.js","./vue-router-legacy-7357262c.js","./pinia-legacy-54e74fbe.js","./vue-demi-legacy-97cfbb01.js"],(function(e,l){"use strict";var n,a,u,o,t,r,i,d,c,m,f,s,p,g,y,V,_,v,h,b,j=document.createElement("style");return j.textContent=".tips{color:#ccc;width:100%}\n",document.head.appendChild(j),{setters:[function(e){n=e.f,a=e.b,u=e.m,o=e.d,t=e.g,r=e.h,i=e.c,d=e.e},function(e){c=e.R},function(e){m=e._},function(e){f=e.c,s=e.O,p=e.R,g=e.o,y=e.a,V=e.V,_=e.S,v=e.P,h=e.a8,b=e.X},null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null],execute:function(){var l={data:function(){return{loading:!1,id:0,form:{province_id:"",city_id:"",level:1},areaList:[]}},created:function(){this.id=this.$route.query.id,this.getData()},methods:{getData:function(){var e=this;c.regionDetail({id:e.id},!0).then((function(l){e.form=l.data.model,e.areaList=l.data.regionData})).catch((function(e){}))},onSubmit:function(){var e=this,l=this.form;e.$refs.form.validate((function(a){a&&(e.loading=!0,c.editRegion(l,!0).then((function(l){e.loading=!1,n({message:"恭喜你，修改成功",type:"success"}),e.$router.push("/region/Index")})).catch((function(l){e.loading=!1})))}))},initCity:function(){this.form.city_id=""}}},j={class:"product-add"},x=y("div",{class:"common-form"},"新增物流公司",-1),U=y("div",{class:"tips"},"數字越小越靠前",-1);e("default",m(l,[["render",function(e,l,n,c,m,w){var k=u,C=o,q=t,z=r,D=i,L=d,S=a;return g(),f("div",j,[s(S,{ref:"form",model:m.form,"label-width":"200px"},{default:p((function(){return[x,s(C,{label:"地區型別"},{default:p((function(){return[y("div",null,[s(k,{modelValue:m.form.level,"onUpdate:modelValue":l[0]||(l[0]=function(e){return m.form.level=e}),label:1},{default:p((function(){return[V("省份")]})),_:1},8,["modelValue"]),s(k,{modelValue:m.form.level,"onUpdate:modelValue":l[1]||(l[1]=function(e){return m.form.level=e}),label:2},{default:p((function(){return[V("城市")]})),_:1},8,["modelValue"]),s(k,{modelValue:m.form.level,"onUpdate:modelValue":l[2]||(l[2]=function(e){return m.form.level=e}),label:3},{default:p((function(){return[V("地區")]})),_:1},8,["modelValue"])])]})),_:1}),m.form.level>1?(g(),_(C,{key:0,label:"選擇上級"},{default:p((function(){return[m.form.level>1?(g(),_(z,{key:0,modelValue:m.form.province_id,"onUpdate:modelValue":l[3]||(l[3]=function(e){return m.form.province_id=e}),placeholder:"省",onChange:w.initCity},{default:p((function(){return[(g(!0),f(v,null,h(m.areaList,(function(e,l){return g(),_(q,{label:e.name,value:e.id,key:l},null,8,["label","value"])})),128))]})),_:1},8,["modelValue","onChange"])):b("",!0),""!=m.form.province_id&&m.form.level>2?(g(),_(z,{key:1,modelValue:m.form.city_id,"onUpdate:modelValue":l[4]||(l[4]=function(e){return m.form.city_id=e}),placeholder:"市"},{default:p((function(){return[(g(!0),f(v,null,h(m.areaList[m.form.province_id].city,(function(e,l){return g(),_(q,{label:e.name,value:e.id,key:l},null,8,["label","value"])})),128))]})),_:1},8,["modelValue"])):b("",!0)]})),_:1})):b("",!0),s(C,{label:"地區名稱 ",prop:"name",rules:[{required:!0,message:" "}]},{default:p((function(){return[s(D,{modelValue:m.form.name,"onUpdate:modelValue":l[5]||(l[5]=function(e){return m.form.name=e}),class:"max-w460"},null,8,["modelValue"])]})),_:1}),s(C,{label:"簡稱",prop:"shortname"},{default:p((function(){return[s(D,{modelValue:m.form.shortname,"onUpdate:modelValue":l[6]||(l[6]=function(e){return m.form.shortname=e}),class:"max-w460"},null,8,["modelValue"])]})),_:1}),s(C,{label:"全稱",prop:"merger_name"},{default:p((function(){return[s(D,{modelValue:m.form.merger_name,"onUpdate:modelValue":l[7]||(l[7]=function(e){return m.form.merger_name=e}),class:"max-w460"},null,8,["modelValue"])]})),_:1}),s(C,{label:"拼音",prop:"pinyin"},{default:p((function(){return[s(D,{modelValue:m.form.pinyin,"onUpdate:modelValue":l[8]||(l[8]=function(e){return m.form.pinyin=e}),class:"max-w460"},null,8,["modelValue"])]})),_:1}),s(C,{label:"郵編",prop:"zip_code"},{default:p((function(){return[s(D,{modelValue:m.form.zip_code,"onUpdate:modelValue":l[9]||(l[9]=function(e){return m.form.zip_code=e}),class:"max-w460"},null,8,["modelValue"])]})),_:1}),s(C,{label:"首字母",prop:"first"},{default:p((function(){return[s(D,{modelValue:m.form.first,"onUpdate:modelValue":l[10]||(l[10]=function(e){return m.form.first=e}),class:"max-w460"},null,8,["modelValue"])]})),_:1}),s(C,{label:"經度",prop:"lng"},{default:p((function(){return[s(D,{modelValue:m.form.lng,"onUpdate:modelValue":l[11]||(l[11]=function(e){return m.form.lng=e}),class:"max-w460"},null,8,["modelValue"])]})),_:1}),s(C,{label:"緯度",prop:"lat"},{default:p((function(){return[s(D,{modelValue:m.form.lat,"onUpdate:modelValue":l[12]||(l[12]=function(e){return m.form.lat=e}),class:"max-w460"},null,8,["modelValue"])]})),_:1}),s(C,{label:"排序"},{default:p((function(){return[s(D,{modelValue:m.form.sort,"onUpdate:modelValue":l[13]||(l[13]=function(e){return m.form.sort=e}),type:"number",class:"max-w460"},null,8,["modelValue"]),U]})),_:1}),s(C,null,{default:p((function(){return[s(L,{type:"primary",onClick:w.onSubmit,loading:m.loading},{default:p((function(){return[V("提交")]})),_:1},8,["onClick","loading"]),s(L,{onClick:l[14]||(l[14]=function(l){return e.$router.back()})},{default:p((function(){return[V("取消")]})),_:1})]})),_:1})]})),_:1},8,["model"])])}]]))}}}));
