import{s as e,t as a,e as s,d as r,f as l,c as t,l as i,i as o,p as n,v as d}from"./element-plus-84a27f94.js";import{P as p}from"./plus-c5329662.js";import"./qs-b80b041e.js";import{_ as c,u}from"./index-5ae5860a.js";import{ap as m,o as _,c as g,a as h,P as f,S as b,W as y,$ as w,T as v,Q as j,a9 as k,X as x,Y as D,bb as I,b9 as C}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const{token:z}=u(),V={components:{},data:()=>({loading:!0,tableData:[],pageSize:20,totalDataNumber:0,curPage:1,formInline:{is_settled:"-1",order_no:"",search:"",user_id:"",token:z},open_edit:!1,userModel:{}}),props:{},watch:{$route(e,a){null!=e.query.user_id?this.formInline.user_id=e.query.user_id:this.formInline.user_id="",this.curPage=1,this.getData()}},created(){null!=this.$route.query.user_id&&(this.formInline.user_id=this.$route.query.user_id),this.getData()},methods:{handleCurrentChange(e){let a=this;a.curPage=e,a.loading=!0,a.getData()},getData(e){let a=this,s=a.formInline;s.page=a.curPage,s.list_rows=a.pageSize,p.agentOrder(s,!0).then((e=>{a.loading=!1,a.tableData=e.data.list.data,a.totalDataNumber=e.data.list.total})).catch((e=>{a.loading=!1}))},onSubmit(){this.loading=!0,this.getData()},onExport:function(){let e=this.formInline;this.$filter.onExportFunc("/index.php/shop/plus.agent.order/export",e)},handleSizeChange(e){this.curPage=1,this.pageSize=e,this.getData()},editClick(e){this.userModel=e,this.open_edit=!0},closeDialogFunc(e,a){"add"==a&&(this.open_add=e.openDialog,"success"==e.type&&this.getData()),"edit"==a&&(this.open_edit=e.openDialog,"success"==e.type&&this.getData())}}},S=e=>(I("data-v-45ec46ce"),e=e(),C(),e),N={class:"user"},P={class:"common-seach-wrap"},q={class:"product-content"},$={class:"table-wrap"},U={class:"pic"},E={alt:""},F={class:"info"},M={class:"name gray3"},A={class:"d-s-s d-c"},O={key:0,class:"d-s-c ww100 border-b-d"},Q={class:"referee-name text-ellipsis"},T=S((()=>h("span",{class:"gray9"},"一級分銷商：",-1))),W={class:"blue"},X={class:"referee-name text-ellipsis"},Y=S((()=>h("span",{class:"gray9"},"使用者ID：",-1))),B={class:"gray6"},G={class:"referee-name text-ellipsis"},H=S((()=>h("span",{class:"gray9"},"分銷佣金：",-1))),J={class:"orange"},K={key:1,class:"d-s-c ww100 border-b-d"},L={class:"referee-name text-ellipsis"},R=S((()=>h("span",{class:"gray9"},"二級分銷商：",-1))),Z={class:"blue"},ee={class:"referee-name text-ellipsis"},ae=S((()=>h("span",{class:"gray9"},"使用者ID：",-1))),se={class:"gray6"},re={class:"referee-name text-ellipsis"},le=S((()=>h("span",{class:"gray9"},"分銷佣金：",-1))),te={class:"orange"},ie={key:2,class:"d-s-c ww100 border-b-d"},oe={class:"referee-name text-ellipsis"},ne=S((()=>h("span",{class:"gray9"},"三級分銷商：",-1))),de={class:"blue"},pe={class:"referee-name text-ellipsis"},ce=S((()=>h("span",{class:"gray9"},"使用者ID：",-1))),ue={class:"gray6"},me={class:"referee-name text-ellipsis"},_e=S((()=>h("span",{class:"gray9"},"分銷佣金：",-1))),ge={class:"orange"},he={class:"orange"},fe={class:"fb orange"},be=S((()=>h("span",{class:"gray9"},"付款狀態：",-1))),ye=S((()=>h("span",{class:"gray9"},"發貨狀態：",-1))),we=S((()=>h("span",{class:"gray9"},"收貨狀態：",-1))),ve={key:0},je={key:0,class:"green"},ke={key:1,class:"red"},xe={key:1},De={class:"pagination"};const Ie=c(V,[["render",function(p,c,u,I,C,z){const V=e,S=a,Ie=s,Ce=r,ze=l,Ve=t,Se=i,Ne=o,Pe=n,qe=m("auth"),$e=m("img-url"),Ue=d;return _(),g("div",N,[h("div",P,[f(Ve,{size:"small",inline:!0,model:C.formInline,class:"demo-form-inline"},{default:b((()=>[f(Ie,{label:"佣金結算"},{default:b((()=>[f(S,{modelValue:C.formInline.is_settled,"onUpdate:modelValue":c[0]||(c[0]=e=>C.formInline.is_settled=e),placeholder:"是否結算佣金"},{default:b((()=>[f(V,{label:"全部",value:"-1"}),f(V,{label:"已結算",value:"1"}),f(V,{label:"未結算",value:"0"})])),_:1},8,["modelValue"])])),_:1}),f(Ie,{label:"訂單號"},{default:b((()=>[f(Ce,{size:"small",modelValue:C.formInline.order_no,"onUpdate:modelValue":c[1]||(c[1]=e=>C.formInline.order_no=e),placeholder:"請輸入訂單號"},null,8,["modelValue"])])),_:1}),f(Ie,{label:"買家暱稱/手機號"},{default:b((()=>[f(Ce,{modelValue:C.formInline.search,"onUpdate:modelValue":c[2]||(c[2]=e=>C.formInline.search=e),placeholder:"請輸入買家暱稱/手機號"},null,8,["modelValue"])])),_:1}),f(Ie,{label:"使用者id"},{default:b((()=>[f(Ce,{modelValue:C.formInline.user_id,"onUpdate:modelValue":c[3]||(c[3]=e=>C.formInline.user_id=e),placeholder:"請輸入使用者ID"},null,8,["modelValue"])])),_:1}),f(Ie,null,{default:b((()=>[f(ze,{type:"primary",onClick:z.onSubmit},{default:b((()=>[y("查詢")])),_:1},8,["onClick"])])),_:1}),f(Ie,null,{default:b((()=>[w((_(),v(ze,{size:"small",type:"success",onClick:z.onExport},{default:b((()=>[y("匯出")])),_:1},8,["onClick"])),[[qe,"/plus/agent/order/export"]])])),_:1})])),_:1},8,["model"])]),h("div",q,[h("div",$,[w((_(),v(Ne,{data:C.tableData,size:"small",border:"",style:{width:"100%"}},{default:b((()=>[f(Se,{prop:"order_master.order_no",label:"訂單號"}),f(Se,{prop:"order_master.create_time",label:"商品資訊"},{default:b((e=>[(_(!0),g(j,null,k(e.row.order_master.product,((e,a)=>(_(),g("div",{class:"product-info p-10-0",key:a},[h("div",U,[w(h("img",E,null,512),[[$e,e.image.file_path]])]),h("div",F,[h("div",M,x(e.product_name),1)])])))),128))])),_:1}),f(Se,{prop:"referee.value",label:"分銷商",width:"400"},{default:b((e=>[h("div",A,[e.row.first_user_id>0?(_(),g("div",O,[h("p",Q,[T,h("span",W,x(e.row.agent_first.nickName),1)]),h("p",X,[Y,h("span",B,x(e.row.agent_first.user_id),1)]),h("p",G,[H,h("span",J,"￥"+x(e.row.first_money),1)])])):D("",!0),e.row.second_user_id>0?(_(),g("div",K,[h("p",L,[R,h("span",Z,x(e.row.agent_second.nickName),1)]),h("p",ee,[ae,h("span",se,x(e.row.agent_second.user_id),1)]),h("p",re,[le,h("span",te,"￥"+x(e.row.second_money),1)])])):D("",!0),e.row.third_user_id>0?(_(),g("div",ie,[h("p",oe,[ne,h("span",de,x(e.row.agent_third.nickName),1)]),h("p",pe,[ce,h("span",ue,x(e.row.agent_third.user_id),1)]),h("p",me,[_e,h("span",ge,"￥"+x(e.row.third_money),1)])])):D("",!0)])])),_:1}),f(Se,{prop:"nickName",label:"單價/數量",width:"130"},{default:b((e=>[(_(!0),g(j,null,k(e.row.order_master.product,((e,a)=>(_(),g("div",{key:a},[h("span",he,"￥"+x(e.product_price),1),y(" ×"+x(e.total_num),1)])))),128))])),_:1}),f(Se,{prop:"order_master.pay_price",label:"實付款",width:"100"},{default:b((e=>[h("span",fe,x(e.row.order_master.pay_price),1)])),_:1}),f(Se,{prop:"order_master.user.nickName",label:"買家",width:"100"}),f(Se,{prop:"mobile",label:"交易狀態",width:"130"},{default:b((e=>[h("p",null,[be,y(" "+x(e.row.order_master.pay_status.text),1)]),h("p",null,[ye,y(" "+x(e.row.order_master.delivery_status.text),1)]),h("p",null,[we,y(" "+x(e.row.order_master.receipt_status.text),1)])])),_:1}),f(Se,{prop:"referee.value",label:"佣金結算",width:"70"},{default:b((e=>[0==e.row.is_invalid?(_(),g("div",ve,[1==e.row.is_settled?(_(),g("span",je,"已結算")):D("",!0),0==e.row.is_settled?(_(),g("span",ke,"未結算")):D("",!0)])):(_(),g("div",xe,"已取消"))])),_:1}),f(Se,{prop:"create_time",label:"時間"})])),_:1},8,["data"])),[[Ue,C.loading]])]),h("div",De,[f(Pe,{onSizeChange:z.handleSizeChange,onCurrentChange:z.handleCurrentChange,background:"","current-page":C.curPage,"page-size":C.pageSize,layout:"total, prev, pager, next, jumper",total:C.totalDataNumber},null,8,["onSizeChange","onCurrentChange","current-page","page-size","total"])])])])}],["__scopeId","data-v-45ec46ce"]]);export{Ie as default};
