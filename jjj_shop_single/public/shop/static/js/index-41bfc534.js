import{a as e,E as t,f as a,l as i,i as s,v as o}from"./element-plus-84a27f94.js";import{_ as l,A as r}from"./index-5ae5860a.js";import{ap as d,o as n,c as p,a as c,$ as m,T as u,S as h,W as j,P as b}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const f={class:"user"},g={class:"common-level-rail"},_={class:"product-content"},v={class:"table-wrap"};const k=l({components:{},inject:["reload"],data:()=>({loading:!0,tableData:[],formInline:{user:"",region:""},open_add:!1,open_edit:!1,userModel:{}}),created(){this.getTableList()},methods:{getTableList(){let e=this;r.roleList({},!0).then((t=>{e.loading=!1,e.tableData=t.data.list})).catch((t=>{e.loading=!1}))},addClick(){this.$router.push("/auth/role/add")},editClick(e){this.$router.push({path:"/auth/role/edit",query:{role_id:e.role_id}})},refresh(){this.reload()},deleteClick(a){let i=this;e.confirm("此操作將永久刪除該記錄, 是否繼續?","提示",{confirmButtonText:"確定",cancelButtonText:"取消",type:"warning"}).then((()=>{i.loading=!0,r.roleDelete({role_id:a.role_id},!0).then((e=>{i.loading=!1,1==e.code?(t({message:"恭喜你，該角色刪除成功",type:"success"}),i.getTableList()):i.loading=!1})).catch((e=>{i.loading=!1}))})).catch((()=>{}))}}},[["render",function(e,t,l,r,k,y){const C=a,w=i,x=s,z=d("auth"),T=o;return n(),p("div",f,[c("div",g,[m((n(),u(C,{size:"small",type:"primary",icon:"Plus",onClick:y.addClick},{default:h((()=>[j("新增角色")])),_:1},8,["onClick"])),[[z,"/auth/role/add"]])]),c("div",_,[c("div",v,[m((n(),u(x,{size:"small",data:k.tableData,border:"",style:{width:"100%"}},{default:h((()=>[b(w,{prop:"role_id",label:"角色ID"}),b(w,{prop:"role_name_h1",label:"角色名稱"}),b(w,{prop:"sort",label:"排序"}),b(w,{prop:"create_time",label:"新增時間"}),b(w,{fixed:"right",label:"操作",width:"110"},{default:h((e=>[m((n(),u(C,{onClick:t=>y.editClick(e.row),type:"text",size:"small"},{default:h((()=>[j("編輯")])),_:2},1032,["onClick"])),[[z,"/auth/role/edit"]]),m((n(),u(C,{onClick:t=>y.deleteClick(e.row),type:"text",size:"small"},{default:h((()=>[j("刪除")])),_:2},1032,["onClick"])),[[z,"/auth/role/delete"]])])),_:1})])),_:1},8,["data"])),[[T,k.loading]])])])])}]]);export{k as default};
