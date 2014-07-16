<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
if(sizeof($_POST)>0)
{
	$fname			=	$_POST['fname'];
	$lname			=	$_POST['lname'];
	$company		=	$_POST['company'];
	if($company	== "")
	{
		$msg	.=	"<li>Please enter Company Name of the client.</li>";
	}
	$email			=	$_POST['email'];
	$mobile			=	$_POST['mobile'];
	$address1		=	$_POST['address1'];
	$address2		=	$_POST['address2'];
	if($msg	!="")
	{
		echo $msg;
		exit;
	}
	$clientcountries=	$_POST['clientcountries'];
	$newclientcities=	$_POST['newclientcities'];
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
	$username		=	$_POST['username'];
	$password		=	$_POST['password'];
	$nic			=	$_POST['nic'];
	$afields		=	array('firstname','lastname','email','mobile','phone','address1','address2','fkcityid','fkstateid','zip','fkcountryid','fax','username','password','nic');
	$adata			=	array($fname,$lname,$email,$mobile,$phone,$address1,$address2,$newclientcities,$state,$zip,$clientcountries,$fax,$username,$password,$nic);
	$fkaddressbookid=	$AdminDAO->insertrow("addressbook",$afields,$adata);
	$cfields		=	array('fkaddressbookid','company');
	$cdata			=	array($fkaddressbookid,$company);
	$fkclientid		=	$AdminDAO->insertrow("client",$cfields,$cdata);
	echo $fkclientid;
}
?>