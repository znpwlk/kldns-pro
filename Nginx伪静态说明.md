# Nginx 伪静态是什么

维护者：znpwlk

概念
- 伪静态并不是真的“静态文件”，而是利用 Nginx 的 URL 重写能力，让地址看起来像规范的静态路径，实际由后端动态程序处理。
- 常用指令包括 try_files、rewrite、location、map 等，通过匹配路径、重写参数、回退到入口脚本来实现。

为什么要用
- 链接更易读：语义化路径便于用户理解与记忆。
- 搜索友好：稳定的 URL 结构更利于索引与排名。
- 分享传播：无参数的短链接更适合在社交媒体传播。
- 缓存与代理：规范化路径更容易被 CDN/代理正确缓存与命中。

常见写法

通用入口路由（优先尝试真实文件，其次目录，最后回退到入口）

```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

基于正则的重写（将语义化路径映射到后端参数）

```
location / {
    rewrite ^/article/([0-9]+)/?$ /index.php?route=article&id=$1 last;
    rewrite ^/u/([a-zA-Z0-9_-]{3,32})$ /index.php?route=profile&name=$1 last;
}
```

统一结尾斜杠（规范化同一资源的多种写法）

```
map $request_uri $canonical {
    default $request_uri;
    ~^(.+)/$ $1;
}
server {
    if ($request_uri != $canonical) { return 301 $canonical; }
}
```

多语言路径前缀（预取语言，再在入口拼接）

```
map $uri $locale {
    default "";
    ~^/(en|zh|fr)/ $1;
}
location / {
    try_files $uri $uri/ /index.php?locale=$locale&$query_string;
}
```

关键注意点
- 顺序与匹配：location 的优先级与 rewrite 的执行阶段会影响结果，先规划全局匹配，再补充局部特例。
- 循环与重定向：确保规范化不会产生无限重写或跳转，返回 301 时检查是否会自我重定向。
- 边界与大小写：明确路径大小写规则与结尾斜杠策略，避免重复内容导致权重分散。
- 参数安全：后端读取路径参数时务必做白名单、类型校验，不要将用户输入直接拼接为文件系统路径。
- 入口回退：try_files 的最后一个回退目标要可控，确保不存在把任意路径注入到不安全处理链的风险。

总结
- 伪静态本质是“地址的外观约定 + 后端的语义解析”。良好的规则应该简单、稳定、可预期，便于被用户与搜索引擎理解，同时不牺牲安全性与维护性。