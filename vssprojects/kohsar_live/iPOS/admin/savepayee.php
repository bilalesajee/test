<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;

if(sizeof($_POST)>0)
{
/*	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	exit;*/
	$id				=	$_GET['new'];
	$saleid			=	$_SESSION['tempsaleid'];
	//$ctype		=	$_POST['ctype'];
	$acclimit		=	trim(filter($_POST['acclimit'])," ");
	$acctitle		=	trim(filter($_POST['acctitle'])," ");
	$status			=	$_POST['status'];
	$creationdate	=	time();
	
	$payeeid		=	$_POST['payeeid'];	
	if($payeeid!='' && $payeeid>0)
	{		
	    $unique	=	$AdminDAO->getrows("$dbname_detail.account","id"," title='$acctitle' AND id <> '$payeeid'");
		if(sizeof($unique)>0)
		{
			echo "A payee with this title $acctitle already exists";
			exit;
		}
		$field2		=	array('title','creationdate','status','accountlimit');
		$data2		=	array($acctitle,$creationdate,$status,$acclimit);
		$AdminDAO->updaterow("$dbname_detail.account",$field2,$data2,"id='$payeeid'");
	}
	else
	{
	    $unique	=	$AdminDAO->getrows("$dbname_detail.account","id"," title='$acctitle' AND id <> '$payeeid'");
		if(sizeof($unique)>0)
		{
			echo "A payee with this title $acctitle already exists";
			exit;
		}		
		$field2		=	array('title','creationdate','fkaddressbookid','status','accountlimit');
		$data2		=	array($acctitle,$creationdate,$_SESSION['addressbookid'],$status,$acclimit);
		$insertid2	=	$AdminDAO->insertrow("$dbname_detail.account",$field2,$data2);
	}
}
else
{
	echo "insufficient data";
	exit;
}
?>