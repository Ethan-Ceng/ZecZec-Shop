import{a as e,E as a,s as l,t,e as s,d as i,f as r,c as o,l as n,i as d,p as c,q as u,v as p}from"./element-plus-84a27f94.js";import{T as m}from"./table-cf885400.js";import{_ as h}from"./index-5ae5860a.js";import{ap as b,o as g,c as _,a as j,P as f,S as C,Q as z,a9 as v,T as y,W as k,$ as x,X as D}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const S={class:"user"},w={class:"common-seach-wrap"},T={class:"product-content"},F={class:"table-wrap"},M={class:"pagination"},V={class:"gray"},L={class:"gray"},P={class:"gray"};const N=h({data:()=>({loading:!1,tableData:[],pageSize:15,totalDataNumber:0,curPage:1,searchForm:{table_id:0,search:""},dialogDetail:!1,currentModel:{tableM:{},user:{}},table_list:[]}),created(){this.getTableList()},methods:{handleCurrentChange(e){let a=this;a.curPage=e,a.loading=!0,a.getTableList()},handleSizeChange(e){this.curPage=1,this.pageSize=e,this.getTableList()},getTableList(){let e=this,a=e.searchForm;a.page=e.curPage,a.list_rows=e.pageSize,e.loading=!0,m.getRecordList(a,!0).then((a=>{e.loading=!1,e.tableData=a.data.list.data,e.totalDataNumber=a.data.list.total,e.table_list=a.data.table_list})).catch((e=>{}))},onSubmit(){this.curPage=1,this.getTableList()},detailClick(e){this.currentModel=e,this.dialogDetail=!0},deleteClick(l){let t=this;e.confirm("此操作將永久刪除該記錄, 是否繼續?","提示",{confirmButtonText:"確定",cancelButtonText:"取消",type:"warning"}).then((()=>{t.loading=!0,m.delRecord({table_record_id:l.table_record_id},!0).then((e=>{t.loading=!1,1==e.code?(a({message:"恭喜你，刪除成功",type:"success"}),t.getTableList()):t.loading=!1})).catch((e=>{t.loading=!1}))})).catch((()=>{}))},onExport:function(){let e=this.searchForm;e.table_id<=0?a({message:"請選擇表單",type:"error"}):this.$filter.onExportFunc("/index.php/shop/plus.table.record/export",e)}}},[["render",function(e,a,m,h,N,E){const U=l,q=t,B=s,I=i,R=r,$=o,A=n,Q=d,W=c,X=u,G=b("auth"),H=p;return g(),_("div",S,[j("div",w,[f($,{size:"small",inline:!0,model:N.searchForm,class:"demo-form-inline"},{default:C((()=>[f(B,{label:"選擇表單"},{default:C((()=>[f(q,{size:"small",modelValue:N.searchForm.table_id,"onUpdate:modelValue":a[0]||(a[0]=e=>N.searchForm.table_id=e),placeholder:"所有表單"},{default:C((()=>[f(U,{label:"全部",value:0}),(g(!0),_(z,null,v(N.table_list,((e,a)=>(g(),y(U,{key:a,label:e.name,value:e.table_id},null,8,["label","value"])))),128))])),_:1},8,["modelValue"])])),_:1}),f(B,{label:"會員資訊"},{default:C((()=>[f(I,{modelValue:N.searchForm.search,"onUpdate:modelValue":a[1]||(a[1]=e=>N.searchForm.search=e),placeholder:"請輸入暱稱|手機號|ID"},null,8,["modelValue"])])),_:1}),f(B,null,{default:C((()=>[f(R,{size:"small",type:"primary",icon:"Search",onClick:E.onSubmit},{default:C((()=>[k("查詢")])),_:1},8,["onClick"]),x((g(),y(R,{size:"small",type:"success",onClick:E.onExport},{default:C((()=>[k("匯出")])),_:1},8,["onClick"])),[[G,"/plus/table/record/export"]])])),_:1})])),_:1},8,["model"])]),j("div",T,[j("div",F,[x((g(),y(Q,{size:"small",data:N.tableData,border:"",style:{width:"100%"}},{default:C((()=>[f(A,{prop:"tableM.name",label:"表單名稱"}),f(A,{prop:"user.user_id",label:"使用者ID"}),f(A,{prop:"user.nickName",label:"使用者暱稱"}),f(A,{prop:"create_time",label:"新增時間"}),f(A,{fixed:"right",label:"操作",width:"110"},{default:C((e=>[f(R,{onClick:a=>E.detailClick(e.row),type:"text",size:"small"},{default:C((()=>[k("詳情")])),_:2},1032,["onClick"]),x((g(),y(R,{onClick:a=>E.deleteClick(e.row),type:"text",size:"small"},{default:C((()=>[k("刪除")])),_:2},1032,["onClick"])),[[G,"/plus/table/record/delete"]])])),_:1})])),_:1},8,["data"])),[[H,N.loading]])]),j("div",M,[f(W,{onSizeChange:E.handleSizeChange,onCurrentChange:E.handleCurrentChange,background:"","current-page":N.curPage,"page-size":N.pageSize,layout:"total, prev, pager, next, jumper",total:N.totalDataNumber},null,8,["onSizeChange","onCurrentChange","current-page","page-size","total"])])]),f(X,{title:"表單詳情",modelValue:N.dialogDetail,"onUpdate:modelValue":a[2]||(a[2]=e=>N.dialogDetail=e),width:"50%"},{default:C((()=>[f($,{size:"small"},{default:C((()=>[f(B,{label:"表單名稱："},{default:C((()=>[j("span",V,D(N.currentModel.tableM.name),1)])),_:1}),f(B,{label:"使用者資訊："},{default:C((()=>[j("span",L,D(N.currentModel.user.nickName)+"("+D(N.currentModel.user.user_id)+")",1)])),_:1}),(g(!0),_(z,null,v(N.currentModel.tableData,((e,a)=>(g(),y(B,{label:e.name+"：",key:a},{default:C((()=>[j("span",P,D(e.value),1)])),_:2},1032,["label"])))),128))])),_:1})])),_:1},8,["modelValue"])])}]]);export{N as default};
