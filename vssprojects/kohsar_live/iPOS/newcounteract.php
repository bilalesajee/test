<?php
session_start();
include_once("includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$saleid			=	$_POST['saleid'];
	$counterinfo	=	$_POST['counter'];
	$counterdata	=	explode("-",$counterinfo);
	$countername	=	$counterdata[0];
	$closingid		=	$counterdata[1];
	$addressbookid	=	$counterdata[2];
	$curclosingid	=	$_POST['closingid'];
	// moving sales to new counter
	
	//1. adjusting sales data
	$sfields	=	array('countername','fkuserid','fkclosingid','status');
	$sdata		=	array($countername,$addressbookid,$closingid,3);
	$AdminDAO->updaterow("$dbname_detail.sale",$sfields,$sdata,"pksaleid='$saleid'");	
	//2. adjusting saledetail data
	$sdfields	=	array('fkclosingid');
	$sddata		=	array($closingid);
	$AdminDAO->updaterow("$dbname_detail.saledetail",$sdfields,$sddata,"fksaleid='$saleid'");	
	//3. adjusting payment data
	$AdminDAO->updaterow("$dbname_detail.cashpayment",$sdfields,$sddata,"fksaleid='$saleid'");
	$AdminDAO->updaterow("$dbname_detail.ccpayment",$sdfields,$sddata,"fksaleid='$saleid'");
	$AdminDAO->updaterow("$dbname_detail.fcpayment",$sdfields,$sddata,"fksaleid='$saleid'");
	$AdminDAO->updaterow("$dbname_detail.chequepayment",$sdfields,$sddata,"fksaleid='$saleid'");
	
	//reset sale session
	$_SESSION['tempsaleid']		=	'';
	// added by yasir 21-09-11
	$_SESSION['purchaseorderid']=	'';
	$_SESSION['quotetitle']		=	'';
	//
}
?>