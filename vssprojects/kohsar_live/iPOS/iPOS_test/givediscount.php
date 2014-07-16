<?php 
include_once("includes/security/adminsecurity.php");
global $AdminDAO;

if(sizeof($_POST)>0)
{     
	$discount	=	$_POST['discount'];
	
	// added by yasir - 04-07-11
	$total	=	$_POST['total'];
	if ($discount < 0 || $discount > $total){
		echo "Please enter valid amount for discount."; 
		exit;
	}
	//
	$fivepercent	=	(5*$total)/100;
	if ($discount > $fivepercent && $_GET['conf']!=1){
		echo "Greater than five percent discount."; 
		exit;	
	}	
	
	$fields		=	array('globaldiscount');
	$values		=	array($discount);
	$saleid		=	$_SESSION['tempsaleid'];
	$AdminDAO->updaterow("$dbname_detail.sale",$fields,$values,"pksaleid='$saleid'");
		
	echo "Discount added successfully.";
	exit;
}
else
{
	echo "Invalid Data.";
	exit;
}
?>