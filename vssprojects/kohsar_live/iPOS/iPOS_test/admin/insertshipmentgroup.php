<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$V;
	$id				=	$_REQUEST['id'];
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
		$groupname		=	$_REQUEST['groupname'];
		$percentage		=	$_REQUEST['percentage'];
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		$groupname		=	filter($_REQUEST['groupname']);
		$percentage		=	filter($_REQUEST['percentage']);
	}//end edit
	$namearray	=	$AdminDAO->getrows("shipmentgroups","pkshipmentgroupid"," shipmentgroupname='$groupname' AND pkshipmentgroupid<>'$id'");
	if(count($namearray)>0)
	{
		$msg.="<li>This Group name <u>$groupname</u> already exists.</li>";
	}
	if($groupname=='')
	{
		$msg.="<li><u>Group name</u> is left blank.</li>";
	}
	if($percentage=='')
	{
		$msg.="<li><u>Percentage</u> is left blank.</li>";
	}
	if($msg!='')
	{
		echo $msg;
		exit;
	}
	$table		=	"shipmentgroups";
	$field		=	array("shipmentgroupname","percentage");
	$value		=	array($groupname,$percentage);
	if($id=='-1')
	{
		$AdminDAO->insertrow($table,$field,$value);
	}
	else
	{
		$AdminDAO->updaterow($table,$field,$value," pkshipmentgroupid='$id' ");
	}
exit;
?>