<?php

error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 	= 	$_REQUEST['id'];
$qs		=	$_SESSION['qstring'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if($id!="-1")
{
	// this is the edit section
	$packs = $AdminDAO->getrows("packing","*"," pkpackingid='$id'");
	foreach($packs as $pack)
	{
		$packingname = $pack['packingname'];
	}
}
if(sizeof($_POST)>0)
{
		$newpackname 	= 	filter($_POST['packname']);
		$packingid		=	$_POST['packing'];
		$shipmentid		=	$_POST['shipmentid'];
		if($newpackname=='')
		{
			echo"Box Name can not be left Blank.";
			exit;
		}
		$fields = array('packingname','fkpackingid','fkshipmentid');
		$values = array($newpackname,$packingid,$shipmentid);

	if($id!='-1')//updates records 
	{
		$AdminDAO->updaterow("packing",$fields,$values," pkpackingid='$id' ");
	}
	else
	{
		// this is the add section	
		$id = $AdminDAO->insertrow("packing",$fields,$values);
	}//end of else
exit;
}// end post
?>