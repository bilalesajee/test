<?php
include_once("includes/security/adminsecurity.php");
global $AdminDAO;
include_once("includes/classes/bill.php");
$Bill			=	new Bill($AdminDAO);
$time					=	time();
$saleid					=	$_SESSION['tempsaleid'];

$breakmode				=	$_SESSION['breakmode'];
if($breakmode==1)
{
	print"Your counter is in break mode. You are unable to procceed foreign currency sale.";
	exit;
}

$fcamount				=	trim(filter($_POST['fcamount'])," ");
if($fcamount<0)
{
	$tendered	=	0;
}
else
{
	$tendered	=	$fcamount;
}
$currencyid				=	$_POST['currency'];
$newfc					=	trim($_POST['newfc']," ");
$fcrate					=	$_POST['fcrate'];
if($newfc!='')
{
	if($fcrate=='')
	{
		print"Please enter Currency exchange rate to continue.";
		exit;
	}
	else
	{
		$f=array("currencysymbol","rate");	
		$v=array($newfc,$fcrate);	
		$currencyid	=	$AdminDAO->insertrow("currency",$f,$v);
	}
}
if($currencyid == "")
{
	echo "Please select at least one currency.";
	exit;
}
/*if($fcamount=='' || $fcamount<=0)
{
	echo "Please enter foreign currency amount.";
	exit;
}*/
$fccharges				=	$_POST['fccharges'];
$price					=	$_POST['remainingprice'];
$price					=	round(($price/$fcrate),2);
$price					=	ceil($price);
$fields					=	array('fksaleid','paytime','amount','fkcurrencyid','fcamount','fctendered','rate','charges','tendered','returned','paymentmethod','fkclosingid');
if($fcamount<$price) 
{
		$recamount	=	$fcamount;
	//$data				=	array($saleid,$time,$fcamount,$currencyid,$fcrate,$fccharges,$tendered,$returned,$closingsession);
}
else 
{
		$recamount	=	$price;
	//$data				=	array($saleid,$time,$price,$currencyid,$fcrate,$fccharges,$tendered,$returned,$closingsession);
}
$data				=	array($saleid,$time,$recamount*$fcrate,$currencyid,$recamount,$tendered,$fcrate,$fccharges,$tendered*$fcrate,$returned,'fc',$closingsession);
$insertid			=	$AdminDAO->insertrow("$dbname_detail.payments",$fields,$data);

//updates the payment in the sale table
$Bill->updatepayment($recamount*$fcrate,'fc',$saleid); // $recamoun*$fcrate replaced from $recamoun by Yasir -- 06-07-11
?>