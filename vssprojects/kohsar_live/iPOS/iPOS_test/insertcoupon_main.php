<?php
include("includes/security/adminsecurity.php");
global $AdminDAO;
$id			=	$_REQUEST['id'];


 $closingsession	=	$_SESSION['closingsession'];

$counter	=	$_SESSION['countername'];
//print_r($_REQUEST);
//$status		=	$_REQUEST['status'];paymentmethod
$couponid		=	$_REQUEST['pkcouponid'];
$paymentmethod		=	$_REQUEST['paymentmethod'];
$creditcharges		=	$_REQUEST['creditcharges'];

$cctype		=	$_REQUEST['card'];
$reason		=	$_REQUEST['reason'];
//$status		=	$_REQUEST['status'];
$ccnumber		=	$_REQUEST['ccnumber'];
$fcrate		=	$_REQUEST['fcrate'];
$fccharges		=	$_REQUEST['fccharges'];
$chequenumber		=	$_REQUEST['chequenumber'];
$currencyid				=	$_REQUEST['currency'];
$chequedate				=	$_REQUEST['chequedate'];

if($paymentmethod =='c')
{
$paymenttype	= "c";	
}
else
{
	$paymenttype ="";
	}

if($paymentmethod =='c')
{
$amount		=	$_REQUEST['amount1'];	
}
else if($paymentmethod =='cc')
{
$amount		=	$_REQUEST['amount2'];
$bank		=	$_REQUEST['bank2'];	
}
else if($paymentmethod =='fc')
{
$amount		=	$_REQUEST['amount3'];	
}
else if($paymentmethod =='ch')
{
$amount		=	$_REQUEST['amount4'];	
$bank		=	$_REQUEST['bank'];
}



if(sizeof($_POST)>0)
{
     
	$field		=	array('amount','reason','ccno','fkcctypeid','fkbankid','charges','fccharges','chequeno','chequedate','fkcurrencyid','rate','paymentmethod','fkclosingid','paymenttype','datetime','countername');
	$value		=	array($amount,$reason,$ccnumber,$cctype,$bank,$creditcharges,$fccharges,$chequenumber,$chequedate,$currencyid,$fcrate,$paymentmethod,$closingsession,$paymenttype,time(),$counter);

  if($id=="-1")
	{
		
		echo $AdminDAO->insertrow("$dbname_detail.coupon_management",$field,$value);
		//echo $couponid;
		//$adv_l= file_get_contents("http://192.168.5.119/kohsar_live/customer_balance.php?amount={$amount}&countername={$counter}&reasons={$reasons}");
	}
	else
	{
		$AdminDAO->updaterow("$dbname_detail.coupon_management",$field,$value,"`pkcouponid`='$id'");
		//$adv_l= file_get_contents("http://192.168.5.119/kohsar_live/customer_balance.php?amount={$amount}&countername={$counter}&reasons={$reasons}");
	}
}
?>
