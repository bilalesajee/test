<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
if(sizeof($_POST)>0)
{
	/*echo "<pre>";
	print_r($_POST);
	echo "</pre>";*/
	$sectionname	=	filter($_POST['sectionname']);
    $status			=	$_POST['status'];
    $sectionid		=	$_POST['sectionid'];
	if($sectionname=='')
	{
		echo $error="<li>Section Name must be provided.</li> ";
		exit;
	}
	$fields			=	array('sectionname','status');
	$data			=	array($sectionname,$status);
	if($sectionid==-1)
	{
		$AdminDAO->insertrow("section",$fields,$data);
	}
	else
	{
		$AdminDAO->updaterow("section",$fields,$data,"pksectionid='$sectionid'");
	}
}
else
{
	echo "Insufficient data";
	exit;
}

?>