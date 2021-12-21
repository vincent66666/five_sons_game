<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
return [
    // 上传文件保存配置，本地local_public，阿里云oss, 腾讯 cos, 七牛云 qiniu
    'upload_adapter'        => env('UPLOAD_ADAPTER', 'local_public'),
    // 保存本地需要配置图片url
    'local_public_url'      => env('UPLOAD_LOCAL_PUBLIC_URL', 'http://127.0.0.1:9509'),
    // 保存本地需要配置图片url
    'oss_public_url'      => env('UPLOAD_OSS_PUBLIC_URL'),
    // 文件上传大小限制（单位字节B） 500MB
    'file_max_size'         => 1024 * 1024 * 20,
    // 上传文件目录
    'upload_file_directory' => env('UPLOAD_FILE_DIRECTORY', 'upload'),
    // 文件名:P生产环境，D开发环境，T测试环境
    'file_name_format'      => env('UPLOAD_PREFIX', 'T') . '{time}_{rand:5}',
    // 文件上传允许类型
    'file_allow_files'      => [
        'png',
        'jpg',
        'jpeg',
        'gif',
        'bmp',
        'flv',
        'swf',
        'mkv',
        'avi',
        'rm',
        'rmvb',
        'mpeg',
        'mpg',
        'ogg',
        'ogv',
        'mov',
        'wmv',
        'mp4',
        'webm',
        'mp3',
        'wav',
        'mid',
        'rar',
        'zip',
        'tar',
        'gz',
        '7z',
        'bz2',
        'cab',
        'iso',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'ppt',
        'pptx',
        'pdf',
        'txt',
        'md',
        'xml',
        'apk',
    ],
];
