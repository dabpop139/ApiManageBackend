<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// [ 应用入口文件 ]
// 定义应用目录

$allowOrigin = '*';

header("Access-Control-Allow-Origin: " . $allowOrigin);
// header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,X-User-Token,If-Modified-Since,Cache-Control,Content-Type,Accept-Language,Origin,Accept-Encoding");
header("Expires: 0");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

//如果是OPTIONS请求，就结束执行下面语句
if($_SERVER['REQUEST_METHOD']=='OPTIONS'){
    exit('OPTIONS REQUEST END');
}

define('APP_PATH', __DIR__ . '/../application/');

// 判断是否安装FastAdmin
if (!is_file(APP_PATH . 'admin/command/Install/install.lock'))
{
    header("location:./install.php");
    exit;
}

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
