<?php @session_start();

	//echo $_SESSION['closingsession'];

	//echo time();

	//ini_set("error_reporting",0);

	//$path	=	"d:/wamp/www/esajee/pos/";

	//$newpath	=	realpath(dirname(__FILE__));

	//$path		=	str_replace('includes\security', '', $newpath);

   $posfolder				="vssprojects".DIRECTORY_SEPARATOR."kohsar_live".DIRECTORY_SEPARATOR."iPOS";  //Only Enter Folder Name
   $newpath	=	$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$posfolder.DIRECTORY_SEPARATOR;
   $path		=	str_replace('includes'.DIRECTORY_SEPARATOR.'security', '', $newpath);

	error_reporting(0);		

	include_once($path."includes/conf/conf.php");

//	include_once($path."includes/conf/userconfig.php");//add comment by ahsan 22/02/2012

	include_once($path."includes/classes/valid.php");

	require_once($path."includes/classes/filter.php");	

?>