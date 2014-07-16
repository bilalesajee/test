<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;
*/
if(sizeof($_POST)>0)
{
	$shipmentid	=	$_POST['shipmentid'];
	$ids		=	$_POST['chargesid'];
	$amountinrs	=	0;
	for($i=0;$i<sizeof($ids);$i++)
	{
		$id				=	$ids[$i];
		$chargevalue	=	$_POST['charges_'.($i+1)];
		if($chargevalue=='')$chargevalue=0;
		$fields			=	array('chargesinrs');
		$data			=	array($chargevalue);
		$AdminDAO->updaterow("`shipmentcharges`",$fields,$data,"pkshipmentchargesid='$id'");
		$amountinrs		=	$amountinrs+$chargevalue;
	}
	$fields2		=	array('amountinrs');
	$data2			=	array($amountinrs);	
	$AdminDAO->updaterow("`shipment`",$fields2,$data2,"pkshipmentid='$shipmentid'");
	echo $shipmentid;
}
?>