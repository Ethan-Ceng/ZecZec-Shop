import{E as e,d as l,e as a,m as o,g as t,n as d,c as r,f as s,q as i}from"./element-plus-84a27f94.js";import{G as m}from"./grade-6ceb59c7.js";import{_ as n}from"./index-5ae5860a.js";import{o as p,T as u,S as _,a as f,P as c,W as g,Y as b}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const h={data:()=>({form:{name:"",open_agent_money:0,agent_money:0,open_agent_user:0,agent_user:0,weight:100,first_percent:0,second_percent:0,third_percent:0,auto_upgrade:1,condition_type:"and"},formLabelWidth:"120px",dialogVisible:!1,submit_loading:!1,grade_level:3}),props:["open_add"],created(){this.dialogVisible=this.open_add,this.getData()},methods:{getData(){let e=this;m.toAddgrade({},!0).then((l=>{e.grade_level=parseInt(l.data.basicSetting.level)})).catch((e=>{}))},addGrade(){let l=this,a=this.form;l.$refs.form.validate((o=>{o&&(l.submit_loading=!0,a.open_agent_money=1==a.open_agent_money?1:0,a.open_agent_user=1==a.open_agent_user?1:0,m.addgrade(a,!0).then((a=>{l.submit_loading=!1,e({message:a.msg,type:"success"}),l.dialogFormVisible(!0)})).catch((e=>{l.submit_loading=!1})))}))},dialogFormVisible(e){e?this.$emit("closeDialog",{type:"success",openDialog:!1}):this.$emit("closeDialog",{type:"error",openDialog:!1})}}},V=f("div",{class:"gray9"},"權重越大，等級越高",-1),y=f("div",{class:"gray9"},"在預設分傭比例上上調如果原來是5%，這裡上調3%，那麼一級拿8%佣金",-1),j={class:"d-s-c mt16"},w=f("span",{class:"ml10"},"元",-1),v={class:"d-s-c mt16"},U=f("span",{class:"ml10"},"人",-1),k={class:"dialog-footer"};const x=n(h,[["render",function(e,m,n,h,x,W){const L=l,q=a,C=o,D=t,F=d,z=r,G=s,$=i;return p(),u($,{title:"新增等級",modelValue:x.dialogVisible,"onUpdate:modelValue":m[11]||(m[11]=e=>x.dialogVisible=e),onClose:W.dialogFormVisible,"close-on-click-modal":!1,"close-on-press-escape":!1,width:"600px"},{footer:_((()=>[f("div",k,[c(G,{onClick:W.dialogFormVisible},{default:_((()=>[g("取 消")])),_:1},8,["onClick"]),c(G,{type:"primary",onClick:W.addGrade,disabled:x.submit_loading},{default:_((()=>[g("確 定")])),_:1},8,["onClick","disabled"])])])),default:_((()=>[c(z,{size:"small",model:x.form,ref:"form"},{default:_((()=>[c(q,{label:"等級名稱","label-width":x.formLabelWidth,prop:"name",rules:[{required:!0,message:"請輸入等級名稱"}]},{default:_((()=>[c(L,{modelValue:x.form.name,"onUpdate:modelValue":m[0]||(m[0]=e=>x.form.name=e),placeholder:"請輸入等級名稱"},null,8,["modelValue"])])),_:1},8,["label-width"]),c(q,{label:"等級權重","label-width":x.formLabelWidth,prop:"weight",rules:[{required:!0,message:"請輸入等級權重"}]},{default:_((()=>[c(L,{modelValue:x.form.weight,"onUpdate:modelValue":m[1]||(m[1]=e=>x.form.weight=e),type:"number",placeholder:"請輸入等級權重"},null,8,["modelValue"]),V])),_:1},8,["label-width"]),x.grade_level>=1?(p(),u(q,{key:0,label:"一級上調","label-width":x.formLabelWidth,prop:"first_percent",rules:[{required:!0,message:"請輸入等級權重"}]},{default:_((()=>[c(L,{modelValue:x.form.first_percent,"onUpdate:modelValue":m[2]||(m[2]=e=>x.form.first_percent=e),type:"number",placeholder:"請輸入上調比例"},null,8,["modelValue"]),y])),_:1},8,["label-width"])):b("",!0),x.grade_level>=2?(p(),u(q,{key:1,label:"二級上調","label-width":x.formLabelWidth,prop:"second_percent",rules:[{required:!0,message:"請輸入等級權重"}]},{default:_((()=>[c(L,{modelValue:x.form.second_percent,"onUpdate:modelValue":m[3]||(m[3]=e=>x.form.second_percent=e),type:"number",placeholder:"請輸入上調比例"},null,8,["modelValue"])])),_:1},8,["label-width"])):b("",!0),x.grade_level>=3?(p(),u(q,{key:2,label:"三級上調","label-width":x.formLabelWidth,prop:"third_percent",rules:[{required:!0,message:"請輸入等級權重"}]},{default:_((()=>[c(L,{modelValue:x.form.third_percent,"onUpdate:modelValue":m[4]||(m[4]=e=>x.form.third_percent=e),type:"number",placeholder:"請輸入上調比例"},null,8,["modelValue"])])),_:1},8,["label-width"])):b("",!0),c(q,{label:"自動升級","label-width":x.formLabelWidth,prop:"auto_upgrade"},{default:_((()=>[c(D,{modelValue:x.form.auto_upgrade,"onUpdate:modelValue":m[5]||(m[5]=e=>x.form.auto_upgrade=e)},{default:_((()=>[c(C,{label:1},{default:_((()=>[g("允許自動升級")])),_:1}),c(C,{label:0},{default:_((()=>[g("禁止自動升級")])),_:1})])),_:1},8,["modelValue"])])),_:1},8,["label-width"]),c(q,{label:"升級條件","label-width":x.formLabelWidth},{default:_((()=>[f("div",null,[c(D,{modelValue:x.form.condition_type,"onUpdate:modelValue":m[6]||(m[6]=e=>x.form.condition_type=e)},{default:_((()=>[c(C,{label:"and"},{default:_((()=>[g("滿足以下所有條件")])),_:1}),c(C,{label:"or"},{default:_((()=>[g("滿足以下任意條件")])),_:1})])),_:1},8,["modelValue"])]),f("div",j,[c(F,{modelValue:x.form.open_agent_money,"onUpdate:modelValue":m[7]||(m[7]=e=>x.form.open_agent_money=e)},{default:_((()=>[g("累計佣金")])),_:1},8,["modelValue"]),c(L,{modelValue:x.form.agent_money,"onUpdate:modelValue":m[8]||(m[8]=e=>x.form.agent_money=e),type:"number",disabled:0==x.form.open_agent_money,style:{width:"160px","margin-left":"10px"}},null,8,["modelValue","disabled"]),w]),f("div",v,[c(F,{modelValue:x.form.open_agent_user,"onUpdate:modelValue":m[9]||(m[9]=e=>x.form.open_agent_user=e)},{default:_((()=>[g("直推分銷商")])),_:1},8,["modelValue"]),c(L,{modelValue:x.form.agent_user,"onUpdate:modelValue":m[10]||(m[10]=e=>x.form.agent_user=e),type:"number",disabled:0==x.form.open_agent_user,style:{width:"160px","margin-left":"10px"}},null,8,["modelValue","disabled"]),U])])),_:1},8,["label-width"])])),_:1},8,["model"])])),_:1},8,["modelValue","onClose"])}]]);export{x as default};
