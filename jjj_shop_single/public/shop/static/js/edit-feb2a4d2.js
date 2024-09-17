import{_ as e}from"./Specs-58a287cd.js";import{P as t}from"./Product-14aa5af4.js";import{E as o,d as s,e as a,m as d,g as l,J as i,f as r,l as u,i as c,c as p}from"./element-plus-84a27f94.js";import{B as m}from"./buy-170f6d73.js";import{_ as n,f as _}from"./index-5ae5860a.js";import{o as f,c as h,a as y,P as x,S as b,W as g,T as D,Y as k,X as j}from"./@vue-8fe4574d.js";import"./product-6ff3546d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const P={components:{Product:t,Specs:e},data:()=>({form:{buy_id:0,name:"",status:1,start_time:"",end_time:"",sort:"",limit_product:[],product_ids:[],send_type:"",max_times:""},tableData:[],prodcutData:[],limitProdcutData:[],loading:!1,isproduct:!1,exclude_ids:[],islimitproduct:!1,formLabelWidth:"120px",curProduct:{product_id:0},SpecExcludeIds:[],isspecs:!1,limit_exclude_ids:[]}),created(){this.getData()},methods:{checkTimes(){this.form.max_times=Math.max(1,parseInt(this.form.max_times))},deleteClick(e,t){let o=this.prodcutData[e];o.spec_sku_id=0,o.product_attr=""},addProduct(){this.exclude_ids=[],this.prodcutData.length>0?this.prodcutData.forEach(((e,t)=>{this.exclude_ids=this.exclude_ids.concat(e.product_id),t==this.prodcutData.length-1&&(this.isproduct=!0)})):this.isproduct=!0},closeProductFunc(e){let t=this;t.isproduct=e.openDialog,"success"==e.type&&t.prodcutData.push({product_id:e.params.product_id,product_name:e.params.product_name,product_num:1,product_attr:"",spec_type:e.params.spec_type,spec_sku_id:0})},getData(){let e=this,t=e.$route.query.buy_id;m.detailBuy({buy_id:t},!0).then((t=>{e.form=_(e.form,t.data.model),e.form.product_ids=[],e.prodcutData=t.data.model.product_ids,e.limitProdcutData=t.data.model.limit_product,e.form.start_time=t.data.model.start_time_text,e.form.end_time=t.data.model.end_time_text})).catch((e=>{}))},delProduct(e){let t=this.prodcutData.indexOf(e);this.prodcutData.splice(t,1)},onSubmit(){let e=this;if(!e.checkGroup())return void o.error("未選擇規格");let t=e.form;t.product_ids=e.prodcutData,t.product_ids=JSON.stringify(t.product_ids),t.limit_product=e.limitProdcutData,e.$refs.form.validate((s=>{s&&(e.loading=!0,m.editBuy(t,!0).then((t=>{e.loading=!1,1==t.code?(o({message:t.msg,type:"success"}),e.$router.push("/plus/buyactivity/index")):e.loading=!1})).catch((t=>{e.loading=!1})))}))},checkGroup(){if(0==this.form.is_product)return!0;let e=!0;return this.prodcutData.forEach(((t,o)=>{20!=t.spec_type||t.spec_sku_id||(e=!1)})),e},gotoBack(){this.$router.back(-1)},addLimitProduct(){this.limit_exclude_ids=[],this.limitProdcutData.length>0?this.limitProdcutData.forEach(((e,t)=>{this.limit_exclude_ids=this.limit_exclude_ids.concat(1*e.product_id),t==this.limitProdcutData.length-1&&(this.islimitproduct=!0)})):this.islimitproduct=!0},closeLimitProductFunc(e){let t=this;t.islimitproduct=e.openDialog,"success"==e.type&&t.limitProdcutData.push({product_id:e.params.product_id,product_name:e.params.product_name,product_num:1})},delLimitProduct(e){let t=this.limitProdcutData.indexOf(e);this.limitProdcutData.splice(t,1)},specsFunc(e,t,o){this.curProduct=t,this.curIndex=e,this.specType=!!o,this.isspecs=!0},closeSpecs(e){this.isspecs=!1,e&&"success"==e.type&&(this.prodcutData[this.curIndex].spec_sku_id=e.params.spec_sku_id,this.prodcutData[this.curIndex].product_sku_id=e.params.product_sku_id,this.prodcutData[this.curIndex].product_attr=e.params.spec_name)}}},V={class:"user"},v=y("div",{class:"common-form"},"買送設定",-1),C={class:"product-content"},w=y("span",null,"-",-1),I=y("div",{class:"gray9 f12"},"單次為達到贈送條件只贈送一次；倍數贈送為買2件A商品送1件B商品，買4件A商品送2件B商品",-1),S=y("div",{class:"gray9 f12"},"贈送型別為倍數贈送時的最大贈送倍數，最小數值為1",-1),U=y("div",{class:"tips"},"值越小越靠前",-1),q=y("div",{class:"common-form"},"購買商品",-1),Y=y("div",{class:"gray9 f12"},"滿足任一條件贈送",-1),B=y("div",{class:"common-form"},"贈送商品",-1),L=y("div",{class:"gray9 f12"},"活動商品（拼團秒殺砍價等...）不參與贈送",-1),E={key:0},F={key:1,rules:[{required:!0,message:" "}]},$={key:2},z={class:"common-button-wrapper"};const M=n(P,[["render",function(o,m,n,_,P,M){const H=s,T=a,A=d,J=l,O=i,G=r,W=u,N=c,R=p,X=t,K=e;return f(),h("div",V,[v,y("div",C,[x(R,{ref:"form",model:P.form,rules:o.formRules,"label-width":"150px"},{default:b((()=>[x(T,{label:"名稱",prop:"name",rules:[{required:!0,message:" "}]},{default:b((()=>[x(H,{type:"text",modelValue:P.form.name,"onUpdate:modelValue":m[0]||(m[0]=e=>P.form.name=e),placeholder:o.請輸入活動名稱,class:"max-w460"},null,8,["modelValue","placeholder"])])),_:1}),x(T,{label:"活動狀態"},{default:b((()=>[x(J,{modelValue:P.form.status,"onUpdate:modelValue":m[1]||(m[1]=e=>P.form.status=e)},{default:b((()=>[x(A,{label:1},{default:b((()=>[g("開啟")])),_:1}),x(A,{label:0},{default:b((()=>[g("關閉")])),_:1})])),_:1},8,["modelValue"])])),_:1}),x(T,{label:"活動時間",rules:[{required:!0,message:" "}],prop:"start_time"},{default:b((()=>[x(O,{modelValue:P.form.start_time,"onUpdate:modelValue":m[2]||(m[2]=e=>P.form.start_time=e),type:"datetime","value-format":"YYYY-MM-DD HH:mm:ss",placeholder:"選擇開始日期"},null,8,["modelValue"]),w,x(O,{modelValue:P.form.end_time,"onUpdate:modelValue":m[3]||(m[3]=e=>P.form.end_time=e),type:"datetime","value-format":"YYYY-MM-DD HH:mm:ss",placeholder:"選擇結束日期"},null,8,["modelValue"])])),_:1}),x(T,{label:"贈送型別"},{default:b((()=>[x(J,{modelValue:P.form.send_type,"onUpdate:modelValue":m[4]||(m[4]=e=>P.form.send_type=e)},{default:b((()=>[x(A,{label:10},{default:b((()=>[g("單次")])),_:1}),x(A,{label:20},{default:b((()=>[g("倍數贈送")])),_:1})])),_:1},8,["modelValue"]),I])),_:1}),20==P.form.send_type?(f(),D(T,{key:0,label:"最大贈送倍數",prop:"max_times",rules:[{required:!0,message:" "}]},{default:b((()=>[x(H,{type:"number",min:"1",modelValue:P.form.max_times,"onUpdate:modelValue":m[5]||(m[5]=e=>P.form.max_times=e),placeholder:o.請輸入最大贈送倍數,class:"max-w460",onInput:m[6]||(m[6]=e=>M.checkTimes())},null,8,["modelValue","placeholder"]),S])),_:1})):k("",!0),x(T,{label:"排序",prop:"sort",rules:[{required:!0,message:" "}]},{default:b((()=>[x(H,{type:"text",modelValue:P.form.sort,"onUpdate:modelValue":m[7]||(m[7]=e=>P.form.sort=e),placeholder:"請輸入排序",class:"max-w460"},null,8,["modelValue"]),U])),_:1}),q,x(T,{label:""},{default:b((()=>[x(G,{style:{"margin-bottom":"10px"},type:"primary",onClick:m[8]||(m[8]=e=>M.addLimitProduct())},{default:b((()=>[g("新增購買商品")])),_:1}),Y,x(N,{data:P.limitProdcutData,style:{width:"60%"}},{default:b((()=>[x(W,{prop:"product_id",label:"商品id"}),x(W,{prop:"product_name",label:"商品名稱"}),x(W,{prop:"product_num",label:"數量",rules:[{required:!0,message:" "}]},{default:b((e=>[x(H,{type:"number",modelValue:e.row.product_num,"onUpdate:modelValue":t=>e.row.product_num=t,placeholder:"",min:"1"},null,8,["modelValue","onUpdate:modelValue"])])),_:1}),x(W,{label:"操作"},{default:b((e=>[x(G,{type:"text",size:"small",onClick:t=>M.delLimitProduct(e.row)},{default:b((()=>[g("刪除 ")])),_:2},1032,["onClick"])])),_:1})])),_:1},8,["data"])])),_:1}),B,x(T,{label:""},{default:b((()=>[x(G,{type:"primary",onClick:m[9]||(m[9]=e=>M.addProduct())},{default:b((()=>[g("新增贈送商品")])),_:1}),L,x(N,{data:P.prodcutData,style:{width:"60%"}},{default:b((()=>[x(W,{prop:"product_id",label:"商品id"}),x(W,{prop:"product_name",label:"商品名稱"}),x(W,{prop:"product_num",label:"規格"},{default:b((e=>[20==e.row.spec_type&&e.row.spec_sku_id?(f(),h("span",E,[g(j(e.row.product_attr)+" ",1),x(G,{icon:"CloseBold",text:"",onClick:t=>M.deleteClick(e.$index)},null,8,["onClick"])])):k("",!0),20!=e.row.spec_type||e.row.spec_sku_id?k("",!0):(f(),h("span",F,[x(G,{size:"small",icon:"Plus",onClick:t=>M.specsFunc(e.$index,e.row)},{default:b((()=>[g("選擇規格 ")])),_:2},1032,["onClick"])])),10==e.row.spec_type?(f(),h("span",$,"單規格")):k("",!0)])),_:1}),x(W,{prop:"product_num",label:"數量",rules:[{required:!0,message:" "}]},{default:b((e=>[x(H,{type:"number",modelValue:e.row.product_num,"onUpdate:modelValue":t=>e.row.product_num=t,placeholder:"",min:"1"},null,8,["modelValue","onUpdate:modelValue"])])),_:1}),x(W,{prop:"product_",label:"操作"},{default:b((e=>[x(G,{type:"text",size:"small",onClick:t=>M.delProduct(e.row)},{default:b((()=>[g(" 刪除 ")])),_:2},1032,["onClick"])])),_:1})])),_:1},8,["data"])])),_:1})])),_:1},8,["model","rules"]),y("div",z,[x(G,{type:"info",onClick:M.gotoBack},{default:b((()=>[g("返回")])),_:1},8,["onClick"]),x(G,{type:"primary",onClick:M.onSubmit,loading:P.loading},{default:b((()=>[g("提交")])),_:1},8,["onClick","loading"])])]),x(X,{isproduct:P.isproduct,excludeIds:P.exclude_ids,islist:!1,onCloseDialog:M.closeProductFunc},null,8,["isproduct","excludeIds","onCloseDialog"]),x(X,{isproduct:P.islimitproduct,islist:!1,excludeIds:P.limit_exclude_ids,onCloseDialog:M.closeLimitProductFunc},null,8,["isproduct","excludeIds","onCloseDialog"]),x(K,{isspecs:P.isspecs,productId:P.curProduct.product_id,excludeIds:P.SpecExcludeIds,islist:!1,onClose:M.closeSpecs},null,8,["isspecs","productId","excludeIds","onClose"])])}]]);export{M as default};
