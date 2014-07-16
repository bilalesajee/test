<?php
include_once("../../includes/security/adminsecurity.php");
global $AdminDAO,$V;
$id 			=	$_GET['id'];
$name 			=	$_POST['name'];
$description	=  	$_POST['description'];
$category_id	=	$_POST['categories'];
$creation_date	=	"";
if($name=='')
{
	print"Type name can not be left blank.";
	exit;
}
if($category_id ==0)
{
	print"Type Category can not be left blank";
	exit;
}
if($name)
{
	$unique = $AdminDAO->isunique('type', 'id', $id, 'name', $code)   ;
	if($unique=='1')
	{
		echo "Type Name <b><u>$code</u></b> already exists. Please provide any other name.";	
		exit;
	}
}
$field	=	array('name','description','category_id');
$value	=	array("$name","$description",$category_id);
if($id > 0)
{
	$AdminDAO->updaterow('type',$field,$value," id='$id' ");
}
else
{
	$field[]	=	"creation_date";
	$value[]	=	time();
	$AdminDAO->insertrow('type',$field,$value);
}
exit;
?>