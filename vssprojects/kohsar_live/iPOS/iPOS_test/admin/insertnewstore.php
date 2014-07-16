<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
if(sizeof($_POST)>0)
{
	$newstorename	=	$_POST['newstorename'];
	if($newstorename == "")
	{
		$msg	.=	"<li>Please enter Store Name.</li>";
	}
	$email			=	$_POST['email'];
	$address		=	$_POST['address'];
	$clientcountries=	$_POST['clientcountries'];
	$newclientcities=	$_POST['newclientcities'];
	if($msg	!="")
	{
		echo $msg;
		exit;
	}
	if($newclientcities == "")
	{
		$newclientcities	=	$_POST['clientcities'];
	}
	else
	{
		$field		=	array('cityname','fkcountryid');
		$value		=	array($newclientcities,$clientcountries);
		$newclientcities	=	$AdminDAO->insertrow('city',$field,$value);
	}
	$state			=	$_POST['state'];
	$zip			=	$_POST['zip'];
	$phone			=	$_POST['phone'];
	$fax			=	$_POST['fax'];
	$sfields		=	array('storename','storephonenumber','storeaddress','fkcityid','fkstateid','zipcode','fkcountryid','email','fax');
	$sdata			=	array($newstorename,$phone,$address,$newclientcities,$state,$zip,$clientcountries,$email,$fax);
	$fkdeststoreid	=	$AdminDAO->insertrow("store",$sfields,$sdata);
	echo $fkdeststoreid;
}
?>