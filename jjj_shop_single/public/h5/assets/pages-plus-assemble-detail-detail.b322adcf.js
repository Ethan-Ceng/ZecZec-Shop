import{_ as e,o as s,c as t,w as l,b as o,d as i,f as a,r as c,F as r,e as d,g as u,k as p,t as n,i as _,j as m,h,a as f,n as k,u as b,S as g,I as w,x as y,y as S,z as C,R as v,B as x,q as M,Y as P,X as A}from"./index-6e5c77a7.js";import{C as D}from"./countdown.ae2f98a7.js";import{M as j}from"./Mpservice.14b0894b.js";import{P as F}from"./uni-popup.f3f35788.js";import{j as H}from"./specSelect.07370fba.js";import{s as B}from"./mp-share.5df2fe0f.js";import{A as O}from"./app-share.6adf735f.js";import{_ as V}from"./fenxiang.6373a85a.js";/* empty css                                                                  */import"./share.0e953495.js";const I=e({components:{Popup:F,Countdown:D},data:()=>({isPopup:!1,type:0,width:600,height:1200,backgroundColor:"none",boxShadow:"none"}),props:["ismore","bill"],watch:{},created(){this.isPopup=this.ismore},methods:{rturnObjec:e=>({type:"text",startstamp:0,endstamp:e.end_time}),hidePopupFunc(){this.$emit("closeMore",!1)},goBill(e){this.$emit("gobill",e)}}},[["render",function(e,f,k,b,g,w){const y=u,S=_,C=m,v=p("Countdown"),x=h,M=p("Popup");return s(),t(M,{show:g.isPopup,width:g.width,height:g.height,type:"middle",backgroundColor:g.backgroundColor,boxShadow:g.boxShadow,padding:0,onHidePopup:w.hidePopupFunc},{default:l((()=>[o(y,{class:"more-bill-box"},{default:l((()=>[o(y,{class:"d-c-c p30 f30 border-b"},{default:l((()=>[i(" 正在拼團 ")])),_:1}),o(y,{class:"list"},{default:l((()=>[e.index<10?(s(!0),a(r,{key:0},c(k.bill,((e,a)=>(s(),t(y,{class:"item d-b-c p20 border-b",key:a},{default:l((()=>[o(y,{class:"d-s-c"},{default:l((()=>[o(y,{class:"photo"},{default:l((()=>[o(S,{src:e.user.avatarUrl,mode:"widthFix"},null,8,["src"])])),_:2},1024),o(y,{class:"d-s-c ml20 d-c lh150"},{default:l((()=>[o(y,{class:"d-s-c ww100"},{default:l((()=>[o(C,null,{default:l((()=>[i(n(e.user.nickName),1)])),_:2},1024),e.dif_people>0?(s(),t(C,{key:0,class:"ml10"},{default:l((()=>[i("還差"+n(e.dif_people)+"人",1)])),_:2},1024)):(s(),t(C,{key:1,class:"ml10"},{default:l((()=>[i("拼團已完成")])),_:1}))])),_:2},1024),o(y,{class:"ww100 gray9"},{default:l((()=>[o(v,{config:w.rturnObjec(e)},null,8,["config"])])),_:2},1024)])),_:2},1024)])),_:2},1024),o(y,{class:""},{default:l((()=>[o(x,{class:"btn-red",type:"default",onClick:s=>w.goBill(e)},{default:l((()=>[i("去拼單")])),_:2},1032,["onClick"])])),_:2},1024)])),_:2},1024)))),128)):d("",!0),k.bill.length>10?(s(),t(y,{key:1,class:"d-c-c p30 gray9 f28"},{default:l((()=>[i(" 僅顯示10個拼單 ")])),_:1})):d("",!0)])),_:1})])),_:1})])),_:1},8,["show","width","height","backgroundColor","boxShadow","onHidePopup"])}],["__scopeId","data-v-91307f65"]]);const U=e({components:{Spec:e({data:()=>({Visible:!1,form:{detail:{product_sku:{},show_sku:{},show_point_sku:{assemble_price:0}},show_sku:{sum:1}},stock:0,selectSpec:"",isAll:!1,clock:!1}),props:["isPopup","productModel"],onLoad(){},mounted(){},computed:{isadd:function(){return this.form.show_sku.sum>=this.stock||this.form.show_sku.sum>=this.form.detail.limit_num},issub:function(){return this.form.show_sku.sum<=1}},watch:{isPopup:function(e,s){e!=s&&(this.Visible=e,this.isOpenSpec||(this.form=this.productModel,this.isOpenSpec=!0,this.initShowSku(),this.form.specData&&this.form.specData.spec_attr.forEach(((e,s)=>{this.selectAttr(s,0)}))),this.form.type=this.productModel.type)},"form.specData":{handler(e,s){let t="",l="";if(this.isAll=!0,e){for(let s=0;s<e.spec_attr.length;s++)null==this.form.productSpecArr[s]?(this.isAll=!1,t+=e.spec_attr[s].group_name+" "):e.spec_attr[s].spec_items.forEach((e=>{this.form.productSpecArr[s]==e.item_id&&(l+='"'+e.spec_value+'" ')}));this.isAll?l="已選: "+l:t="請選擇: "+t}this.selectSpec=this.isAll?l:t},deep:!0,immediate:!0}},methods:{closePopup(){this.$emit("close",this.form.specData,null)},confirmFunc(){null==this.form.specData||this.isAll?(this.$emit("confirm"),this.createdOrder()):f({title:"請選擇規格",icon:"none",duration:2e3})},initShowSku(){this.form.show_sku.sku_image=this.form.detail.product.image[0].file_path,this.form.show_sku.assemble_price=this.form.detail.assemble_price,this.form.show_sku.product_sku_id=0,this.form.show_sku.line_price=this.form.detail.line_price,this.form.show_sku.assemble_stock=this.form.detail.stock,this.form.show_sku.assemble_product_sku_id=0,this.form.show_sku.sum=1,this.stock=this.form.detail.stock},selectAttr(e,s){let t=this,l=t.form.specData.spec_attr[e].spec_items,o=l[s];if(o.checked)o.checked=!1,t.form.productSpecArr[e]=null;else{for(let e=0;e<l.length;e++)l[e].checked=!1;o.checked=!0,t.form.productSpecArr[e]=o.item_id}H(t.form.specData.spec_attr,e,t.form.productSpecArr,t.form.productSku);let i=!0;for(let a=0;a<t.form.productSpecArr.length;a++){if(null==t.form.productSpecArr[a])return i=!1,void t.initShowSku()}i?t.updateSpecProduct():t.initShowSku()},updateSpecProduct(){let e=this,s=e.form.productSpecArr.join("_"),t=e.form.specData.spec_list.find((e=>e.spec_sku_id==s));if(t){if(e.clock=!1,"object"==typeof t){let s=e.form.detail.assembleSku.find((e=>e.product_sku_id==t.product_sku_id));e.stock=s.assemble_stock,e.form.show_sku.sum>e.stock&&(e.form.show_sku.sum=e.stock>0?e.stock:1),e.form.show_sku.product_sku_id=t.spec_sku_id,e.form.show_sku.assemble_price=s.assemble_price,e.form.show_sku.line_price=t.spec_form.product_price,e.form.show_sku.assemble_stock=s.assemble_stock,e.form.show_sku.assemble_product_sku_id=s.assemble_product_sku_id,t.spec_form.image_id>0?e.form.show_sku.sku_image=t.spec_form.image_path:e.form.show_sku.sku_image=e.form.detail.product.image[0].file_path}}else e.clock=!0},createdOrder(){let e=this,s=e.form.detail.assemble_product_id,t=e.form.show_sku.sum,l=e.form.show_sku.product_sku_id,o=e.form.show_sku.assemble_product_sku_id;e.gotoPage("/pages/order/confirm-order?product_num="+t+"&assemble_product_id="+s+"&product_sku_id="+l+"&assemble_product_sku_id="+o+"&assemble_bill_id="+e.form.show_sku.assemble_bill_id+"&order_type=assemble")},add(){if(!(this.stock<=0))return this.form.show_sku.sum>=this.stock?(f({title:"數量超過了庫存",icon:"none",duration:2e3}),!1):this.form.show_sku.sum>=this.form.detail.limit_num?(f({title:"數量超過了限購數量",icon:"none",duration:2e3}),!1):void this.form.show_sku.sum++},sub(){if(!(this.stock<=0))return this.form.show_sku.sum<2?(f({title:"商品數量至少為1",icon:"none",duration:2e3}),!1):void this.form.show_sku.sum--}}},[["render",function(e,p,f,y,S,C){const v=u,x=_,M=m,P=h,A=g,D=w;return s(),t(v,{class:k(S.Visible?"product-popup open":"product-popup close"),onTouchmove:p[5]||(p[5]=b((()=>{}),["stop","prevent"])),onClick:C.closePopup},{default:l((()=>[o(v,{class:"popup-bg"}),o(v,{class:"main",onClick:p[4]||(p[4]=b((()=>{}),["stop"]))},{default:l((()=>[o(v,{class:"header"},{default:l((()=>[o(x,{src:S.form.show_sku.sku_image,mode:"aspectFit",class:"avt"},null,8,["src"]),o(v,{class:"price d-s-c"},{default:l((()=>[null==S.form.specData||S.isAll?(s(),a(r,{key:0},[o(M,null,{default:l((()=>[i("¥")])),_:1}),o(M,{class:"num fb"},{default:l((()=>[i(n(S.form.show_sku.assemble_price),1)])),_:1}),o(M,{class:"old-price"},{default:l((()=>[i("¥"+n(S.form.show_sku.line_price),1)])),_:1})],64)):(s(),a(r,{key:1},[o(M,{class:"f22"},{default:l((()=>[i("¥")])),_:1}),o(M,{class:"f40 fb"},{default:l((()=>[i(n(S.form.detail.assemble_price),1)])),_:1})],64))])),_:1}),o(v,{class:"stock"},{default:l((()=>[i("庫存："+n(S.form.show_sku.assemble_stock),1)])),_:1}),o(v,{class:"p-20-0 select_spec"},{default:l((()=>[i(n(S.selectSpec),1)])),_:1}),o(v,{class:"close-btn",onClick:C.closePopup},{default:l((()=>[o(M,{class:"icon iconfont icon-guanbi"})])),_:1},8,["onClick"])])),_:1}),o(v,{class:"body"},{default:l((()=>[null!=S.form.specData?(s(),t(v,{key:0},{default:l((()=>[null!=S.form.specData?(s(),t(A,{key:0,"scroll-y":"true",class:"specs mt20",style:{"max-height":"600rpx"}},{default:l((()=>[(s(!0),a(r,null,c(S.form.specData.spec_attr,((e,u)=>(s(),t(v,{class:"specs mt20",key:u},{default:l((()=>[o(v,{class:"specs-hd p-20-0"},{default:l((()=>[o(M,{class:"f24 gray9"},{default:l((()=>[i(n(e.group_name),1)])),_:2},1024),null==S.form.productSpecArr[u]?(s(),t(M,{key:0,class:"ml10 red"},{default:l((()=>[i("請選擇"+n(e.group_name),1)])),_:2},1024)):d("",!0)])),_:2},1024),o(v,{class:"specs-list"},{default:l((()=>[(s(!0),a(r,null,c(e.spec_items,((e,o)=>(s(),t(P,{class:k(e.checked?"btn-checked":"btn-checke"),key:o,onClick:e=>C.selectAttr(u,o)},{default:l((()=>[i(n(e.spec_value),1)])),_:2},1032,["class","onClick"])))),128))])),_:2},1024)])),_:2},1024)))),128))])),_:1})):d("",!0)])),_:1})):d("",!0),o(v,{class:"level-box count_choose"},{default:l((()=>[o(M,{class:"key"},{default:l((()=>[i("數量")])),_:1}),o(v,{class:"d-s-c"},{default:l((()=>[o(v,{class:k(["icon-box minus d-c-c",{"num-wrap":!C.issub}]),onClick:p[0]||(p[0]=e=>C.sub())},{default:l((()=>[o(M,{class:"icon iconfont icon-jian",style:{"font-size":"20rpx",color:"#333333"}})])),_:1},8,["class"]),o(v,{class:"text-wrap"},{default:l((()=>[o(D,{type:"text",modelValue:S.form.show_sku.sum,"onUpdate:modelValue":p[1]||(p[1]=e=>S.form.show_sku.sum=e)},null,8,["modelValue"])])),_:1}),o(v,{class:k(["icon-box plus d-c-c",{"num-wrap":!C.isadd}]),onClick:p[2]||(p[2]=e=>C.add())},{default:l((()=>[o(M,{class:"icon iconfont icon-jia",style:{"font-size":"20rpx",color:"#333333"}})])),_:1},8,["class"])])),_:1})])),_:1})])),_:1}),o(v,{class:"btns white"},{default:l((()=>[S.clock?(s(),t(P,{key:1,class:"confirm-btn"},{default:l((()=>[i("暫無庫存")])),_:1})):(s(),t(P,{key:0,class:"confirm-btn",onClick:p[3]||(p[3]=e=>C.confirmFunc(S.form))},{default:l((()=>[i("確認")])),_:1}))])),_:1})])),_:1})])),_:1},8,["class","onClick"])}],["__scopeId","data-v-f22cd0b1"]]),Countdown:D,Mpservice:j,Bill:e({components:{Countdown:D},data:()=>({}),props:["bill"],created(){},methods:{rturnObjec:e=>({type:"text",startstamp:0,endstamp:e.end_time}),openMore(){this.$emit("openMore",!0)},goBill(e){this.$emit("gobill",e)}}},[["render",function(e,f,k,b,g,w){const y=m,S=u,C=_,v=p("Countdown"),x=h;return k.bill.length>0?(s(),t(S,{key:0,class:"bg-white mt20"},{default:l((()=>[o(S,{class:"group-hd p-0-20 d-a-c border-b"},{default:l((()=>[o(S,{class:"left"},{default:l((()=>[o(y,{class:"f30"},{default:l((()=>[i("這些人剛剛購買成功，可參與拼單")])),_:1})])),_:1}),o(S,{class:"right",onClick:w.openMore},{default:l((()=>[o(y,null,{default:l((()=>[i("檢視更多")])),_:1}),o(y,{class:"iconfont icon-you white"})])),_:1},8,["onClick"])])),_:1}),o(S,{class:"group-bd bill-user-list"},{default:l((()=>[(s(!0),a(r,null,c(k.bill,((e,a)=>(s(),t(S,{class:"item d-b-c p20",key:a},{default:l((()=>[o(S,{class:"userinfo d-s-c"},{default:l((()=>[o(S,{class:"photo"},{default:l((()=>[o(C,{src:e.user.avatarUrl,mode:"widthFix"},null,8,["src"])])),_:2},1024),o(y,{class:"ml10"},{default:l((()=>[i(n(e.user.nickName),1)])),_:2},1024)])),_:2},1024),o(S,{class:"btns d-s-c"},{default:l((()=>[o(S,{class:"d-s-c d-c"},{default:l((()=>[e.dif_people>0?(s(),t(y,{key:0,class:"ml10"},{default:l((()=>[i("還差"+n(e.dif_people)+"人成團",1)])),_:2},1024)):(s(),t(y,{key:1,class:"ml10"},{default:l((()=>[i("已完成拼團")])),_:1})),o(S,{class:"gray9"},{default:l((()=>[o(v,{config:w.rturnObjec(e)},null,8,["config"])])),_:2},1024)])),_:2},1024),o(x,{type:"primary",class:"btn-red ml20",onClick:s=>w.goBill(e)},{default:l((()=>[i("去拼單")])),_:2},1032,["onClick"])])),_:2},1024)])),_:2},1024)))),128))])),_:1})])),_:1})):d("",!0)}],["__scopeId","data-v-8bebeecf"]]),MoreBill:I,share:B,AppShare:O},data:()=>({from_type:10,phoneHeight:0,scrollviewHigh:0,loadding:!0,indicatorDots:!0,autoplay:!0,interval:2e3,duration:500,product_id:null,imagList:[],activeDetail:{},detail:{product_sku:{},show_sku:{assemble_price:"",product_sku_id:0,line_price:"",stock_num:0,sku_image:"",assemble_product_sku_id:0},show_point_sku:{}},bill:[],isPopup:!1,specData:null,productModel:{},assemble_product_id:0,countdownConfig:{startstamp:0,endstamp:0},isMpservice:!1,ismore:!1,assemble_bill_id:null,user_id:null,productSku:[],isMpShare:!1,isAppShare:!1,appParams:{title:"",summary:"",path:""},url:""}),onLoad(e){this.user_id=this.getUserId(),this.assemble_product_id=e.assemble_product_id,this.assemble_bill_id=e.assemble_bill_id,this.url=window.location.href},mounted(){this.init(),this.getData()},onShareAppMessage(){let e=this,s=e.getShareUrlParams({assemble_product_id:e.assemble_product_id});return{title:e.detail.product.product_name,path:"/pages/plus/assemble/detail/detail?"+s}},onHide(){},methods:{init(){let e=this;y({success(s){e.phoneHeight=s.windowHeight,S().select(".btns-wrap").boundingClientRect((s=>{let t=e.phoneHeight-s.height;e.scrollviewHigh=t})).exec()}})},getData(){let e=this;C({title:"載入中"}),e.loadding=!0,e._get("plus.assemble.product/detail",{assemble_product_id:e.assemble_product_id,url:e.url},(function(s){if(e.countdownConfig.startstamp=s.data.active.start_time,e.countdownConfig.endstamp=s.data.active.end_time,e.activeDetail=s.data.active,s.data.detail.product.content=v.format_content(s.data.detail.product.content),20==s.data.detail.product.spec_type&&e.initSpecData(s.data.detail.assembleSku,s.data.specData),e.detail=s.data.detail,e.bill=s.data.bill,""!=e.url){let t={assemble_product_id:e.assemble_product_id};e.configWx(s.data.share.signPackage,s.data.share.shareParams,t)}e.loadding=!1,x()}))},initSpecData(e,s){for(let t=0;t<e.length;t++){let s=e[t];if(s.productSku){let e=s.productSku.spec_sku_id.split("_").map(Number);this.productSku.push(e)}}for(let t in s.spec_attr)for(let e=0;e<s.spec_attr[t].spec_items.length;e++){let l=s.spec_attr[t].spec_items[e];this.hasSpec(l.item_id,t)?(l.checked=!1,l.disabled=!1):(s.spec_attr[t].spec_items.splice(e,1),e--)}this.specData=s},hasSpec(e,s){let t=!1;for(let l=0;l<this.productSku.length;l++){if(this.productSku[l][s]==e){t=!0;break}}return t},openPopup(e){if(1==this.activeDetail.is_single&&"order"==e&&0==this.bill.length||0==this.activeDetail.is_single&&"order"==e||"order"!=e){"order"==e&&null==this.assemble_bill_id&&(this.assemble_bill_id=0);let s={specData:this.specData,detail:this.detail,productSpecArr:null!=this.specData?new Array(this.specData.spec_attr.length):[],show_sku:{sku_image:"",seckill_price:0,product_sku_id:0,line_price:0,seckill_stock:0,seckill_product_sku_id:0,assemble_bill_id:this.assemble_bill_id,sum:1},productSku:this.productSku,type:e};this.productModel=s,this.isPopup=!0}else this.ismore=!0},closePopup(){this.isPopup=!1},openMaservice(){this.isMpservice=!0},closeMpservice(){this.isMpservice=!1},gotoProducntDetail(){let e="/pages/product/detail/detail?product_id="+this.detail.product_id;this.gotoPage(e)},openMoreFunc(e){this.ismore=e},gobillFunc(e){this.ismore=!1;for(let s=0;s<e.billUser.length;s++){let t=e.billUser[s];if(this.user_id===t.user_id){let s="/pages/plus/assemble/fight-group-detail/fight-group-detail?assemble_bill_id="+e.assemble_bill_id;return void this.gotoPage(s)}}this.assemble_bill_id=e.assemble_bill_id,this.openPopup()},returnValFunc(e){},showShare(){this.isMpShare=!0},closeBottmpanel(e){this.isMpShare=!1},closeAppShare(e){this.isAppShare=!1}},destroyed(){}},[["render",function(e,f,k,b,w,y){const S=_,C=A,v=P,x=u,D=m,j=p("Countdown"),F=h,H=p("Bill"),B=g,O=p("spec"),I=p("share"),U=p("AppShare"),$=p("Mpservice"),L=p("MoreBill");return s(),t(x,{class:"product-detail"},{default:l((()=>[w.loadding?d("",!0):(s(),t(B,{key:0,"scroll-y":"true",class:"scroll-Y",style:M("height:"+w.scrollviewHigh+"px;")},{default:l((()=>[o(x,{class:"product-pic"},{default:l((()=>[o(v,{class:"swiper","indicator-dots":w.indicatorDots,autoplay:w.autoplay,interval:w.interval,duration:w.duration},{default:l((()=>[(s(!0),a(r,null,c(w.detail.product.image,((e,i)=>(s(),t(C,{key:i},{default:l((()=>[o(S,{src:e.file_path,mode:""},null,8,["src"])])),_:2},1024)))),128))])),_:1},8,["indicator-dots","autoplay","interval","duration"])])),_:1}),o(x,{class:"limited-spike d-b-c m-0-20 mt20"},{default:l((()=>[o(D,{class:"left-name"},{default:l((()=>[i("限時拼團")])),_:1}),o(x,{class:"right"},{default:l((()=>[o(j,{ref:"countdown",config:w.countdownConfig,onReturnVal:y.returnValFunc},null,8,["config","onReturnVal"])])),_:1})])),_:1}),o(x,{class:"bg-white m-0-20 mb20 p30 bottom-radius"},{default:l((()=>[o(x,{class:"price-wrap pr"},{default:l((()=>[o(x,{class:"left"},{default:l((()=>[o(x,{class:"new-price"},{default:l((()=>[o(D,{class:"f24 redF6"},{default:l((()=>[i("￥")])),_:1}),o(D,{class:"num"},{default:l((()=>[i(n(w.detail.assemble_price),1)])),_:1})])),_:1}),o(D,{class:"old-price"},{default:l((()=>[i(n("￥"+w.detail.line_price),1)])),_:1})])),_:1}),o(x,{class:"share-box"},{default:l((()=>[o(F,{onClick:y.showShare,"open-type":"share"},{default:l((()=>[o(S,{class:"share_img",src:V,mode:""})])),_:1},8,["onClick"])])),_:1})])),_:1}),o(D,{class:"already-sale"},{default:l((()=>[i("已出售"+n(w.detail.product_sales)+"件",1)])),_:1}),o(x,{class:"product-name"},{default:l((()=>[i(n(w.detail.product.product_name),1)])),_:1}),o(x,{class:"product-describe"},{default:l((()=>[i(n(w.detail.product.selling_point),1)])),_:1})])),_:1}),o(H,{bill:w.bill,onOpenMore:y.openMoreFunc,onGobill:y.gobillFunc},null,8,["bill","onOpenMore","onGobill"]),o(x,{class:"product-content"},{default:l((()=>[o(x,{class:"group-hd border-b-e"},{default:l((()=>[o(x,{class:"d-s-c"},{default:l((()=>[o(x,{class:"pro_nameline"}),o(D,{class:"min-name f32 fb"},{default:l((()=>[i("商品介紹")])),_:1})])),_:1})])),_:1}),0==w.detail.product.is_picture?(s(),t(x,{key:0,class:"content-box",innerHTML:w.detail.product.content},null,8,["innerHTML"])):d("",!0),1==w.detail.product.is_picture?(s(),t(x,{key:1,class:"content-box"},{default:l((()=>[(s(!0),a(r,null,c(w.detail.product.contentImage,((e,i)=>(s(),t(x,{class:"ww100",key:i},{default:l((()=>[o(S,{class:"ww100",src:e.file_path,mode:"widthFix"},null,8,["src"])])),_:2},1024)))),128))])),_:1})):d("",!0)])),_:1})])),_:1},8,["style"])),o(x,{class:"btns-wrap d-s-c d-stretch"},{default:l((()=>[w.loadding?d("",!0):(s(),a(r,{key:0},[o(x,{class:"customer-service d-c-c"},{default:l((()=>[o(x,{class:"icon-box d-c-c",onClick:f[0]||(f[0]=s=>e.gotoPage("/pages/index/index"))},{default:l((()=>[o(F,{class:"d-c-c d-c bg-white"},{default:l((()=>[o(D,{class:"btn_btom pr icon iconfont icon-shouye gray3",style:{height:"50rpx","line-height":"60rpx"}}),o(D,{class:"f22 gray3",style:{height:"50rpx","line-height":"40rpx"}},{default:l((()=>[i("首頁")])),_:1})])),_:1})])),_:1}),o(x,{class:"icon-box",onClick:y.openMaservice},{default:l((()=>[o(F,{class:"d-c-c d-c bg-white"},{default:l((()=>[o(D,{class:"icon iconfont icon-kefu gray3",style:{height:"50rpx","line-height":"60rpx"}}),o(D,{class:"f22 gray3",style:{height:"50rpx","line-height":"40rpx"}},{default:l((()=>[i("客服")])),_:1})])),_:1})])),_:1},8,["onClick"])])),_:1}),o(x,{class:"buy-alone flex-1 d-c-c d-c",onClick:f[1]||(f[1]=e=>y.gotoProducntDetail())},{default:l((()=>[o(D,null,{default:l((()=>[i("￥"+n(w.detail.product.product_price),1)])),_:1}),o(F,{type:"primary"},{default:l((()=>[i("單獨購買")])),_:1})])),_:1}),o(x,{class:"make-group flex-1 d-c-c d-c",onClick:f[2]||(f[2]=e=>y.openPopup("order"))},{default:l((()=>[o(D,null,{default:l((()=>[i("￥"+n(w.detail.assemble_price),1)])),_:1}),o(F,{type:"primary"},{default:l((()=>[i("立即拼團")])),_:1})])),_:1})],64))])),_:1}),o(O,{isPopup:w.isPopup,productModel:w.productModel,onClose:y.closePopup},null,8,["isPopup","productModel","onClose"]),o(I,{isMpShare:w.isMpShare,onClose:y.closeBottmpanel},null,8,["isMpShare","onClose"]),o(U,{isAppShare:w.isAppShare,appParams:w.appParams,onClose:y.closeAppShare},null,8,["isAppShare","appParams","onClose"]),w.isMpservice?(s(),t($,{key:1,isMpservice:w.isMpservice,onClose:y.closeMpservice},null,8,["isMpservice","onClose"])):d("",!0),w.ismore?(s(),t(L,{key:2,bill:w.bill,ismore:!0,onCloseMore:y.openMoreFunc,onGobill:y.gobillFunc},null,8,["bill","onCloseMore","onGobill"])):d("",!0)])),_:1})}],["__scopeId","data-v-6470fe66"]]);export{U as default};
