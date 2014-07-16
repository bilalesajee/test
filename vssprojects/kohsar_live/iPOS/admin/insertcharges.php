<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id	=	$_REQUEST['id'];
if(sizeof($_POST)>0)
{
/*	echo "<pre>";
	print_r($_POST);
	echo "</pre>";*/
	$name			=	filter($_POST['chargename']);
	if($name=="")
	{
		echo "Charge Name can not be left empty";
		exit;
	}
	if($name)
	{
		$unique = $AdminDAO->isunique('charges', 'pkchargesid', $id, 'chargesname', $name);
		if($unique=='1')
		{
				echo"Charge with this name <b><u>$name</u></b> already exists. Please choose another name.";	
				exit;
		}
	}
	if($id!='-1')
	{
		$fields			=	array('pkchargesid','chargesname');
		$values			=	array($id,$name);	
		$AdminDAO->updaterow("charges",$fields,$values,"pkchargesid = '$id'");
	}
	else
	{
		$fields			=	array('chargesname');
		$values			=	array($name);	
		$AdminDAO->insertrow("charges",$fields,$values);
		
	}
}
else
{
	echo "false";
}
?>