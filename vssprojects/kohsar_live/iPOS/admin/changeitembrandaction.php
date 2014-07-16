<?php
session_start();
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$barcodeid 		=	$_REQUEST['id'];
$barcodeid		=	explode(',',$barcodeid);
$brandid 		=	$_REQUEST['brandid'];
$prebrandid 	=	$_REQUEST['prebrandid'];
if($brandid=='')
{
		echo $error="<li>Brand name must be provided.</li> ";
		exit;
}
foreach($barcodeid as $bc)
{
	if($bc!='')
	{
		if($bc!='' && $brandid!='')
		{
			// && $prebrandid!=''
			// and fkbrandid='$prebrandid'
			///$sql	=	"UPDATE barcodebrand set fkbrandid='$brandid' where fkbarcodeid='$bc'";

			$tblj	= 	'barcodebrand';
			$field	=	array('fkbrandid');
			$value	=	array($brandid);
			
			$AdminDAO->updaterow($tblj,$field,$value,"fkbarcodeid='$bc'");			
			///$AdminDAO->queryresult($sql);
			//3. Updating Product name ...
			$AdminDAO->updateproductname($bc);
		}
	}
}
exit;
?>