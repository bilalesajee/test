<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$V;
$id			=	$_REQUEST['id'];
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
	$name		=	$_REQUEST['name'];
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$name		=	filter($_REQUEST['name']);
}//end edit
$desc		=	filter($_REQUEST['desc']);
$status		=	$_REQUEST['status'];
if(sizeof($_POST)>0)
{
	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		if($name=='')
		{
			echo $error="<li>Reason title must be provided.</li> ";
			exit;
		}
	}//end edit
	$field		=	array('reasontitle','reasondiscription','reasonsatus');
	$value		=	array($name,$desc,$status);
	$unique = $AdminDAO->isunique('discountreason', 'pkreasonid', $id, 'reasontitle', $name);
	if($unique=='1')
	{
			echo "<li>Discount Reason with this name <b><u>$name</u></b> already exists. Please choose another name.</li>";
			exit;
	}
	else if($id=="-1")
	{
		
		$AdminDAO->insertrow('discountreason',$field,$value);
	}
	else
	{
		$AdminDAO->updaterow('discountreason',$field,$value,"`pkreasonid`='$id'");
	}
}//else
//echo $err;
?>