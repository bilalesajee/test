<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
if(sizeof($_POST)>0)
{
    
	$sectionid		=	$_POST['sectionid'];
	$screen			=	$_POST['screen'];
	$ufields		=	array('fksectionid');
	$udata			=	array(0);
	if(sizeof($screen)<1)
	{
		echo "<li>Please select atleast one screen.</li>";
		exit;
	}
	$AdminDAO->updaterow("screen",$ufields,$udata,"fksectionid='$sectionid'");
	for($i=0;$i<sizeof($screen);$i++)
	{
		$screenid		=	$screen[$i];
		$fields			=	array('fksectionid');
		$data			=	array($sectionid);
		$AdminDAO->updaterow("screen",$fields,$data,"pkscreenid='$screenid'");
	}
}
else
{
	echo "Insufficient data";
	exit;
}
?>