<?php 
include_once("includes/conf/conf.php");
$msg	=	"";
session_start();
//file_get_contents("https://main.esajee.com/admin/accounts/insertloginhistory.php?action=logout&sess_id=".urlencode(session_id()));	

 $sess_id =urlencode(session_id());
mysql_query("update main_kohsar.loginhistory set logouttime='".time()."' where sess_id='$sess_id' and logouttime='0'");

if(isset($_SESSION['countername'])){//edit by Ahsan on 10/02/2012, added if condition

	$counter	=	$_SESSION['countername'];

}

$_SESSION['breakmode']='';

if(isset($_GET['msg'])){//edit by Ahsan on 10/02/2012, added if condition

	$msg	=	$_GET['msg'];

}

session_destroy();

if(strstr($_SERVER['HTTP_REFERER'],'/admin/')){//from store, start edit by Ahsan on 09/02/2012

	header("Location:admin/index.php");

	exit;

}//end edit
	///////////////////////////////////////////////////For Login Histroy///////////////////////////////////
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////
header("Location:userlogin.php?pos=$counter&msg=$msg");

exit;

?>