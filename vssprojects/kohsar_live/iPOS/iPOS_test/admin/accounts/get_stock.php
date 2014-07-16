<html>
<head>
<title>Remaining Stock Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
</head>
<body>
<div style="width:8.0in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif; padding-left:230px;" align="left"><b>ESAJEE'S</b>
</div>
<div style="width:8.0in;font-size:11px;font-family:Comic Sans MS, cursive;padding-left:200px;" align="left"><b>Think globally shop locally</b></div><br />
<br />

</body>
<html>

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

$sql			=	"SELECT sport,spath,storeip,storename from store where 1=1 $cond";
$result2			=	$AdminDAO->queryresult($sql);


$array = array();
?>
<table width="558" cellpadding="0" cellspacing="0" class="table" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">

  <tr>
  	
   
    <th width="79" bgcolor="#999999">Location</th>
    <th width="104" bgcolor="#999999">Stock</th>
    <th width="97" bgcolor="#999999">Trade Price</th>
   
    <th width="146" bgcolor="#999999">Retail Price</th>
  </tr>
 
  <?php
foreach($result2 as $row)
{
 $storeip	=	$row['storeip'];
 $storename		=	$row['storename'];
 $port	=	$row['sport'];
 $path		=	$row['spath'];
 if($storeip=="210.2.171.10"){
echo  $stock=file_get_contents("http://{$storeip}:/admin/accounts/remaining_stock_rpt.php?barcode={$barcode}&loc={$loc}");
 }else  if($storeip=="202.147.178.163"){
echo  $stock=file_get_contents("https://warehouse.esajee.com/admin/accounts/remaining_stock_rpt.php?barcode={$barcode}&loc={$loc}");	 
	 }else  if($storeip=="203.223.163.162"){
echo  $stock=file_get_contents("https://dha.esajee.com/admin/accounts/remaining_stock_rpt.php?barcode={$barcode}&loc={$loc}");	 
	 }else  if($storeip=="203.223.163.218"){
echo  $stock=file_get_contents("https://gulberg.esajee.com/admin/accounts/remaining_stock_rpt.php?barcode={$barcode}&loc={$loc}");	 
	 }else  if($storeip=="203.223.163.170"){
echo  $stock=file_get_contents("https://pharmadha.esajee.com/admin/accounts/remaining_stock_rpt.php?barcode={$barcode}&loc={$loc}");	 
	 }
//echo  $stock=file_get_contents("http://{$storeip}:{$port}{$path}/admin/accounts/remaining_stock_rpt.php?barcode={$barcode}&loc={$loc}");

//$c = json_decode($stock, true); 
//if(count($c) > 0) 
//{
	//$array[] = $c;
	//}
//210.2.171.10echo json_encode($array);
}

//echo json_encode($array);

?> </table>