import{_ as t,D as e,o as n,c as a,w as s,g as i,b as o}from"./index-6e5c77a7.js";const r=t({data:()=>({type:"",content:""}),onLoad(t){this.type=t.type;let n="";n="service"==this.type?"使用者協議":"隱私協議",e({title:n}),this.getData()},methods:{getData(){let t=this;t._get("user.userapple/policy",{},(function(e){"service"==t.type?t.content=e.data.service:t.content=e.data.privacy}))}}},[["render",function(t,e,r,c,l,p){const d=i;return n(),a(d,null,{default:s((()=>[o(d,{innerHTML:l.content},null,8,["innerHTML"])])),_:1})}]]);export{r as default};
