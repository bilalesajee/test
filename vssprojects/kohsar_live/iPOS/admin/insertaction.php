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
	$actionlabel 	= 	filter($_POST['actionlabel']);
	$actioncode	 	= 	filter($_POST['actioncode']);
	$fkscreenid	 	= 	$_POST['fkscreenid'];
	if($actionlabel=='')
	{
		$msg	=	"<li>Action Name can not be left blank</li>";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	$fields = array('actionlabel','actioncode','fkscreenid');
	$values = array($actionlabel, $actioncode, $fkscreenid);
	if($id!='-1')//updates records 
	{
		$AdminDAO->updaterow("action",$fields,$values," pkactionid='$id' ");
	}
	else
	{
		// this is the add section	
		$id = $AdminDAO->insertrow("action",$fields,$values);
	}//end of else
exit;
}// end post
?>