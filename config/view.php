<?
return [
    // ... 其他配置 ...
    // 'view_replace_str'  =>  [
    //     '__CSS__' => '/static/css', // CSS目录的URL路径前缀
    //     '__JS__'  => '/static/js',  // JS目录的URL路径前缀
    //     '__IMG__' => '/static/img', // 图片目录的URL路径前缀
    //     '__LIB__' => '/lib',       // 第三方库目录的URL路径前缀
    //     '__PUBLIC__' => '/', // 公共资源目录的URL路径前
    // ],
    'view_replace_str' => [
        '__STATIC__' => '/static',
        '__UPLOAD__' => '/uploads',
        '__PUBLIC__' => '/',
    ]
];
?>