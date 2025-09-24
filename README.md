# kldns-pro

一个面向大众的 DNS/域名管理与运维辅助项目，基于 PHP/Laravel 架构进行修复优化与安全加固，强调可用性、稳定性与清晰的交互体验。

## 项目简介
- 目标：提供更顺手的后台与用户侧体验，降低运维成本，提升可观察性与安全性
- 特点：简洁交互、清晰导航、统一异常处理、验证码稳定、可缩放侧边栏
- 受众：个人站长、中小企业运维团队、对安全与稳定有要求的使用者

## 安全性
- 后端统一异常处理，避免不必要的 4xx 网络错误弹窗
- 验证码与会话管理稳定，降低误判与拦截
- 避免在前端、日志中暴露敏感信息，减少攻击面
- 支持常见 Web 服务器的安全部署（Apache/Nginx）

## 部署
项目默认提供 Apache 的 .htaccess 重写支持；使用 Nginx 时需要在服务配置里开启“伪静态”（重写到前端控制器）。

### Apache（已内置 .htaccess）
- 直接部署到支持 .htaccess 的虚拟主机或 Apache 环境
- 确保 PHP 与扩展满足 Laravel 版本所需

### Nginx 伪静态（rewrite 到 index.php）
Nginx 不支持 .htaccess；所谓“伪静态”就是把非真实文件或目录的请求统一交给前端控制器 index.php，由框架路由处理。典型做法是在 Nginx 的 server 配置里使用 try_files：

```
server {
    root /path/to/kldns-pro;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 7d;
        access_log off;
    }
}
```

## 环境要求（示例）
- PHP 7.2+（或更高，按 composer.lock 约束为准）
- Composer（用于依赖管理）
- Nginx 或 Apache（生产环境建议配合 HTTPS 与反向代理缓存）
- MySQL/MariaDB 等（按 install.sql 初始化）

## 维护者
- znpwlk

## 许可证
- 详见仓库中的 LICENSE
