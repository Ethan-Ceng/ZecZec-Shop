import{f as e,b as l,m as a,d as o,g as m,h as t,c as d,e as r}from"./element-plus-4e26fc63.js";import{R as i}from"./region-ca2c0c1a.js";import{_ as s}from"./index-bb8b9786.js";import{c as u,O as n,R as p,o as f,a as c,V,S as _,P as b,a8 as h,X as v}from"./@vue-5c89b57d.js";import"./lodash-es-493ac664.js";import"./async-validator-cf877c1f.js";import"./@vueuse-e57ebffb.js";import"./dayjs-342c85a3.js";import"./call-bind-0966096f.js";import"./get-intrinsic-ccd8a43d.js";import"./has-symbols-456daba2.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-9b3bb84c.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./request-58949a77.js";import"./axios-85bcd05e.js";import"./qs-9a0be292.js";import"./side-channel-7d553c0c.js";import"./object-inspect-1e1e9601.js";import"./vue-router-feb6ca35.js";import"./pinia-3964e703.js";import"./vue-demi-71ba0ef2.js";const g={data:()=>({loading:!1,id:0,form:{province_id:"",city_id:"",level:1},areaList:[]}),created(){this.id=this.$route.query.id,this.getData()},methods:{getData(){let e=this;i.regionDetail({id:e.id},!0).then((l=>{e.form=l.data.model,e.areaList=l.data.regionData})).catch((e=>{}))},onSubmit(){let l=this,a=this.form;l.$refs.form.validate((o=>{o&&(l.loading=!0,i.editRegion(a,!0).then((a=>{l.loading=!1,e({message:"恭喜你，修改成功",type:"success"}),l.$router.push("/region/Index")})).catch((e=>{l.loading=!1})))}))},initCity(){this.form.city_id=""}}},j={class:"product-add"},y=c("div",{class:"common-form"},"新增物流公司",-1),x=c("div",{class:"tips"},"數字越小越靠前",-1);const U=s(g,[["render",function(e,i,s,g,U,w){const k=a,C=o,q=m,z=t,D=d,L=r,$=l;return f(),u("div",j,[n($,{ref:"form",model:U.form,"label-width":"200px"},{default:p((()=>[y,n(C,{label:"地區型別"},{default:p((()=>[c("div",null,[n(k,{modelValue:U.form.level,"onUpdate:modelValue":i[0]||(i[0]=e=>U.form.level=e),label:1},{default:p((()=>[V("省份")])),_:1},8,["modelValue"]),n(k,{modelValue:U.form.level,"onUpdate:modelValue":i[1]||(i[1]=e=>U.form.level=e),label:2},{default:p((()=>[V("城市")])),_:1},8,["modelValue"]),n(k,{modelValue:U.form.level,"onUpdate:modelValue":i[2]||(i[2]=e=>U.form.level=e),label:3},{default:p((()=>[V("地區")])),_:1},8,["modelValue"])])])),_:1}),U.form.level>1?(f(),_(C,{key:0,label:"選擇上級"},{default:p((()=>[U.form.level>1?(f(),_(z,{key:0,modelValue:U.form.province_id,"onUpdate:modelValue":i[3]||(i[3]=e=>U.form.province_id=e),placeholder:"省",onChange:w.initCity},{default:p((()=>[(f(!0),u(b,null,h(U.areaList,((e,l)=>(f(),_(q,{label:e.name,value:e.id,key:l},null,8,["label","value"])))),128))])),_:1},8,["modelValue","onChange"])):v("",!0),""!=U.form.province_id&&U.form.level>2?(f(),_(z,{key:1,modelValue:U.form.city_id,"onUpdate:modelValue":i[4]||(i[4]=e=>U.form.city_id=e),placeholder:"市"},{default:p((()=>[(f(!0),u(b,null,h(U.areaList[U.form.province_id].city,((e,l)=>(f(),_(q,{label:e.name,value:e.id,key:l},null,8,["label","value"])))),128))])),_:1},8,["modelValue"])):v("",!0)])),_:1})):v("",!0),n(C,{label:"地區名稱 ",prop:"name",rules:[{required:!0,message:" "}]},{default:p((()=>[n(D,{modelValue:U.form.name,"onUpdate:modelValue":i[5]||(i[5]=e=>U.form.name=e),class:"max-w460"},null,8,["modelValue"])])),_:1}),n(C,{label:"簡稱",prop:"shortname"},{default:p((()=>[n(D,{modelValue:U.form.shortname,"onUpdate:modelValue":i[6]||(i[6]=e=>U.form.shortname=e),class:"max-w460"},null,8,["modelValue"])])),_:1}),n(C,{label:"全稱",prop:"merger_name"},{default:p((()=>[n(D,{modelValue:U.form.merger_name,"onUpdate:modelValue":i[7]||(i[7]=e=>U.form.merger_name=e),class:"max-w460"},null,8,["modelValue"])])),_:1}),n(C,{label:"拼音",prop:"pinyin"},{default:p((()=>[n(D,{modelValue:U.form.pinyin,"onUpdate:modelValue":i[8]||(i[8]=e=>U.form.pinyin=e),class:"max-w460"},null,8,["modelValue"])])),_:1}),n(C,{label:"郵編",prop:"zip_code"},{default:p((()=>[n(D,{modelValue:U.form.zip_code,"onUpdate:modelValue":i[9]||(i[9]=e=>U.form.zip_code=e),class:"max-w460"},null,8,["modelValue"])])),_:1}),n(C,{label:"首字母",prop:"first"},{default:p((()=>[n(D,{modelValue:U.form.first,"onUpdate:modelValue":i[10]||(i[10]=e=>U.form.first=e),class:"max-w460"},null,8,["modelValue"])])),_:1}),n(C,{label:"經度",prop:"lng"},{default:p((()=>[n(D,{modelValue:U.form.lng,"onUpdate:modelValue":i[11]||(i[11]=e=>U.form.lng=e),class:"max-w460"},null,8,["modelValue"])])),_:1}),n(C,{label:"緯度",prop:"lat"},{default:p((()=>[n(D,{modelValue:U.form.lat,"onUpdate:modelValue":i[12]||(i[12]=e=>U.form.lat=e),class:"max-w460"},null,8,["modelValue"])])),_:1}),n(C,{label:"排序"},{default:p((()=>[n(D,{modelValue:U.form.sort,"onUpdate:modelValue":i[13]||(i[13]=e=>U.form.sort=e),type:"number",class:"max-w460"},null,8,["modelValue"]),x])),_:1}),n(C,null,{default:p((()=>[n(L,{type:"primary",onClick:w.onSubmit,loading:U.loading},{default:p((()=>[V("提交")])),_:1},8,["onClick","loading"]),n(L,{onClick:i[14]||(i[14]=l=>e.$router.back())},{default:p((()=>[V("取消")])),_:1})])),_:1})])),_:1},8,["model"])])}]]);export{U as default};
