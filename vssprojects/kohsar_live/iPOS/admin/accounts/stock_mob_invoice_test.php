<?php ob_start();
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
 
 $jsondata=json_decode($_GET['jsondata'],true);
 $barcode=$jsondata['barcode'];
 $expirydate=strtotime($jsondata['expirydate']);
 $tradeprice=$jsondata['tradeprice'];
 $retailprice=$jsondata['retailprice'];
 $invoice=$jsondata['invoice'];
 $quantity=$jsondata['quantity'];
 //$addby=$_SESSION['addressbookid'];
 $username=$jsondata['username'];
 $storeid = $_SESSION['storeid'];
 $change_price=$jsondata['update_price'] ;


 
 
 
  
///////////////////////////////////////////barcode Check//////////////////////////////////////////////
   $query			=	"SELECT pkbarcodeid,barcode from barcode where barcode = '$barcode'  ";
   $result		=	$AdminDAO->queryresult($query);
   $fkbarid			=	$result[0]['pkbarcodeid'];  
   $barcode_code		=	$result[0]['barcode']; 
   $count_result=count($result);
   
 /////////////////////////////////////Getting PKID////////////////////////////////////////////////////////  
  $query_user		=	"SELECT pkaddressbookid from addressbook where username = '$username'  ";
  $result_user		=	$AdminDAO->queryresult($query_user);
  $pkaddressbookid	=	$result_user[0]['pkaddressbookid'];  
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
if($count_result==0){
	echo "Barcode Does Not Exists ";		 
	exit;
}else{

///////////////////////////////////////////Check Invoice Exsists//////////////////////////////////////////////////////////////////////////////////////////
 $check_invoice		=	$AdminDAO->queryresult("SELECT pksupplierinvoiceid, invoice_status, fksupplierid from $dbname_detail.supplierinvoice where pksupplierinvoiceid= '$invoice'  ");
 $pksupplierinvoiceid =	$check_invoice[0]['pksupplierinvoiceid']; 
 $fksupplierid		  =	$check_invoice[0]['fksupplierid']; 
 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
 if(count($check_invoice) > 0)
 {
	 if($check_invoice[0]['invoice_status'] == 2) 
	 {
		echo "Invoice is void, ";		 
		exit;
	 }
	 
	 if($check_invoice[0]['invoice_status'] == 1) 
	 {
		echo "Invoice is close ";		 
		exit;
	 }
//////////////////////////////////////////////////////Check Barcode and Invoice////////////////////////////////////////////////////////////////	 
  $check_update		=	$AdminDAO->queryresult("SELECT fksupplierinvoiceid,pkstockid,fkbarcodeid from $dbname_detail.stock where fksupplierinvoiceid='$invoice' and fkbarcodeid='$fkbarid' ");
$stockid			=	$check_update[0]['pkstockid'];  
$barcode_stock	    =	$check_update[0]['fkbarcodeid']; 
 
 ////////////////////////////////////////////////////Updation/////////////////////////////////////////////////////////////////////
 if(count($check_update) > 0){  
 
 $field=array("batch","quantity","unitsremaining","expiry","retailprice","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","fksupplierinvoiceid", "addtime");
  $value=array(0,$quantity,0,$expirydate,$retailprice,0,0,0,$fkbarid,$fksupplierid,$storeid,$pkaddressbookid,0,time(),$invoice, time());


if($tradeprice!='')
{
 $field[]='purchaseprice';	
 $field[]='costprice';	
 $field[]='priceinrs';	
 
 $value[]=$tradeprice;	
 $value[]=$tradeprice;	
 $value[]=$tradeprice;
}
$res=$AdminDAO->updaterow("$dbname_detail.stock",$field,$value,"pkstockid='$stockid'");
 }else{
///////////////////////////////////////////////////////Insertion////////////////////////////////////////////////////////////////////	 
$field=array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","fksupplierinvoiceid", "addtime");
$value=array(0,$quantity,0,$expirydate,$tradeprice,$tradeprice,$retailprice,$tradeprice,0,0,0,$fkbarid,$fksupplierid,$storeid,$pkaddressbookid,0,time(),$invoice, time());
$res=$AdminDAO->insertrow("$dbname_detail.stock",$field,$value);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 
}
if($change_price=='true'){
 $change=file_get_contents("https://kohsar.esajee.com/admin/accounts/mobile_changeprice.php?np={$retailprice}&bcid={$fkbarid}&empid={$pkaddressbookid}");
}

if($res > -1){
  echo "Saved Successfully";
}else{
  echo "Not Saved Successfully"; 	
}

   }else{
	    echo "Invoice does not exists"; 
	    exit; 
	 } 

	
}//Barcode Check
  	
	
?>
