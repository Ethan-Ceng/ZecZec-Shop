import{_ as e}from"./GetCoupon-4aa1d1f3.js";import{_ as a}from"./Upload-71ca9325.js";import{E as l,d as o,e as t,f as i,C as s,J as d,m as n,n as r,L as m,c as p}from"./element-plus-84a27f94.js";import{I as u}from"./invitationgift-7d68eec1.js";import{_ as c}from"./index-5ae5860a.js";import{o as _,c as f,a as h,P as g,S as y,W as v,Y as V,Q as b,a9 as j,X as w,T as x,bb as U,b9 as k}from"./@vue-8fe4574d.js";import"./coupon-7cc893e2.js";import"./file-b7a04c7e.js";/* empty css                                                               */import"./AddCategory-69aab672.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const C={components:{GetCoupon:e,Upload:a},data:()=>({form:{name:"",inv_condition:"0",value1:[],image_id:"",is_show:1,share_image_id:"",share_title:"",share_desc:""},tableData:[],loading:!1,open_add:!1,rewardData:[{invitation_num:1,is_coupon:!1,point:0,is_point:!1,coupon_ids:"",coupon_name:"",is_balance:!1,balance:""}],coupon_index:0,isupload:!1,formRules:{image_id:[{required:!0,message:"請上傳背景圖",trigger:"blur"}],share_image_id:[{required:!0,message:"請上傳分享圖",trigger:"blur"}]}}),created(){},methods:{addInv(){this.rewardData.push({invitation_num:1,is_coupon:!1,point:0,is_point:!1,coupon_ids:"",coupon_name:"",is_balance:!1,balance:""})},addCoupon(e){this.open_add=!0,this.coupon_index=e},closeProductDialogFunc(e){let a=this,o=a.coupon_index;a.open_add=e.openDialog,-1==a.rewardData[o].coupon_name.indexOf(e.params.name)?(a.rewardData[o].coupon_ids+=e.params.coupon_id+",",""==a.rewardData[o].coupon_name?a.rewardData[o].coupon_name+=e.params.name:a.rewardData[o].coupon_name+=","+e.params.name):l.error("請勿重複新增")},onSubmit(){let e=this;if(0==e.rewardData.length)return void l.error("請新增獎項");let a=e.form;a.reward_data=e.rewardData,e.$refs.form.validate((o=>{o&&(e.loading=!0,u.addInvitation(a,!0).then((a=>{e.loading=!1,1==a.code?(l({message:a.msg,type:"success"}),e.$router.push("/plus/invitation/index")):e.loading=!1})).catch((a=>{e.loading=!1})))}))},openUpload(e){this.type=e,this.isupload=!0},returnImgsFunc(e){null!=e&&e.length>0&&("image"==this.type?(this.file_path=e[0].file_path,this.form.image_id=e[0].file_id):"share"==this.type&&(this.share_file_path=e[0].file_path,this.form.share_image_id=e[0].file_id)),this.isupload=!1},gotoBack(){this.$router.back(-1)},delReward(e){this.rewardData.splice(e,1)}}},D=e=>(U("data-v-8d87e431"),e=e(),k(),e),I={class:"user"},z=D((()=>h("div",{class:"common-form"},"新增活動會場",-1))),q={class:"product-content"},R={key:0,class:"img"},Y=["src"],F=D((()=>h("div",{class:"ww100"},"建議尺寸750*1340",-1))),P={key:0,class:"img"},S=["src"],$=D((()=>h("div",{class:"ww100"},"建議尺寸800*800",-1))),A={class:"block"},B=D((()=>h("span",{class:"demonstration"},null,-1))),G=D((()=>h("div",{class:"common-form"},"邀請成功的條件",-1))),M=D((()=>h("div",{class:"common-form"},"禮品設定",-1))),E={style:{"font-weight":"900"}},J=D((()=>h("span",null,"每邀請  ",-1))),L=D((()=>h("span",null,"  人獲得禮品",-1))),O={style:{padding:"10px 20px 20px 40px"}},Q={class:"common-button-wrapper"};const T=c(C,[["render",function(l,u,c,U,k,C){const D=o,T=t,W=i,X=s,Z=d,H=n,K=r,N=m,ee=p,ae=a,le=e;return _(),f("div",I,[z,h("div",q,[g(ee,{ref:"form",model:k.form,rules:k.formRules,"label-width":"150px"},{default:y((()=>[g(T,{label:"活動標題",prop:"name",rules:[{required:!0,message:" "}]},{default:y((()=>[g(D,{type:"text",modelValue:k.form.name,"onUpdate:modelValue":u[0]||(u[0]=e=>k.form.name=e),placeholder:"請輸入活動標題",class:"max-w460"},null,8,["modelValue"])])),_:1}),g(T,{label:"分享標題",prop:"share_title",rules:[{required:!0,message:" "}]},{default:y((()=>[g(D,{type:"text",modelValue:k.form.share_title,"onUpdate:modelValue":u[1]||(u[1]=e=>k.form.share_title=e),placeholder:"請輸入分享標題",class:"max-w460"},null,8,["modelValue"])])),_:1}),g(T,{label:"分享內容",prop:"share_desc",rules:[{required:!0,message:" "}]},{default:y((()=>[g(D,{type:"text",modelValue:k.form.share_desc,"onUpdate:modelValue":u[2]||(u[2]=e=>k.form.share_desc=e),placeholder:"請輸入分享內容",class:"max-w460"},null,8,["modelValue"])])),_:1}),g(T,{label:"背景圖",prop:"image_id"},{default:y((()=>[g(X,null,{default:y((()=>[g(W,{type:"primary",onClick:u[3]||(u[3]=e=>C.openUpload("image"))},{default:y((()=>[v("選擇圖片")])),_:1}),""!=k.form.image_id?(_(),f("div",R,[h("img",{src:l.file_path,width:"100",height:"100"},null,8,Y)])):V("",!0)])),_:1}),F])),_:1}),g(T,{label:"分享圖",prop:"share_image_id"},{default:y((()=>[g(X,null,{default:y((()=>[g(W,{type:"primary",onClick:u[4]||(u[4]=e=>C.openUpload("share"))},{default:y((()=>[v("選擇圖片")])),_:1}),""!=k.form.share_image_id?(_(),f("div",P,[h("img",{src:l.share_file_path,width:"100",height:"100"},null,8,S)])):V("",!0)])),_:1}),$])),_:1}),g(T,{label:"活動日期",prop:"value1",rules:[{required:!0,message:" "}]},{default:y((()=>[h("div",A,[B,g(Z,{modelValue:k.form.value1,"onUpdate:modelValue":u[5]||(u[5]=e=>k.form.value1=e),type:"datetimerange","value-format":"YYYY-MM-DD","range-separator":"至","start-placeholder":"開始日期","end-placeholder":"結束日期"},null,8,["modelValue"])])])),_:1}),G,g(T,{label:"邀請成功的條件 "},{default:y((()=>[g(H,{modelValue:k.form.inv_condition,"onUpdate:modelValue":u[6]||(u[6]=e=>k.form.inv_condition=e),label:"0"},{default:y((()=>[v("邀請成為會員")])),_:1},8,["modelValue"]),g(H,{modelValue:k.form.inv_condition,"onUpdate:modelValue":u[7]||(u[7]=e=>k.form.inv_condition=e),label:"1"},{default:y((()=>[v("邀請成為會員且消費")])),_:1},8,["modelValue"])])),_:1}),M,(_(!0),f(b,null,j(k.rewardData,((e,a)=>(_(),f("div",{key:a,class:"mt16"},[h("div",null,[h("span",E,"獎項"+w(a+1)+":  ",1),J,g(D,{type:"number",modelValue:e.invitation_num,"onUpdate:modelValue":a=>e.invitation_num=a,size:"small",style:{width:"100px"}},null,8,["modelValue","onUpdate:modelValue"]),L,g(K,{modelValue:e.is_balance,"onUpdate:modelValue":a=>e.is_balance=a,class:"pl16"},{default:y((()=>[v("餘額")])),_:2},1032,["modelValue","onUpdate:modelValue"]),g(D,{type:"number",modelValue:e.balance,"onUpdate:modelValue":a=>e.balance=a,size:"small",style:{width:"100px"}},null,8,["modelValue","onUpdate:modelValue"]),g(K,{modelValue:e.is_point,"onUpdate:modelValue":a=>e.is_point=a,class:"pl16"},{default:y((()=>[v("積分")])),_:2},1032,["modelValue","onUpdate:modelValue"]),g(D,{type:"number",modelValue:e.point,"onUpdate:modelValue":a=>e.point=a,size:"small",style:{width:"100px"}},null,8,["modelValue","onUpdate:modelValue"]),g(K,{modelValue:e.is_coupon,"onUpdate:modelValue":a=>e.is_coupon=a,class:"pl16"},{default:y((()=>[v("優惠券")])),_:2},1032,["modelValue","onUpdate:modelValue"]),g(D,{type:"text",modelValue:e.coupon_name,"onUpdate:modelValue":a=>e.coupon_name=a,disabled:"true",size:"small",style:{width:"200px","margin-right":"6px","margin-left":"6px"}},null,8,["modelValue","onUpdate:modelValue"]),g(W,{type:"primary",size:"small",onClick:e=>C.addCoupon(a)},{default:y((()=>[v("選擇優惠券")])),_:2},1032,["onClick"]),g(N,{type:"primary",onClick:e=>C.delReward(a),style:{float:"right"}},{default:y((()=>[v("刪除")])),_:2},1032,["onClick"])])])))),128)),h("div",O,[g(N,{type:"primary",onClick:C.addInv},{default:y((()=>[v("新增獎勵項")])),_:1},8,["onClick"])])])),_:1},8,["model","rules"]),h("div",Q,[g(W,{type:"info",onClick:C.gotoBack},{default:y((()=>[v("返回")])),_:1},8,["onClick"]),g(W,{type:"primary",onClick:C.onSubmit,loading:k.loading},{default:y((()=>[v("提交")])),_:1},8,["onClick","loading"])])]),k.isupload?(_(),x(ae,{key:0,isupload:k.isupload,type:l.type,onReturnImgs:C.returnImgsFunc},{default:y((()=>[v("上傳圖片")])),_:1},8,["isupload","type","onReturnImgs"])):V("",!0),k.open_add?(_(),x(le,{key:1,open_add:k.open_add,onCloseDialog:u[8]||(u[8]=e=>C.closeProductDialogFunc(e))},{default:y((()=>[v("選擇優惠券彈出層")])),_:1},8,["open_add"])):V("",!0)])}],["__scopeId","data-v-8d87e431"]]);export{T as default};
