<?php
include_once("../includes/security/adminsecurity.php");

global $AdminDAO,$V;


$id			=	$_REQUEST['id'];


 //$closingsession	=	$_SESSION['closingsession'];

//$counter	=	$_SESSION['countername'];

//$status		=	$_REQUEST['status'];paymentmethod

$barcodeid		=	$_REQUEST['barcodeid'];
$pkbarcodeid		=	$_REQUEST['pkbarcodeid'];
$barcode		=	$_REQUEST['barcode'];
$fkbarcodeid		=	$_REQUEST['fkbarcodeid'];
$reorderlevel		=	$_REQUEST['reorderlevel'];



if(sizeof($_POST)>0)
{
     
	$field		=	array('barcode','reorderlevel','fkbarcodeid','datetime');
	$value		=	array($barcode,$reorderlevel,$barcodeid,time());

  if($id=="-1")
	{
		
	 $AdminDAO->insertrow("$dbname_detail.re_order_level",$field,$value);
		
	}
	else
	{
			$field1		=	array('barcode','reorderlevel','fkbarcodeid','datetime');
	$value1		=	array($barcode,$reorderlevel,$fkbarcodeid,time());
		$AdminDAO->updaterow("$dbname_detail.re_order_level",$field1,$value1,"`fkbarcodeid`='$id'");
		
	}
}
?>