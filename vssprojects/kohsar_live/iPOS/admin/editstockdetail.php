<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
$stockid			=	$_REQUEST['stockid'];
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$storedb			=	$_REQUEST['store'];
}//end edit
$brands				=	$_REQUEST['brands'];
$suppliers			=	$_REQUEST['suppliers'];
$batch				=	filter($_REQUEST['batch']);
$purchaseprice		=	filter($_REQUEST['purchaseprice']);
$costprice			=	filter($_REQUEST['costprice']);
$retailprice		=	filter($_REQUEST['retailprice']);
$priceinrs			=	filter($_REQUEST['priceinrs']);
$shipmentcharges	=	filter($_REQUEST['shipmentcharges']);
$totalunits			=	filter($_REQUEST['totalunits']);
$remainingunits		=	filter($_REQUEST['remainingunits']);
$stockdate			=	strtotime($_REQUEST['stockdate']);
if(sizeof($_POST)>0)
{
	$field		=	array('fkbrandid','fksupplierid','batch','purchaseprice','costprice','retailprice','priceinrs','shipmentcharges','quantity','unitsremaining','expiry');
	$value		=	array($brands,$suppliers,$batch,$purchaseprice,$costprice,$retailprice,$priceinrs,$shipmentcharges,$totalunits,$remainingunits,$stockdate);
	$AdminDAO->updaterow("$dbname_detail.stock",$field,$value,"pkstockid = '$stockid'");
}//else
?>