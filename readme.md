# 部署文檔

## jjj_shop_single 後臺 server

## jjj_shop_single_admin saas 管理後臺

admin
Zec@123456

## jjj_shop_single_shop shop 管理後臺

admin / Zec@123456

## jjj_shop_single_app h5 前端，對應測試接口業務邏輯可以參考

## ZecZec-Web 網站前臺 部署到 pc

nginx 靜態化配置

```
// 如果是访问admin的，则由thinkphp处理
location /admin {
    if (!-e $request_filename){
        rewrite  ^(.*)$  /index.php?s=$1  last;   break;
    }
}
 
// 如果是访问api的，则由thinkphp处理
location /api {
    if (!-e $request_filename){
        rewrite  ^(.*)$  /index.php?s=$1  last;   break;
    }
}
 
// 其他的由vue处理，并重新设置根目录为public下的dist内複製到public，优先访问index.html
location / {
    root   /project_root/public/pc;
    index  index.html index.htm;
    try_files $uri $uri/ /index.html;
}
```
