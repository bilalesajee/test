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
$loc=$_GET['loc'];

 $query			=	"SELECT pkbarcodeid from barcode where barcode = '$barcodeid' or itemdescription like '$barcodeid' ";
$result		=	$AdminDAO->queryresult($query);

$barcode			=	$result[0]['pkbarcodeid'];

if($loc == 'All')
{
	$cond = '';
}
else
{
	$cond = " and pkstoreid = '$loc'";
}

$sql			=	"SELECT port,path,storeip,storename from store where 1=1 $cond";
$result2			=	$AdminDAO->queryresult($sql);


$array = array();

foreach($result2 as $row)
{
 $storeip	=	$row['storeip'];
 $storename		=	$row['storename'];
 $port	=	$row['port'];
 $path		=	$row['path'];
 echo "http://{$storeip}:{$port}{$path}/admin/accounts/remaining_stock_rpt.php?barcode={$barcode}&loc={$loc}";
echo  $stock=file_get_contents("http://{$storeip}:{$port}{$path}/admin/accounts/remaining_stock_rpt.php?barcode={$barcode}&loc={$loc}");

//$c = json_decode($stock, true); 
//if(count($c) > 0) 
//{
	//$array[] = $c;
	//}
//210.2.171.10echo json_encode($array);
}

//echo json_encode($array);

?>