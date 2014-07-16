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
 $barcodeid=$_GET['barcode'];

 $query			=	"SELECT pkbarcodeid from barcode where barcode = '$barcodeid' or itemdescription like '$barcodeid' ";
$result		=	$AdminDAO->queryresult($query);

$barcode			=	$result[0]['pkbarcodeid'];




$sql			=	"SELECT sport,spath,storeip,storename from store where pkstoreid = 3 ";
$result2			=	$AdminDAO->queryresult($sql);
//print_r($result2);

$array = array();

foreach($result2 as $row)
{
 $storeip	=	$row['storeip'];
 $storename		=	$row['storename'];
 $port	=	$row['sport'];
 $path		=	$row['spath'];
 $url = "https://{$storeip}{$path}/admin/accounts/remaining_stock2.php?barcode={$barcode}";
 $stock = file_get_contents($url);
 if($stock != 'null')
 $stockA[] = json_decode($stock, true);
 
}

echo json_encode($stockA);

?>