import{_ as e,ac as t,A as a,z as s,a9 as o,J as i,B as n,o as c,c as m,g as p}from"./index-6e5c77a7.js";const u=e({data:()=>({imageList:[]}),onLoad(){},props:["num"],mounted(){this.chooseImageFunc()},methods:{chooseImageFunc(){let e=this;t({count:e.$props.num||9,sizeType:["original","compressed"],sourceType:["album","camera"],success:function(t){e.uploadFile(t.tempFilePaths)},fail:function(t){e.$emit("getImgs",null)},complete:function(e){}})},uploadFile:function(e){let t=this,c=0,m=e.length,p={token:a("token"),app_id:t.getAppId()};s({title:"圖片上傳中"}),e.forEach((function(e,a){o({url:t.websiteUrl+"/index.php?s=/api/file.upload/image",filePath:e,name:"iFile",formData:p,success:function(e){let a="object"==typeof e.data?e.data:JSON.parse(e.data);1===a.code?t.imageList.push(a.data):i({title:"提示",content:a.msg})},complete:function(){c++,m===c&&(n(),t.$emit("getImgs",t.imageList))}})}))}}},[["render",function(e,t,a,s,o,i){const n=p;return c(),m(n)}]]);export{u as U};
