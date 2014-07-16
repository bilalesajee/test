<?php 
include_once("includes/security/adminsecurity.php");
global $AdminDAO;

if(sizeof($_POST)>0)
{     

	$couponid	=	$_POST['discount'];
	 $scoupon	=	$AdminDAO->getrows("$dbname_detail.coupon_management","amount,status","pkcouponid='$couponid'");

     //$discount	=	$_POST['discount'];
	 $amount_coupon	=	$scoupon[0]['amount'];
	 $status	=	$scoupon[0]['status'];
	
	if($status=='0' || $status=='2' || $status=="")
	{
		echo "Invalid Coupon"; 
		exit;
		}
		else
		{

	$fields		=	array('globaldiscount','fkcouponid');
	$values		=	array($amount_coupon,$couponid);
	$saleid		=	$_SESSION['tempsaleid'];
	$AdminDAO->updaterow("$dbname_detail.sale",$fields,$values,"pksaleid='$saleid'");
	
	$fields_coupon		=	array('status');
	$values_coupon		=	array('2');
	$AdminDAO->updaterow("$dbname_detail.coupon_management",$fields_coupon,$values_coupon,"pkcouponid='$couponid'");
	echo " Coupon amount added successfully.";

	exit;
	 
}
}
?>