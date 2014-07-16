<?php
 ob_start();
error_reporting(0); 
session_start();
$_SESSION = array(
    "storeid" => 3,
    "siteconfig" => 2,
    "countername" => -1,
    "siteconfig" => 2,
    "addressbookid" => 40,
    "name" => 'System Admin',
    "admin_section" => 'Admin logged in',
    "groupid" => 6,
    "groupname" => 'Administrator',);
	
include("../../includes/security/adminsecurity.php");

global $AdminDAO, $Component;
$jsondata=json_decode($_GET['barcode'],true);
 

 
$query			=	"SELECT pkbarcodeid from barcode where barcode = '$jsondata'  ";
 $result		=	$AdminDAO->queryresult($query);
 $barcode1			=	$result[0]['pkbarcodeid'];  

 $itemcounterprice	=	$AdminDAO->getrows("$dbname_detail.pricechange","price","fkbarcodeid='$barcode1'");
 $tradeprice		=	$itemcounterprice[0]['price'];
if($tradeprice==''){
$itempricedata	=	$AdminDAO->getrows("$dbname_detail.stock","retailprice","fkbarcodeid='$barcode1' ORDER BY pkstockid DESC LIMIT 1");
$tradeprice		=	$itempricedata[0]['retailprice'];
	
	
	}

echo $tradeprice;
?>
