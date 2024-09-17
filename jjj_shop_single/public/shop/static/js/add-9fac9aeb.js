import{P as e}from"./Product-14aa5af4.js";import{_ as o}from"./GetCoupon-4aa1d1f3.js";import{_ as a}from"./Upload-71ca9325.js";import{E as l,d as t,e as r,f as d,C as s,J as u,n as m,l as p,i,m as n,g as c,s as f,t as _,c as g}from"./element-plus-84a27f94.js";import{U as h}from"./user-4135dd62.js";import{P as b}from"./package-2ce13fbc.js";import{_ as y}from"./index-5ae5860a.js";import{o as V,c as j,a as v,P as k,S as x,W as U,Y as w,T as C,Q as P,a9 as D}from"./@vue-8fe4574d.js";import"./product-6ff3546d.js";import"./coupon-7cc893e2.js";import"./file-b7a04c7e.js";/* empty css                                                               */import"./AddCategory-69aab672.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const q={components:{GetCoupon:o,Product:e,Upload:a},data(){return{validatePass:(e,o,a)=>{""===o?a(new Error("請輸入選購數量")):o>this.prodcutData.length?a(new Error("選購數量必須小於商品數量")):a()},form:{is_coupon:!1,is_product:!1,coupon:[],product:[],coupon_num:1,is_point:!1,point:0,is_times:0,times:0,is_grade:0,grade_ids:"",value1:[],name:"",product_name:"",total_num:"",image_id:"",product_num:"",code_type:10},Grade:{},prodcutData:[],loading:!1,open_add:!1,isproduct:!1,exclude_ids:[],isupload:!1,formRules:{image_id:[{required:!0,message:"請上傳背景圖",trigger:"blur"}]},formLabelWidth:"120px"}},created(){this.getGradeList()},methods:{getGradeList(){let e=this,o={};o.page=e.curPage,o.list_rows=e.pageSize,h.gradelist(o,!0).then((o=>{e.Grade=o.data.list.data})).catch((e=>{}))},addCoupon(){this.open_add=!0},addProduct(){this.isproduct=!0},closeProductDialogFunc(e){let o=this;if(o.open_add=e.openDialog,"success"==e.type)if(o.form.coupon.length<1)o.form.coupon.push({coupon_id:e.params.coupon_id,coupon_num:1,name:e.params.name});else{let a=!0;o.form.coupon.forEach(((o,l)=>{o.coupon_id==e.params.coupon_id&&(a=!1)})),a?o.form.coupon.push({coupon_id:e.params.coupon_id,coupon_num:1,name:e.params.name}):l.error("請勿重複新增")}},closeProductFunc(e){let o=this;o.isproduct=e.openDialog,"success"==e.type&&(o.form.product.push(e.params.product_id),o.prodcutData.push(e.params))},delProduct(e){let o=this,a=o.prodcutData.indexOf(e);o.prodcutData.splice(a,1),o.form.product.splice(a,1)},delcoupon(e){let o=this.form.coupon.indexOf(e);this.form.coupon.splice(o,1)},onSubmit(){let e=this,o=e.form;o.coupon=e.form.coupon||"",e.form.is_coupon||e.form.is_product||e.form.is_point?e.form.is_coupon&&e.form.coupon.length<=0?l.error("請至少設定一個優惠券"):e.form.is_product&&e.form.product.length<=0?l.error("請至少設定一個一個商品"):e.form.is_point&&e.form.point<=0?l.error("設定積分不能為0"):e.$refs.form.validate((a=>{a&&(e.loading=!0,b.savePackage(o,!0).then((o=>{e.loading=!1,1==o.code?(l({message:o.msg,type:"success"}),e.$router.push("/plus/package/index")):e.loading=!1})).catch((o=>{e.loading=!1})))})):l.error("請至少設定一個禮包型別")},openUpload(e){this.type=e,this.isupload=!0},returnImgsFunc(e){null!=e&&e.length>0&&(this.file_path=e[0].file_path,this.form.image_id=e[0].file_id),this.isupload=!1},gotoBack(){this.$router.back(-1)},max10(e,o){e>=10&&(this.form.coupon[o].coupon_num=10),e<=0&&(this.form.coupon[o].coupon_num="")}}},I={class:"user"},G=v("div",{class:"common-form"},"新增活動會場",-1),z={class:"product-content"},F={key:0,class:"img"},Y=["src"],E=v("div",{class:"gray"},"建議上傳圖片尺寸為100px*100px",-1),R={class:"block"},S=v("span",{class:"demonstration"},null,-1),$=v("div",{class:"common-form"},"禮包設定",-1),L=v("div",{class:"common-form"},"購買設定",-1),A=v("div",{class:"common-form"},"發放設定",-1),B=v("div",{class:"gray9"},"一批一碼：這個活動使用一個二維碼；一物一碼：生成的每個二維碼不一樣；",-1),J={class:"common-button-wrapper"};const M=y(q,[["render",function(l,h,b,y,q,M){const O=t,W=r,Q=d,T=s,H=u,K=m,N=p,X=i,Z=n,ee=c,oe=f,ae=_,le=g,te=a,re=o,de=e;return V(),j("div",I,[G,v("div",z,[k(le,{ref:"form",model:q.form,rules:q.formRules,"label-width":"150px"},{default:x((()=>[k(W,{label:"活動標題",prop:"name",rules:[{required:!0,message:" "}]},{default:x((()=>[k(O,{type:"text",modelValue:q.form.name,"onUpdate:modelValue":h[0]||(h[0]=e=>q.form.name=e),placeholder:"請輸入活動標題",class:"max-w460"},null,8,["modelValue"])])),_:1}),k(W,{label:"背景圖",prop:"image_id"},{default:x((()=>[k(T,null,{default:x((()=>[k(Q,{type:"primary",onClick:M.openUpload},{default:x((()=>[U("選擇圖片")])),_:1},8,["onClick"]),""!=q.form.image_id?(V(),j("div",F,[v("img",{src:l.file_path,width:"100",height:"100"},null,8,Y)])):w("",!0),E])),_:1})])),_:1}),k(W,{label:"活動日期",prop:"value1",rules:[{required:!0,message:" "}]},{default:x((()=>[v("div",R,[S,k(H,{modelValue:q.form.value1,"onUpdate:modelValue":h[1]||(h[1]=e=>q.form.value1=e),type:"datetimerange","value-format":"YYYY-MM-DD","range-separator":"至","start-placeholder":"開始日期","end-placeholder":"結束日期"},null,8,["modelValue"])])])),_:1}),$,k(W,{label:"優惠券 "},{default:x((()=>[k(K,{modelValue:q.form.is_coupon,"onUpdate:modelValue":h[2]||(h[2]=e=>q.form.is_coupon=e)},{default:x((()=>[U("只能選擇不限等級、不限數量、不限領取數量的優惠券")])),_:1},8,["modelValue"])])),_:1}),q.form.is_coupon?(V(),C(W,{key:0,label:""},{default:x((()=>[k(Q,{type:"primary",onClick:h[3]||(h[3]=e=>M.addCoupon()),class:"mb16"},{default:x((()=>[U("新增")])),_:1}),k(X,{data:q.form.coupon,style:{width:"60%"}},{default:x((()=>[k(N,{prop:"coupon_id",label:"優惠券id"}),k(N,{prop:"name",label:"優惠券"}),k(N,{prop:"coupon_num",label:"數量"},{default:x((e=>[k(O,{type:"number",modelValue:e.row.coupon_num,"onUpdate:modelValue":o=>e.row.coupon_num=o,placeholder:"",min:"1",max:"10",onInput:o=>M.max10(e.row.coupon_num,e.$index)},null,8,["modelValue","onUpdate:modelValue","onInput"])])),_:1}),k(N,{prop:"address",label:"操作"},{default:x((e=>[k(Q,{type:"text",size:"small",onClick:o=>M.delcoupon(e.row)},{default:x((()=>[U("刪除")])),_:2},1032,["onClick"])])),_:1})])),_:1},8,["data"])])),_:1})):w("",!0),k(W,{label:"商品"},{default:x((()=>[k(K,{modelValue:q.form.is_product,"onUpdate:modelValue":h[4]||(h[4]=e=>q.form.is_product=e)},{default:x((()=>[U("選擇商品")])),_:1},8,["modelValue"])])),_:1}),q.form.is_product?(V(),C(W,{key:1,label:"商品選購數量",prop:"product_num",rules:[{required:!0,trigger:"blur",validator:q.validatePass}]},{default:x((()=>[k(O,{type:"number",modelValue:q.form.product_num,"onUpdate:modelValue":h[5]||(h[5]=e=>q.form.product_num=e),min:"1",placeholder:"請輸入商品選購數量",class:"max-w460"},null,8,["modelValue"])])),_:1},8,["rules"])):w("",!0),q.form.is_product?(V(),C(W,{key:2,label:""},{default:x((()=>[k(Q,{type:"primary",onClick:h[6]||(h[6]=e=>M.addProduct()),class:"mb16"},{default:x((()=>[U("新增")])),_:1}),k(X,{data:q.prodcutData,style:{width:"40%"}},{default:x((()=>[k(N,{prop:"product_name",label:"商品"}),k(N,{prop:"product_",label:"操作"},{default:x((e=>[k(Q,{type:"text",size:"small",onClick:o=>M.delProduct(e.row)},{default:x((()=>[U("刪除")])),_:2},1032,["onClick"])])),_:1})])),_:1},8,["data"])])),_:1})):w("",!0),k(W,{label:"積分 "},{default:x((()=>[k(K,{modelValue:q.form.is_point,"onUpdate:modelValue":h[7]||(h[7]=e=>q.form.is_point=e)},{default:x((()=>[U("不受每人每日限領規則限制")])),_:1},8,["modelValue"])])),_:1}),q.form.is_point?(V(),C(W,{key:3,label:"積分數量"},{default:x((()=>[k(O,{type:"number",min:"1",modelValue:q.form.point,"onUpdate:modelValue":h[8]||(h[8]=e=>q.form.point=e),class:"max-w460"},{append:x((()=>[U(" 積分 ")])),_:1},8,["modelValue"])])),_:1})):w("",!0),L,k(W,{label:"購買金額",prop:"money",rules:[{required:!0,message:" "}]},{default:x((()=>[k(O,{type:"number",min:"1",modelValue:q.form.money,"onUpdate:modelValue":h[9]||(h[9]=e=>q.form.money=e),class:"max-w460"},{append:x((()=>[U(" 元 ")])),_:1},8,["modelValue"])])),_:1}),k(W,{label:"會員購買等級 "},{default:x((()=>[k(ee,{modelValue:q.form.is_grade,"onUpdate:modelValue":h[10]||(h[10]=e=>q.form.is_grade=e)},{default:x((()=>[k(Z,{label:0},{default:x((()=>[U("不限")])),_:1}),k(Z,{label:1},{default:x((()=>[U("指定等級")])),_:1})])),_:1},8,["modelValue"])])),_:1}),q.form.is_grade?(V(),C(W,{key:4,label:"會員等級 "},{default:x((()=>[k(ae,{modelValue:q.form.grade_ids,"onUpdate:modelValue":h[11]||(h[11]=e=>q.form.grade_ids=e),multiple:"",placeholder:"請選擇"},{default:x((()=>[(V(!0),j(P,null,D(q.Grade,((e,o)=>(V(),C(oe,{key:o,label:e.name,value:e.grade_id},null,8,["label","value"])))),128))])),_:1},8,["modelValue"])])),_:1})):w("",!0),k(W,{label:"購買次數 "},{default:x((()=>[k(ee,{modelValue:q.form.is_times,"onUpdate:modelValue":h[12]||(h[12]=e=>q.form.is_times=e)},{default:x((()=>[k(Z,{label:0},{default:x((()=>[U("不限")])),_:1}),k(Z,{label:1},{default:x((()=>[U("限購")])),_:1})])),_:1},8,["modelValue"])])),_:1}),q.form.is_times?(V(),C(W,{key:5,label:""},{default:x((()=>[k(O,{min:"1",type:"number",modelValue:q.form.times,"onUpdate:modelValue":h[13]||(h[13]=e=>q.form.times=e),class:"max-w460"},{prepend:x((()=>[U(" 每人限購 ")])),append:x((()=>[U(" 次 ")])),_:1},8,["modelValue"])])),_:1})):w("",!0),A,k(W,{label:"發放數量",prop:"total_num",rules:[{required:!0,message:" "}]},{default:x((()=>[k(O,{min:"1",type:"number",modelValue:q.form.total_num,"onUpdate:modelValue":h[14]||(h[14]=e=>q.form.total_num=e),placeholder:"請輸入發放數量",class:"max-w460"},null,8,["modelValue"])])),_:1}),k(W,{label:"二維碼",prop:"code_type",rules:[{required:!0,message:" "}]},{default:x((()=>[k(ee,{modelValue:q.form.code_type,"onUpdate:modelValue":h[15]||(h[15]=e=>q.form.code_type=e)},{default:x((()=>[k(Z,{label:10},{default:x((()=>[U("一批一碼")])),_:1}),k(Z,{label:20},{default:x((()=>[U("一物一碼")])),_:1})])),_:1},8,["modelValue"]),B])),_:1})])),_:1},8,["model","rules"]),v("div",J,[k(Q,{type:"info",onClick:M.gotoBack},{default:x((()=>[U("返回")])),_:1},8,["onClick"]),k(Q,{type:"primary",onClick:M.onSubmit,loading:q.loading},{default:x((()=>[U("提交")])),_:1},8,["onClick","loading"])])]),q.isupload?(V(),C(te,{key:0,isupload:q.isupload,type:l.type,onReturnImgs:M.returnImgsFunc},{default:x((()=>[U("上傳圖片")])),_:1},8,["isupload","type","onReturnImgs"])):w("",!0),q.open_add?(V(),C(re,{key:1,open_add:q.open_add,onCloseDialog:h[16]||(h[16]=e=>M.closeProductDialogFunc(e))},{default:x((()=>[U("選擇優惠券彈出層")])),_:1},8,["open_add"])):w("",!0),k(de,{isproduct:q.isproduct,excludeIds:q.exclude_ids,islist:!1,onCloseDialog:M.closeProductFunc},null,8,["isproduct","excludeIds","onCloseDialog"])])}]]);export{M as default};
