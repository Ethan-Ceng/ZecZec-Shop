import{a as t,E as e,f as a,l as s,i,p as o,v as l}from"./element-plus-84a27f94.js";import{P as r}from"./Product-14aa5af4.js";import{P as d}from"./pointproduct-cdf8cedb.js";import{_ as p}from"./index-5ae5860a.js";import{ap as n,$ as c,o as u,c as m,a as g,T as h,S as j,W as b,P as _,X as f,Y as C}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./product-6ff3546d.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const y={components:{Product:r},data:()=>({tableData:[],pageSize:15,totalDataNumber:0,curPage:1,loading:!0,open_edit:!1,userModel:{},isproduct:!1,exclude_ids:[]}),created(){this.getTableList()},methods:{handleCurrentChange(t){let e=this;e.curPage=t,e.loading=!0,e.getTableList()},handleSizeChange(t){this.curPage=1,this.pageSize=t,this.getTableList()},getTableList(){let t=this,e={};e.page=t.curPage,e.list_rows=t.pageSize,d.productList(e,!0).then((e=>{t.exclude_ids=e.data.exclude_ids,t.tableData=e.data.list.data,t.totalDataNumber=e.data.list.total,t.loading=!1})).catch((e=>{t.loading=!1}))},changeProduct(t){this.isproduct=!0},closeProductFunc(t){this.isproduct=!1,"success"==t.type&&this.addClick(t.params.product_id)},addClick(t){this.$router.push("/plus/points/product/add?product_id="+t)},editClick(t){this.$router.push({path:"/plus/points/product/edit",query:{point_product_id:t}})},onSubmit(){let t=this;t.form,t.loading=!0,t.getTableList()},deleteClick(a){let s=this;t.confirm("此操作將永久刪除該記錄, 是否繼續?","提示",{confirmButtonText:"確定",cancelButtonText:"取消",type:"warning"}).then((()=>{s.loading=!0,d.deleteProduct({id:a},!0).then((t=>{s.loading=!1,e({message:t.msg,type:"success"}),s.getTableList()})).catch((t=>{s.loading=!1}))})).catch((()=>{s.loading=!1}))}}},k={class:"user"},v={class:"common-form d-s-c"},w=g("span",null,"活動商品",-1),x={class:"ml20"},z={class:"product-content point-list"},P={class:"table-wrap"},S={class:"product-info"},T={class:"pic",style:{width:"40px",height:"40px"}},D={alt:""},L={class:"info"},N={class:"name"},$={key:0},q={key:1},B={class:"orange fb"},F={key:0,class:"green"},I={key:1,class:"gray"},A={class:"pagination"};const E=p(y,[["render",function(t,e,d,p,y,E){const J=a,M=s,W=i,X=o,Y=r,G=n("auth"),H=n("img-url"),K=l;return c((u(),m("div",k,[g("div",v,[w,g("div",x,[c((u(),h(J,{type:"primary",size:"small",icon:"Plus",onClick:E.changeProduct},{default:j((()=>[b("選擇商品")])),_:1},8,["onClick"])),[[G,"/plus/points/product/add"]])])]),g("div",z,[g("div",P,[_(W,{size:"small",data:y.tableData,border:"",style:{width:"100%"}},{default:j((()=>[_(M,{prop:"product_name",label:"商品名稱",width:"360"},{default:j((t=>[g("div",S,[g("div",T,[c(g("img",D,null,512),[[H,t.row.product.image[0].file_path]])]),g("div",L,[g("div",N,f(t.row.product.product_name),1)])])])),_:1}),_(M,{label:"規格"},{default:j((t=>[10==t.row.product.spec_type?(u(),m("span",$,"單規格")):C("",!0),20==t.row.product.spec_type?(u(),m("span",q,"多規格")):C("",!0)])),_:1}),_(M,{label:"積分"},{default:j((t=>[b(f(t.row.sku[0].point_num),1)])),_:1}),_(M,{label:"金額"},{default:j((t=>[g("span",B,f(t.row.sku[0].point_money),1)])),_:1}),_(M,{prop:"limit_num",label:"限購數量"}),_(M,{prop:"stock",label:"活動庫存"}),_(M,{prop:"sort",label:"排序"}),_(M,{label:"狀態"},{default:j((t=>[10==t.row.status?(u(),m("span",F,"上架")):C("",!0),20==t.row.status?(u(),m("span",I,"下架")):C("",!0)])),_:1}),_(M,{fixed:"right",label:"操作",width:"110"},{default:j((t=>[c((u(),h(J,{onClick:e=>E.editClick(t.row.point_product_id),type:"text",size:"small"},{default:j((()=>[b("編輯")])),_:2},1032,["onClick"])),[[G,"/plus/points/product/edit"]]),c((u(),h(J,{onClick:e=>E.deleteClick(t.row.point_product_id),type:"text",size:"small"},{default:j((()=>[b("刪除")])),_:2},1032,["onClick"])),[[G,"/plus/points/product/delete"]])])),_:1})])),_:1},8,["data"])]),g("div",A,[_(X,{onSizeChange:E.handleSizeChange,onCurrentChange:E.handleCurrentChange,background:"","current-page":y.curPage,"page-size":y.pageSize,layout:"total, prev, pager, next, jumper",total:y.totalDataNumber},null,8,["onSizeChange","onCurrentChange","current-page","page-size","total"])])]),_(Y,{isproduct:y.isproduct,excludeIds:y.exclude_ids,islist:!1,onCloseDialog:E.closeProductFunc},null,8,["isproduct","excludeIds","onCloseDialog"])])),[[K,y.loading]])}]]);export{E as default};
