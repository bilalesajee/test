<?php
include_once("includes/security/adminsecurity.php");
include_once("includes/classes/bill.php");
global $AdminDAO;
$Bill			=	new Bill($AdminDAO);

$time					=	time();
$saleid					=	$_SESSION['tempsaleid'];
$chequeamount			=	trim(filter($_POST['chequeamount'])," ");
if($chequeamount<0)
{
	$tendered	=	0;
}
else
{
	$tendered	=	$chequeamount;
}
/*if($chequeamount=='' || $chequeamount<=0)
{
	echo "Please enter cheque amount.";
	exit;
}*/
$chequenumber			=	$_POST['chequenumber'];
$bank					=	$_POST['bank'];
$newbank				=	$_POST['newbank'];
if($newbank!='')
{
	$field	=	array("	bankname");
	$value	=	array($newbank);
	$bank	=	$AdminDAO->insertrow("bank",$field,$value);
}
if($chequenumber=='')
{
	echo "Please enter cheque number.";
	exit;
}
if($newbank=='' && $bank=='')
{
	echo "Please select a bank.";
	exit;
}
$price					=	$_POST['remainingprice'];
$chequedate				=	$_POST['chequedate'];
$fields				=	array('fksaleid','paytime','amount','fkbankid','chequeno','chequedate','tendered','paymentmethod','fkclosingid');
if($chequeamount<$price) 
{
	$recamount	=	$chequeamount;
	//$data				=	array($saleid,$time,$chequeamount,$bank,$chequenumber,$chequedate,$tendered,$closingsession);
}
else
{
	$recamount	=	$price;
	//$data				=	array($saleid,$time,$price,$bank,$chequenumber,$chequedate,$tendered,$closingsession);
}
$data				=	array($saleid,$time,$recamount,$bank,$chequenumber,$chequedate,$tendered,'ch',$closingsession);
$insertid			=	$AdminDAO->insertrow("$dbname_detail.payments",$fields,$data);

//updates the payment in the sale table
$Bill->updatepayment($recamount,'ch',$saleid);

?>