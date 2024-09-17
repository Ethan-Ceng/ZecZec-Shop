System.register(["./element-plus-legacy-4010b94c.js","./table-legacy-63d68e8b.js","./index-legacy-a1d733aa.js","./@vue-legacy-a5eb5da2.js","./@vueuse-legacy-7e5b8ef2.js","./lodash-es-legacy-93596df5.js","./async-validator-legacy-aa1fd2de.js","./dayjs-legacy-4687536e.js","./call-bind-legacy-b90429f9.js","./get-intrinsic-legacy-4f34f38e.js","./has-symbols-legacy-afcc0593.js","./has-proto-legacy-d280ab1e.js","./function-bind-legacy-b76e1749.js","./has-legacy-06d86c07.js","./@element-plus-legacy-f19c70ad.js","./escape-html-legacy-ae962a8c.js","./normalize-wheel-es-legacy-13d39621.js","./@ctrl-legacy-33dbca08.js","./vue-router-legacy-b3eb717b.js","./pinia-legacy-9406b588.js","./vue-demi-legacy-97cfbb01.js","./axios-legacy-3cda2001.js","./qs-legacy-5fc46ed9.js","./side-channel-legacy-8f83241e.js","./object-inspect-legacy-2e2b0934.js","./vue-clipboard2-legacy-b2d9783e.js","./clipboard-legacy-b868140c.js","./vue-ueditor-wrap-legacy-53d1452b.js"],(function(e,l){"use strict";var t,a,n,i,u,c,s,r,o,g,d,p,h,y,b,f,j,m;return{setters:[function(e){t=e.a,a=e.E,n=e.f,i=e.l,u=e.i,c=e.p,s=e.v},function(e){r=e.T},function(e){o=e._},function(e){g=e.ap,d=e.o,p=e.c,h=e.a,y=e.$,b=e.T,f=e.S,j=e.W,m=e.P},null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null],execute:function(){var l={class:"user"},C={class:"common-level-rail"},v={class:"product-content"},z={class:"table-wrap"},k={class:"pagination"};e("default",o({data:function(){return{loading:!0,tableData:[],pageSize:15,totalDataNumber:0,curPage:1}},created:function(){this.getTableList()},methods:{handleCurrentChange:function(e){var l=this;l.curPage=e,l.loading=!0,l.getTableList()},handleSizeChange:function(e){this.curPage=1,this.pageSize=e,this.getTableList()},getTableList:function(){var e=this,l={};l.page=e.curPage,l.list_rows=e.pageSize,r.getList(l,!0).then((function(l){e.loading=!1,e.tableData=l.data.list.data,e.totalDataNumber=l.data.list.total})).catch((function(e){}))},addClick:function(){this.$router.push("/plus/table/table/add")},editClick:function(e){this.$router.push({path:"/plus/table/table/edit",query:{table_id:e.table_id}})},deleteClick:function(e){var l=this;t.confirm("此操作將永久刪除該記錄, 是否繼續?","提示",{confirmButtonText:"確定",cancelButtonText:"取消",type:"warning"}).then((function(){l.loading=!0,r.del({table_id:e.table_id},!0).then((function(e){l.loading=!1,1==e.code?(a({message:"恭喜你，刪除成功",type:"success"}),l.getTableList()):l.loading=!1})).catch((function(e){l.loading=!1}))})).catch((function(){}))}}},[["render",function(e,t,a,r,o,_){var S=n,w=i,x=u,T=c,P=g("auth"),D=s;return d(),p("div",l,[h("div",C,[y((d(),b(S,{size:"small",type:"primary",icon:"Plus",onClick:_.addClick},{default:f((function(){return[j("新增表單")]})),_:1},8,["onClick"])),[[P,"/plus/table/table/add"]])]),h("div",v,[h("div",z,[y((d(),b(x,{size:"small",data:o.tableData,border:"",style:{width:"100%"}},{default:f((function(){return[m(w,{prop:"name",label:"表單名稱"}),m(w,{prop:"total_count",label:"表單記錄"}),m(w,{prop:"create_time",label:"新增時間"}),m(w,{fixed:"right",label:"操作",width:"120"},{default:f((function(e){return[y((d(),b(S,{onClick:function(l){return _.editClick(e.row)},type:"text",size:"small"},{default:f((function(){return[j("編輯")]})),_:2},1032,["onClick"])),[[P,"/plus/table/table/edit"]]),y((d(),b(S,{onClick:function(l){return _.deleteClick(e.row)},type:"text",size:"small"},{default:f((function(){return[j("刪除")]})),_:2},1032,["onClick"])),[[P,"/plus/table/table/delete"]])]})),_:1})]})),_:1},8,["data"])),[[D,o.loading]])]),h("div",k,[m(T,{onSizeChange:_.handleSizeChange,onCurrentChange:_.handleCurrentChange,background:"","current-page":o.curPage,"page-size":o.pageSize,layout:"total, prev, pager, next, jumper",total:o.totalDataNumber},null,8,["onSizeChange","onCurrentChange","current-page","page-size","total"])])])])}]]))}}}));
