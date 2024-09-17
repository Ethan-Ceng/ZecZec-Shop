import{_ as e}from"./AddCategory-69aab672.js";import{E as t,u as a,f as i,w as l,p as s,q as o}from"./element-plus-84a27f94.js";import{F as n}from"./file-b7a04c7e.js";import r from"./AddCategory-74763e52.js";import{o as c,c as p,P as d,S as g,a as u,W as m,Q as h,a9 as f,M as C,X as y,a1 as j,Y as v,V as _,T as F}from"./@vue-8fe4574d.js";import{_ as k}from"./index-5ae5860a.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const b={components:{AddCategory:r},data:()=>({dialogWidth:"910px",activeType:null,typeList:[],imageUrl:null,dialogVisible:!0,fileList:[],addLoading:!1,this_config:{total:1},record:0,loading:!0,tableData:[],pageSize:36,totalDataNumber:0,curPage:1,isShowCategory:!1}),props:["config"],created(){this.getFileCategory(),this.getData()},methods:{getFileCategory(){let e=this;n.fileCategory({},!0).then((t=>{e.typeList=[{group_id:null,group_name:"全部"}].concat(t.data.group_list)})).catch((t=>{e.loading=!1}))},addCategory:function(){this.isShowCategory=!0},closeCategoryFunc(e){null!=e&&this.getFileCategory(),this.isShowCategory=!1},deleteCategoryFunc(e){this.$confirm("此操作將永久刪除該記錄, 是否繼續?","提示",{confirmButtonText:"確定",cancelButtonText:"取消",type:"warning"}).then((()=>{this.deleteCategory(e)})).catch((()=>{t({type:"info",message:"已取消刪除"})}))},deleteCategory(e){let a=this;n.deleteCategory({group_id:e}).then((e=>{t({message:"刪除成功",type:"success"}),a.getFileCategory()})).catch((e=>{t.error("刪除失敗")}))},selectTypeFunc(e){this.activeType=e,this.curPage=1,this.getData()},handleCurrentChange(e){this.curPage=e,this.getData()},handleSizeChange(e){this.curPage=1,this.pageSize=e,this.getData()},getData:function(){let e=this,t={page:e.curPage,pageSize:e.pageSize,group_id:e.activeType};n.fileList(t,!0).then((t=>{e.loading=!1,e.fileList=t.data.file_list,e.totalDataNumber=e.fileList.total})).catch((t=>{e.loading=!1}))},fileChange(e){},onBeforeUploadImage(e){const a="image/jpeg"===e.type||"image/jpg",i=e.size/1024/1024<1;return a||t.error("上傳檔案只能是圖片格式!"),i||t.error("上傳檔案大小不能超過 1MB!"),a&&i},UploadImage(e){let i=this;const l=a.service({lock:!0,text:"圖片上傳中,請等待",spinner:"el-icon-loading",background:"rgba(0, 0, 0, 0.7)"}),s=new FormData;s.append("iFile",e.file),n.uploadFile(s).then((t=>{l.close(),i.getData(),e.onSuccess()})).catch((a=>{l.close(),t({message:"本次上傳圖片失敗",type:"warning"}),e.onError()}))},selectFileFunc(e,a){if(e.selected)e.selected=!1,this.record--;else{if(this.record>=this.this_config.total)return void t({message:"本次最多隻能上傳 "+this.this_config.total+" 個檔案",type:"warning"});e.selected=!0,this.record++}this.fileList.data[a]=e},deleteFileFunc(e){},confirmFunc(){let e=[];for(let t=0;t<this.fileList.data.length;t++){let a=this.fileList.data[t];if(a.selected){let t={file_id:a.file_id,file_path:a.file_path};e.push(t)}}this.$emit("returnImgs",e)},cancelFunc(){this.$emit("returnImgs",null)}}},z={class:"upload-wrap"},w={class:"upload-wrap-inline mb16 clearfix"},S={class:"leval-item"},L={class:"leval-item upload-btn"},x={class:"fileContainer"},D={class:"file-type"},T=["onClick"],I={key:0,class:"operation"},P=u("p",null,[u("i",{class:"el-icon-edit"})],-1),U=["onClickCapture"],V=[u("i",{class:"el-icon-delete"},null,-1)],B={class:"file-content"},q={class:"fileContainerUI clearfix"},A=["onClick"],$={key:0,class:"selectedIcon"},N=[u("i",{class:"el-icon-check"},null,-1)],W=["scr"],E={class:"desc"},M={class:"bottom-shade"},Q=["onClick"],X={class:"pagination-wrap"},Y={class:"dialog-footer"};const G=k(b,[["render",function(t,a,n,r,k,b){const G=i,H=l,J=s,K=o,O=e;return c(),p("div",z,[d(K,{title:"檔案管理",modelValue:k.dialogVisible,"onUpdate:modelValue":a[0]||(a[0]=e=>k.dialogVisible=e),"close-on-click-modal":!1,"custom-class":"upload-dialog","close-on-press-escape":!1,onClose:b.cancelFunc,width:k.dialogWidth},{footer:g((()=>[u("div",Y,[d(G,{onClick:b.cancelFunc},{default:g((()=>[m("取 消")])),_:1},8,["onClick"]),d(G,{type:"primary",onClick:b.confirmFunc},{default:g((()=>[m("確 定")])),_:1},8,["onClick"])])])),default:g((()=>[u("div",w,[u("div",S,[d(G,{size:"small",icon:"Plus",onClick:b.addCategory},{default:g((()=>[m("新增分類")])),_:1},8,["onClick"])]),u("div",L,[d(H,{class:"avatar-uploader",multiple:"",ref:"upload",action:"string",accept:"image/jpeg,image/png,image/jpg","before-upload":b.onBeforeUploadImage,"http-request":b.UploadImage,"show-file-list":!1,"on-change":b.fileChange},{default:g((()=>[d(G,{size:"small",type:"primary"},{default:g((()=>[m("點選上傳")])),_:1})])),_:1},8,["before-upload","http-request","on-change"])])]),u("div",x,[u("div",D,[u("ul",null,[(c(!0),p(h,null,f(k.typeList,((e,t)=>(c(),p("li",{class:C(k.activeType==e.group_id?"item active":"item"),key:t,onClick:t=>b.selectTypeFunc(e.group_id)},[m(y(e.group_name)+" ",1),null!=e.group_id?(c(),p("div",I,[P,u("p",{onClickCapture:j((t=>b.deleteCategoryFunc(e.group_id)),["stop"])},V,40,U)])):v("",!0)],10,T)))),128))])]),u("div",B,[u("ul",q,[(c(!0),p(h,null,f(k.fileList.data,((e,t)=>(c(),p("li",{class:"file",key:t,onClick:a=>b.selectFileFunc(e,t)},[1==e.selected?(c(),p("div",$,N)):v("",!0),u("img",{scr:e.file_path,style:_("background-image:url("+e.file_path+")"),alt:""},null,12,W),u("p",E,y(e.file_name),1),u("div",M,[u("i",{class:"el-icon-delete",onClick:t=>b.deleteFileFunc(e)},null,8,Q)])],8,A)))),128))])])]),u("div",X,[d(J,{onSizeChange:b.handleSizeChange,onCurrentChange:b.handleCurrentChange,"current-page":k.curPage,"page-sizes":[12,24,36,42,48],"page-size":k.pageSize,layout:"total, sizes, prev, pager, next, jumper",total:k.totalDataNumber},null,8,["onSizeChange","onCurrentChange","current-page","page-size","total"])])])),_:1},8,["modelValue","onClose","width"]),k.isShowCategory?(c(),F(O,{key:0,onCloseCategory:b.closeCategoryFunc},null,8,["onCloseCategory"])):v("",!0)])}]]);export{G as default};
