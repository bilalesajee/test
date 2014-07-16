<?php
include("includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs,$DiscountDAO;
$action		=	$_REQUEST['action'];
if($action=='del')
{
	$saledetailid=	filter($_REQUEST['saledetailid']);
	$dstockid	=	filter($_REQUEST['stockid']);
	$boxsize	=	filter($_REQUEST['boxsize']);
	$quantity	=	filter($_REQUEST['quantity']);
	$price		=	filter($_REQUEST['price']);
	if($boxsize>0 && $quantity)
	{
		$quantity=$quantity*$boxsize;
	}
	if($quantity>0)
	{
		$sql="UPDATE $dbname_detail.stock set unitsremaining=(unitsremaining+$quantity) where pkstockid='$dstockid'";//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
		//$field=array("unitsremaining");
		//$value=array("unitsremaining+".$quantity);
		$AdminDAO->queryresult($sql);	
	}
	session_start();
	$saleid		=	$_SESSION['tempsaleid'];
	$delreason	=	$_REQUEST['delreason'];
	$AdminDAO->deleterows("$dbname_detail.saledetail","  fkstockid='$dstockid' AND fksaleid='$saleid' AND boxsize='$boxsize' AND saleprice='$price'","1");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
	$field	=	array("fksaleid","fkstockid","datetime","fkresonid");
	$data	=	array($saleid,$dstockid,time(),$delreason);
	
	$AdminDAO->insertrow("$dbname_detail.saledeleteditems",$field,$data);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
	echo "The Item has been deleted from the sale.";
	exit;
}
?>