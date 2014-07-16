<?php
session_start();
include("includes/security/adminsecurity.php");
global $AdminDAO;
if(sizeof($_POST)>0)
{
	$currentbarcode		=	trim(filter($_POST['currentbarcode'])," ");
	$fixedbarcode		=	trim(filter($_POST['fixedbarcode'])," ");		
	$savetime=time();
	$field2		=	array('fkaddressbookid','currentbarcode','fixedbarcode','savetime');
	$data2		=	array($empid,$currentbarcode,$fixedbarcode,$savetime);
	$insertid2	=	$AdminDAO->insertrow("$dbname_detail.barcodefilter",$field2,$data2);
exit;
}
?>