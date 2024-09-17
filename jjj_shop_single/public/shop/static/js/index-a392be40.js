import{a,E as t,f as e,d as i,e as s,c as l,l as r,i as o,p as n,v as d}from"./element-plus-84a27f94.js";import{B as c}from"./bargain-d601917c.js";import{_ as p}from"./index-5ae5860a.js";import{o as m,c as u,a as h,P as g,S as b,W as j,$ as _,T as C}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const v={class:"bargain-active-index"},f={class:"d-b-c"},y={class:"mb18"},z={class:"product-content"},k={class:"table-wrap"},w={class:"pagination"};const x=p({data:()=>({loading:!0,searchForm:{search:""},tableData:[],pageSize:10,totalDataNumber:0,curPage:1}),created(){this.getData()},methods:{onSubmit(){this.curPage=1,this.getData()},getData(){let a=this,t=a.searchForm;t.page=a.curPage,t.list_rows=a.pageSize,a.loading=!0,c.bargainList(t,!0).then((t=>{a.loading=!1,a.tableData=t.data.list.data,a.totalDataNumber=t.data.list.total})).catch((a=>{}))},handleCurrentChange(a){this.curPage=a,this.getData()},handleSizeChange(a){this.curPage=1,this.pageSize=a,this.getData()},addClick(){this.$router.push("/plus/bargain/active/add")},editsClick(a){this.$router.push({path:"/plus/bargain/active/edit",query:{bargain_activity_id:a}})},deleteClick(e){let i=this;a.confirm("此操作將永久刪除該記錄, 是否繼續?","提示",{confirmButtonText:"確定",cancelButtonText:"取消",type:"warning"}).then((()=>{c.deleteBargain({bargain_activity_id:e},!0).then((a=>{t({message:a.msg,type:"success"}),i.getData()})).catch((a=>{}))})).catch((()=>{t({type:"info",message:"已取消刪除"})}))}}},[["render",function(a,t,c,p,x,D){const S=e,P=i,F=s,B=l,N=r,T=o,V=n,$=d;return m(),u("div",v,[h("div",f,[h("div",y,[g(S,{size:"small",icon:"Plus",type:"primary",onClick:D.addClick},{default:b((()=>[j("新增砍價活動")])),_:1},8,["onClick"])]),g(B,{size:"small",inline:!0,model:x.searchForm,class:"demo-form-inline"},{default:b((()=>[g(F,{label:""},{default:b((()=>[g(P,{modelValue:x.searchForm.search,"onUpdate:modelValue":t[0]||(t[0]=a=>x.searchForm.search=a),placeholder:"請輸入活動名稱"},null,8,["modelValue"])])),_:1}),g(F,null,{default:b((()=>[g(S,{type:"primary",icon:"Search",onClick:D.onSubmit},{default:b((()=>[j("查詢")])),_:1},8,["onClick"])])),_:1})])),_:1},8,["model"])]),h("div",z,[h("div",k,[_((m(),C(T,{size:"small",data:x.tableData,border:"",style:{width:"100%"}},{default:b((()=>[g(N,{prop:"title",label:"活動名稱"}),g(N,{prop:"start_time_text",label:"開始日期",width:"150"}),g(N,{prop:"end_time_text",label:"結束時間",width:"150"}),g(N,{prop:"status_text",label:"活動狀態",width:"120"}),g(N,{prop:"product_num",label:"產品數",width:"70"}),g(N,{prop:"total_sales",label:"訂單數",width:"70"}),g(N,{prop:"create_time",label:"建立時間",width:"150"}),g(N,{fixed:"right",label:"操作",width:"110"},{default:b((a=>[g(S,{onClick:t=>D.editsClick(a.row.bargain_activity_id),type:"text",size:"small"},{default:b((()=>[j("編輯")])),_:2},1032,["onClick"]),g(S,{onClick:t=>D.deleteClick(a.row.bargain_activity_id),type:"text",size:"small"},{default:b((()=>[j("刪除")])),_:2},1032,["onClick"])])),_:1})])),_:1},8,["data"])),[[$,x.loading]])]),h("div",w,[g(V,{onSizeChange:D.handleSizeChange,onCurrentChange:D.handleCurrentChange,background:"","current-page":x.curPage,"page-size":x.pageSize,layout:"total, prev, pager, next, jumper",total:x.totalDataNumber},null,8,["onSizeChange","onCurrentChange","current-page","page-size","total"])])])])}]]);export{x as default};
