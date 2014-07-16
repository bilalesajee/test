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
	$moveqty	=	$_POST['moveqty'];
	$shipment	=	$_POST['shipment'];
	if($shipment == "")
	{
		echo "Please select a shipment.";
		exit;
	}
	for($i=0;$i<sizeof($ids);$i++)
	{
		$shiplistid	=	$ids[$i];
		$status		=	$AdminDAO->getrows("shiplist","fkstatusid","pkshiplistid='$shiplistid' AND fkstatusid in (3,4,5)");
		if(sizeof($status)>0)
		{
			$msg	=	"Some items were not in Request status and could not be moved.";
		}
		else
		{
			$fields		=	array('fkstatusid');
			$data		=	array(2);
			$AdminDAO->updaterow("shiplist",$fields,$data,"pkshiplistid='$shiplistid'");
			$fields2	=	array('fkshiplistid','fkshipmentid','quantity','fkaddressbookid');
			$data2		=	array($shiplistid,$shipment,$moveqty[$i],$_SESSION['addressbookid']);
			$AdminDAO->insertrow("shiplistdetails",$fields2,$data2);
		}
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
}
?>