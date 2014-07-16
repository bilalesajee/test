<?php
session_start();
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$numb					=	$_POST['numb'];
$stockid				=	$_SESSION['stock'][$numb];
$pricechangeid			=	$_SESSION['pricechange'][$numb];
$pricechangehistoryid	=	$_SESSION['pricechangehistory'][$numb];
$stockadjustmentid		=	$_SESSION['stockadjustment'][$numb];
$damagesid				=	$_SESSION['damages'][$numb];
$instancestockid		=	$_SESSION['instancestock'][$numb];
if($stockid!='')
{
	//$AdminDAO->queryresult("DELETE FROM $dbname_detail.stock WHERE `pkstockid`='$stockid'");
	$AdminDAO->deleterows("$dbname_detail.stock","`pkstockid`='$stockid'",1);	
}
if($pricechangeid!='')
{
	//$AdminDAO->queryresult("DELETE FROM $dbname_detail.pricechange WHERE `pkpricechangeid`='$pricechangeid'");	
	$AdminDAO->deleterows("$dbname_detail.pricechange","`pkpricechangeid`='$pricechangeid'",1);	
}
if($pricechangehistoryid!='')
{
	//$AdminDAO->queryresult("DELETE FROM $dbname_detail.pricechangehistory WHERE `pkpricechangehistoryid`='$pricechangehistoryid'");	
	$AdminDAO->deleterows("$dbname_detail.pricechangehistory","`pkpricechangehistoryid`='$pricechangehistoryid'",1);	
}
if($stockadjustmentid!='')
{
	//$AdminDAO->queryresult("DELETE FROM $dbname_detail.stockadjustment WHERE `pkstockadjustmentid`='$stockadjustmentid'");	
	$AdminDAO->deleterows("$dbname_detail.stockadjustment","`pkstockadjustmentid`='$stockadjustmentid'",1);	
}
if($damagesid!='')
{
	//$AdminDAO->queryresult("DELETE FROM $dbname_detail.damages WHERE `pkdamageid`='$damagesid'");	
	$AdminDAO->deleterows("$dbname_detail.damages","`pkdamageid`='$damagesid'",1);	
}
if($instancestockid!='')
{
	//$AdminDAO->queryresult("DELETE FROM $dbname_detail.instancestock WHERE `pkinstancestockid`='$instancestockid'");	
	$AdminDAO->deleterows("$dbname_detail.instancestock","`pkinstancestockid`='$instancestockid'",1);	
}
?>