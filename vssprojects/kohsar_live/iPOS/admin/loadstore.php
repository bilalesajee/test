<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$desttype	=	$_GET['type'];
$selected	=	$_GET['id'];
if($desttype==1)
{
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, if condition added
		$storearray	= 	$AdminDAO->getrows("store","pkstoreid,storename", "storedeleted<>1");
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		$storearray	= 	$AdminDAO->getrows("store","pkstoreid,storename", "storedeleted<>1 AND storestatus=1");
	}//end edit
	$storesel	=	"<select name=\"store\" id=\"store\" style=\"width:150px;\" ><option value=\"\" >Select Store</option>";
	for($i=0;$i<sizeof($storearray);$i++)
	{
		$storename		=	$storearray[$i]['storename'];
		$storeid		=	$storearray[$i]['pkstoreid'];
		$select			=	"";
		if($storeid == $selected)
		{
			$select = "selected=\"selected\"";
		}
		$storesel2		.=	"<option value=\"$storeid\" $select>$storename</option>";
	}
	$store			=	$storesel.$storesel2."</select>";
	echo $store;	
}
else
{
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, if condition added
		$clientarray	= 	$AdminDAO->getrows("client","pkclientid,company", "1");
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		$clientarray	= 	$AdminDAO->getrows("client","pkclientid,name", "client_status='active'");
	}//end edit
	$clientsel	=	"<select name=\"client\" id=\"client\" style=\"width:150px;\" ><option value=\"\" >Select Client</option>";
	for($i=0;$i<sizeof($clientarray);$i++)
	{
		if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, if condition added
			$company		=	$clientarray[$i]['company'];
		}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
			$company		=	$clientarray[$i]['name'];
		}//end edit
		$clientid		=	$clientarray[$i]['pkclientid'];
		$select			=	"";
		if($clientid == $selected)
		{
			$select = "selected=\"selected\"";
		}
		$clientsel2		.=	"<option value=\"$clientid\" $select>$company</option>";
	}
	$client			=	$clientsel.$clientsel2."</select>";
	echo $client;	
}
?>