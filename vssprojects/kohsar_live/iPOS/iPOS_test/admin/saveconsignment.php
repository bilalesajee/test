<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
//header('Content-Disposition: attachment; filename="query.sql"');
$id			=	$_GET['id'];
$statusres	=	$AdminDAO->getrows("consignment","fkstatusid,log","pkconsignmentid='$id'");
$status		=	$statusres[0]['fkstatusid'];
if($status != 8)
{
	echo "<script>alert('Either this movement has already been received or you have not processed it yet. Make sure the movement status is \"Not Received\"');window.close();</script>";
	exit;
}
else
{
	$filename	=	$id.".sql";
	$handle		=	fopen($filename,"w+");
	$string		=	$statusres[0]['log'];
	fwrite($handle,$string);
	fclose($handle);
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT");
	header("Content-type: application/x-download");
	header('Content-Length: '.filesize($filename));
	header("Content-Disposition: attachment; filename=".basename($filename).";");
	header("Content-Transfer-Encoding: text");
	readfile($filename);
	//updating consignment
	$consignment		=	"UPDATE consignment SET fkstatusid=7 WHERE pkconsignmentid='$id'";
	//$AdminDAO->queryresult($consignment);
			$tblj	= 	'consignment';
			$field	=	array('fkstatusid');
			$value	=	array('7');
			$AdminDAO->updaterow($tblj,$field,$value,"pkconsignmentid='$id'");	
				
	$consignmentdetail	=	"UPDATE consignmentdetail SET newretailprice=retailprice,receivedby='$empid',receivedquantity=quantity,updatetime=UNIX_TIMESTAMP(NOW()),receivetime=UNIX_TIMESTAMP(NOW()),fkdamagetypeid=1,consignmentdetailstatus=1 WHERE fkconsignmentid='$id'";
	//$AdminDAO->queryresult($consignmentdetail);
			$tblj	= 	'consignmentdetail';
			$field	=	array('newretailprice','receivedby','receivedquantity','updatetime','receivetime','fkdamagetypeid','consignmentdetailstatus');
			$value	=	array('retailprice',$empid,'quantity','UNIX_TIMESTAMP(NOW())','UNIX_TIMESTAMP(NOW())','1','1');
			$AdminDAO->updaterow($tblj,$field,$value,"fkconsignmentid='$id'");		
}
?>