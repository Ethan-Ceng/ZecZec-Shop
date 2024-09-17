import{E as e,B as s,C as a,s as t,t as l,e as r,d as i,c as d,f as o,v as p}from"./element-plus-84a27f94.js";import{C as n}from"./card-8cac6905.js";import c from"./Add-5ac2b69d.js";import{_ as m,d as u}from"./index-5ae5860a.js";import{ae as _,ap as v,$ as f,o as y,c as g,a as b,P as h,S as j,W as x,X as k,Q as C,Y as w,T as L,a9 as z}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./order-10f620ac.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const P={components:{Add:c},data:()=>({active:0,loading:!0,detail:{pay_status:[],pay_type:[],delivery_type:[],user:{},address:[],product:[],order_status:[],extract:[],extract_store:[],express:[],delivery_status:[],extractClerk:[],region:{}},open_add:!1,pageSize:20,totalDataNumber:0,curPage:1,form:{express_id:null,express_no:""},forms:{is_cancel:1},extract_form:{order:{extract_status:1}},virtual_form:{virtual_content:""},order:{},delivery_type:0,exStyle:[],shopList:[],userModel:{},create_time:"",expressList:[],shopClerkList:[],open_edit:!1}),created(){this.getParams()},methods:{next(){this.active++>4&&(this.active=0)},getParams(){let e=this;const s=this.$route.query.order_id;n.orderdetail({order_id:s},!0).then((s=>{e.loading=!1,e.detail=s.data.detail,e.expressList=s.data.expressList,e.shopClerkList=s.data.shopClerkList})).catch((s=>{e.loading=!1}))},onSubmit(){let s=this,a=s.form;if(null==a.express_id)return void e.error("請選擇物流公司");if(""==a.express_no)return void e.error("請填寫物流單號");let t=this.$route.query.order_id;n.delivery({param:a,order_id:t},!0).then((a=>{s.loading=!1,e({message:"恭喜你，發貨成功",type:"success"}),s.getParams()})).catch((e=>{s.loading=!1}))},editClick(e){this.userModel=u(e),this.open_edit=!0},closeDialogFunc(e,s){"edit"==s&&(this.open_edit=e.openDialog,this.getParams())},cancelFunc(){this.$router.back(-1)}}},V={class:"pb50"},D={class:"product-content"},S=b("div",{class:"common-form"},"基本資訊",-1),A={class:"table-wrap"},F={class:"pb16"},$=b("span",{class:"gray9"},"訂單號：",-1),q={class:"pb16"},M=b("span",{class:"gray9"},"買家：",-1),E={class:"pb16"},N=b("span",{class:"gray9"},"發貨狀態：",-1),T=b("div",{class:"common-form mt16"},"商品資訊",-1),U={class:"table-wrap"},B={class:"pb16"},I=b("span",{class:"gray9"},"商品圖片：",-1),Q=["src"],W={class:"pb16"},X=b("span",{class:"gray9"},"商品名稱：",-1),Y={class:"pb16"},G=b("span",{class:"gray9"},"商品規格：",-1),H={class:"pb16"},J=b("span",{class:"gray9"},"商品價格：",-1),K=b("div",{class:"common-form mt16"},"收貨資訊",-1),O={class:"table-wrap"},R={class:"pb16"},Z=b("span",{class:"gray9"},"收貨人：",-1),ee={class:"pb16"},se=b("span",{class:"gray9"},"收貨電話：",-1),ae={class:"pb16"},te=b("span",{class:"gray9"},"收貨地址：",-1),le={key:0},re=b("div",{class:"common-form mt16"},"去發貨",-1),ie={key:1},de=b("div",{class:"common-form mt16"},"發貨資訊",-1),oe={class:"table-wrap"},pe={class:"pb16"},ne=b("span",{class:"gray9"},"物流公司：",-1),ce={class:"pb16"},me=b("span",{class:"gray9"},"物流單號：",-1),ue={class:"pb16"},_e=b("span",{class:"gray9"},"發貨時間：",-1),ve={class:"common-button-wrapper"};const fe=m(P,[["render",function(e,n,c,m,u,P){const fe=s,ye=a,ge=_("Add"),be=t,he=l,je=r,xe=i,ke=d,Ce=o,we=v("auth"),Le=p;return f((y(),g("div",V,[b("div",D,[S,b("div",A,[h(ye,null,{default:j((()=>[h(fe,{span:5},{default:j((()=>[b("div",F,[$,x(" "+k(u.detail.order_no),1)])])),_:1}),h(fe,{span:5},{default:j((()=>[b("div",q,[M,x(" "+k(u.detail.user.nickName)+" ",1),b("span",null,"使用者ID：("+k(u.detail.user.user_id)+")",1)])])),_:1}),h(fe,{span:5},{default:j((()=>[b("div",E,[N,0==u.detail.delivery_status?(y(),g(C,{key:0},[x("待發貨")],64)):w("",!0),1==u.detail.delivery_status?(y(),g(C,{key:1},[x("已發貨")],64)):w("",!0)])])),_:1})])),_:1})]),u.open_edit?(y(),L(ge,{key:0,open_edit:u.open_edit,order:u.userModel,onClose:n[0]||(n[0]=e=>P.closeDialogFunc(e,"edit"))},null,8,["open_edit","order"])):w("",!0),T,b("div",U,[h(ye,null,{default:j((()=>[h(fe,{span:5},{default:j((()=>[b("div",B,[I,b("img",{src:u.detail.product_image,width:"80",height:"80"},null,8,Q)])])),_:1}),h(fe,{span:5},{default:j((()=>[b("div",W,[X,x(" "+k(u.detail.product_name),1)])])),_:1}),h(fe,{span:5},{default:j((()=>[b("div",Y,[G,x(" "+k(u.detail.product_attr),1)])])),_:1}),h(fe,{span:5},{default:j((()=>[b("div",H,[J,x(" "+k(u.detail.product_price),1)])])),_:1})])),_:1})]),b("div",null,[K,b("div",O,[h(ye,null,{default:j((()=>[h(fe,{span:5},{default:j((()=>[b("div",R,[Z,x(" "+k(u.detail.name),1)])])),_:1}),h(fe,{span:5},{default:j((()=>[b("div",ee,[se,x(" "+k(u.detail.mobile),1)])])),_:1}),h(fe,{span:5},{default:j((()=>[b("div",ae,[te,x(" "+k(u.detail.region.province)+" "+k(u.detail.region.city)+" "+k(u.detail.region.region)+" "+k(u.detail.detail),1)])])),_:1})])),_:1})])]),f((y(),g("div",null,[0==u.detail.delivery_status?(y(),g("div",le,[re,h(ke,{size:"small",ref:"form",model:u.form,"label-width":"100px"},{default:j((()=>[h(je,{label:"物流公司"},{default:j((()=>[h(he,{modelValue:u.form.express_id,"onUpdate:modelValue":n[1]||(n[1]=e=>u.form.express_id=e),placeholder:"請選擇快遞公司",style:{width:"460px"}},{default:j((()=>[(y(!0),g(C,null,z(u.expressList,((e,s)=>(y(),L(be,{label:e.express_name,key:s,value:e.express_id},null,8,["label","value"])))),128))])),_:1},8,["modelValue"])])),_:1}),h(je,{label:"物流單號"},{default:j((()=>[h(xe,{modelValue:u.form.express_no,"onUpdate:modelValue":n[2]||(n[2]=e=>u.form.express_no=e),class:"max-w460"},null,8,["modelValue"])])),_:1})])),_:1},8,["model"])])):(y(),g("div",ie,[de,b("div",oe,[h(ye,null,{default:j((()=>[h(fe,{span:5},{default:j((()=>[b("div",pe,[ne,x(" "+k(u.detail.express.express_name),1)])])),_:1}),h(fe,{span:5},{default:j((()=>[b("div",ce,[me,x(" "+k(u.detail.express_no),1)])])),_:1}),h(fe,{span:5},{default:j((()=>[b("div",ue,[_e,x(" "+k(u.detail.delivery_time),1)])])),_:1})])),_:1})])]))])),[[we,"/order/order/delivery"]])]),b("div",ve,[h(Ce,{size:"small",type:"info",onClick:P.cancelFunc},{default:j((()=>[x("返回上一頁")])),_:1},8,["onClick"]),0==u.detail.delivery_status?(y(),L(Ce,{key:0,size:"small",type:"primary",onClick:P.onSubmit},{default:j((()=>[x("確認發貨 ")])),_:1},8,["onClick"])):w("",!0)])])),[[Le,u.loading]])}]]);export{fe as default};
