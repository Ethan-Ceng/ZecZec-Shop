import{s as e,t as a,e as l,d as t,f as i,c as s,l as o,i as n,p as d,q as r,v as c}from"./element-plus-84a27f94.js";import{D as u}from"./data-05520354.js";import{_ as p}from"./index-5ae5860a.js";import{F as m,K as g,w as h,L as b,o as f,T as C,S as _,P as S,W as z,a as V,c as I,Q as v,a9 as w,$ as y,X as k}from"./@vue-8fe4574d.js";const x=m({props:{is_open:Boolean,excludeIds:Array},setup(e){const a=g({loading:!0,curPage:1,pageSize:15,totalDataNumber:0,formInline:{grade_id:"",nickName:""},gradeList:[],tableData:[],sex:["女","男","未知"],multipleSelection:[],dialogVisible:!1});h((()=>e.is_open),((e,t)=>{e!=t&&(a.dialogVisible=e,e&&l())}),{deep:!0,immediate:!0});const l=()=>{a.loading=!0;let l=a.formInline;l.page=a.curPage,l.list_rows=a.pageSize,u.getUser(l,!0).then((l=>{if(a.loading=!1,e.excludeIds&&void 0!==e.excludeIds&&e.excludeIds.length>0){const a=e.excludeIds.map((e=>e.user_id));l.data.list.data.forEach((e=>{a.indexOf(e.user_id)>-1?e.noChoose=!1:e.noChoose=!0}))}a.tableData=l.data.list.data,a.totalDataNumber=l.data.list.total,a.gradeList=l.data.grade})).catch((e=>{a.loading=!1}))};return{...b(a),open:open,getTableList:l}},methods:{selectableFunc:e=>"boolean"!=typeof e.noChoose||e.noChoose,handleCurrentChange(e){this.curPage=e,this.getTableList()},handleSizeChange(e){this.curPage=1,this.pageSize=e,this.getTableList()},search(){this.curPage=1,this.tableData=[],this.getTableList()},confirmFunc(){let e=this.multipleSelection;this.emitFunc(e)},cancelFunc(e){this.emitFunc()},emitFunc(e){this.dialogVisible=!1,e&&void 0!==e?this.$emit("close",{type:"success",params:e}):this.$emit("close",{type:"error"})},selectUser(e){this.multipleSelection=e,this.confirmFunc()},handleSelectionChange(e){this.multipleSelection=e}}}),D={class:"common-seach-wrap"},F={class:"product-content"},L={class:"table-wrap"},N=["src"],P={class:"orange"},U={class:"pagination"};const j=p(x,[["render",function(u,p,m,g,h,b){const x=e,j=a,T=l,$=t,q=i,A=s,B=o,E=n,K=d,O=r,Q=c;return f(),C(O,{title:"選擇使用者",modelValue:u.dialogVisible,"onUpdate:modelValue":p[3]||(p[3]=e=>u.dialogVisible=e),onClose:u.cancelFunc,"close-on-click-modal":!1,"close-on-press-escape":!1,width:"800px"},{footer:_((()=>[S(q,{size:"small",onClick:p[2]||(p[2]=e=>u.dialogVisible=!1)},{default:_((()=>[z("取 消")])),_:1}),S(q,{size:"small",type:"primary",onClick:u.confirmFunc},{default:_((()=>[z("確 定")])),_:1},8,["onClick"])])),default:_((()=>[V("div",D,[S(A,{size:"small",inline:!0,model:u.formInline,class:"demo-form-inline"},{default:_((()=>[S(T,{label:"等級"},{default:_((()=>[S(j,{modelValue:u.formInline.grade_id,"onUpdate:modelValue":p[0]||(p[0]=e=>u.formInline.grade_id=e),placeholder:"請選擇會員等級",style:{width:"120px"}},{default:_((()=>[S(x,{label:"全部",value:"0"}),(f(!0),I(v,null,w(u.gradeList,((e,a)=>(f(),C(x,{key:a,label:e.name,value:e.grade_id},null,8,["label","value"])))),128))])),_:1},8,["modelValue"])])),_:1}),S(T,{label:"關鍵詞"},{default:_((()=>[S($,{placeholder:"請輸入微信暱稱|手機號|ID",modelValue:u.formInline.nickName,"onUpdate:modelValue":p[1]||(p[1]=e=>u.formInline.nickName=e)},null,8,["modelValue"])])),_:1}),S(T,null,{default:_((()=>[S(q,{icon:"Search",onClick:u.search},{default:_((()=>[z("查詢")])),_:1},8,["onClick"])])),_:1})])),_:1},8,["model"])]),V("div",F,[V("div",L,[y((f(),C(E,{data:u.tableData,size:"small",border:"",style:{width:"100%"},onSelectionChange:u.handleSelectionChange},{default:_((()=>[S(B,{prop:"user_id",label:"ID"}),S(B,{prop:"",label:"微信頭像",width:"70"},{default:_((e=>[V("img",{src:e.row.avatarUrl,class:"radius",width:"30",height:"30"},null,8,N)])),_:1}),S(B,{prop:"nickName",label:"暱稱"}),S(B,{prop:"mobile",label:"手機號"}),S(B,{prop:"balance",label:"使用者餘額"},{default:_((e=>[V("span",P,"￥"+k(e.row.balance),1)])),_:1}),S(B,{prop:"grade.name",label:"會員等級"}),S(B,{prop:"pay_money",label:"累積消費金額"}),S(B,{prop:"create_time",label:"註冊時間",width:"140"}),S(B,{type:"selection",selectable:u.selectableFunc,width:"45"},null,8,["selectable"])])),_:1},8,["data","onSelectionChange"])),[[Q,u.loading]])]),V("div",U,[S(K,{onSizeChange:u.handleSizeChange,onCurrentChange:u.handleCurrentChange,background:"","current-page":u.curPage,"page-sizes":[2,10,20,50,100],"page-size":u.pageSize,layout:"total, prev, pager, next, jumper",total:u.totalDataNumber},null,8,["onSizeChange","onCurrentChange","current-page","page-size","total"])])])])),_:1},8,["modelValue","onClose"])}]]);export{j as _};
