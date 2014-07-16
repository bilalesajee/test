<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 		= 	$_REQUEST['id'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
/*if($id!="-1")
{
	// this is the edit section
	$packs = $AdminDAO->getrows("shiplist","*"," pkpackingid='$id'");
	foreach($packs as $pack)
	{
		$packingname = $pack['packingname'];
	}
}*/
if(sizeof($_POST)>0)
{
	$consignmentname	=	filter($_POST['consignmentname']);
	if($consignmentname=='')
	{
		$msg.="<li>Please enter consignment name.</li>";
	}
	$destinationstore	=	$_POST['destinationstore'];
	$sourcestore		=	$_POST['sourcestore'];
	if($sourcestore	== '')
	{
		$msg.="<li>Please select consignment source store.</li>";
	}
	else if($destinationstore=='')
	{
		$msg.="<li>Please select consignment Destination store.</li>";	
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	$deadline			=	strtotime(implode("-",array_reverse(explode("-",$_POST['deadline']))));
	$fkaddressbookid	=	$empid;
	$addtime			=	time();
	$fkstatusid			=	$_POST['status'];//1 for pending
	$notes				=	filter($_POST['notes']);
	$fkdriverid			=	$_POST['driver'];
	$fksupervisorid		=	$_POST['supervisor'];
	$fkvehicleid		=	$_POST['vehicle'];
	$fields = array('consignmentname','fkstoreid','fkdeststoreid','deadline','fkaddressbookid','addtime','fkstatusid','fkdriverid','fksupervisorid','fkvehicleid','notes');
	$values = array($consignmentname,$sourcestore,$destinationstore,$deadline,$fkaddressbookid,$addtime,$fkstatusid,$fkdriverid,$fksupervisorid,$fkvehicleid,$notes);
	if($id!='-1')//updates records 
	{
		$AdminDAO->updaterow("consignment",$fields,$values," pkconsignmentid='$id' ");
	}
	else
	{
		// this is the add section	
		$id = $AdminDAO->insertrow("consignment",$fields,$values);
	}//end of else
exit;
}// end post
?>