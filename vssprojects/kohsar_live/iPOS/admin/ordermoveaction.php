<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$orders 		= 	$_POST['id'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;
*/
if(sizeof($_POST)>0)
{
	$shipment	=	$_POST['shipment'];
	if($shipment == "")
	{
		echo "Please select a shipment.";
		exit;
	}
	$ids		=	explode(",",$orders);
	for($i=0;$i<sizeof($ids);$i++)
	{
		$id		=	$ids[$i];
		// move only those that are in request status
		$status	=	$AdminDAO->getrows("`order`","fkstatusid","pkorderid='$id' AND fkstatusid<>1");
		if(sizeof($status)>0)
		{
			unset($ids[$i]);
		}
	}
	if(sizeof($ids)==0)
	{
		echo "None of the selected Order(s) is in Movable State.";
		exit;
	}
	$orders		=	implode(",",$ids);
	$fields		=	array('fkstatusid','fkshipmentid');
	$data		=	array(2,$shipment);
	$AdminDAO->updaterow("`order`",$fields,$data,"pkorderid IN ($orders)");
}
?>