<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;

//deleterecord($tbl,$pk,$value)
if($_REQUEST['oper']=='del'){
//	echo "<pre>";
	//print_r($_POST);
//	echo "</pre>";
	//echo "called";exit;
}
//print_r($_REQUEST);
//exit;
if(sizeof($_POST)>0)
{
	$fksupplierid		=	$_POST['fksupplierid'];
	$batch				=	$_POST['batch'];
    $expiry				=	$_POST['expiry'];
    $quantity			=	$_POST['quantity'];
    $weight				=	$_POST['weight'];
    $purchaseid			=	$_POST['id'];
	$purchaseprice		=	$_POST['purchaseprice'];
	$addressbookid		=	$_SESSION['addressbookid'];
	
	
//	$boxbarcode			=	$_POST['boxbarcode'];
//    $barcode			=	$_POST['barcode'];
//    $itemdescription	=	$_POST['itemdescription'];
//
//    $lasttradeprice		=	$_POST['lasttradeprice'];
//    $purchaseprice		=	$_POST['purchaseprice'];

//    $boxno				=	$_POST['box'];
 //   $boxitem			=	$_POST['boxtotal'];

	$shiplistval		=	$_POST['shiplistid'];
	$shiplistdetailsid	=	$_POST['shiplistdetailsid'];
	$currencyid			=	$_POST['shipcurrency'];
	$exchangerate		=	$_POST['exchangerate'];
	$shipmentid			=	$_POST['shipmentid'];

	$purchasetime		=	time();
	
	if($quantity =='' || $quantity ==0){
		$msg	.=	"<li>Please enter the quantity</li>";
	}
	if($weight 	==''){
		$msg	.=	"<li>Please enter the weight</li>";
	}
	if($batch 	==''){
		$msg	.=	"<li>Please enter the batch</li>";
	}
	
	$expiry			=	implode("-",array_reverse(explode("-",$expiry)));
	//exit;
	if($expiry!=''){
		if($expiry<date('Y-m-d')){
			$msg	.=	"<li>Please enter valid date [DD-MM-yyyy]</li>";
		}
	}	
	$fields		=	array('quantity','weight'	,'fksupplierid'	,'batch','expiry'	,'fkaddressbookid'	,'purchaseprice');
	$data		=	array($quantity	,$weight	,$fksupplierid	,$batch	,$expiry	,$addressbookid		,$purchaseprice);
	// here we need to update the quantity in the packing table.
	if($msg)
	{
		echo $msg;
		exit;
	}else{
		//$AdminDAO->queryresult($Query);
		$AdminDAO->updaterow("purchase",$fields,$data," pkpurchaseid='$purchaseid' ","","0");
		
		//echo "1";
	}
	exit;

	
}// end post
?>
