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
	$fieldname 		= 	filter($_POST['fieldname']);
	$fieldlabel	 	= 	filter($_POST['fieldlabel']);
	$fkscreenid	 	= 	$_POST['fkscreenid'];
	if($fieldname=='')
	{
		$msg	=	"<li>Field Name can not be left blank</li>";
	}
	if($fieldlabel=='')
	{
		$msg	.=	"<li>Field Label can not be left blank</li>";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	$fields = array('fieldname','fieldlabel','fkscreenid');
	$values = array($fieldname, $fieldlabel, $fkscreenid);
	if($id!='-1')//updates records 
	{
		$AdminDAO->updaterow("field",$fields,$values," pkfieldid='$id' ");
	}
	else
	{
		// this is the add section	
		$id = $AdminDAO->insertrow("field",$fields,$values);
	}//end of else
exit;
}// end post
?>