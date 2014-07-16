<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$V;
$id			=	$_REQUEST['id'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$field		=	array('quotetitle','fkaccountid','addtime','fkaddressbookid','deadline','ponum','terms','status','expired');
	$quotetitle	=	filter($_POST['quotetitle']);
	if(!$quotetitle)
	{
		$msg	.=	"<li>Please select a customer</li>";
	}
	$customer	=	$_POST['customer'];
	if(!$customer)
	{
		$msg	.=	"<li>Please select a customer</li>";
	}
	$deadline	=	strtotime(implode("-",array_reverse(explode("-",$_POST['deadline']))));
	$ponum		=	$_POST['ponum'];
	$terms		=	filter($_POST['terms']);
	$status		=	$_POST['status'];
	$expired	=	$_POST['expired'];
	$value		=	array($quotetitle,$customer,time(),$empid,$deadline,$ponum,$terms,$status,$expired);
	$unique 	= $AdminDAO->isunique("$dbname_detail.purchaseorder", 'pkpurchaseorderid', $id, 'quotetitle', $quotetitle);
	if($unique=='1')
	{
			$msg	.=	"<li>Quotation with this name <b><u>$quotetitle</u></b> already exists. Please choose another name.</li>";
			exit;
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	if($id=="-1")
	{
		
		$AdminDAO->insertrow("$dbname_detail.purchaseorder",$field,$value);
	}
	else
	{
		$AdminDAO->updaterow("$dbname_detail.purchaseorder",$field,$value,"`pkpurchaseorderid`='$id'");
	}
}//else
//echo $err;
?>