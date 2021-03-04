#### 项目说明
- data.sql为Mysql的数库文件
- 复制.env.sample为.env后配置相关的数据库连接
- Golang后端版本为Golang版本的后端实现

- Apache配置示例：
```ini
Listen 8044
<VirtualHost *:8044>
    DocumentRoot "F:\WebRoot\KKApiManageBackEnd\public"
    SetEnv RUNTIME_ENVIROMENT local
    
  <Directory "F:\WebRoot\KKApiManageBackEnd\public">
      Options Indexes FollowSymLinks ExecCGI
      AllowOverride All
      Order allow,deny
      Allow from all
      Require all granted
  </Directory>
</VirtualHost>
```