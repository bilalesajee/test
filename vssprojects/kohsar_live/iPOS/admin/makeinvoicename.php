<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO;

$cid				=	$_REQUEST['cid'];
/****************************PRODUCT DATA*****************************/
if($cid!='')
{
	$invoice_array		=	$AdminDAO->getrows('invoice','count(*) as totalinv ',"fkcountryid='$cid'");
	$totalinv	 		=	$invoice_array[0]['totalinv'];
	$invoice_array		=	$AdminDAO->getrows('countries','code3',"pkcountryid='$cid'");
	$code3		 		=	$invoice_array[0]['code3'];
	$totalinv	=	$totalinv+1;
	$name	=	$code3.'-'.$totalinv.'-'.date('m-y');
}//end of if cid
?>
<input name="invoicename" id="invoicename" type="text" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" readonly="readonly" value="<?php echo $name;?>">
