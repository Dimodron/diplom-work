<?php	
	error_reporting(E_ALL & ~E_NOTICE);//скрывать notice
	include "mysqli.php";
	date_default_timezone_set('Europe/Moscow');
	$DB = new db_driver();
	$DB->obj=array( "sql_database"   => "museum_record"         ,
                       "sql_user"       => "root"     ,
                       "sql_pass"       => ""         ,
                       "sql_host"       => "localhost");
	$DB->connect();
	include "functions.php";
	include "templ.php";
	$func = new func();
	$templ = new templ();
	$action = (isset($_GET['act']))?$_GET['act']:"";
	switch($action){
		case'visitor':
		$func->visitor();
		break;
		case'visitor_add':
		$func->visitor_add();
		break;
		case'visitor_insert':
		$func->visitor_insert();
		break;
		case'success':
		$func->success();
		break;
	}
?>