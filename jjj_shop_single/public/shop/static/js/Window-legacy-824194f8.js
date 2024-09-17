System.register(["./Setlink-legacy-37451578.js","./element-plus-legacy-4010b94c.js","./vuedraggable-legacy-8efc7d0e.js","./@vue-legacy-a5eb5da2.js","./index-legacy-a1d733aa.js","./article-legacy-d51f9f2c.js","./product-legacy-4257f291.js","./@vueuse-legacy-7e5b8ef2.js","./lodash-es-legacy-93596df5.js","./async-validator-legacy-aa1fd2de.js","./dayjs-legacy-4687536e.js","./call-bind-legacy-b90429f9.js","./get-intrinsic-legacy-4f34f38e.js","./has-symbols-legacy-afcc0593.js","./has-proto-legacy-d280ab1e.js","./function-bind-legacy-b76e1749.js","./has-legacy-06d86c07.js","./@element-plus-legacy-f19c70ad.js","./escape-html-legacy-ae962a8c.js","./normalize-wheel-es-legacy-13d39621.js","./@ctrl-legacy-33dbca08.js","./vue-legacy-34290877.js","./sortablejs-legacy-206512cd.js","./vue-router-legacy-b3eb717b.js","./pinia-legacy-9406b588.js","./vue-demi-legacy-97cfbb01.js","./axios-legacy-3cda2001.js","./qs-legacy-5fc46ed9.js","./side-channel-legacy-8f83241e.js","./object-inspect-legacy-2e2b0934.js","./vue-clipboard2-legacy-b2d9783e.js","./clipboard-legacy-b868140c.js","./vue-ueditor-wrap-legacy-53d1452b.js"],(function(l,e){"use strict";var n,t,a,s,u,i,c,o,r,d,m,g,f,y,p,v,k,h,j,b,I,x,V,_,w;return{setters:[function(l){n=l._},function(l){t=l.M,a=l.d,s=l.f,u=l.N,i=l.j,c=l.g,o=l.e,r=l.k,d=l.c},function(l){m=l.d},function(l){g=l.ae,f=l.ap,y=l.o,p=l.c,v=l.a,k=l.X,h=l.P,j=l.S,b=l.a1,I=l.W,x=l.Y,V=l.T,_=l.$},function(l){w=l._},null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null],execute:function(){var e={components:{Setlink:n,draggable:m},data:function(){return{is_linkset:!1,index:null}},created:function(){this.curItem.style.paddingTop=parseInt(this.curItem.style.paddingTop),this.curItem.style.paddingLeft=parseInt(this.curItem.style.paddingLeft)},props:["curItem","selectedIndex"],methods:{changeLink:function(l){this.is_linkset=!0,this.index=l},closeLinkset:function(l){this.is_linkset=!1,l&&(this.curItem.data[this.index].linkUrl=l.url,this.curItem.data[this.index].name="連結到 "+l.type+" "+l.name)}}},C={class:"common-form"},U=v("div",{class:"f16 gray3 form-subtitle"},"樣式設定",-1),z={class:"form-item"},L=v("div",{class:"form-label"},"底部背景：",-1),D={class:"flex-1 d-s-c",style:{height:"36px"}},B={class:"form-item"},S=v("div",{class:"form-label"},"上邊距：",-1),T={class:"form-item"},$=v("div",{class:"form-label"},"下邊距：",-1),A={class:"form-item"},E=v("div",{class:"form-label"},"圖片邊距：",-1),F=v("div",{class:"form-chink"},null,-1),R=v("div",{class:"f16 gray3 form-subtitle"},"佈局方式",-1),q={key:0,class:"red"},M=v("div",{class:"gray"},"請確保所有圖片的尺寸/比例相同。",-1),N=v("div",{class:"f16 gray3 form-subtitle",style:{"margin-bottom":"10px"}},[I(" 圖片設定 "),v("span",{class:"gray f12"},"請確保所有圖片的尺寸/比例相同；滑鼠拖拽左側圓點可調整導航順序")],-1),P={class:"d-c-c param-img-item navbar"},W={class:"d-c d-c-c",style:{"margin-right":"28px"}},X={class:"icon"},Y=["onClick"],G={class:"right"},H={class:"url-box mb16 flex-1 d-s-c ww100"},J=v("span",{class:"key-name"},"名稱",-1),K={class:"d-s-c ww100"},O={class:"url-box flex-1 d-s-c"},Q=v("span",{class:"key-name"},"連結",-1),Z={class:"d-c-c pb16"};l("default",w(e,[["render",function(l,e,m,w,ll,el){var nl=t,tl=a,al=s,sl=u,ul=i,il=c,cl=o,ol=g("CloseBold"),rl=r,dl=g("ArrowRight"),ml=g("draggable"),gl=d,fl=n,yl=f("img-url");return y(),p("div",null,[v("div",C,[v("span",null,k(m.curItem.name),1)]),h(gl,{size:"small",model:m.curItem,"label-width":"100px"},{default:j((function(){return[U,v("div",z,[L,v("div",D,[h(nl,{size:"default",modelValue:m.curItem.style.background,"onUpdate:modelValue":e[0]||(e[0]=function(l){return m.curItem.style.background=l})},null,8,["modelValue"]),h(tl,{class:"ml10",modelValue:m.curItem.style.background,"onUpdate:modelValue":e[1]||(e[1]=function(l){return m.curItem.style.background=l})},null,8,["modelValue"]),h(al,{style:{"margin-left":"10px"},onClick:e[2]||(e[2]=b((function(e){return l.$parent.onEditorResetColor(m.curItem.style,"background","#F2F2F2")}),["stop"])),type:"primary",link:""},{default:j((function(){return[I("重置")]})),_:1})])]),v("div",B,[S,h(sl,{modelValue:m.curItem.style.paddingTop,"onUpdate:modelValue":e[3]||(e[3]=function(l){return m.curItem.style.paddingTop=l}),size:"small","show-input":"","show-input-controls":!1,"input-size":"small"},null,8,["modelValue"])]),v("div",T,[$,h(sl,{modelValue:m.curItem.style.paddingBottom,"onUpdate:modelValue":e[4]||(e[4]=function(l){return m.curItem.style.paddingBottom=l}),size:"small","show-input":"","show-input-controls":!1,"input-size":"small"},null,8,["modelValue"])]),v("div",A,[E,h(sl,{modelValue:m.curItem.style.paddingLeft,"onUpdate:modelValue":e[5]||(e[5]=function(l){return m.curItem.style.paddingLeft=l}),size:"small","show-input":"","show-input-controls":!1,"input-size":"small"},null,8,["modelValue"])]),F,R,h(cl,{label:""},{default:j((function(){return[h(il,{modelValue:m.curItem.style.layout,"onUpdate:modelValue":e[6]||(e[6]=function(l){return m.curItem.style.layout=l}),size:"medium"},{default:j((function(){return[h(ul,{label:2},{default:j((function(){return[I("堆積兩列")]})),_:1}),h(ul,{label:3},{default:j((function(){return[I("堆積三列")]})),_:1}),h(ul,{label:4},{default:j((function(){return[I("堆積四列")]})),_:1}),h(ul,{label:-1},{default:j((function(){return[I("櫥窗樣式")]})),_:1})]})),_:1},8,["modelValue"]),-1==m.curItem.style.layout?(y(),p("div",q,"櫥窗樣式最多顯示四張圖片，超出隱藏")):x("",!0),M]})),_:1}),N,m.curItem.data&&m.curItem.data.length>0?(y(),V(ml,{key:0,modelValue:m.curItem.data,"onUpdate:modelValue":e[7]||(e[7]=function(l){return m.curItem.data=l}),group:"people","item-key":"index",class:"draggable-list"},{item:j((function(e){var n=e.element,t=e.index;return[v("div",P,[v("div",W,[v("div",X,[_(v("img",{alt:"",onClick:function(e){return l.$parent.onEditorSelectImage(n,"imgUrl")}},null,8,Y),[[yl,n.imgUrl]])])]),v("div",G,[h(rl,{class:"el-icon-DeleteFilled",onClick:function(e){return l.$parent.onEditorDeleleData(t,m.selectedIndex)}},{default:j((function(){return[h(ol)]})),_:2},1032,["onClick"]),v("div",H,[J,h(tl,{maxlength:"6","show-word-limit":"",disabled:"",value:"圖"+(t+1)},null,8,["value"])]),v("div",K,[v("div",O,[Q,h(tl,{onClick:function(l){return el.changeLink(t)},modelValue:n.linkUrl,"onUpdate:modelValue":function(l){return n.linkUrl=l}},{suffix:j((function(){return[h(rl,{color:"#333",size:"16px"},{default:j((function(){return[h(dl)]})),_:1})]})),_:2},1032,["onClick","modelValue","onUpdate:modelValue"])])])])])]})),_:1},8,["modelValue"])):x("",!0),v("div",Z,[h(al,{plain:"",type:"primary",onClick:l.$parent.onEditorAddData},{default:j((function(){return[I("+新增一個")]})),_:1},8,["onClick"])])]})),_:1},8,["model"]),ll.is_linkset?(y(),V(fl,{key:0,is_linkset:ll.is_linkset,onCloseDialog:el.closeLinkset},{default:j((function(){return[I("選擇連結")]})),_:1},8,["is_linkset","onCloseDialog"])):x("",!0)])}]]))}}}));
