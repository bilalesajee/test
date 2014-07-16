<?php
include_once("../includes/security/adminsecurity.php");
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/

if(sizeof($_POST)>0)
{
	$id		=	$_REQUEST['id'];
	$idarr	= 	explode(",", $id);
	$var	=	sizeof($idarr)-1;
	for($i=1; $i<=$var; $i++)
	{
		if($i==1)
		continue;

		$status			=	$_POST['status'];
	
		$fields			=	array('status');
		$data			=	array($status);
		$AdminDAO->updaterow("$dbname_detail.account",$fields,$data, "id = '$idarr[$i]'");
	
	}	
}
else
{
	echo "Unknown Value";
	exit;		
}
?>