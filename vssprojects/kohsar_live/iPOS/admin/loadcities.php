<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id				=	$_GET['id'];
$cid			=	$_GET['cid'];
$selected_id	=	$_GET['sid'];
$srccities		=	$AdminDAO->getrows("city","pkcityid,cityname,code","fkcountryid='$id'");
$citysel			=	"<select name=\"$cid\" id=\"$cid\" style=\"width:150px;\"><option value=\"\">Select City</option>";
for($c=0;$c<sizeof($srccities);$c++)
{
	$cityname		=	$srccities[$c]['cityname'];
	$cityid			=	$srccities[$c]['pkcityid'];
	$code			=	$srccities[$c]['code'];
	$select		=	"";
	if($cityid == $selected_id)
	{
		$select = "selected=\"selected\"";
	}
	$citysel2		.=	"<option value=\"$cityid\" $select>$cityname - $code</option>";
}
$cities			=	$citysel.$citysel2."</select>";
echo $cities;
?>