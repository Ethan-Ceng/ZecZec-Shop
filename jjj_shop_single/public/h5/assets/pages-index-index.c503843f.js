import{_ as t,s as e,a as s,o as a,c as o,w as l,b as i,n as c,d as n,t as r,e as d,f as u,r as h,F as p,i as g,g as _,h as f,j as m,k as y,l as x,m as k,p as b,q as w,u as C,I as P,v as S,x as T,y as v,z as B,A as H,B as F,C as I,D as j,E as D,G as L}from"./index-6e5c77a7.js";import{_ as R,a as W}from"./diy.e1652478.js";import{r as M}from"./uni-app.es.5424dc16.js";import{P as A}from"./uni-popup.f3f35788.js";import{u as Q}from"./uni-load-more.58b2601d.js";import"./countdown-act.34a2c1ec.js";import"./upload.182a2719.js";/* empty css                                                                  */const U=t({components:{diy:R,Homepush:t({components:{Popup:A},data:()=>({isPopup:!1,type:0,width:600,height:800,backgroundColor:"none",boxShadow:"none",form:{},coupon:[]}),props:["homepush_data"],created(){},mounted(){this.setData()},methods:{setData(){this.isPopup=!0,this.form=this.homepush_data,this.type=this.homepush_data.type,this.coupon=this.homepush_data.coupon},hidePopupFunc(t){e("homepush_name",this.homepush_data.name),this.isPopup=!1},jumpPage(t){this.hidePopupFunc(),this.gotoPage("/"+t)},getCoupon(){let t=this,e=[];t.coupon.forEach((t=>{e.push(t.coupon_id)})),t._get("user.coupon/receiveList",{coupon_ids:JSON.stringify(e)},(function(e){s({title:"領取成功",icon:"success",mask:!0,duration:2e3}),t.hidePopupFunc()}))}}},[["render",function(t,e,s,x,k,b){const w=g,C=_,P=f,S=m,T=y("Popup");return a(),o(C,{class:"home-push-wrap"},{default:l((()=>[i(T,{show:k.isPopup,width:k.width,height:k.height,backgroundColor:k.backgroundColor,boxShadow:k.boxShadow,padding:0,onHidePopup:b.hidePopupFunc},{default:l((()=>[i(C,{class:c(["home-push",1==k.type||3==k.type?"home-push-bg":""])},{default:l((()=>[1==k.type?(a(),o(C,{key:0,class:"image-text"},{default:l((()=>[i(C,{class:"pic"},{default:l((()=>[i(w,{src:k.form.file_path,mode:"aspectFill"},null,8,["src"])])),_:1}),i(C,{class:"title"},{default:l((()=>[n(r(k.form.title),1)])),_:1}),i(C,{class:"des"},{default:l((()=>[n(r(k.form.remark),1)])),_:1}),i(C,{class:"btns"},{default:l((()=>[i(P,{class:"button",type:"primary",onClick:e[0]||(e[0]=t=>b.jumpPage(k.form.link.url))},{default:l((()=>[n(r(k.form.des),1)])),_:1})])),_:1})])),_:1})):d("",!0),2==k.type?(a(),o(C,{key:1,class:"image-only",onClick:e[1]||(e[1]=t=>b.jumpPage(k.form.link.url))},{default:l((()=>[i(C,{class:"pic"},{default:l((()=>[i(w,{src:k.form.file_path,mode:"aspectFill"},null,8,["src"])])),_:1})])),_:1})):d("",!0),3==k.type?(a(),o(C,{key:2,class:"cuopon"},{default:l((()=>[null!=k.form.file_path&&""!=k.form.file_path?(a(),o(C,{key:0,class:"cuopon-title d-c-c"},{default:l((()=>[i(w,{class:"image",src:k.form.file_path,mode:"aspectFill"},null,8,["src"])])),_:1})):d("",!0),i(C,{class:"list"},{default:l((()=>[(a(!0),u(p,null,h(k.coupon,((t,e)=>(a(),o(C,{class:"item",key:e},{default:l((()=>[i(C,{class:"info"},{default:l((()=>[i(C,{class:"num"},{default:l((()=>[i(S,{class:"f30"},{default:l((()=>[n(r(t.name),1)])),_:2},1024)])),_:2},1024)])),_:2},1024),i(C,{class:"explan"},{default:l((()=>[i(S,{class:"name"},{default:l((()=>[n(r(t.type),1)])),_:2},1024)])),_:2},1024)])),_:2},1024)))),128))])),_:1}),i(C,{class:"btns"},{default:l((()=>[i(P,{class:"button",type:"primary",onClick:e[2]||(e[2]=t=>b.getCoupon())},{default:l((()=>[n("立即領取")])),_:1})])),_:1})])),_:1})):d("",!0)])),_:1},8,["class"]),i(C,{class:"close-btns",onClick:e[3]||(e[3]=t=>b.hidePopupFunc(!0))},{default:l((()=>[i(S,{class:"icon iconfont icon-guanbi"})])),_:1})])),_:1},8,["show","width","height","backgroundColor","boxShadow","onHidePopup"])])),_:1})}],["__scopeId","data-v-046e8969"]]),navBar:W,uniLoadMore:Q,searchProduct:t({data:()=>({form:{},arr:[]}),mounted(){this.getData()},props:["isShow"],methods:{getData(){let t=this;x({key:"search_list",success:function(e){null!=e&&null!=e.data&&(t.arr=e.data)}})},stopTouchMove:()=>!0,search(t){let e=null;if(null!=t)e=t;else{e=this.form.keyWord;let t=this.arr;if(void 0===e||null==e||""==e)return s({title:"請輸入搜尋的關鍵字",icon:"none",duration:2e3}),!1;t.push(e),k({key:"search_list",data:t,success:function(){console.log("success")}})}this.gotoPage("/pages/product/list/list?search="+e+"&category_id=0&sortType=all")},clearStorage(){let t=this;b({key:"search_list",success:function(e){t.arr=[]}})},closeSearch(){this.$emit("close")}}},[["render",function(t,e,s,c,g,f){const y=_,x=m,k=P;return s.isShow?(a(),o(y,{key:0,onTouchmove:C(f.stopTouchMove,["stop","prevent"])},{default:l((()=>[i(y,{class:"search-wrap"},{default:l((()=>[i(y,{class:"state_top"}),i(y,{class:"head_top",style:w("height:"+t.topBarTop()+"px;")},null,8,["style"]),i(y,{class:"index-search-box d-b-c",id:"searchBox",style:w(0==t.topBarHeight()?"":"height:"+t.topBarHeight()+"px;padding-right: 200rpx")},{default:l((()=>[i(y,{class:"reg180",style:w(0==t.topBarHeight()?"":"height:"+t.topBarHeight()+"px;")},{default:l((()=>[i(x,{onClick:f.closeSearch,class:"icon iconfont icon-you"},null,8,["onClick"])])),_:1},8,["style"]),i(y,{class:"index-search t-c flex-1",style:w(0==t.topBarHeight()?"":"height:"+t.topBarHeight()+"px;")},{default:l((()=>[i(x,{class:"icon iconfont icon-sousuo"}),i(k,{type:"text",modelValue:g.form.keyWord,"onUpdate:modelValue":e[0]||(e[0]=t=>g.form.keyWord=t),class:"flex-1 ml10 f30 gray3","placeholder-class":"f24 gray6",placeholder:"輸入你要的商品","confirm-type":"search",onConfirm:e[1]||(e[1]=t=>f.search())},null,8,["modelValue"])])),_:1},8,["style"])])),_:1},8,["style"]),i(y,{class:"p-0-20 bg-white"},{default:l((()=>[i(y,{class:"group-hd"},{default:l((()=>[i(y,{class:"left"},{default:l((()=>[i(x,{class:"min-name"},{default:l((()=>[n("最近搜尋")])),_:1})])),_:1}),i(y,{class:"right"},{default:l((()=>[i(x,{class:"icon iconfont icon-lajitong",onClick:f.clearStorage},null,8,["onClick"])])),_:1})])),_:1}),i(y,{class:"before-search"},{default:l((()=>[(a(!0),u(p,null,h(g.arr,((t,e)=>(a(),o(x,{class:"item",key:e,onClick:t=>f.search(g.arr[e])},{default:l((()=>[n(r(g.arr[e]),1)])),_:2},1032,["onClick"])))),128))])),_:1})])),_:1})])),_:1})])),_:1},8,["onTouchmove"])):d("",!0)}],["__scopeId","data-v-2e0ec33b"]])},data:()=>({loading:!0,loadding:!0,phoneHeight:0,scrollviewHigh:0,background:"",listData:[],indicatorDots:!0,autoplay:!0,interval:2e3,duration:500,items:[],is_collection:!1,is_follow:"0",is_homepush:!1,homepush_data:{},target:0,thisindex:0,category_list:[],product_list:[],page:1,last_page:0,no_more:!1,title_name:"",bgcolor:"",url:"",jweixin:null,toplogo:"",titleTextColor:"",diytop:0,showSearch:!1,openCategory:{color:"#000000",open:0},searchIconTxt:"",title_type:"",rightSearch:"",hide_search:!1}),watch:{thisindex:function(t,e){t!=e&&(console.log(t),this.category_id=this.category_list[t].category_id,this.toggleInit(),0!=t&&this.getProduct())},bgcolor:function(){const t=this.bgcolor||"#ffffff",e=["#ffffff","#FFFFFF"].includes(t);this.$nextTick((()=>{this.searchStyle={color:e?"#ccc":t},this.topBarHeight()&&this.topBarHeight()>0&&(this.searchStyle.height=`${this.topBarHeight()}px`),this.searchIconTxt={color:e?"#ccc":t},this.rightSearch={borderColor:e?"#ccc":t}}))}},computed:{loadingType(){return this.loading?1:0!=this.product_list.length&&this.no_more?2:0},scrolltop(){let t=80-2*this.diytop;return t<=0?0:t}},onReady(){S()},onShareAppMessage(){return{title:this.title_name,path:"/pages/index/index?"+this.getShareUrlParams()}},onTabItemTap(){window.location.href=this.websiteUrl+this.h5_addr+"/pages/index/index"},onLoad(t){t.invitation_id&&e("invitation_id",t.invitation_id),t.referee_id&&e("referee_id",t.referee_id),this.isWeixin()&&(this.url=window.location.href),this.getData(),this.getList()},onPullDownRefresh(){0==this.thisindex?this.getData():(this.toggleInit(),this.getProduct())},onReachBottom(){this.scrolltolowerFunc(),console.log("到底了")},mounted(){this.init()},methods:{init(){let t=this;T({success(e){t.phoneHeight=e.windowHeight,v().select(".top_head").boundingClientRect((e=>{let s=t.phoneHeight-e.height;t.scrollviewHigh=s})).exec()}})},onRefresh(){},getData(){let t=this;B({title:"載入中"}),t._get("index/index",{url:t.url},(function(e){t.listData=e.data.list,t.background=e.data.background,t.items=e.data.items,t.title_name=e.data.page.params.title,t.bgcolor=e.data.page.style.titleBackgroundColor,t.hide_search=e.data.page.style.hide_search,t.toplogo=e.data.page.params.toplogo,t.setPage(e.data.page),t.title_type=e.data.page.params.title_type,t.titleTextColor=e.data.page.style.titleTextColor,t.openCategory=e.data.page.category;let s=H("homepush_name");if(e.data.setting.homepush.is_open&&s!=e.data.setting.homepush.name&&(t.homepush_data=e.data.setting.homepush,t.is_homepush=!0,t.is_homepush=!0),""!=t.url){t.jweixin=t.configWxScan(e.data.signPackage);let s={};t.configWx(e.data.share.signPackage,e.data.share.shareParams,s)}F(),t.loadding=!1,I()}))},getList(){let t=this;t._get("product.category/index",{},(function(e){t.category_list=e.data.list;t.category_list.unshift({name:"推薦",category_id:"0"})}))},getProduct(){let t=this,e=t.page;t.loading=!0,t._get("product.product/lists",{page:e||1,category_id:t.category_id,search:"",sortType:"all",sortPrice:0,list_rows:20},(function(e){t.loading=!1,I(),t.product_list=t.product_list.concat(e.data.list.data),t.last_page=e.data.list.last_page,e.data.list.last_page<=1&&(t.no_more=!0)}))},scrolltolowerFunc(){let t=this;t.page<t.last_page&&(t.page++,t.getProduct()),t.no_more=!0},setPage(t){j({title:t.params.name});let e="#000000";"white"==t.style.titleTextColor&&(e="#ffffff"),D({frontColor:e,backgroundColor:"#ffffff"})},toggleInit(){this.page=1,this.last_page=0,this.no_more=!1,this.product_list=[]},scroll(t){this.diytop=t.detail.scrollTop},setIndex(t){this.thisindex=t},stopTouchMove:()=>!0,scanQrcode:function(){let t=this;t.jweixin.scanQRCode({needResult:1,scanType:["qrCode","barCode"],success:function(e){t.gotoPage("/pages/store/clerkorder?order_no="+e.resultStr)},error:function(t){s({title:"掃碼失敗，請重試"})}})},closeSearch(){this.showSearch=!1}}},[["render",function(t,e,s,x,k,b){const C=_,P=m,S=g,T=M(L("navBar"),W),v=M(L("diy"),R),B=y("uni-load-more"),H=f,F=y("Homepush"),I=y("searchProduct"),j=y("tabBar");return a(),o(C,{"data-theme":t.theme(),class:c(t.theme()||"")},{default:l((()=>[i(C,{class:"top_head pr",style:w("background-color: "+k.bgcolor+";")},{default:l((()=>[i(C,{class:"state_top"}),i(C,{class:"head_top",style:w("height:"+t.topBarTop()+"px;")},null,8,["style"]),i(C,{class:"d-b-c",style:w(0==t.topBarHeight()?"":"height:"+t.topBarHeight()+"px;")},{default:l((()=>[i(C,{class:"index_log"},{default:l((()=>["text"==k.title_type?(a(),o(P,{key:0,style:w("color:"+k.titleTextColor+";")},{default:l((()=>[n(r(k.title_name),1)])),_:1},8,["style"])):(a(),o(S,{key:1,src:k.toplogo||"/static/logo.png",mode:"heightFix"},null,8,["src"]))])),_:1}),k.hide_search?(a(),o(C,{key:0,class:"flex-1"})):(a(),o(C,{key:1,class:"d-s-c flex-1"},{default:l((()=>[i(C,{class:"top_search special",style:{color:"#999"},onClick:e[0]||(e[0]=t=>k.showSearch=!0)},{default:l((()=>[i(P,{class:"icon iconfont icon-sousuo",style:w(k.searchIconTxt)},null,8,["style"]),n("搜尋商品 ")])),_:1}),i(C,{class:"top_search_right",onClick:b.scanQrcode,style:w(k.rightSearch)},{default:l((()=>[i(P,{class:"icon iconfont icon-saoyisao1"})])),_:1},8,["onClick","style"])])),_:1}))])),_:1},8,["style"]),k.openCategory.open?(a(),o(C,{key:0,class:"nuter"},{default:l((()=>[i(T,{style:{width:"100%"},color:k.openCategory.color,onCurrentIndex:b.setIndex,currentI:k.thisindex,navList:k.category_list},null,8,["color","onCurrentIndex","currentI","navList"])])),_:1})):(a(),o(C,{key:1,style:{height:"20rpx"}}))])),_:1},8,["style"]),i(C,{class:"top_bg",style:w("background-color: "+k.bgcolor+";")},null,8,["style"]),i(C,null,{default:l((()=>[0==k.thisindex?(a(),o(C,{key:0},{default:l((()=>[i(v,{style:{position:"relative"},diyItems:k.items},null,8,["diyItems"])])),_:1})):d("",!0),0!=k.thisindex?(a(),o(C,{key:1},{default:l((()=>[i(C,{class:"product-list"},{default:l((()=>[(a(!0),u(p,null,h(k.product_list,((e,s)=>(a(),o(C,{key:s,class:c(["product_item o-h",s%2==1?"product_item_right":""]),onClick:s=>t.gotoPage("/pages/product/detail/detail?product_id="+e.product_id)},{default:l((()=>[i(C,null,{default:l((()=>[i(S,{src:e.product_image,mode:"",style:{width:"345rpx",height:"345rpx"}},null,8,["src"])])),_:2},1024),i(C,{class:"text-ellipsis-2 f26 pro_name p-0-20 mt20"},{default:l((()=>[n(r(e.product_name),1)])),_:2},1024),i(C,{class:"mt36 mb20 p-0-20"},{default:l((()=>[i(P,{class:"f20 fb redF6"},{default:l((()=>[n("￥")])),_:1}),i(P,{class:"f32 fb redF6"},{default:l((()=>[n(r(e.product_sku.product_price),1)])),_:2},1024),i(P,{class:"text-d-line f20 gray9 ml10"},{default:l((()=>[n("￥")])),_:1}),i(P,{class:"text-d-line f24 gray9"},{default:l((()=>[n(r(e.product_sku.line_price),1)])),_:2},1024)])),_:2},1024)])),_:2},1032,["class","onClick"])))),128))])),_:1}),0!=k.product_list.length||k.loading?(a(),o(B,{key:1,loadingType:b.loadingType},null,8,["loadingType"])):(a(),o(C,{key:0,class:"d-c-c p30 ww100"},{default:l((()=>[i(P,{class:"iconfont icon-wushuju"}),i(P,{class:"cont"},{default:l((()=>[n("親，暫無相關記錄哦")])),_:1})])),_:1}))])),_:1})):d("",!0)])),_:1}),k.is_collection?(a(),o(C,{key:0,class:"collection-box",style:w("top:"+(t.topBarTop()+t.topBarHeight()+10)+"px;")},{default:l((()=>[i(C,{class:"inner"},{default:l((()=>[i(P,null,{default:l((()=>[n("點選“")])),_:1}),i(P,{class:"point"},{default:l((()=>[n(".")])),_:1}),i(P,{class:"point point-big"},{default:l((()=>[n(".")])),_:1}),i(P,{class:"point"},{default:l((()=>[n(".")])),_:1}),i(P,null,{default:l((()=>[n("”新增到我的小程式，\\n微信首頁下拉即可快速訪問店鋪")])),_:1})])),_:1}),i(H,{type:"primary",class:"close-btn",onClick:e[1]||(e[1]=t=>k.is_collection=!1)},{default:l((()=>[n("x")])),_:1})])),_:1},8,["style"])):d("",!0),k.is_homepush?(a(),o(F,{key:1,homepush_data:k.homepush_data},null,8,["homepush_data"])):d("",!0),i(I,{isShow:k.showSearch,onClose:b.closeSearch},null,8,["isShow","onClose"]),i(j)])),_:1},8,["data-theme","class"])}],["__scopeId","data-v-fccc1664"]]);export{U as default};
