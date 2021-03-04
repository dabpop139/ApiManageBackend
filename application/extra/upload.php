<?php

//上传配置
return [
    'driver'            => 'local', //local,oss
    // oss驱动配置
    'oss'               => [
        'ali' => [
            'access_key_Id'     => '',
            'access_key_secret' => '',
            'endpoint'          => 'oss-cn-hangzhou.aliyuncs.com',
            'cname'             => '',
            'bucket'            => ''
        ]
    ],
    /**
     * 上传地址,默认是本地上传
     */
    'uploadurl'         => 'ajax/upload',
    /**
     * CDN地址
     * !!! 程序上没有完全支持所以不能配置
     */
    'cdnurl'            => '',
    /**
     * 文件保存格式
     */
    'savekey'           => '/uploads/{year}{mon}{day}/{filemd5}{.suffix}',
    'savekey_cschat'    => '/uploads_cschat/{year}{mon}{day}/{filemd5}{.suffix}',
    /**
     * 最大可上传大小
     */
    'maxsize'           => '10mb',
    /**
     * 最大可上传尺寸
     */
    'image_limitwidth'  => 1920,
    'image_limitheight' => 2000,

    /**
     * 可上传的文件类型
     */
    'mimetype'          => 'jpg,png,bmp,jpeg,gif,zip,rar,xls,xlsx',
    /**
     * 是否支持批量上传
     */
    'multiple'          => false,
];
