import{_ as t,u as e}from"./index-5ae5860a.js";import s from"./index-7a04ab9c.js";import i from"./index-0b2023ba.js";import r from"./index-a92ff01c.js";import a from"./index-e3c09768.js";import o from"./index-6512e394.js";import{F as m,K as p,L as d,ae as n,o as u,c,T as l,Y as j}from"./@vue-8fe4574d.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./call-bind-0c463fe3.js";import"./object-inspect-860361a9.js";import"./element-plus-84a27f94.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";import"./card-8cac6905.js";import"./Edit-12121358.js";import"./Add-1ffabe72.js";import"./cancel-b2d93e41.js";import"./order-10f620ac.js";import"./vuedraggable-54f344de.js";import"./vue-a056b7b7.js";import"./sortablejs-88eb33dd.js";import"./Upload-71ca9325.js";import"./file-b7a04c7e.js";/* empty css                                                               */import"./AddCategory-69aab672.js";const h=m({components:{Card:s,CategoryIndex:i,Code:r,Order:a,SettingIndex:o},setup(){const{bus_emit:t,bus_off:s,bus_on:i}=e(),r=p({bus_emit:t,bus_off:s,bus_on:i,loading:!0,form:{},param:{},activeName:"",sourceList:[{key:"card",value:"卡券管理",path:"/plus/card/card/index"},{key:"category",value:"分類管理",path:"/plus/card/category/index"},{key:"code",value:"提貨碼管理",path:"/plus/card/code/index"},{key:"order",value:"提貨訂單",path:"/plus/card/order/index"},{key:"setting",value:"卡券設定",path:"/plus/card/setting/index"}],tabList:[],paramsFlag:!1});return{...d(r)}},created(){this.tabList=this.authFilter(),this.tabList.length>0&&(this.activeName=this.tabList[0].key),null!=this.$route.query.type&&(this.activeName=this.$route.query.type),this.bus_on("activeValue",(t=>{this.activeName=t}));let t={active:this.activeName,list:this.tabList,tab_type:"card"};this.bus_emit("tabData",t)},beforeUnmount(){this.bus_emit("tabData",{active:null,tab_type:"card",list:[]}),this.bus_off("activeValue")},methods:{authFilter(){let t=[];for(let e=0;e<this.sourceList.length;e++){let s=this.sourceList[e];this.$filter.isAuth(s.path)&&t.push(s)}return t}}}),v={class:"common-seach-wrap"};const b=t(h,[["render",function(t,e,s,i,r,a){const o=n("Card"),m=n("CategoryIndex"),p=n("Code"),d=n("Order"),h=n("SettingIndex");return u(),c("div",v,["card"==t.activeName?(u(),l(o,{key:0})):j("",!0),"category"==t.activeName?(u(),l(m,{key:1})):j("",!0),"code"==t.activeName?(u(),l(p,{key:2})):j("",!0),"order"==t.activeName?(u(),l(d,{key:3})):j("",!0),"setting"==t.activeName?(u(),l(h,{key:4})):j("",!0)])}]]);export{b as default};
