import{_ as t,x as e,y as a,o as l,c as s,w as o,g as r,b as c,n as i,d,f as n,r as u,F as p,q as _,j as f,k as g,S as h,t as m,i as b,h as w}from"./index-6e5c77a7.js";import{u as y}from"./uni-load-more.58b2601d.js";const D=t({components:{uniLoadMore:y},data:()=>({phoneHeight:0,scrollviewHigh:0,state_active:-1,tableData:[],list_rows:5,last_page:0,page:1,no_more:!1,loading:!0,topRefresh:!1}),computed:{loadingType(){return this.loading?1:0!=this.tableData.length&&this.no_more?2:0}},mounted(){this.init(),this.getData()},onPullDownRefresh(){},methods:{init(){let t=this;e({success(e){t.phoneHeight=e.windowHeight,a().select(".top-tabbar").boundingClientRect((e=>{let a=t.phoneHeight-e.height;t.scrollviewHigh=a})).exec()}})},getData(){let t=this;t.loading=!0;let e=t.page,a=t.state_active,l=t.list_rows;t._get("user.refund/lists",{state:a,page:e||1,list_rows:l},(function(e){if(t.loading=!1,t.tableData=t.tableData.concat(e.data.list.data),t.last_page=e.data.list.last_page,t.last_page<=1)return t.no_more=!0,!1}))},stateFunc(t){let e=this;e.state_active!=t&&(e.tableData=[],e.loading=!0,e.page=1,e.state_active=t,e.getData())},gotoRefundDetail(t){this.gotoPage("/pages/order/refund/detail/detail?order_refund_id="+t)},scrolltoupperFunc(){},scrolltolowerFunc(){let t=this;t.no_more||(t.page++,t.page<=t.last_page?t.getData():t.no_more=!0)}}},[["render",function(t,e,a,y,D,v){const k=r,F=f,x=b,H=w,R=g("uni-load-more"),C=h;return l(),s(k,{class:"refund-list"},{default:o((()=>[c(k,{class:"top-tabbar"},{default:o((()=>[c(k,{class:i(-1==D.state_active?"tab-item active":"tab-item"),onClick:e[0]||(e[0]=t=>v.stateFunc(-1))},{default:o((()=>[d(" 全部 ")])),_:1},8,["class"]),c(k,{class:i(0==D.state_active?"tab-item active":"tab-item"),onClick:e[1]||(e[1]=t=>v.stateFunc(0))},{default:o((()=>[d(" 待處理 ")])),_:1},8,["class"])])),_:1}),c(C,{"scroll-y":"true",class:"scroll-Y",style:_("height:"+D.scrollviewHigh+"px;"),"lower-threshold":"50",onScrolltoupper:v.scrolltoupperFunc,onScrolltolower:v.scrolltolowerFunc},{default:o((()=>[c(k,{class:i(D.topRefresh?"top-refresh open":"top-refresh")},{default:o((()=>[(l(),n(p,null,u(3,((t,e)=>c(k,{class:"circle",key:e}))),64))])),_:1},8,["class"]),c(k,{class:"list"},{default:o((()=>[(l(!0),n(p,null,u(D.tableData,((t,e)=>(l(),s(k,{class:"item bg-white p30 mb20",key:e},{default:o((()=>[c(k,{class:"d-b-c"},{default:o((()=>[c(F,null,{default:o((()=>[d(m(t.create_time),1)])),_:2},1024),c(F,{class:"red"},{default:o((()=>[d(m(t.state_text),1)])),_:2},1024)])),_:2},1024),c(k,{class:"one-product d-s-c pt20"},{default:o((()=>[c(k,{class:"cover"},{default:o((()=>[c(x,{src:t.orderproduct.image.file_path,mode:"aspectFit"},null,8,["src"])])),_:2},1024),c(k,{class:"flex-1"},{default:o((()=>[c(k,{class:"pro-info"},{default:o((()=>[d(m(t.orderproduct.product_name),1)])),_:2},1024),c(k,{class:"pt10 p-0-30"},{default:o((()=>[c(F,{class:"f24 gray9"})])),_:1})])),_:2},1024)])),_:2},1024),c(k,{class:"d-e-c pt20"},{default:o((()=>[c(k,null,{default:o((()=>[d(" 商品金額： "),c(F,{class:"red"},{default:o((()=>[d("¥"+m(t.orderproduct.total_price),1)])),_:2},1024)])),_:2},1024)])),_:2},1024),c(k,{class:"d-e-c pt10"},{default:o((()=>[c(k,null,{default:o((()=>[d(" 訂單實付金額： "),c(F,{class:"red"},{default:o((()=>[d("¥"+m(t.orderproduct.total_pay_price),1)])),_:2},1024)])),_:2},1024)])),_:2},1024),c(k,{class:"d-e-c mt20 pt20 border-t"},{default:o((()=>[c(H,{type:"default",class:"btn-gray-border",onClick:e=>v.gotoRefundDetail(t.order_refund_id)},{default:o((()=>[d("檢視詳情")])),_:2},1032,["onClick"])])),_:2},1024)])),_:2},1024)))),128)),0!=D.tableData.length||D.loading?(l(),s(R,{key:1,loadingType:v.loadingType},null,8,["loadingType"])):(l(),s(k,{key:0,class:"d-c-c p30"},{default:o((()=>[c(F,{class:"iconfont icon-wushuju"}),c(F,{class:"cont"},{default:o((()=>[d("親，暫無相關記錄哦")])),_:1})])),_:1}))])),_:1})])),_:1},8,["style","onScrolltoupper","onScrolltolower"])])),_:1})}]]);export{D as default};
