<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
$bcid	=	$_GET['barcodeid'];
/**************RIGHTS***************************/
//$rights	 	=	$userSecurity->getRights(4);
/*$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
*/
/************* DUMMY SET ***************/
$labels = array("ID","Quantity","Remaining","Expiry","Purchase Price","Cost Price","Sale Price","Shipment","Supplier");
$fields = array("pkstockid","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","shipmentname","companyname");
$dest 	= 	'lastpurchases.php';
$div	=	'purchasediv';
$form 	= 	"purchasefrm";	
define(IMGPATH,'../images/');
$query 	= 	"		SELECT 
						pkstockid,
						quantity,
						unitsremaining,
						FROM_UNIXTIME(expiry,'%d-%m-%y') expiry,
						purchaseprice,
						costprice,
						retailprice,
						companyname,
						shipmentname
					FROM
						$dbname_detail.stock,supplier,shipment
					WHERE
						fkbarcodeid		=	'$bcid' AND
						fksupplierid	=	pksupplierid AND
						fkshipmentid	=	pkshipmentid						
					ORDER BY
						pkstockid
					DESC
					";
$navbtn	=	"";
$limit	=	5;
/*
if(in_array('9',$actions))
{
	$navbtn .="	<a class=\"button2\" id=deletestores onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Stores\"><span class=\"deleterecord\">&nbsp;</span></a>";
}
/********** END DUMMY SET ***************/
?><head>
</head>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Purchases</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>
<br />
<br />
<script>
document.getElementById('barcode').focus();
</script>