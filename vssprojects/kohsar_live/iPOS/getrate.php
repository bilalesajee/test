<?php
include_once("includes/security/adminsecurity.php");
global $AdminDAO;
if(sizeof($_POST)>0)
{
	$currencyid	=	$_POST['currency'];
	$remaining	=	$_POST['remainingprice'];
	$rates		=	$AdminDAO->getrows("currency","*", " pkcurrencyid = '$currencyid'");
	$rate		=	$rates[0]['rate'];
	$newrate	=	round(($remaining/$rate),2);
	$newrate	=	ceil($newrate);
	echo "$rate,$newrate";
}
else
{
	echo "Not Valid";
}
?>