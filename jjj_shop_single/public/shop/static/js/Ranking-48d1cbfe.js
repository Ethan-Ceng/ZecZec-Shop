import{x as a,y as e}from"./element-plus-84a27f94.js";import{_ as s}from"./index-5ae5860a.js";import{ap as t,o as i,c as l,a as m,P as o,S as r,Q as d,a9 as n,X as c,$ as p,Y as u,W as v,bb as h,b9 as j}from"./@vue-8fe4574d.js";import"./@vueuse-3cd91d51.js";import"./lodash-es-656e9c2f.js";import"./async-validator-cf877c1f.js";import"./dayjs-9faf8871.js";import"./call-bind-0c463fe3.js";import"./get-intrinsic-bac01933.js";import"./has-symbols-456daba2.js";import"./has-proto-4a87f140.js";import"./function-bind-72d06d3b.js";import"./has-885c3436.js";import"./@element-plus-06559b09.js";import"./escape-html-1935ddb3.js";import"./normalize-wheel-es-3222b0a2.js";import"./@ctrl-91de2ec7.js";import"./vue-router-49f0fa2f.js";import"./pinia-265da389.js";import"./vue-demi-71ba0ef2.js";import"./axios-85bcd05e.js";import"./qs-b80b041e.js";import"./side-channel-ba7aab8a.js";import"./object-inspect-860361a9.js";import"./vue-clipboard2-f8268db2.js";import"./clipboard-dd0324d2.js";import"./vue-ueditor-wrap-dff311b3.js";const b={data:()=>({activeName:"sale",listData:[]}),inject:["dataModel"],created(){this.listData=this.dataModel.productSaleRanking},mounted(){},methods:{handleClick(a){this.activeName=a,"sale"==this.activeName?this.listData=this.dataModel.productSaleRanking:"view"==this.activeName?this.listData=this.dataModel.productViewRanking:"refund"==this.activeName&&(this.listData=this.dataModel.productRefundRanking)}}},f={class:"right-box d-s-s d-c"},y=(a=>(h("data-v-a7bc08d1"),a=a(),j(),a))((()=>m("div",{class:"lh30 f16 tl fb"},"商品排行榜",-1))),g={class:"ww100 mt10"},k={class:"list ww100"},w={key:0},N={class:"key-box"},_={key:0,alt:"",class:"ml10"},x={key:1,alt:"",class:"ml10"},D={key:2,alt:"",class:"ml10"},M={class:"text-ellipsis-2 flex-1 ml10"},R={class:"gray9 tr",style:{width:"80px"}},T={key:1,class:"tc pt30"};const C=s(b,[["render",function(s,h,j,b,C,P){const V=a,O=e,S=t("img-url");return i(),l("div",f,[y,m("div",g,[o(O,{modelValue:C.activeName,"onUpdate:modelValue":h[0]||(h[0]=a=>C.activeName=a),type:"card",onTabChange:P.handleClick},{default:r((()=>[o(V,{label:"銷量TOP10",name:"sale"}),o(V,{label:"瀏覽TOP10",name:"view"}),o(V,{label:"退款TOP10",name:"refund"})])),_:1},8,["modelValue","onTabChange"])]),m("div",k,[C.listData.length>0?(i(),l("ul",w,[(i(!0),l(d,null,n(C.listData,((a,e)=>(i(),l("li",{key:e,class:"d-s-c p-6-0 border-b-d"},[m("span",N,c(e+1),1),m("span",null,["sale"==C.activeName?p((i(),l("img",_,null,512)),[[S,a.image[0].file_path]]):u("",!0),"refund"==C.activeName?p((i(),l("img",x,null,512)),[[S,a.image[0].file_path]]):u("",!0),"view"==C.activeName?p((i(),l("img",D,null,512)),[[S,a.image[0].file_path]]):u("",!0)]),m("span",M,c(a.product_name),1),m("span",R,["sale"==C.activeName?(i(),l(d,{key:0},[v(" 銷量："+c(a.total_sales_num||0),1)],64)):u("",!0),"view"==C.activeName?(i(),l(d,{key:1},[v(" 瀏覽："+c(a.view_times),1)],64)):u("",!0),"refund"==C.activeName?(i(),l(d,{key:2},[v(" 退款："+c(a.refund_count),1)],64)):u("",!0)])])))),128))])):(i(),l("div",T,"暫無上榜記錄"))])])}],["__scopeId","data-v-a7bc08d1"]]);export{C as default};
