<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$id		=	$_GET['id'];
if($id)
{
	$storeshipments	=	"AND fkdeststoreid='$id'";
}
$shipment_array			=	$AdminDAO->getrows('shipment','pkshipmentid, shipmentname '," isopened='o' $storeshipments AND `shipmentdeleted`<>1");


$shipment				=	$Component->makeComponent("d","shipment",$shipment_array,"pkshipmentid","shipmentname",1,$selected_shipment,'onchange=getshipmentgroup(this.value)','class=eselect');
echo $shipment;
?>