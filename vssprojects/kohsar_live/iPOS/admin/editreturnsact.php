<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*print_r($_POST);
exit;*/
$returnid		=	$_POST['returnid'];
$stockid		=	$_POST['stockid'];
$qty			=	$_POST['qty'];
$original		=	$_POST['original'];
$updateqty		=	$original-$qty;
$returnstatus	=	$_POST['returnstatus'];
$returntype		=	$_POST['returntype'];
if(sizeof($_POST)>0)
{
	//adjusting returns
	$field		=	array("quantity","fkreturntypeid","returnstatus");
	$value		=	array($qty,$returntype,$returnstatus);
	$AdminDAO->updaterow("$dbname_detail.returns",$field,$value,"pkreturnid = '$returnid'");
	//adjusting stocks
	$field2		=	array("unitsremaining");
	$value2		=	array("unitsremaining+$updateqty");
	$AdminDAO->updaterow("$dbname_detail.stock",$field2,$value2,"pkstockid='$stockid'");
		
	//$stockquery	=	"UPDATE $dbname_detail.stock SET unitsremaining=unitsremaining+$updateqty WHERE pkstockid='$stockid'";
	//$AdminDAO->queryresult($stockquery);
}//else
?>