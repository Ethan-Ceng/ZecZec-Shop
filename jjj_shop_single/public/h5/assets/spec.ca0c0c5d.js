import{_ as s,a as o,o as t,c as e,w as i,b as r,d as c,t as u,e as l,f as _,r as p,F as a,n as m,u as d,g as f,i as h,j as k,h as n,S as w,I as g}from"./index-6e5c77a7.js";const S=s({data:()=>({Visible:!1,form:{detail:{},show_sku:{sku_image:""}},stock:0,selectSpec:"",isOpenSpec:!1,type:"",clock:!1}),props:["isPopup","productModel"],onLoad(){},mounted(){},computed:{isadd:function(){return this.form.show_sku.sum>=this.stock||this.form.show_sku.sum>=this.form.detail.limit_num},issub:function(){return this.form.show_sku.sum<=1}},watch:{isPopup:function(s,o){s!=o&&(this.Visible=s,this.form=this.productModel,this.isOpenSpec=!0,this.initShowSku(),this.form.type=this.productModel.type)},"form.specData":{handler(s,o){let t="",e="";if(this.isAll=!0,s){for(let o=0;o<s.spec_attr.length;o++)null==this.form.productSpecArr[o]?(this.isAll=!1,t+=s.spec_attr[o].group_name+" "):s.spec_attr[o].spec_items.forEach((s=>{this.form.productSpecArr[o]==s.item_id&&(e+='"'+s.spec_value+'" ')}));this.isAll?e="已選: "+e:t="請選擇: "+t}this.selectSpec=this.isAll?e:t},deep:!0,immediate:!0}},methods:{initShowSku(){this.form.show_sku.sku_image=this.form.detail.image[0].file_path,this.form.show_sku.product_price=this.form.detail.product_price,20==this.form.detail.spec_type&&this.form.detail.product_price!=this.form.detail.product_max_price&&(this.form.show_sku.product_price=this.form.detail.product_price+"-"+this.form.detail.product_max_price),this.form.show_sku.spec_sku_id=0,this.form.show_sku.line_price=this.form.detail.line_price,this.form.show_sku.stock_num=this.form.detail.product_stock,this.form.show_sku.sum=1,this.stock=this.form.detail.product_stock,"advance"==this.form.plus_name&&(this.form.show_sku.product_price=this.form.plus_sku[0].product_price,this.form.show_sku.line_price="",this.form.show_sku.sku_image=this.form.plus_sku[0].productSku.image?this.form.plus_sku[0].productSku.image.file_path:this.form.detail.image[0].file_path,this.form.show_sku.stock_num=this.form.plus_sku[0].advance_stock,this.stock=this.form.plus_sku[0].advance_stock),"seckill"==this.form.plus_name&&(this.form.show_sku.product_price=this.form.plus_sku[0].seckill_price,this.form.show_sku.line_price=this.form.plus_sku[0].product_price,this.form.show_sku.sku_image=this.form.plus_sku[0].productSku.image?this.form.plus_sku[0].productSku.image.file_path:this.form.detail.image[0].file_path,this.form.show_sku.stock_num=this.form.plus_sku[0].seckill_stock,this.stock=this.form.plus_sku[0].seckill_stock)},selectAttr(s,o){let t=this,e=t.form.specData.spec_attr[s].spec_items,i=e[o];if(i.checked)i.checked=!1,t.form.productSpecArr[s]=null;else{for(let s=0;s<e.length;s++)e[s].checked=!1;i.checked=!0,t.form.productSpecArr[s]=i.item_id}for(let r=0;r<t.form.productSpecArr.length;r++)if(null==t.form.productSpecArr[r])return void t.initShowSku();t.updateSpecProduct()},updateSpecProduct(){let s=this,o=s.form.productSpecArr.join("_"),t=s.form.specData.spec_list;null!=s.form.plus_sku&&(t=s.form.plus_sku);let e=t.find((t=>s.form.plus_name?t.productSku.spec_sku_id==o:t.spec_sku_id==o));if(!e)return s.clock=!0,void s.initShowSku();s.clock=!1,s.form.plus_name&&!e.spec_form&&(e.spec_form=e.productSku),"object"==typeof e&&(s.form.plus_name?(s.stock=e[s.form.plus_name+"_stock"],s.form.show_sku.sum>s.stock&&(s.form.show_sku.sum=s.stock>0?s.stock:1)):(s.stock=e.spec_form.stock_num,s.form.show_sku.sum>s.stock&&(s.form.show_sku.sum=s.stock>0?s.stock:1)),s.form.show_sku.spec_sku_id=o,s.form.show_sku.product_price=e.spec_form.product_price,s.form.show_sku.line_price=e.spec_form.line_price,e.spec_form.image_id>0?s.form.show_sku.sku_image=e.spec_form.image_path:s.form.show_sku.sku_image=s.form.detail.image[0].file_path,s.form.show_sku.stock_num=e.spec_form.stock_num,s.form.plus_name&&(s.form.show_sku.product_price=e.product_price,"seckill"==s.form.plus_name&&(s.form.show_sku.product_price=e.seckill_price),s.form.show_sku.stock_num=e[s.form.plus_name+"_stock"],s.form.show_sku.line_price="",s.form.show_sku.sku_image=e.spec_form.image?e.spec_form.image.file_path:s.form.detail.image[0].file_path,s.form.show_sku.advance_product_id=e.spec_form.image?e.spec_form.image.file_path:s.form.detail.image[0].file_path))},closePopup(){this.$emit("close",this.form.specData,null)},confirmFunc(){if(null!=this.form.specData)for(let s=0;s<this.form.productSpecArr.length;s++)if(null==this.form.productSpecArr[s])return void o({title:"請選擇規格",icon:"none",duration:2e3});"card"==this.form.type?this.addCart():("order"==this.form.type||"deposit"==this.form.type)&&this.createdOrder()},addCart(){let s=this,t=s.form.detail.product_id,e=s.form.show_sku.sum,i=s.form.show_sku.spec_sku_id;if(20==s.form.detail.spec_type&&0==i)return o({title:"請選擇屬性",icon:"none",duration:2e3}),!1;s._post("order.cart/add",{product_id:t,total_num:e,spec_sku_id:i},(function(t){o({title:t.msg,duration:2e3}),s.$emit("close",null,t.data.cart_total_num)}))},createdOrder(){let s=this.form.detail.product_id,o=this.form.show_sku.sum,t=this.form.show_sku.spec_sku_id,e="";0!=this.room_id&""!=this.room_id&&(e="&room_id="+this.form.room_id);let i="/pages/order/confirm-order?product_id="+s+"&product_num="+o+"&product_sku_id="+t+"&order_type=buy"+e;if("deposit"==this.form.type){if("advance"==this.form.plus_name){let e=this.form.detail.advance.sku.find((s=>s.productSku.spec_sku_id==t));i="/pages/order/confirm-order?product_id="+s+"&product_num="+o+"&product_sku_id="+t+"&advance_product_sku_id="+e.advance_product_sku_id+"&advance_product_id="+e.advance_product_id+"&order_type=deposit"}if("seckill"==this.form.plus_name){let s=this.form.detail.secKill.seckillSku.find((s=>s.productSku.spec_sku_id==t));i="/pages/order/confirm-order?seckill_product_id="+s.seckill_product_id+"&product_num="+o+"&product_sku_id="+s.productSku.spec_sku_id+"&seckill_product_sku_id="+s.seckill_product_sku_id+"&order_type=seckill"}}this.gotoPage(i)},add(){if(!(this.stock<=0))return this.form.show_sku.sum>=this.stock?(o({title:"數量超過了庫存",icon:"none",duration:2e3}),!1):this.form.detail.limit_num>0&&this.form.show_sku.sum>=this.form.detail.limit_num?(o({title:"數量超過了限購數量",icon:"none",duration:2e3}),!1):void this.form.show_sku.sum++},sub(){if(!(this.stock<=0))return this.form.show_sku.sum<2?(o({title:"商品數量至少為1",icon:"none",duration:2e3}),!1):void this.form.show_sku.sum--}}},[["render",function(s,o,S,y,b,v){const A=f,x=h,C=k,P=n,D=w,j=g;return t(),e(A,{class:m(b.Visible?"product-popup open":"product-popup close"),onTouchmove:o[6]||(o[6]=d((()=>{}),["stop","prevent"])),onClick:v.closePopup},{default:i((()=>[r(A,{class:"popup-bg"}),r(A,{class:"main",onClick:o[5]||(o[5]=d((()=>{}),["stop"]))},{default:i((()=>[r(A,{class:"header"},{default:i((()=>[r(x,{onClick:o[0]||(o[0]=o=>s.yulan(b.form.show_sku.sku_image,0)),src:b.form.show_sku.sku_image,mode:"aspectFit",class:"avt"},null,8,["src"]),r(A,{class:"price"},{default:i((()=>[c(" ¥ "),r(C,{class:"num"},{default:i((()=>[c(u(b.form.show_sku.product_price),1)])),_:1}),b.form.show_sku.line_price?(t(),e(C,{key:0,class:"old-price"},{default:i((()=>[c("¥"+u(b.form.show_sku.line_price),1)])),_:1})):l("",!0)])),_:1}),r(A,{class:"stock"},{default:i((()=>[c("庫存："+u(b.form.show_sku.stock_num),1)])),_:1}),r(A,{class:"select_spec"},{default:i((()=>[c(u(b.selectSpec),1)])),_:1}),r(A,{class:"close-btn",onClick:v.closePopup},{default:i((()=>[r(C,{class:"icon iconfont icon-guanbi"})])),_:1},8,["onClick"])])),_:1}),r(A,{class:"body"},{default:i((()=>[r(A,null,{default:i((()=>[null!=b.form.specData?(t(),e(D,{key:0,"scroll-y":"true",class:"specs mt20",style:{"max-height":"600rpx"}},{default:i((()=>[(t(!0),_(a,null,p(b.form.specData.spec_attr,((s,o)=>(t(),e(A,{class:"specs mt20",key:o},{default:i((()=>[r(A,{class:"specs-hd p-20-0"},{default:i((()=>[r(C,{class:"f26 gray3"},{default:i((()=>[c(u(s.group_name),1)])),_:2},1024)])),_:2},1024),r(A,{class:"specs-list"},{default:i((()=>[(t(!0),_(a,null,p(s.spec_items,((s,r)=>(t(),e(P,{class:m(s.checked?"btn-checked":"btn-checke"),key:r,onClick:s=>v.selectAttr(o,r)},{default:i((()=>[c(u(s.spec_value),1)])),_:2},1032,["class","onClick"])))),128))])),_:2},1024)])),_:2},1024)))),128))])),_:1})):l("",!0)])),_:1}),r(A,{class:"level-box count_choose"},{default:i((()=>[r(C,{class:"key"},{default:i((()=>[c("數量")])),_:1}),r(A,{class:"d-s-c"},{default:i((()=>[r(A,{class:m(["icon-box minus d-c-c",{"num-wrap":!v.issub}]),onClick:o[1]||(o[1]=s=>v.sub())},{default:i((()=>[r(C,{class:"icon iconfont icon-jian",style:{"font-size":"20rpx",color:"#333333"}})])),_:1},8,["class"]),r(A,{class:"text-wrap"},{default:i((()=>[r(j,{type:"text",modelValue:b.form.show_sku.sum,"onUpdate:modelValue":o[2]||(o[2]=s=>b.form.show_sku.sum=s)},null,8,["modelValue"])])),_:1}),r(A,{class:m(["icon-box plus d-c-c",{"num-wrap":!v.isadd}]),onClick:o[3]||(o[3]=s=>v.add())},{default:i((()=>[r(C,{class:"icon iconfont icon-jia",style:{"font-size":"20rpx",color:"#333333"}})])),_:1},8,["class"])])),_:1})])),_:1})])),_:1}),r(A,{class:"btns"},{default:i((()=>[b.clock?(t(),e(P,{key:1,class:"confirm-btn"},{default:i((()=>[c("暫無庫存")])),_:1})):(t(),e(P,{key:0,class:"confirm-btn",onClick:o[4]||(o[4]=s=>v.confirmFunc(b.form))},{default:i((()=>[c("確認")])),_:1}))])),_:1})])),_:1})])),_:1},8,["class","onClick"])}],["__scopeId","data-v-8d50a132"]]);export{S as s};
