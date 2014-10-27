<?php
// +----------------------------------------------------------------------
// | MyfPHP 闵益飞PHP MVC框架  -- 路由类
// +----------------------------------------------------------------------
// | Copyright (c) 2012 http://www.minyifei.cn All rights reserved.
// +----------------------------------------------------------------------
// | 交流论坛：http://bbs.minyifei.cn
// +----------------------------------------------------------------------
date_default_timezone_set('PRC');
header("Content-Type:text/html; charset=utf-8");
session_start();
//项目跟路径
if(!defined('SITE_PATH')) define('SITE_PATH', dirname($_SERVER['SCRIPT_NAME']));
//项目路径
if(!defined('APP_PATH')) define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']));
//系统配置路径
if(!defined('APP_SYS_PATH')) define('APP_SYS_PATH', dirname(__FILE__));
// 项目日志目录
if(!defined('LOG_PATH')){define('LOG_PATH',  dirname(APP_SYS_PATH).'/runtime/logs/');  if(!is_dir(LOG_PATH)) mkdir(LOG_PATH); }

//基础函数
require_once(APP_SYS_PATH."/functions.php");
require_once(APP_SYS_PATH."/verify.php");
//日志
require_once(APP_SYS_PATH."/log.php");
//加载数据库操作文件
require_once(APP_SYS_PATH."/db.php");
require_once(APP_SYS_PATH."/dao.php");
//文件读写
require_once(APP_SYS_PATH."/file.php");
//无限菜单
require_once(APP_SYS_PATH."/tree.php");
//分页相关
require_once(APP_SYS_PATH."/page.php");
//http请求
require_once(APP_SYS_PATH."/http.php");
//父Action类
require_once(APP_SYS_PATH."/action.php");
//require_once(APP_SYS_PATH."/session.mysql.php");
//smarty
require_once(APP_SYS_PATH."/smt/Smarty.class.php");

if(C("URLTYPE")=="rewrite"){

	//伪静态
	$uri = $_SERVER["QUERY_STRING"];
    if(strpos($uri, "s=")>-1){
        $uri = str_replace("s=/", "", $uri);
        $uri = str_replace("s=", "", $uri);
        $urls = explode("&", $uri);
        $myf_vars=explode("/",$urls[0]);
    }

}

if(C("DB_DEBUG")){
	DB::$debug = TRUE;
}
//默认控制器
if(!empty($myf_vars[0])){
	$myf_m = $myf_vars[0];
}else{
	$myf_m = (!empty($_GET["m"]))?$_GET["m"]:"index";
}
//默认方法
$uid=null;
if(!empty($myf_vars[1])){
    if(is_numeric($myf_vars[1])){
        $uid = $myf_vars[1];
        $myf_action = "index";
    }else{
        $myf_action = $myf_vars[1];
    }
}else{
	$myf_action = (!empty($_GET["a"]))?$_GET["a"]:"index";
}
$myf_m = htmlspecialchars($myf_m);
$myf_action = htmlspecialchars($myf_action);
$myf_module = ucfirst($myf_m)."Action";
$myf_file = APP_PATH."/app/Action/".$myf_module.".class.php";
if(file_exists($myf_file)){
	require_once($myf_file);
	$myf_c = new $myf_module;
	$myf_c->_init($myf_m);
    session("myf_paths",array("module"=>$myf_m,"action"=>$myf_action));
	//执行前置方法
	$myf_c->_before_index();
	$myf_c->{$myf_action}($uid);
	//执行后置方法
	$myf_c->_after_index();
}else{
   echo "error 404";
}
