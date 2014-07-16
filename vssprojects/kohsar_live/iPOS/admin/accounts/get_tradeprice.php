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
//$jsonbardata=json_decode($_GET['barcode'],true);
//$jsoninvdata=json_decode($_GET['invoice_no'],true);
 
$jsonbardata=$_GET['barcode'];
$jsoninvdata=$_GET['invoice_no'];

 
$query			=	"SELECT pkbarcodeid from barcode where barcode = '$jsonbardata'  ";
$result		=	$AdminDAO->queryresult($query);
$fkbarcodeid			=	$result[0]['pkbarcodeid'];  
if($fkbarcodeid > 0){ 

$qty=0;
$tp=0;
$rp=0;
 $expiry=0;
if($jsoninvdata > 0){

  $query_show_stock1 = "SELECT FROM_UNIXTIME(s.expiry, '%d-%m-%Y') expiry,
 (select sum(gb.unitsremaining) as stock from $dbname_detail.stock gb where gb.fkbarcodeid ='$fkbarcodeid'  and gb.fksupplierinvoiceid='$jsoninvdata' ) as qty ,
 (select round(g.costprice,2) from $dbname_detail.stock g where g.fkbarcodeid ='$fkbarcodeid' order by g.pkstockid desc limit 1) as tp,
  case when ifnull( (select p.price from $dbname_detail.pricechange p where p.fkbarcodeid = '$fkbarcodeid') ,0) = '0' then s.retailprice else ifnull( (select p.price from $dbname_detail.pricechange p where p.fkbarcodeid = '$fkbarcodeid') ,0) end rp
FROM
$dbname_detail.stock s 
WHERE
s.fkbarcodeid ='$fkbarcodeid' and s.fksupplierinvoiceid='$jsoninvdata'  order by s.pkstockid desc limit 1 ";
$reportresult = $AdminDAO->queryresult($query_show_stock1);
 $mobidata = $reportresult[0];	
	 
	}else{
$reportresult = $AdminDAO->queryresult("SELECT  case when ifnull( (select p.price from $dbname_detail.pricechange p where p.fkbarcodeid = '$fkbarcodeid') ,0) = '0' then s.retailprice else ifnull( (select p.price from $dbname_detail.pricechange p where p.fkbarcodeid = '$fkbarcodeid') ,0) end rp
FROM
$dbname_detail.stock s WHERE s.fkbarcodeid ='$fkbarcodeid' limit 1 ");
 $mobidata = $reportresult[0];	

	}


}
echo json_encode($mobidata);
?>
