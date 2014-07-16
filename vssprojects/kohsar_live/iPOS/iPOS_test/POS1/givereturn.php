<?php 
include_once("includes/security/adminsecurity.php");
global $AdminDAO;
if(sizeof($_POST)>0)
{
	$saleid		=	$_SESSION['tempsaleid'];
	$return		=	$_POST['returnamount'];
	$fields		=	array('adjustment');
	$values		=	array($return);
	$AdminDAO->updaterow("$dbname_detail.sale",$fields,$values,"pksaleid='$saleid'");
	echo "Sale completed successfully.";
	exit;
}
else
{
	echo "Invalid Data.";
	exit;
}
?>