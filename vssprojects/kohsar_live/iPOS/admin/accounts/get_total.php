<?php
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
	
include_once("../../includes/security/adminsecurity.php");
global $AdminDAO;
if(sizeof($_REQUEST)>0)
{
	$customerid=$_REQUEST['customerid'];
	// $query="SELECT SUM(totalamount) as totalamount, SUM(globaldiscount) as discount ,ROUND(SUM(cash)) as cash,ROUND(SUM(cc)) as cc,ROUND(SUM(fc)) as fc,ROUND(SUM(cheque)) as cheque     FROM $dbname_detail.sale	WHERE fkaccountid='$customerid'";
  
   $query="SELECT customer_sale as totalamount, customer_discount as discount ,customer_payment FROM main.customer	WHERE pkcustomerid='$customerid'";
  
    $customer_total		=	$AdminDAO->queryresult($query);
	$bal=$customer_total[0]['totalamount'];
	$disc=$customer_total[0]['discount'];
	/*$cash=	$customer_total[0]['cash'];
    $cc  =	$customer_total[0]['cc'];
    $fc	 =	$customer_total[0]['fc'];
    $cheque	=	$customer_total[0]['cheque'];
   $totalpaid=	floor($cash+$cc+$fc+$cheque);*/
   $totalpaid=$customer_total[0]['customer_payment'];
	echo json_encode(array('sale' => $bal,'dicount'=>$disc,'totalpaid'=>$totalpaid));
	
}

?>
