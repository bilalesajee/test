<?php @session_start();
	//echo $_SESSION['closingsession'];
	//echo time();
	//ini_set("error_reporting",0);
	//$path	=	"d:/wamp/www/esajee/pos/";
	//$newpath	=	realpath(dirname(__FILE__));
	//$path		=	str_replace('includes\security', '', $newpath);
	$newpath	=	$_SERVER['DOCUMENT_ROOT'].'/';
	$path		=	str_replace('includes\security', '', $newpath);
	error_reporting(0);		
	include_once($path."includes/conf/conf.php");
//	include_once($path."includes/conf/userconfig.php");//add comment by ahsan 22/02/2012
	include_once($path."includes/classes/valid.php");
	require_once($path."includes/classes/filter.php");	
?>