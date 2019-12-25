# nameless-blog_apis
无名博客后端，基于php实现

## 接口详情
### 授权接口
需要登录后使用

| 接口名 | 描述 | 涉及表 |
| ------------ | :-----: | :---------: |
| aLogin | 自动登录  | S:me | 
| login | 登录  | S:me | 
| v0api | 状态/签名修改 | SU:me | 
| v1api | 草稿列表获取  | S:nit/ait | 
| v2api | 舍弃/创建草稿  | SUD:ni/n\*t/ai/a\*t | 
| v3api | 管理页面文章笔记获取  | S:ai/ni | 
| v4api | 置顶/取消置顶  | U:ai | 
| v5api | 文章删除  | -- | 
| v6api | 获取历史动态  | S:gossip | 
| v7api | 增添/删除动态  | ID:gossip | 
| v8api | 相册初始化/加载更多  | S:album | 
| v9api | 相册删除图片/上传图片  | SID:album | 
| v10api | 设置页面修改  | -- | 


| 接口名 | 描述 | 涉及表 |
| ------------ | :-----: | :---------: |
| initn | 编辑页面笔记创建/获取 | SI:n\*t/  S:note_category/tag_cloud | 
| initw | 编辑页面文章创建/获取 | SI:a\*t/  S:series_link/tag_cloud | 
| launch | 文章笔记发布 | -- | 
| mdimg | 文章笔记图片上传 | No Table | 
| saveTmp | 文章笔记保存  | U:a\*t/n\*t | 

### 普通接口
没有限制，大部分是读操作

| 接口名 | 描述 | 涉及表 |
| ------------ | :-----: | :---------: |
| apiv0 | 侧边栏初始化  | S:me | 
| apiv1 | 文章/笔记页content-main初始化接口,包括trivial的content-aside  | S:-- | 
| apiv2 | 文章content-main加载更多  | S:ai | 
| apiv3 | 文章/笔记获取接口  | SU:ai/ac/ni/nc | 
| apiv4 | 归档初始化  | S:ai/ni/tag_cloud/header_area | 
| apiv5 | 标签页面初始化&相关筛选  | S:ai/ni/tag_cloud | 
| apiv6 | 评论翻页  | S:comment/user | 
| apiv7 | 评论发布  | SUI:user/comment/ni/ai | 
| apiv8 | acg页面content-aside初始化  | S:gossip/header_area/album/ai | 
| apiv9 | 首页初始化&加载更多  | S:ai/gossip/notice | 
| apiv10 | 搜索接口，最多支持两个关键词  | S:ni/nc/ai/ac | 
| apiv11 | link页面获取链接  | S:header_area/user/link | 
| apiv12 | about页面初始化  | S:header_area | 
| apiv13 | 随机背景接口  | No Table | 
| apiv14 | 系列页面接口  | S:series_link/ai | 

## Log
修复&新增,**(SP为重要保存点)**
- 2019-10-24 a0eaedc 数据库重构前保存
### log01 **(SP评论表去冗余前)**
- [X] apiv1：note接口新增notice信息
- [X] 创建Series页面的接口apiv14
- [X] apiv7：评论接口新增机器人验证（简单数学题）
- [X] v10api：新系列的创建与要添加描述
- [X] mdimg：新增图片删除接口
### log02 
- [X] apiv6&apiv7：评论表去冗余(去除uname,ulink,avatar,to_uname)，原评论表结构保存为bak

### log03 **(SP数据库（文章系列相关）重组完成)**
- [X] 数据库（文章系列相关）重组完成
### log04 **(SP数据库（笔记分类相关）重组完成)**
- [X] 数据库（笔记分类相关）重组完成
### log04 **(SP数据库（标签管理相关）重组完成)**
- [X] 数据库（标签管理相关）重组完成
### log05
- [X] 评论区头像拉取功能支持
- [X] 评论区回复提醒功能实现
### log06
- [X] 新增番组接口
## 注意
源代码默认开发环境状态，如要投入生产，请按照下面修改
- `apiv7.php`中 **passthru**函数中的地址为php执行文件的绝对地址
- `utils/init.php`中 **Access-Control-Allow-Origin**修改为网站域名；define常量按实际修改