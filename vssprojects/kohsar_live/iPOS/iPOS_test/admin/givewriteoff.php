<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
//woamount='+woamount+'&actualamount='+actualamount+'&pksaleid='+pksaleid
if(sizeof($_GET)>0)
{
	$woamount			=	$_GET['woamount'];
	$actualamount		=	$_GET['actualamount'];
	$pksaleid			=	$_GET['pksaleid'];
	$favouredbyid		=	$_GET['favouredbyid'];
	$woamount			=	$actualamount-$woamount;	
	$timedate			=	time();	
	/*$sql="insert 
				into 
					$dbname_detail.baddebts
				SET
					amount='$woamount',
					actualamount='$actualamount',
					permitedbyid='$favouredbyid',
					updatedbyid='$empid',
					datetime='$timedate',
					fkbillid='$pksaleid'";
		$AdminDAO->queryresult($sql);*/
		//$sqlupd="";	
		$fields		=	array('amount','actualamount','permitedbyid','updatedbyid','datetime','fkbillid');
		$values		=	array($woamount,$actualamount,$favouredbyid,$empid,$timedate,$pksaleid);		
		$insertid 	=	$AdminDAO->insertrow("$dbname_detail.baddebts",$fields,$values);
}
?>