import{E as t,f as i,v as a}from"./element-plus-84a27f94.js";import{_ as o,d as s}from"./index-5ae5860a.js";import{P as e}from"./page-ebe86fd4.js";import r from"./Type-7670cbe9.js";import p from"./Model-fad55089.js";import m from"./Params-62293f8d.js";import{ae as d,$ as l,o as j,c as n,a as c,T as u,Y as f,P as g,S as y,W as D}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";import"./Setpages-630958df.js";import"./Setcenter-0218f513.js";import"./Search-0935f4d9.js";import"./Banner-5c427b8a.js";import"./ImageSingle-fa2b6acb.js";import"./Window-5fbd721a.js";import"./Video-73fb7a69.js";import"./Article-bb7a0fa1.js";import"./Special-d4698b92.js";import"./Notice-ceeb01ba.js";import"./NavBar-4f9c8b12.js";import"./Product-a478d889.js";import"./product-6ff3546d.js";import"./Coupon-379001aa.js";import"./Store-2195931c.js";import"./Service-106d2c74.js";import"./RichText-89f899ff.js";import"./Blank-54df9f05.js";import"./Guide-6a00977f.js";import"./Seckill-6fcffa6b.js";import"./Preview-99cc19a4.js";import"./assembleProduct-5b4b7ad3.js";import"./BargainProduct-48da4e51.js";import"./Wxlive-5ab18b1f.js";import"./Title-ce0dba27.js";import"./Base-0390aef9.js";import"./Order-68f18054.js";import"./vuedraggable-54f344de.js";import"./vue-a056b7b7.js";import"./sortablejs-88eb33dd.js";import"./ShipinLive-b3464f4c.js";import"./store-4d27d007.js";import"./Upload-71ca9325.js";import"./file-b7a04c7e.js";/* empty css                                                               */import"./AddCategory-69aab672.js";import"./Setpages-64d4cd73.js";import"./Setcenter-caa449ec.js";import"./Search-e53f86d8.js";import"./Banner-6cfc876d.js";import"./Setlink-c577cec3.js";import"./article-f18c4871.js";import"./ImageSingle-fd22b26a.js";import"./Window-77cb9d97.js";import"./Video-a125dfc1.js";import"./Article-3de09f94.js";import"./Special-513bf0a5.js";import"./Notice-0bccc949.js";import"./NavBar-a01bec19.js";import"./Product-92b6711b.js";import"./Coupon-a98c82c9.js";import"./Store-1f63d091.js";import"./Service-bb82284d.js";import"./RichText-413d8367.js";import"./UE-97bf9096.js";import"./Blank-c2e85361.js";import"./Guide-2232b969.js";import"./Seckill-dc9c35a9.js";import"./Preview-278c3cdd.js";import"./assembleProduct-3e9da0fc.js";import"./BargainProduct-432d9c98.js";import"./Wxlive-d4600759.js";import"./Title-14c26775.js";import"./Base-1001d467.js";import"./Order-54200510.js";import"./Product-14aa5af4.js";import"./ShipinLive-f40490bf.js";const h={class:"diy-container clearfix"},v={class:"diy-menu"},S={class:"diy-phone"},_={class:"diy-info"},b={class:"common-button-wrapper"};const P=o({components:{Type:r,Model:p,Params:m},data:()=>({loading:!0,defaultData:{},diyData:{items:[]},opts:{},form:{umeditor:{},curItem:{},selectedIndex:-1}}),created(){this.getData()},methods:{getData(){let t=this;t.page_id=t.$route.query.page_id,e.editPage({page_id:t.page_id},!0).then((i=>{t.defaultData=i.data.defaultData,t.diyData=i.data.jsonData,t.form.curItem=t.diyData.page,t.opts=i.data.opts,t.loading=!1})).catch((i=>{t.loading=!1}))},onAddItem:function(t){let i=s(this.defaultData[t]),a=0;this.form.selectedIndex<0?(a=0,this.diyData.items.unshift(i)):(a=this.form.selectedIndex+1,this.diyData.items.splice(a,0,i)),this.$refs.model.onEditer(a)},Submit(){let i=this;i.loading=!0;let a=i.diyData,o=i.page_id;e.pageEdit({params:JSON.stringify(a),page_id:o},!0).then((a=>{i.loading=!1,t({message:"恭喜你，修改成功",type:"success"}),i.getData()})).catch((t=>{i.loading=!1}))},gotoBack(){this.$router.back(-1)}}},[["render",function(t,o,s,e,r,p){const m=d("Type"),P=d("Model"),k=d("Params"),x=i,B=a;return l((j(),n("div",h,[c("div",v,[r.loading?f("",!0):(j(),u(m,{key:0,defaultData:r.defaultData},null,8,["defaultData"]))]),c("div",S,[r.loading?f("",!0):(j(),u(P,{key:0,diyType:"page",ref:"model",form:r.form,defaultData:r.defaultData,diyData:r.diyData,isDiy:!0},null,8,["form","defaultData","diyData"]))]),c("div",_,[r.loading?f("",!0):(j(),u(k,{key:0,diyType:"page",form:r.form,defaultData:r.defaultData,diyData:r.diyData,isDiy:!0},null,8,["form","defaultData","diyData"]))]),c("div",b,[g(x,{size:"small",type:"info",onClick:p.gotoBack},{default:y((()=>[D("返回上一頁")])),_:1},8,["onClick"]),g(x,{size:"small",type:"primary",onClick:o[0]||(o[0]=t=>p.Submit()),loading:r.loading},{default:y((()=>[D("儲存")])),_:1},8,["loading"])])])),[[B,r.loading]])}],["__scopeId","data-v-3c36653e"]]);export{P as default};
