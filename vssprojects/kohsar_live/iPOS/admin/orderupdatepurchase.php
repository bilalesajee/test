<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$id				=	$_POST['id'];
	$expiry			=	implode("-",array_reverse(explode("-",$_POST['expiry'])));
	$weight			=	$_POST['weight'];
	$quantity		=	$_POST['quantity'];
	$purchaseprice	=	$_POST['purchaseprice'];
	$batch			=	$_POST['batch'];
	$shipmentid		=	$_POST['fkshipmentid'];
	$supplierid		=	$_POST['supplierid'];
	$barcodeid		=	$_POST['barcodeid'];
	$orderid		=	$_POST['orderid'];
	$addressbookid	=	$_SESSION['addressbookid'];
	$addtime		=	date('Y-m-d h:i:s',time());
	if($quantity =='' || $quantity ==0){
		$msg	.=	"<li>Please enter the quantity</li>";
	}
	if($expiry!=''){
		if($expiry<date('Y-m-d')){
			$msg	.=	"<li>Please enter valid expiry date</li>";
		}
	}	
	$fields		=	array('fkaddressbookid','datetime','fkshipmentid','fkorderid','fkbarcodeid','quantity','purchaseprice','weight','fksupplierid','batch','expiry');
	$data		=	array($addressbookid,$addtime,$shipmentid,$orderid,$barcodeid,$quantity,$purchaseprice,$weight,$supplierid,$batch,$expiry);
	if($msg)
	{
		echo $msg;
		exit;
	}
	else
	{
	/*echo "<pre>";
	print_r($data);
	echo "</pre>";
	exit;*/
		$AdminDAO->updaterow("orderpurchase",$fields,$data," pkorderpurchaseid='$id'");
	}
	exit;
}// end post
?>