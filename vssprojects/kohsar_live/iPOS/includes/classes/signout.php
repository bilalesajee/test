<?php 
session_start();
$msg	=	"";
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
header("Location:userlogin.php?pos=$counter&msg=$msg");
exit;
?>