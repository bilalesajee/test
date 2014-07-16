<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 		= 	$_REQUEST['id'];
$ids		=	explode(",",$id);
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$status	=	$_POST['status'];
	if($status == "")
	{
		echo "Please select a status.";
		exit;
	}
	for($i=0;$i<sizeof($ids);$i++)
	{
		$shiplistid	=	$ids[$i];
		$fields		=	array('fkstatusid');
		$data		=	array($status);
		$AdminDAO->updaterow("shiplist",$fields,$data,"pkshiplistid='$shiplistid'");
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
}
?>