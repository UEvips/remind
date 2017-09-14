<?php
return array(
	//'配置项'=>'配置值'
	'URL_CASE_INSENSITIVE'  =>  true,//url区分大小写 true不区分
	'MODULE_ALLOW_LIST'     =>  array('Remind'),//允许通过url访问模块列表，其他不可以
	'ERROR_PAGE'=>__PUBLIC__.'/error.html', // 定义错误跳转页面URL地址
	//数据库配置信息
	'DB_TYPE'   => 'mysqli', // 数据库类型
	'DB_HOST'   => 'localhost', // 服务器地址
	'DB_NAME'   => 'remind', // 数据库名
	'DB_USER'   => 'remind', // 用户名
	'DB_PWD'    => 'Y7p3J2j3', // 密码
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => 'tb_', // 数据库表前缀 
	'DEFAULT_FILTER'   =>  'htmlspecialchars',
        'KEY'=>'^*%$',
);