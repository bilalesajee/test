<?php
include_once("includes/security/adminsecurity.php");
$hisoryID=$_SESSION['historyid'];
global $AdminDAO;
if(sizeof($_POST)>0)
{
	$declaredamount	=	$_POST['amount'];
	if($declaredamount=='')
	{
		echo "Please enter opening amount";
		exit;
	}
	// fetching last counter id
	$counterinfo	=	$AdminDAO->getrows("$dbname_detail.closinginfo","MAX(closingnumber) closingnumber","countername='$countername'");
/*	echo "<pre>";
	print_r($counterinfo);
	echo "</pre>";*/
	$closingnum		=	$counterinfo[0]['closingnumber'];
	if($closingnum=='')
	{
		$closingnum	=	1;
	}
	else
	{
		$closingnum++;
	}
//	echo "the counter number is $closingnum";
//	exit;
	$field	=	array("openingdate","fkaddressbookid","countername","closingnumber","fkstoreid","openingbalance","fkloginhistoryid");
	$data	=	array(time(),$empid,$countername,$closingnum,$storeid,$declaredamount,$hisoryID);
	$closingsession	=	$AdminDAO->insertrow("$dbname_detail.closinginfo",$field,$data);
	$_SESSION['closingsession']=$closingsession;
}
?>