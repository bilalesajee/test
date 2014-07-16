<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$shipmentid 	= 	$_POST['id'];
	$status 		= 	$_POST['stat'];
	$fields			=	array('fkstatusid');
	$data			=	array($status);
	$AdminDAO->updaterow("`shipment`",$fields,$data,"pkshipmentid = '$shipmentid'");
}
else
{
	echo "Please select a shipment.";
	exit;
}
?>