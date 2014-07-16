<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$id 			=	$_POST['id'];
	$quantity 		=	$_POST['quantity'];
	$prevquantity 	=	$_POST['prevquantity'];
	$packnumber 	=	$_POST['packnumber'];
	if($quantity>$prevquantity)
	{
		$msg="Pack Quantity could not be increased from here";
	}
	if($packnumber=='' || $packnumber==0)
	{
		$msg.="Packing packnumber can not be left Blank.";
	}
	if($quantity=='' || $quantity==0)
	{
		$msg.="Please enter valid packing quantity.";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	$fields	=	array('packnumber','quantity');
	$data	=	array($packnumber,$quantity);
	$AdminDAO->updaterow("orderpack",$fields,$data,"pkorderpackid='$id'");
}// end post
?>