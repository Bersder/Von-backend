# Von-backend
>Von 博客（原nameless-blog）的后端接口

[更新日志](./CHANGE_LOG.md)
## Using
前后端版本号共享，两者版本号需要匹配：**若前端版本为 V1，后端版本应选取 V2 满足 V1<=V2<=V1的下一个版本**

进入项目根目录安装依赖 `composer install`。源代码默认开发环境状态，如要投入使用，要稍作修改
- `apiv7.php, apiv3.php, apiv16p2.php`中 **passthru**函数中的按实际情况修改php执行文件的绝对地址，源码默认是`/usr/local/php/bin/php`
- `utils/init.php`中 **Access-Control-Allow-Origin**修改为网站域名；define常量按实际修改

php.ini 配置
- `allow_url_fopen` 设置 ON，启用`user_agent`并设置伪装
- `upload_max_filesize`按需设置，nginx也要对应配置
- `display_errors` 设置 OFF