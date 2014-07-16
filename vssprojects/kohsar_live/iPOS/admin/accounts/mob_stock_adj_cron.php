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
global $AdminDAO;
$remarks	=	"Mobile Adjusted Stock";

$getstr_id=$AdminDAO->queryresult("SELECT pkstoreid from main.store where storedb='$dbname_detail'");	
$stor_id_=$getstr_id[0]['pkstoreid'];
$result_getstk=$AdminDAO->queryresult("SELECT * from main.mobile_stock_adj where status=0 and location='$stor_id_' order by id asc");	

foreach($result_getstk as $row){
	
	$pkbarcodeid=$row['fkbarcodeid'];	
	$quantity=$row['quantity'];
	$result_stock_=$row['orgquantity'];
	$add_by=$row['addby'];
	$tam=$row['datetime'];
	$add=$row['added'];
	$sub=$row['subtracted'];
	$id__=$row['id'];
	
	
    $get_stkid=$AdminDAO->queryresult("SELECT pkstockid   from $dbname_detail.stock where fkbarcodeid='$pkbarcodeid' order by pkstockid desc limit 1  ");
	$lateststkid=$get_stkid[0]['pkstockid'];    	
    if($lateststkid > 0){	
	if($add > $sub){
	$type=0;
	$q="update $dbname_detail.stock set unitsremaining=unitsremaining+$add where pkstockid='$lateststkid'";
	}else{
	$type=1;
	$q="update $dbname_detail.stock set unitsremaining=unitsremaining-$sub where pkstockid='$lateststkid'";	
	}

	
	
	
	$field		=	array('addtime','remarks','add_time','add_by');
    $value		=	array($tam,$remarks,$tam,$add_by);
	$pkstockadjustmentid= $AdminDAO->insertrow("$dbname_detail.stock_adjustment",$field,$value);		
	$field_detail		=	array('fkbarcodeid','fkstockadjustmentid','quantity','type','orgquantity','fkstockid','datetime');
    $value_detail		=	array($pkbarcodeid,$pkstockadjustmentid,$quantity,$type,$result_stock_,$lateststkid,$tam);
    $AdminDAO->insertrow("$dbname_detail.stock_adjustment_detail",$field_detail,$value_detail);	
    $AdminDAO->queryresult($q);	
	
	file_get_contents("https://main.esajee.com/admin/accounts/set_mob_adj.php?id=".$id__);
	
	}
}
?>