<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
if(sizeof($_REQUEST)>0)
{
	$demandid		=	$_REQUEST['demandid'];
	$productname	=	filter($_REQUEST['productname']);
	$advance		=	filter($_REQUEST['advance']);
	$deadline		=	filter($_REQUEST['deadline']);
	$customerinfo	=	filter($_REQUEST['customerinfo']);
	$description	=	filter($_REQUEST['description']);
	if($productname=='')
	{
		$err.="<li>Product name is left blank please enter Product Name.</li>";	
	}
	if($deadline=='')
	{
		$err.="<li>Dead line is left blank please enter Dead Line.</li>";	
	}

	if($customerinfo=='')
	{
		$err.="<li>Customer Information is left blank please enter Customer information.</li>";	
	}
	if($err!='')
	{
		echo $err;
		exit;
	}

	$fields	=	array("productname","advance","deadline","customerinfo","description","demanddate","fkstoreid");
	$values	=	array($productname,$advance,$deadline,$customerinfo,$description,date('Y-m-d'),$storeid);
	$table	=	"customerdemands";
	if($demandid=='-1')
	{
		$AdminDAO->insertrow($table,$fields,$values);
		exit;
	}
	else
	{
		$AdminDAO->updaterow($table,$fields,$values," pkcustomerdemandsid='$demandid' ");
		exit;
	}
}
?>