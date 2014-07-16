<?php
session_start();
include("includes/security/adminsecurity.php");
global $AdminDAO;
$stockid		=	$_REQUEST['stockid'];
$type			=	$_REQUEST['type'];
$productprice	=	$AdminDAO->getrows("$dbname_detail.stock","*","pkstockid='$stockid'");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012

if($type=='price')
{
	$boxprice		=	$productprice[0]['retailprice'];
}
else
{
	$boxprice		=	$productprice[0]['boxprice'];
}
?>
<script language="javascript" type="text/javascript">
	document.getElementById('price').value='<?php echo $boxprice;?>';

</script>