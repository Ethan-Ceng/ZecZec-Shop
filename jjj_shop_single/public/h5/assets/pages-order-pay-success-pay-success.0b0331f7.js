import{_ as e,z as a,B as s,o as d,c as o,w as t,e as r,g as c,b as l,d as i,t as n,j as u,h as _,G as p}from"./index-6e5c77a7.js";import{_ as f}from"./recommendProduct.cb8c20bf.js";import{r as m}from"./uni-app.es.5424dc16.js";const g=e({components:{recommendProduct:f},data:()=>({loadding:!0,indicatorDots:!0,autoplay:!0,interval:2e3,duration:500,order_id:0,detail:{order_status:[],address:{region:[]},product:[],pay_type:[],delivery_type:[],pay_status:[]}}),onLoad(e){this.order_id=e.order_id},mounted(){a({title:"載入中"}),this.getData()},methods:{getData(){let e=this,a=e.order_id;e._get("user.order/detail",{order_id:a},(function(d){e.detail=d.data.order,e.loadding=!1,s(),d.data.show_table&&e.showSuccess("您的訂單需要補充相關資訊，請在訂單詳情補充",(function(){e.gotoPage("/pages/order/order-detail?order_id="+a)}))}))},goHome(){this.gotoPage("/pages/index/index")},goMyorder(){this.gotoPage("/pages/order/myorder")}}},[["render",function(e,a,s,g,y,b){const h=u,x=c,P=_,k=m(p("recommendProduct"),f);return y.loadding?r("",!0):(d(),o(x,{key:0,class:"pay-success"},{default:t((()=>[l(x,{class:"success-icon d-c-c d-c"},{default:t((()=>[l(h,{class:"iconfont icon-queren"}),l(h,{class:"name"},{default:t((()=>[i("支付成功")])),_:1})])),_:1}),l(x,{class:"success-price d-c-c"},{default:t((()=>[i(" ￥"),l(h,{class:"num"},{default:t((()=>[i(n(y.detail.pay_price),1)])),_:1})])),_:1}),y.detail.points_bonus>0?(d(),o(x,{key:0,class:"order-info mt30 f28"},{default:t((()=>[l(x,{class:"d-b-c p20 border-b"},{default:t((()=>[l(h,{class:"gray9"},{default:t((()=>[i(n(e.points_name())+"贈送",1)])),_:1}),l(h,{class:"gray3"},{default:t((()=>[i(n(y.detail.points_bonus),1)])),_:1})])),_:1})])),_:1})):r("",!0),l(x,{class:"success-btns d-b-c"},{default:t((()=>[l(P,{type:"default",class:"flex-1 mr10",onClick:a[0]||(a[0]=e=>b.goHome())},{default:t((()=>[i("返回首頁")])),_:1}),l(P,{type:"primary",class:"flex-1 ml10",onClick:b.goMyorder},{default:t((()=>[i("我的訂單")])),_:1},8,["onClick"])])),_:1}),l(x,null,{default:t((()=>[l(k,{location:30})])),_:1})])),_:1}))}],["__scopeId","data-v-89716247"]]);export{g as default};
