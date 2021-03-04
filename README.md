#### 项目说明
关键词：API接口管理，API管理

本项目为KKApiManage的后端服务程序部分，包含PHP版本的和Golang版本的，请根据自己的需求选用。

前端仓库部分请访问：https://gitee.com/dabpop139/api-manage

- data.sql为Mysql的数库文件
- 复制.env.sample为.env后配置相关的数据库连接
- Golang后端版本为Golang版本的后端实现

- Apache配置示例：
```
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

![KKApiManage](https://images.gitee.com/uploads/images/2021/0304/151620_796af1b6_84445.jpeg "KKApiManage")