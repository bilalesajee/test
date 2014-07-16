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


 $query			=	"SELECT pkbarcodeid,barcode from barcode where barcode = '$barcode'  ";
 $result		=	$AdminDAO->queryresult($query);

 $fkbarid			=	$result[0]['pkbarcodeid'];  
 $barcode_code		=	$result[0]['barcode']; 
 $count_result=count($result);
 
 
 
  $query_user		=	"SELECT pkaddressbookid from addressbook where username = '$username'  ";
  $result_user		=	$AdminDAO->queryresult($query_user);
  $pkaddressbookid	=	$result_user[0]['pkaddressbookid'];  



if($count_result==0){
	echo "Barcode Does Not Exists ";		 
	exit;
}else{



if($change_price=='true'){
//	echo "Price here";
	 $change=file_get_contents("https://kohsar.esajee.com/admin/accounts/mobile_changeprice.php?np={$retailprice}&bcid={$fkbarid}&empid={$pkaddressbookid}");
}
////////////////check barcodeid in stock///////////////////////////////
 $query_stock			=	"SELECT fksupplierinvoiceid,pkstockid,fkbarcodeid from $dbname_detail.stock where fksupplierinvoiceid='$invoice' and fkbarcodeid='$fkbarid' ";
 $result_stock		=	$AdminDAO->queryresult($query_stock);

echo "<br>";
echo $stockid			=	$result_stock[0]['pkstockid'];  
echo "<br>";
echo  $barcode_stock	    =	$result_stock[0]['fkbarcodeid']; 
 
 ////////////////////////////////////////////////////
 if(count($result_stock) > 0){
	
  $query1			=	"SELECT pksupplierinvoiceid, invoice_status, fksupplierid from $dbname_detail.supplierinvoice where pksupplierinvoiceid= '$invoice'  ";
  $result1		=	$AdminDAO->queryresult($query1);
  $pksupplierinvoiceid			=	$result1[0]['pksupplierinvoiceid']; 
  $fksupplierid			=	$result1[0]['fksupplierid']; 
  if(count($result1) > 0){
	  
	 if($result1[0]['invoice_status'] == 2) 
	 {
		echo "Invoice is void, ";		 
		exit;
	 }
	 
	 if($result1[0]['invoice_status'] == 1) 
	 {
		echo "Invoice is close ";		 
		exit;
	 }
	
	
 $field		=	array("batch","quantity","unitsremaining","expiry","retailprice","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","fksupplierinvoiceid", "addtime");
	$value		=		array(0,$quantity,0,$expirydate,$retailprice,0,0,0,$fkbarid,$fksupplierid,$storeid,$pkaddressbookid,0,time(),$invoice, time());


if($tradeprice!='')
{
 $field[]='purchaseprice';	
 $field[]='costprice';	
 $field[]='priceinrs';	
 
 $value[]=$tradeprice;	
 $value[]=$tradeprice;	
 $value[]=$tradeprice;
  $res=$AdminDAO->updaterow("$dbname_detail.stock",$field,$value,"pkstockid='$stockid'");
}
$res=$AdminDAO->updaterow("$dbname_detail.stock",$field,$value,"pkstockid='$stockid'");

 /*echo "<pre>";
print_r($value);
*/
  if($res==0)
  {
	   
	  echo "Saved Successfully";
	  }
	  else
	  {
		 
		 echo "Not Saved Successfully"; 
		  }
  } else
 {
	  echo "Invoice does not exists"; 
	 }
	
	
}else{



  $query1			=	"SELECT pksupplierinvoiceid, invoice_status, fksupplierid from $dbname_detail.supplierinvoice where pksupplierinvoiceid= '$invoice'  ";
 $result1		=	$AdminDAO->queryresult($query1);
 $pksupplierinvoiceid			=	$result1[0]['pksupplierinvoiceid']; 
 $fksupplierid			=	$result1[0]['fksupplierid']; 
 if(count($result1) > 0)
 {
	 if($result1[0]['invoice_status'] == 2) 
	 {
		echo "Invoice is void, ";		 
		exit;
	 }
	 
	 if($result1[0]['invoice_status'] == 1) 
	 {
		echo "Invoice is close ";		 
		exit;
	 }


  $field		=	array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","fksupplierinvoiceid", "addtime");
	$value		=		array(0,$quantity,0,$expirydate,$tradeprice,$tradeprice,$retailprice,$tradeprice,0,0,0,$fkbarid,$fksupplierid,$storeid,$pkaddressbookid,0,time(),$invoice, time());
/*echo "<pre>";
print_r($value);
*/ $res=$AdminDAO->insertrow("$dbname_detail.stock",$field,$value);




  if(intval($res))
  {
	  
	  echo "Saved Successfully";
	  }
	  else
	  {
		 echo "Not Saved Successfully"; 
		  }
  } else
 {
	  echo "Invoice does not exists"; 
	 }
	
}
	}
?>
