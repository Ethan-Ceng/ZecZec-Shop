import{_ as t,u as s}from"./index-5ae5860a.js";import i from"./pay-3812cb55.js";import{F as e,K as a,L as o,ae as r,o as p,c as m,T as u,Y as n}from"./@vue-8fe4574d.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./call-bind-0c463fe3.js";import"./object-inspect-860361a9.js";import"./element-plus-84a27f94.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";import"./appsetting-d91e6d02.js";const h=e({components:{Pay:i},setup(){const{bus_emit:t,bus_off:i,bus_on:e}=s(),r=a({bus_emit:t,bus_off:i,bus_on:e,loading:!0,form:{},param:{},activeName:"",sourceList:[{key:"pay",value:"支付設定",path:"/appsetting/apph5/pay"}],tabList:[],paramsFlag:!1});return{...o(r)}},created(){this.tabList=this.authFilter(),this.tabList.length>0&&(this.activeName=this.tabList[0].key),null!=this.$route.query.type&&(this.activeName=this.$route.query.type),this.bus_on("activeValue",(t=>{this.activeName=t}));let t={active:this.activeName,list:this.tabList,tab_type:"apph5"};this.bus_emit("tabData",t)},beforeUnmount(){this.bus_emit("tabData",{active:null,tab_type:"apph5",list:[]}),this.bus_off("activeValue")},methods:{authFilter(){let t=[];for(let s=0;s<this.sourceList.length;s++){let i=this.sourceList[s];this.$filter.isAuth(i.path)&&t.push(i)}return t}}}),l={class:"common-seach-wrap"};const c=t(h,[["render",function(t,s,i,e,a,o){const h=r("Pay");return p(),m("div",l,["pay"==t.activeName?(p(),u(h,{key:0})):n("",!0)])}]]);export{c as default};
