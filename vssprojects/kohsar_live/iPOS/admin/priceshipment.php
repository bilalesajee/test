<?php
session_start();
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 		= 	$_POST['id'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$shipmentid				=	$_POST['shipmentid'];
	$shipmentpercentage		=	$_POST['shipmentpercentage'];
	$shipmentcharges		=	$_POST['shipmentcharges'];
	$costprice				=	$_POST['costprice'];
	$retailpercentage		=	$_POST['retailpercentage'];
	$storeids				=	$_POST['storeids'];
	$storeidarr				=	explode(",",$storeids);
	$purchaseid				=	$_POST['purchaseid'];
	// preparing data for receiving
	$fields	=	array('fkstoreid','fkpurchaseid','fkshipmentid','pricedby','pricetime','costprice','retailpercentage','retailprice','shipmentcharges','shipmentpercentage');

	for($i=0;$i<sizeof($costprice);$i++)
	{
		//retrieving stores data
		$storedata	=	$store;
		//receiving store wise
		for($s=0;$s<sizeof($storeidarr);$s++)
		{
			$storeid		=	$storeidarr[$s];
			$retailprice	=	$_POST['store'.$storeid][$i];
			$data			=	array($storeid,$purchaseid[$i],$shipmentid,$_SESSION['addressbookid'],time(),$costprice[$i],$retailpercentage[$i],$retailprice,$shipmentcharges[$i],$shipmentpercentage[$i]);
			$AdminDAO->insertrow("pricing",$fields,$data);
		}
	}
	//7 	Received
	//echo $shipmentid;
	$fields	=	array('fkstatusid');
	$data	=	array("7");
	$AdminDAO->updaterow("shipment",$fields,$data,"pkshipmentid=$shipmentid");
}
?>