import{_ as t,A as e,H as s,z as a,B as i,J as l,o,c,w as d,b as r,d as n,u as p,f as h,r as u,F as g,e as _,g as y,S as f,t as m,i as k,x as w,v as C,y as x,a as D,n as P,K as H,q as S,j as b,k as L,h as F}from"./index-6e5c77a7.js";import{s as v}from"./spec.ca0c0c5d.js";let N;const B=t({components:{spec:v,categoryMaskVue:t({components:{spec:v},props:["dataList"],data:()=>({show:!1,is_auto:0,platFormType:""}),methods:{open(){let t=e("TabBar");t&&(this.is_auto=t.is_auto);const a=s().uniPlatform;this.platFormType=a,this.$props.dataList&&this.$props.dataList.length>0&&(this.show=!this.show)},closeMask(){this.show=!1},addFunc(t){console.log("item",t);let e=this;a({title:"載入中"}),e._post("order.cart/add",{product_id:t.product_id,spec_sku_id:t.spec_sku_id,total_num:1},(function(t){i(),e.loadding=!1,e.$emit("get-shopping-num")}),(function(){e.loadding=!1}))},reduceFunc(t){let e=this;t.totalNum<=1||(a({title:"載入中"}),e._post("order.cart/sub",{product_id:t.product_id,spec_sku_id:t.spec_sku_id},(function(t){e.loadding=!1,i(),e.$emit("get-shopping-num")}),(function(){e.loadding=!1})))},clickDel(t){let e=this;l({title:"提示",content:"您確定要移除該商品嗎?",success(s){s.confirm&&e._post("order.cart/delete",{cart_id:t.cart_id},(function(t){e.$emit("get-shopping-num")}))}})},getCheckedIds(){let t=this,e=[];return t.$props.dataList&&t.$props.dataList.forEach((t=>{e.push(t.cart_id)})),e},onDelete(){let t=this,e=t.getCheckedIds();if(!e.length)return t.showError("您還沒有選擇商品"),!1;l({title:"提示",content:"您確定要清空購物車嗎?",success(s){s.confirm&&t._post("order.cart/delete",{cart_id:e.join()},(function(e){t.$emit("get-shopping-num")}))}})}}},[["render",function(t,e,s,a,i,l){const w=y,C=k,x=f;return i.show?(o(),c(w,{key:0,class:"product-list-mask",onClick:l.closeMask},{default:d((()=>[r(w,{class:"product-list-content"},{default:d((()=>[r(w,{class:"funList d-b-c"},{default:d((()=>[r(w,{class:"funList-l"},{default:d((()=>[n("已選商品")])),_:1}),r(w,{class:"funList-r",onClick:p(l.onDelete,["stop"])},{default:d((()=>[r(w,null,{default:d((()=>[n("清空購物車")])),_:1})])),_:1},8,["onClick"])])),_:1}),r(x,{"scroll-y":"true",style:{height:"600rpx"}},{default:d((()=>[(o(!0),h(g,null,u(s.dataList,(t=>(o(),c(w,{class:"product-item d-s-c",key:t},{default:d((()=>[r(w,{class:"mask-product-img"},{default:d((()=>[r(C,{class:"img",src:t.product_image,mode:"aspectFit"},null,8,["src"])])),_:2},1024),r(w,{class:"d-b-c d-c percent-w100 flex-1",style:{height:"100%"}},{default:d((()=>[r(w,{class:"mask-t"},{default:d((()=>[r(w,{class:"mask-product-title d-b-c"},{default:d((()=>[r(w,{class:"text-ellipsis"},{default:d((()=>[n(m(t.product_name),1)])),_:2},1024),r(w,{class:"iconfont icon-shanchu1",onClick:p((e=>l.clickDel(t)),["stop"])},null,8,["onClick"])])),_:2},1024),t.product_attr?(o(),c(w,{key:0},{default:d((()=>[n(" 已選【"+m(t.product_attr)+"】",1)])),_:2},1024)):_("",!0)])),_:2},1024),r(w,{class:"mask-b d-b-c"},{default:d((()=>[r(w,{class:"mask-price theme-price"},{default:d((()=>[n("￥"+m(t.product_price),1)])),_:2},1024),r(w,{class:"mask-action d-s-c"},{default:d((()=>[r(w,{class:"mask-minus",onClick:p((e=>l.reduceFunc(t)),["stop"])},{default:d((()=>[r(w,{class:"iconfont icon-jian"})])),_:2},1032,["onClick"]),r(w,{class:"mask-num"},{default:d((()=>[n(m(t.total_num),1)])),_:2},1024),r(w,{class:"mask-add",onClick:p((e=>l.addFunc(t)),["stop"])},{default:d((()=>[r(w,{class:"iconfont icon-jia"})])),_:2},1032,["onClick"])])),_:2},1024)])),_:2},1024)])),_:2},1024)])),_:2},1024)))),128))])),_:1})])),_:1})])),_:1},8,["onClick"])):_("",!0)}],["__scopeId","data-v-1f7e75d1"]])},data:()=>({loading:!0,searchName:"搜尋商品",show_type:3,style:1,phoneHeight:0,scrollviewHigh:0,listData:[],childlist:[],select_index:0,catename:"全部商品",productlist:[],page:1,category_id:0,tableData:[],isLogin:!1,shoppingNum:0,shoppingPrice:null,productModel:{},isPopup:!1,specData:null,detail:null,isDomHeight:!0,shoppingHeight:0,searchHeight:0,footerHeight:0,productArr:[],url:"",platFormType:"",osName:""}),onLoad(){const t=s().uniPlatform;let e="";w({success:function(t){e=t.osName}}),this.osName=e,this.platFormType=t,this.isWeixin()&&(this.url=window.location.href)},mounted(){this.init()},onReady(){C()},onShow(){this.productlist=[],this.page=1,this.no_more=!1,this.select_index=0,this.getData()},computed:{loadingType(){return this.loading?1:0!=this.productlist.length&&this.no_more?2:0}},onShareAppMessage(){return{title:this.templet.share_title,path:"/pages/product/category?"+this.getShareUrlParams()}},methods:{lookProduct(){this.$refs.categoryMaskRef.open()},isBuyFast(){if(this.isLogin&&(this.isLogin&&10==this.show_type&&4==this.style||20==this.show_type&&3==this.style)){let t=this.phoneHeight-this.searchHeight-this.shoppingHeight;return this.scrollviewHigh=t-this.footerHeight,!0}return this.scrollviewHigh=this.phoneHeight-this.searchHeight-this.footerHeight,!1},showTwo(){return 20==this.show_type&&(2==this.style||3==this.style)||10==this.show_type&&2==this.style},init(){let t=this;w({success(e){t.phoneHeight=e.windowHeight,x().select("#searchBox").boundingClientRect((e=>{t.searchHeight=e.height||0})).exec();let s=x().select("#footBottom");s&&s.boundingClientRect((e=>{e&&e.height&&(t.footerHeight=e.height)})).exec(),t.isDomHeight=!1}})},hasImages:t=>null!=t.images&&null!=t.images.file_path?t.images.file_path:"",getData(){let t=this;t.loading=!0,t._get("product.category/index",{},(function(e){t.show_type=e.data.template.category_style,t.style=e.data.template.wind_style,t.listData=e.data.list,t.listData&&t.listData.length>0&&(t.listData[0].child&&20==t.show_type?(t.category_id=t.listData[0].child[0].category_id,t.childlist=t.listData[0].child):t.category_id=t.listData[0].category_id),2==t.style?t.getProduct():(10==t.show_type&&4==t.style||20==t.show_type&&3==t.style)&&(t.getProduct(),t.getShoppingNum()),t.background=e.data.background,t.loading=!1}))},changeCategory(t){this.category_id=t,this.productlist=[],this.page=1,this.no_more=!1,this.getProduct()},getCheckedIds(){let t=[];return this.productArr.forEach((e=>{t.push(`${e.cart_id}`)})),t},Submit(){let t=this.getCheckedIds();if(0==t.length)return D({title:"請選擇商品",icon:"none"}),!1;this.gotoPage("/pages/order/confirm-order?order_type=cart&cart_ids="+t)},getShoppingNum(){let t=this;t._post("product.Category/lists",{},(function(e){const{data:{productList:s}}=e;if(t.isLogin=!1,s){t.isLogin=!0,t.tableData=s;let e=0,a=0,i=[];s&&s.length>0&&s.forEach((t=>{i.push(t),e+=t.total_num,a+=parseFloat(t.total_num)*parseFloat(t.product_price)})),t.productArr=i,t.shoppingNum=e,t.shoppingPrice=a.toFixed(2)}}),(e=>{t.productlist=[],t.page=1,t.no_more=!1,t.getData()}))},addShopping(t){20==t.spec_type?this.getSpecData(t.product_id):this.addSingleSpec(t.product_id)},addSingleSpec(t){let e=this;e._post("order.cart/add",{product_id:t,total_num:1,spec_sku_id:0},(function(t){e.getShoppingNum()}))},getSpecData(t){let e=this;e._get("product.product/detail",{product_id:t,url:e.url,visitcode:e.getVisitcode()},(function(t){t.data.specData?(e.isPopup=!1,e.detail=t.data.detail,e.specData=t.data.specData,e.initSpecData(t.data.specData)):D({title:"暫無規格，請於後臺新增!",mask:!1,duration:1500,icon:"none"})}))},initMaskPopup(){let t={specData:this.specData,detail:this.detail,productSpecArr:null!=this.specData?new Array(this.specData.spec_attr.length):[],show_sku:{sku_image:"",price:0,product_sku_id:0,line_price:0,stock:0,product_sku_id:0,sum:1},plus_sku:null,type:"card",plus_name:""};this.productModel=t,this.isPopup=!0},initSpecData(t){for(let e in t.spec_attr)for(let s in t.spec_attr[e].spec_items)t.spec_attr[e].spec_items[s].checked=!1;this.specData=t,this.initMaskPopup()},closePopup(){this.isPopup=!1,this.getShoppingNum()},scrolltolowerFunc(){let t=this;console.log(1),t.no_more||(t.page++,t.page<=t.last_page?t.getProduct():t.no_more=!0)},getProduct(){let t=this,e=t.page,s=t.category_id;t.sortType,t.sortPrice,t.loading=!0,t._get("product.product/lists",{page:e||1,category_id:s,search:"",sortType:"",sortPrice:"",list_rows:10},(function(e){t.loading=!1,t.productlist=t.productlist.concat(e.data.list.data),t.last_page=e.data.list.last_page,e.data.list.last_page<=1&&(t.no_more=!0)}))},selectCategory(t){!function(t,e=500,s=!0){s?N||(N=!0,"function"==typeof t&&t(),setTimeout((()=>{N=!1}),e)):N||(N=!0,setTimeout((()=>{N=!1,"function"==typeof t&&t()}),e))}((()=>{10==this.show_type?(this.select_index=t,this.catename=this.listData[this.select_index].name,this.changeCategory(this.listData[this.select_index].category_id)):this.listData[t].child?(this.childlist=this.listData[t].child,this.select_index=t,this.catename=this.listData[this.select_index].name,this.changeCategory(this.childlist[0].category_id)):(this.select_index=t,this.childlist=[],this.catename=this.listData[this.select_index].name,this.changeCategory(this.listData[this.select_index].category_id))}))},hasSelect(){},gotoList(t){let e=t;this.gotoPage("/pages/product/list/list?category_id="+e+"&sortType=all&search=&sortPrice=0")},wxGetUserInfo:function(t){if(!t.detail.iv)return D({title:"您取消了授權,登入失敗",icon:"none"}),!1},gotoSearch(){this.gotoPage("/pages/product/search/search")}}},[["render",function(t,e,s,a,i,l){const w=b,C=y,x=k,D=f,v=L("categoryMaskVue"),N=F,B=L("tabBar"),T=L("spec");return o(),c(C,{class:P(["category-wrap",t.theme()||""]),"data-theme":t.theme()},{default:d((()=>[r(C,{class:"index-search-box-cate d-b-c",id:"searchBox",style:S(0==t.topBarHeight()?"":"height:"+t.topBarHeight()+"px;padding-top:"+t.topBarTop()+"px")},{default:d((()=>[r(C,{class:"index-search-cate flex-1 t-c",onClick:l.gotoSearch},{default:d((()=>[H("span",{class:"icon iconfont icon-sousuo"}),r(w,{class:"ml10"},{default:d((()=>[n(m(i.searchName),1)])),_:1})])),_:1},8,["onClick"])])),_:1},8,["style"]),r(C,{class:"category-content"},{default:d((()=>[10==i.show_type&&3==i.style?(o(),c(C,{key:0,class:"cotegory-type cotegory-type-1"},{default:d((()=>[r(D,{"scroll-y":"true",class:"scroll-Y",style:S("height:"+i.scrollviewHigh+"px;")},{default:d((()=>[r(C,{class:"list cotegory-padding"},{default:d((()=>[(o(!0),h(g,null,u(i.listData,((t,e)=>(o(),c(C,{class:"item",key:e,onClick:e=>l.gotoList(t.category_id)},{default:d((()=>[r(C,{class:"pic"},{default:d((()=>[r(x,{src:l.hasImages(t),mode:"widthFix"},null,8,["src"])])),_:2},1024),r(C,{class:"p-20-0 tc f28"},{default:d((()=>[n(m(t.name),1)])),_:2},1024)])),_:2},1032,["onClick"])))),128))])),_:1})])),_:1},8,["style"])])),_:1})):_("",!0),20==i.show_type&&(1==i.style||2==i.style||3==i.style)||10==i.show_type&&(1==i.style||2==i.style||4==i.style)?(o(),c(C,{key:1,class:"cotegory-type cotegory-type-3"},{default:d((()=>[l.showTwo()?(o(),c(C,{key:0,class:"category-tab f-s-0"},{default:d((()=>[r(D,{"scroll-y":"true",class:"scroll-Y",style:S("height:"+i.scrollviewHigh+"px;")},{default:d((()=>[r(C,{class:"cotegory-padding"},{default:d((()=>[(o(!0),h(g,null,u(i.listData,((t,e)=>(o(),c(C,{class:P(i.select_index==e?"item active":"item"),key:e,onClick:t=>l.selectCategory(e)},{default:d((()=>[r(w,null,{default:d((()=>[n(m(t.name),1)])),_:2},1024)])),_:2},1032,["class","onClick"])))),128))])),_:1})])),_:1},8,["style"])])),_:1})):_("",!0),1==i.style&&20==i.show_type||4==i.style&&10==i.show_type?(o(),c(C,{key:1,class:"category-tab f-s-0"},{default:d((()=>[r(D,{"scroll-y":"true",class:"scroll-Y",style:S("height:"+i.scrollviewHigh+"px;")},{default:d((()=>[r(C,{class:"cotegory-padding"},{default:d((()=>[(o(!0),h(g,null,u(i.listData,((t,e)=>(o(),c(C,{class:P(i.select_index==e?"item active":"item"),key:e,onClick:t=>l.selectCategory(e)},{default:d((()=>[r(w,null,{default:d((()=>[n(m(t.name),1)])),_:2},1024)])),_:2},1032,["class","onClick"])))),128))])),_:1})])),_:1},8,["style"])])),_:1})):_("",!0),1==i.style&&20==i.show_type?(o(),c(C,{key:2,class:"cotegory-type cotegory-type-2 flex-1"},{default:d((()=>[r(D,{"scroll-y":"true",class:"scroll-Y",style:S("height:"+i.scrollviewHigh+"px;")},{default:d((()=>[r(C,{class:"list cotegory-padding"},{default:d((()=>[(o(!0),h(g,null,u(i.childlist,((t,e)=>(o(),c(C,{class:"item",key:e,onClick:e=>l.gotoList(t.category_id)},{default:d((()=>[r(x,{src:l.hasImages(t),mode:"widthFix"},null,8,["src"]),r(w,null,{default:d((()=>[n(m(t.name),1)])),_:2},1024)])),_:2},1032,["onClick"])))),128))])),_:1})])),_:1},8,["style"])])),_:1})):_("",!0),1==i.style&&10==i.show_type?(o(),c(C,{key:3,class:"cotegory-type cotegory-type-2 flex-1"},{default:d((()=>[r(D,{"scroll-y":"true",class:"scroll-Y",style:S("height:"+i.scrollviewHigh+"px;")},{default:d((()=>[r(C,{class:"list cotegory-padding"},{default:d((()=>[(o(!0),h(g,null,u(i.listData,((t,e)=>(o(),c(C,{class:"item",key:e,onClick:e=>l.gotoList(t.category_id)},{default:d((()=>[r(x,{src:l.hasImages(t),mode:"widthFix"},null,8,["src"]),r(w,null,{default:d((()=>[n(m(t.name),1)])),_:2},1024)])),_:2},1032,["onClick"])))),128))])),_:1})])),_:1},8,["style"])])),_:1})):_("",!0),2==i.style||3==i.style||4==i.style?(o(),c(C,{key:4,class:"category-content pr"},{default:d((()=>[r(D,{"scroll-y":"true",class:"scroll-Y scroll-3","lower-threshold":"10",onScrolltolower:l.scrolltolowerFunc,style:S("height:"+i.scrollviewHigh+"px;")},{default:d((()=>[20!=i.show_type||2!=i.style&&3!=i.style?_("",!0):(o(),c(C,{key:0,class:"catescroll2-top"},{default:d((()=>[(o(!0),h(g,null,u(i.childlist,((t,e)=>(o(),c(C,{onClick:e=>l.changeCategory(t.category_id),class:P(["catescroll2-top-item",{active:t.category_id==i.category_id}]),key:e},{default:d((()=>[n(m(t.name),1)])),_:2},1032,["onClick","class"])))),128))])),_:1})),r(C,{class:"cotegory-padding"},{default:d((()=>[(o(!0),h(g,null,u(i.productlist,((e,s)=>(o(),c(C,{class:"product-item-2",onClick:s=>t.gotoPage("/pages/product/detail/detail?product_id="+e.product_id),key:s},{default:d((()=>[r(x,{class:"product-image-2",src:e.product_image,mode:""},null,8,["src"]),r(C,{class:"flex-1 d-c d-b-s",style:{height:"154rpx"}},{default:d((()=>[r(C,{class:"text-ellipsis-2 f28 gray3"},{default:d((()=>[n(m(e.product_name),1)])),_:2},1024),r(C,{class:"theme-price f36 fb price-wrap"},{default:d((()=>[r(w,{class:"f24"},{default:d((()=>[n("￥")])),_:1}),n(" "+m(e.product_min_price)+" ",1),i.shoppingPrice&&1!=e.isActivity&&l.isBuyFast()&&1!=e.is_virtual&&""==e.custom_form?(o(),c(C,{key:0,class:"add-shopping-wrap theme-bg",onClick:p((t=>l.addShopping(e)),["stop"])},{default:d((()=>[r(C,{class:"icon iconfont icon-icozhuanhuan"})])),_:2},1032,["onClick"])):_("",!0)])),_:2},1024)])),_:2},1024)])),_:2},1032,["onClick"])))),128))])),_:1})])),_:1},8,["onScrolltolower","style"])])),_:1})):_("",!0)])),_:1})):_("",!0)])),_:1}),r(v,{ref:"categoryMaskRef",dataList:i.productArr,onGetShoppingNum:l.getShoppingNum},null,8,["dataList","onGetShoppingNum"]),l.isBuyFast()?(o(),c(C,{key:0,class:"shopping d-b-c customTabBar",id:"shopping"},{default:d((()=>[r(C,{class:"shopping-l d-s-c"},{default:d((()=>[r(C,{class:"shopping-circle",onClick:l.lookProduct},{default:d((()=>[r(C,{class:"shopping-icon icon iconfont icon-gouwuchefill"}),i.shoppingNum&&0!=i.shoppingNum?(o(),c(C,{key:0,class:"shopping-num"},{default:d((()=>[n(m(i.shoppingNum),1)])),_:1})):_("",!0)])),_:1},8,["onClick"]),r(C,{class:"shopping-price d-s-c"},{default:d((()=>[r(C,{class:"shopping-symbol"},{default:d((()=>[n("￥")])),_:1}),r(C,null,{default:d((()=>[n(m(i.shoppingPrice),1)])),_:1})])),_:1})])),_:1}),r(N,{class:"shopping-r",onClick:l.Submit},{default:d((()=>[n(" 去結算 ")])),_:1},8,["onClick"])])),_:1})):_("",!0),i.isDomHeight?(o(),c(C,{key:1,id:"footBottom"})):_("",!0),r(B,{isScroll:!0}),r(T,{isPopup:i.isPopup,productModel:i.productModel,onClose:l.closePopup},null,8,["isPopup","productModel","onClose"])])),_:1},8,["data-theme","class"])}],["__scopeId","data-v-2205ec79"]]);export{B as default};
