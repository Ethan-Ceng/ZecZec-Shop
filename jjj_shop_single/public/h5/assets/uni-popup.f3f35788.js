import{_ as e,o as t,c as a,w as o,q as s,e as d,n as p,d as u,t as l,aa as i,g as n}from"./index-6e5c77a7.js";/* empty css                                                                  */const r=e({props:{show:{type:Boolean,default:!1},type:{type:String,default:"middle"},width:{type:Number,default:600},height:{type:Number,default:800},padding:{type:Number,default:30},backgroundColor:{type:String,default:"#ffffff"},boxShadow:{type:String,default:"0 0 30upx rgba(0, 0, 0, .1)"},msg:{type:String,default:""}},data(){let e=0;return e=0,{offsetTop:0}},methods:{hide:function(){this.$emit("hidePopup")}}},[["render",function(e,r,f,h,g,y){const c=n;return t(),a(c,null,{default:o((()=>[f.show?(t(),a(c,{key:0,class:"uni-mask",style:s({top:g.offsetTop+"px"}),onClick:y.hide},null,8,["style","onClick"])):d("",!0),f.show?(t(),a(c,{key:1,class:p(["uni-popup","uni-popup-"+f.type]),style:s("width:"+f.width+"rpx; height:"+f.height+"rpx;padding:"+f.padding+"rpx;background-color:"+f.backgroundColor+";box-shadow:"+f.boxShadow+";")},{default:o((()=>[""!=f.msg?(t(),a(c,{key:0,class:"popup-head"},{default:o((()=>[u(l(f.msg),1)])),_:1})):d("",!0),i(e.$slots,"default",{},void 0,!0)])),_:3},8,["class","style"])):d("",!0)])),_:3})}],["__scopeId","data-v-33b3807e"]]);export{r as P};
