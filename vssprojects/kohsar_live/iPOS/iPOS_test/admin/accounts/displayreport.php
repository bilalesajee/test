<?php session_start();
include_once("../../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$userSecurity;
$id			=	trim($_GET['id'],",");
$param		=	$_GET['param'];
$report		=	$_GET['report'];

$fromdate	=	$_GET['fromdate'];
$toate		=	$_GET['todate'];

if($param=='account_id')
{
	$_REQUEST['account_id'] = $id;
	$url.=	"ledger.php";
}
else
{
	$reports	=	$AdminDAO->getrows("report","*","id = '$id'");
	$name		=	$reports[0]['name'];
	$url		=	$reports[0]['url'];
	echo "Currently Viewing: <b>$name</br><br>";
}
require_once("reports/$url");
?>