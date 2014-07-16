<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$newitem	=	$_POST['shortdesc'];
$barcodeid	=	$_POST['pkbarcodeid'];
if($newitem!="" && $barcodeid!="")
{
	$field	=	array('shortdescription');
	$value	=	array($newitem);
	$AdminDAO->updaterow("barcode",$field,$value,"pkbarcodeid='$barcodeid'");
	echo "Item name updated successfully";
	exit;
}
else
{
	echo "Please enter complete information";
	exit;
}
?>