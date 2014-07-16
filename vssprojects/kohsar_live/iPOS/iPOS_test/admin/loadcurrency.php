<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency` = 1");
$defaultcurrency = $currency[0]['currencyname'];

$id	=	$_REQUEST['id'];
$price	=	$_REQUEST['price'];
$currency		=	$AdminDAO->getrows("shipment,currency","currencysymbol,rate","shipmentcurrency = pkcurrencyid AND pkshipmentid = '$id'");
$currencyname	=	$currency[0]['currencysymbol'];
$rate			=	$currency[0]['rate'];
if($_GET['p']==1)
{
	echo $currencyname;
}
else
{
	echo "<input type=\"hidden\" id=\"hprice\" value=\"$rate\">";
	echo $currencyname." = ".$defaultcurrency." ".$rate;
}
?>