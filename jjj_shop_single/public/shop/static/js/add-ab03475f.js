import{E as t,f as i,v as o}from"./element-plus-84a27f94.js";import{_ as s,d as a}from"./index-5ae5860a.js";import{P as r}from"./page-ebe86fd4.js";import e from"./Type-7670cbe9.js";import m from"./Model-fad55089.js";import p from"./Params-62293f8d.js";import{ae as d,$ as j,o as l,c as n,a as c,T as u,Y as f,P as g,S as y,W as h}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";import"./Setpages-630958df.js";import"./Setcenter-0218f513.js";import"./Search-0935f4d9.js";import"./Banner-5c427b8a.js";import"./ImageSingle-fa2b6acb.js";import"./Window-5fbd721a.js";import"./Video-73fb7a69.js";import"./Article-bb7a0fa1.js";import"./Special-d4698b92.js";import"./Notice-ceeb01ba.js";import"./NavBar-4f9c8b12.js";import"./Product-a478d889.js";import"./product-6ff3546d.js";import"./Coupon-379001aa.js";import"./Store-2195931c.js";import"./Service-106d2c74.js";import"./RichText-89f899ff.js";import"./Blank-54df9f05.js";import"./Guide-6a00977f.js";import"./Seckill-6fcffa6b.js";import"./Preview-99cc19a4.js";import"./assembleProduct-5b4b7ad3.js";import"./BargainProduct-48da4e51.js";import"./Wxlive-5ab18b1f.js";import"./Title-ce0dba27.js";import"./Base-0390aef9.js";import"./Order-68f18054.js";import"./vuedraggable-54f344de.js";import"./vue-a056b7b7.js";import"./sortablejs-88eb33dd.js";import"./ShipinLive-b3464f4c.js";import"./store-4d27d007.js";import"./Upload-71ca9325.js";import"./file-b7a04c7e.js";/* empty css                                                               */import"./AddCategory-69aab672.js";import"./Setpages-64d4cd73.js";import"./Setcenter-caa449ec.js";import"./Search-e53f86d8.js";import"./Banner-6cfc876d.js";import"./Setlink-c577cec3.js";import"./article-f18c4871.js";import"./ImageSingle-fd22b26a.js";import"./Window-77cb9d97.js";import"./Video-a125dfc1.js";import"./Article-3de09f94.js";import"./Special-513bf0a5.js";import"./Notice-0bccc949.js";import"./NavBar-a01bec19.js";import"./Product-92b6711b.js";import"./Coupon-a98c82c9.js";import"./Store-1f63d091.js";import"./Service-bb82284d.js";import"./RichText-413d8367.js";import"./UE-97bf9096.js";import"./Blank-c2e85361.js";import"./Guide-2232b969.js";import"./Seckill-dc9c35a9.js";import"./Preview-278c3cdd.js";import"./assembleProduct-3e9da0fc.js";import"./BargainProduct-432d9c98.js";import"./Wxlive-d4600759.js";import"./Title-14c26775.js";import"./Base-1001d467.js";import"./Order-54200510.js";import"./Product-14aa5af4.js";import"./ShipinLive-f40490bf.js";const D={class:"diy-container clearfix"},v={class:"diy-menu"},S={class:"diy-phone"},b={class:"diy-info"},P={class:"common-button-wrapper"};const k=s({components:{Type:e,Model:m,Params:p},data:()=>({loading:!0,defaultData:{},diyData:{items:[]},opts:{},form:{umeditor:{},curItem:{},selectedIndex:-1}}),created(){this.getData()},methods:{getData(){let t=this;r.toAddPage({},!0).then((i=>{t.defaultData=i.data.defaultData,t.diyData=i.data.jsonData,t.form.curItem=t.diyData.page,t.opts=i.data.opts,t.loading=!1})).catch((i=>{t.loading=!1}))},onAddItem:function(t){let i=a(this.defaultData[t]);this.diyData.items.push(i),this.$refs.model.onEditer(this.diyData.items.length-1)},Submit(){let i=this,o=i.diyData;o.items.length<1?t({message:"至少要選擇一個元件",type:"warning"}):(i.loading=!0,r.addPage({params:JSON.stringify(o)},!0).then((o=>{i.loading=!1,t({message:"恭喜你，新增成功",type:"success"}),i.gotoBack()})).catch((t=>{i.loading=!1})))},gotoBack(){this.$router.back(-1)}}},[["render",function(t,s,a,r,e,m){const p=d("Type"),k=d("Model"),B=d("Params"),_=i,x=o;return j((l(),n("div",D,[c("div",v,[e.loading?f("",!0):(l(),u(p,{key:0,defaultData:e.defaultData},null,8,["defaultData"]))]),c("div",S,[e.loading?f("",!0):(l(),u(k,{key:0,ref:"model",form:e.form,defaultData:e.defaultData,diyData:e.diyData,isDiy:!0},null,8,["form","defaultData","diyData"]))]),c("div",b,[e.loading?f("",!0):(l(),u(B,{key:0,form:e.form,defaultData:e.defaultData,diyData:e.diyData,isDiy:!0},null,8,["form","defaultData","diyData"]))]),c("div",P,[g(_,{size:"small",type:"info",onClick:m.gotoBack},{default:y((()=>[h("返回上一頁")])),_:1},8,["onClick"]),g(_,{size:"small",type:"primary",onClick:s[0]||(s[0]=t=>m.Submit()),loading:e.loading},{default:y((()=>[h("儲存")])),_:1},8,["loading"])])])),[[x,e.loading]])}],["__scopeId","data-v-e5a5f955"]]);export{k as default};
