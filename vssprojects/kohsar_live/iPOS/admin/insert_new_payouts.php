<?php
//error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;


	

		
		 $pkaccountpaymentid = $_REQUEST['pkaccountpaymentid'];
		
		  $accounttitle = $_REQUEST['accounttitle']; 
		 
	  $reasons = $_REQUEST['reasons']; 
	// echo  $pkaccountpaymentid = $_REQUEST['pkaccountpaymentid']; 
	
if($accounttitle &&  $reasons)
{	
		$fields		=	array('fkaccountid','editreasons');
		  $values		=	array($accounttitle,$reasons);
			$table		=	"$dbname_detail.accountpayment";
		$AdminDAO->updaterow($table,$fields,$values,"pkaccountpaymentid='$pkaccountpaymentid'");

}
else
{

	if($accounttitle)
{
  $fields		=	array('fkaccountid');
  $values		=	array($accounttitle);
	$table		=	"$dbname_detail.accountpayment";
$AdminDAO->updaterow($table,$fields,$values,"pkaccountpaymentid='$pkaccountpaymentid'");

}

}
echo 'yes';
   
?>