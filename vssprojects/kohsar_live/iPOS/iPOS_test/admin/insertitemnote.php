<?php

error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id = $_REQUEST['noteid'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";*/
$items	=	$_POST['products'];
if(sizeof($_POST)>0)
{
	if($id!="-1")
	{
		$AdminDAO->deleterows('itemnote',"fknoteid = '$id'",1);
		$fields	=	array('fknoteid','fkbarcodeid');
		for($i=0;$i<sizeof($items);$i++)
		{
			$data	=	array($id,$items[$i]);
			$AdminDAO->insertrow("itemnote",$fields,$data);
		}
	}
	else
	{
		$fields	=	array('fknoteid','fkbarcodeid');
		for($i=0;$i<sizeof($items);$i++)
		{
			$data	=	array($id,$items[$i]);
			$AdminDAO->insertrow("itemnote",$fields,$data);
		}
	}
}
else
{
	"Invalid Data";
	exit;
}
?>