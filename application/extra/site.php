<?php

return array(
    'name'             => 'kkApiManage',
    'cname'            => 'KK API接口管理系统', // 用于网站发送邮件等
    'slogan'           => 'KK API接口管理系统', // 用于网站标题上用
    'beian'            => '',
    'cdnurl'           => '',
    'version'          => '1.0.1',
    'timezone'         => 'Asia/Shanghai',
    'forbiddenip'      => '',
    'languages'        =>
        array(
            'backend'  => 'zh-cn',
            'frontend' => 'zh-cn',
        ),
    'fixedpage'        => 'dashboard',
    'categorytype'     =>
        array(
            'default' => 'Default',
            'page'    => 'Page',
            'article' => 'Article',
            'test'    => 'Test',
        ),
    'configgroup'      =>
        array(
            'basic'      => 'Basic',
            'email'      => 'Email',
            'dictionary' => 'Dictionary',
            'user'       => 'User',
            'example'    => 'Example',
        ),
    'mail_type'        => '1',
    'mail_smtp_host'   => 'smtpdm.aliyun.com',
    'mail_smtp_port'   => '465',
    'mail_smtp_user'   => 'support@mail.exsample.com',
    'mail_smtp_pass'   => '123456',
    'mail_verify_type' => '2',
    'mail_from'        => 'support@mail.exsample.com',

    'wapdomain' => 'https://m.apimanage.com/',
);