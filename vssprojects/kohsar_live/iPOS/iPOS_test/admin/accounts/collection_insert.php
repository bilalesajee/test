<?php ob_start();

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

$customerid = $_REQUEST['customerid'];
if($customerid==''){
echo "Send Customer Id";
exit;	
	}
$amount = $_REQUEST['amount'];
if($amount==''){
echo "Send Amount";
exit;	
	}


$date = $_REQUEST['date'];

if($date==''){
echo "Send Date";
exit;	
	}
$date =  strtotime($date);

$fcol	=	array("amount","paymentmethod","fkaccountid","fkaddressbookid","datetime","fkclosingid","billnum");

$vcol	=	array($amount,0,$customerid,0,$date,0,0);
$AdminDAO->insertrow("$dbname_detail.collection",$fcol,$vcol);

$fields_coll    =       array('fkaccountid','fksaleid','amount','paymentmethod','charges','fkbankid','chequedate','chequeno','ccno','datetime','datasent','fkclosingid'); 
$data_coll      =       array($customerid,0,$amount,0,0,0,0,0,0,$date,0,0);
$AdminDAO->insertrow("$dbname_detail.collection4acc",$fields_coll,$data_coll);

file_get_contents("https://main.esajee.com/admin/accounts/update_bal_main.php?type=coll&customerid=".$customerid.'&amount='.$amount);
?>





