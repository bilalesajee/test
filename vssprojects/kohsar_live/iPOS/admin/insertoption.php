<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
$id		=	$_REQUEST['id'];
$attid	=	$_POST['attid'];

if(sizeof($_POST)>0)
{
	$optionname	=	filter($_POST['optionname']);
	if($optionname=="")
	{
		echo "Option Name field can not be left blank";
		exit;
	}
	
	if($id=='')
	{
		
		$unique = $AdminDAO->getrows('attributeoption', 'COUNT(*) as c'," attributeoptionname='$optionname' AND fkattributeid='$attid' ");
		
		
		if($unique[0]['c']>0)
		{
			$unique=1;	
		}
	}
	else
	{
		$unique = $AdminDAO->getrows('attributeoption', 'COUNT(*) as c'," attributeoptionname='$optionname' AND fkattributeid='$attid' AND pkattributeoptionid<>'$id'");
		//$unique = $AdminDAO->isunique('attributeoption', 'pkattributeoptionid', $id, 'attributeoptionname', $optionname);
		if($unique[0]['c']>0)
		{
			$unique=1;	
		}
	}
	if($unique=='1')
	{
		echo "Option with this name <b><u>$optionname</u></b> already exists. Please choose another name.";	
		exit;
	}
	$fields		=	array('attributeoptionname','fkattributeid');
	$values		=	array($optionname,$attid);	
	if($id!="")
	{	
		$res = $AdminDAO->getrows("attributeoption","fkattributeid", "pkattributeoptionid = '$id'");
		$attid = $res[0]['fkattributeid'];
		$values =	array($optionname,$attid);
		$AdminDAO->updaterow("attributeoption",$fields,$values, " pkattributeoptionid = '$id'");		
		//print"$attid";
	}
	else
	{
		$AdminDAO->insertrow("attributeoption",$fields,$values);	
		//print"$attid";
	}
}
?>