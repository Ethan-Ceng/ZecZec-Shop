import{_ as s,u as t}from"./index-bb8b9786.js";import{U as e}from"./user-e81d40bc.js";import{c as i,a as o,W as a,o as r}from"./@vue-5c89b57d.js";import"./vue-router-feb6ca35.js";import"./pinia-3964e703.js";import"./vue-demi-71ba0ef2.js";import"./element-plus-4e26fc63.js";import"./lodash-es-493ac664.js";import"./async-validator-cf877c1f.js";import"./@vueuse-e57ebffb.js";import"./dayjs-342c85a3.js";import"./call-bind-0966096f.js";import"./get-intrinsic-ccd8a43d.js";import"./has-symbols-456daba2.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-9b3bb84c.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./request-58949a77.js";import"./axios-85bcd05e.js";import"./qs-9a0be292.js";import"./side-channel-7d553c0c.js";import"./object-inspect-1e1e9601.js";const m=t(),n={class:"home-container"},p={class:"home-title"},j={class:"home-des"};const l=s({data:()=>({userInfo:m,admin_name:""}),created(){this.getData(),this.getTableList()},methods:{getData(){let s=this;e.base(!0).then((t=>{const e=t.data.settings;s.admin_name=e.admin_name})).catch((s=>{}))},getTableList(){e.getVersion({}).then((s=>{this.loading=!1,this.version=s.data.version})).catch((s=>{this.loading=!1}))}}},[["render",function(s,t,e,m,l,c){return r(),i("div",n,[o("h1",p,a(l.admin_name),1),o("p",j," 尊敬的 "+a(l.userInfo&&l.userInfo.username)+" 使用者，歡迎使用商城管理員系統 ",1)])}]]);export{l as default};
