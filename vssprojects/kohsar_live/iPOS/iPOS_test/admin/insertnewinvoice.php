<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$qs,$Key;
$invoicename	=	$_POST['invoicename']; 
$country		=	$_POST['country'];

if($invoicename=='')
{
	$err="<li>Invoice name is left blank.";	
}
if($country=='')
{
	$err.="<li>Invoice Country is not selected.";		
}
if($err1='')
{
	echo $err;
	exit;
}
//echo 
/*$sql="INSERT 
				INTO 
					invoice
				SET
					pkinvoiceid='".$AdminDAO->pkey('invoice','pkinvoiceid')."',
					invoicename='$invoicename',
					fkcountryid='$country',
					datetime='".time()."',
					fkemployeeid='$empid'
				";*/
//$AdminDAO->queryresult($sql);

if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition

	$pkey		=	$AdminDAO->pkey('invoice','pkinvoiceid');
	$fields		=	array('pkinvoiceid','invoicename','fkcountryid','datetime','fkemployeeid');
	$values		=	array($pkey,$invoicename,$country,time(),$empid);
	$table		=	"invoice";


	$AdminDAO->insertrow($table,$fields,$values);
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$sql="INSERT 
			
				INTO 
					invoice
				SET
					pkinvoiceid='".$AdminDAO->pkey('invoice','pkinvoiceid')."',
					invoicename='$invoicename',
					fkcountryid='$country',
					datetime='".time()."',
					fkemployeeid='$empid'
				";
			$pkinvoiceid =	$AdminDAO->pkey('invoice','pkinvoiceid');	
			$datetimej 	 =	time();		
			
			$tblj		 = 	"invoice";
			$field		 =	array('pkinvoiceid','invoicename','fkcountryid','datetime','fkemployeeid');
			$value		 =	array(
						$pkinvoiceid,
						$invoicename,
						$country,
						$datetimej,
						$empid	);
			
	$AdminDAO->insertrow($tblj,$field,$value);			
}//end edit
?>
