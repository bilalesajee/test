<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id = $_REQUEST['id'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$screenname 	= 	filter($_POST['screenname']);
	$filename	 	= 	filter($_POST['filename']);
	$module		 	= 	$_POST['module'];
	$visibility 	= 	$_POST['visibility'];
	$displayorder 	= 	filter($_POST['displayorder']);
	if($screenname=='')
	{
		$msg	=	"<li>Screen Name can not be left blank</li>";
	}
	if($filename=='')
	{
		$msg	.=	"<li>File Name can not be left blank</li>";
	}
	if($module=='')
	{
		$msg	.=	"<li>Please select a module</li>";
	}
	if($visibility=='')
	{
		$msg	.=	"<li>Please select visibility</li>";
	}
	if($displayorder=='')
	{
		$msg	.=	"<li>Please enter display order</li>";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	if($screenname)
	{
		$unique = $AdminDAO->isunique('screen', 'pkscreenid', $id, 'screenname', $screenname);
		if($unique=='1')
		{
				echo"Screen with this name <b><u>$screenname</u></b> already exists. Please choose another name.";	
				exit;
		}
	}
	$fields = array('screenname','url','fkmoduleid','visibility','displayorder');
	$values = array($screenname, $filename, $module,$visibility,$displayorder);
	if($id!='-1')//updates records 
	{
		$AdminDAO->updaterow("screen",$fields,$values," pkscreenid='$id' ");
	}
	else
	{
		// this is the add section	
		$id = $AdminDAO->insertrow("screen",$fields,$values);
	}//end of else
exit;
}// end post
?>