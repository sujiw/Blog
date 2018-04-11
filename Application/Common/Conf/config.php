<?php
return array(
    //'配置项'=>'配置值'
    'LOAD_EXT_CONFIG' => 'db',
    'DOMAIN' => "//".$_SERVER['HTTP_HOST']."/", //网站域名
    'article_thumb_pic' => 'article/thumb/',
    'article_content_pic' => 'article/content/',
    'user_head_pic' => 'user/head/',
    'admin_head_pic' => 'admin/head/',
    'AUTH_CODE' => "66b29ea9655cad8bff1428e4c52f430b", //安装完毕之后不要改变，否则所有密码都会出错
    'AUTH_ON'           => true,                      // 认证开关
    'AUTH_TYPE'         => 1,                         // 认证方式，1为实时认证；2为登录认证。
    'AUTH_GROUP'        => 'auth_group',        // 用户组数据表名
    'AUTH_GROUP_ACCESS' => 'auth_group_access', // 用户-用户组关系表
    'AUTH_RULE'         => 'auth_rule',         // 权限规则表
    'AUTH_USER'         => 'admin',             // 用户信息表

    'THINK_EMAIL' => array(
        'SMTP_HOST' => 'smtp.3it.cc', //SMTP服务器
        'SMTP_PORT' => '465', //SMTP服务器端口
        'SMTP_USER' => 'mdg@3it.cc', //SMTP服务器用户名
        'SMTP_PASS' => '~a123456789', //SMTP服务器密码
        'FROM_EMAIL' => 'mdg@3it.cc',
        'FROM_NAME' => 'SuJiWenBlog', //发件人名称
        'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
        'REPLY_NAME' => '', //回复名称（留空则为发件人名称）
        'SESSION_EXPIRE'=>'72',
    ),

    //'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL' => 2, //URL模式
    'URL_ROUTER_ON' => fasle,//是否开启路由
    'URL_HTML_SUFFIX' => '',
    'URL_ROUTE_RULES' => array(
        ':id\d' => 'Home/Article/index?id=:1',//内容
        '/^c_(\d+)$/' => 'Home/Index/index?cate_id=:1',//分类
        'k/:keywords' => 'Home/Index/index',//搜索
        'message' => 'Home/Comment/index',//留言页面
        'ajax_comment' => 'Home/Comment/ajax_comment',//留言
        'about' => 'Home/Index/about',//关于
        'groups' => 'Home/Index/group',//圈子
        'resume' => 'Home/Index/resume',//简历
    ),
    //'MODULE_ALLOW_LIST' => array('Home','Admin'),
    //'TMPL_EXCEPTION_FILE' => "./Public/404.html"
);