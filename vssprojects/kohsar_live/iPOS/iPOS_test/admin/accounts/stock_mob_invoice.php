

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

 $jsondata=json_decode($_GET['jsondata'],true);
 
 $barcode=$jsondata['barcode'];
 $expirydate=strtotime($jsondata['expirydate']);
$tradeprice=$jsondata['tradeprice'];
$retailprice=$jsondata['retailprice'];
 $invoice=$jsondata['invoice'];
 $quantity=$jsondata['quantity'];
 //$addby=$_SESSION['addressbookid'];
 $username=$jsondata['username'];
 
 $query			=	"SELECT pkbarcodeid from barcode where barcode = '$barcode'  ";
 $result		=	$AdminDAO->queryresult($query);
 $barcode1			=	$result[0]['pkbarcodeid'];  
 
  $query_user			=	"SELECT pkaddressbookid from addressbook where username = '$username'  ";
 $result_user		=	$AdminDAO->queryresult($query_user);
  $pkaddressbookid			=	$result_user[0]['pkaddressbookid'];  


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
	$value		=		array(0,$quantity,0,$expirydate,$tradeprice,$tradeprice,$retailprice,$tradeprice,0,0,0,$barcode1,$fksupplierid,0,$pkaddressbookid,0,time(),$invoice, time());

 $res=$AdminDAO->insertrow("$dbname_detail.stock",$field,$value);
 /////////////////////////////////////////////////amount updation in supplierinvoice table///////////////////////////////////////////////
  $query_invoice			=	"SELECT SUM( s.quantity * s.priceinrs ) AS invoice_amount
              FROM $dbname_detail.stock s
			  left join $dbname_detail.supplierinvoice sup  on sup.pksupplierinvoiceid = s.fksupplierinvoiceid
              WHERE s.fksupplierinvoiceid = '$invoice'
               GROUP BY s.fksupplierinvoiceid ";
             $result_invoice		=	$AdminDAO->queryresult($query_invoice);

             $invoice_amount			=	$result_invoice[0]['invoice_amount'];
			 
			 
			 
			    $fields_invoice		=	array("invamount");
				$data_invoice			=	array($invoice_amount);
				$AdminDAO->updaterow("$dbname_detail.supplierinvoice",$fields_invoice,$data_invoice,"pksupplierinvoiceid='$invoice'");
 
 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
  
?>
