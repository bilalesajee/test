<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$V;
$id			=	$_REQUEST['did'];
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
	$name		=	$_REQUEST['name'];
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$name		=	filter($_REQUEST['name']);
}//end edit
if(sizeof($_POST)>0)
{
	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		if($name=='')
		{
			echo $error="<li>Damage type must be provided.</li> ";
			exit;
		}
	}//end edit
	$field		=	array('damagetype');
	$value		=	array($name);
	$unique = $AdminDAO->isunique('damagetype', 'pkdamagetypeid', $id, 'damagetype', $name);
	if($unique=='1')
	{
			echo "<li>Currency with this name <b><u>$name</u></b> already exists. Please choose another name.</li>";
			exit;
	}
	else if($id=="-1")
	{
		
		$AdminDAO->insertrow('damagetype',$field,$value);
	}
	else
	{
		$AdminDAO->updaterow('damagetype',$field,$value,"`pkdamagetypeid`='$id'");
	}
}//else
//echo $err;
?>