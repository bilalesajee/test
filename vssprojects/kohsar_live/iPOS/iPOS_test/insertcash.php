<?php
include_once("includes/security/adminsecurity.php");
include_once("includes/classes/bill.php");
global $AdminDAO;
$Bill			=	new Bill($AdminDAO);
$saleid					=	$_SESSION['tempsaleid'];
$cash					=	$_POST['cashamount'];
$breakmode				=	$_SESSION['breakmode'];
$hisoryID=$_SESSION['historyid'];
if($breakmode==1)
{
	print"Your counter is in break mode. You are unable to procceed cash sale.";
	exit;
}
if($cash<0)
{
	$tendered	=	0;
}
else
{
	$tendered	=	$cash;
}
/*if($cash=='' || $cash<=0)
{
	echo "Please enter cash amount.";
	exit;
}*/
$price					=	$_POST['remainingprice'];
$time					=	time();
$fields					=	array('fksaleid','paytime','amount','tendered','paymentmethod','fkclosingid','fkloginhistoryid');
if($cash<$price) 
{
	//$data				=	array($saleid,$time,$cash,$tendered,$closingsession);	
	$recamount=$cash;
}
else 
{
//	$data				=	array($saleid,$time,$price,$tendered,'c',$closingsession);
	$recamount=$price;
}
$data				=	array($saleid,$time,$recamount,$tendered,'c',$closingsession,$hisoryID);
$insertid				=	$AdminDAO->insertrow("$dbname_detail.payments",$fields,$data);

//updates the payment in the sale table
$Bill->updatepayment($recamount,'c',$saleid);
?>