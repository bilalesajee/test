<?php
include_once("includes/security/adminsecurity.php");
include_once("includes/classes/bill.php");
global $AdminDAO;
$Bill			=	new Bill($AdminDAO);
$time					=	time();
$saleid					=	$_SESSION['tempsaleid'];
$credit					=	trim(filter($_POST['creditamount'])," ");
if($credit<0)
{
	$tendered	=	0;
}
else
{
	$tendered	=	$credit;
}
/*if($credit=='' || $credit<=0)
{
	echo "Please enter credit card amount.";
	exit;
}*/

$creditcharges			=	$_POST['creditcharges'];
$price					=	$_POST['remainingprice'];
$newcard				=	$_POST['newcard'];
$newbank				=	$_POST['newbank'];
if($newcard!='')
{
	$field	=	array("typename");
	$value	=	array($newcard);
	$cctype	=	$AdminDAO->insertrow("cctype",$field,$value);
}
else
{
	$cctype					=	$_POST['card'];
}
//changes to skip the checks
//Date: 11-01-2009
/*if($cctype=='' && $newcard=='')
{
	echo "Please select credit card type.";
	exit;
}*/
if($newbank!='')
{
	$field	=	array("	bankname");
	$value	=	array($newbank);
	$bank	=	$AdminDAO->insertrow("bank",$field,$value);
}
else
{
	$bank					=	$_POST['banks'];
}
$ccno				=	trim(filter($_POST['ccnumber'])," ");
//changes to skip the checks
//Date: 11-01-2009
/*if($ccno=='')
{
	echo "Please enter credit card number.";
	exit;
}*/
$fields				=	array('fksaleid','paytime','amount','ccno','fkcctypeid','fkbankid','charges','tendered','paymentmethod','fkclosingid');
if($credit<$price) 
{
	$recamount	=	$credit;
	//$data				=	array($saleid,$time,$credit,$ccno,$cctype,$bank,$creditcharges,$tendered,$closingsession);
}
else 
{
	$recamount	=	$price;
	//$data				=	array($saleid,$time,$price,$ccno,$cctype,$bank,$creditcharges,$tendered,$closingsession);
}
$data				=	array($saleid,$time,$recamount,$ccno,$cctype,$bank,$creditcharges,$tendered,'cc',$closingsession);
$insertid			=	$AdminDAO->insertrow("$dbname_detail.payments",$fields,$data);

//updates the payment in the sale table
$Bill->updatepayment($recamount,'cc',$saleid);
?>