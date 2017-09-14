<?php
return array(
	//'配置项'=>'配置值'
	'URL_CASE_INSENSITIVE'  =>  false,//url区分大小写 true不区分
	'MODULE_ALLOW_LIST'     =>  array('Home','Admin'),//允许通过url访问模块列表，其他不可以
	'ERROR_PAGE'=>__PUBLIC__.'/error.html', // 定义错误跳转页面URL地址
	// 'SESSION_TYPE'  => 'Db',  //session方式
	//数据库配置信息
        'DB_TYPE'   => 'mysqli', // 数据库类型
        'DB_HOST'   => 'localhost', // 服务器地址
        'DB_NAME'   => 'xtwork', // 数据库名
        'DB_PORT'   => 3306, // 端口
        'DB_PREFIX' => 'tb_', // 数据库表前缀 
	//	'TMPL_L_DELIM'=>'{',
	//	'TMPL_R_DELIM'=>'}', 
	'DEFAULT_FILTER'   =>  'htmlspecialchars',
	// 显示页面Trace信息
	//'SHOW_PAGE_TRACE' =>true,
   // 'URL_HTML_SUFFIX'       =>  '',  // URL伪静态后缀设置
//	'LOAD_EXT_CONFIG' => 'verify,setting',   // 载入自定义配置文件
    '_ROLE'=>array(
	        'Manager'=>Manager,
	        'ADMIN'=>admin,
	        'XMB'=>'运营',
	        'JSB'=>'技术',
	        'XZB'=>'行政',
	        'KHD'=>'客户',
	        'BMGLY'=>'部门管理员'
           ),
);