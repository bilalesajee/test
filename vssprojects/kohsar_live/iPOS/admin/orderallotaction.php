<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$fkaddressbookid=	$_SESSION['addressbookid'];
$shipmentid		=	$_GET['shipmentid'];
foreach($_POST as $key=>$value)
{
	list($box,$orderpurchaseid,$storeid,$orderid) =	explode("_",$key);
	$condition		=	" fkorderpurchaseid='$orderpurchaseid' AND fkstoreid = '$storeid' AND fkorderid = '$orderid' AND fkshipmentid= '$shipmentid' ";
	$orderallotid 	=	$AdminDAO->getcolumn("orderallot","pkorderallotid",$condition);
	$date			=	date("Y-m-d H:i:s");
	if($orderallotid)
	{
		
		$fields	=	array("fkaddressbookid","quantity","datetime");
		$values	=	array($fkaddressbookid,$value,$date);
		$where	=	" pkorderallotid = $orderallotid";
		$AdminDAO->updaterow("orderallot",$fields,$values,$where);
	}
	else
	{
		$fields	=	array("fkaddressbookid","datetime","fkshipmentid","fkorderid","fkorderpurchaseid","fkstoreid","quantity");
		$values	=	array($fkaddressbookid,$date,$shipmentid,$orderid,$orderpurchaseid,$storeid,$value);
		$AdminDAO->insertrow("orderallot",$fields,$values);
	}
}//foreach
echo "Shipment Allocation Assigned Successfully.";
?>