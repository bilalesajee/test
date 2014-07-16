<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];
$barcode	=	$_REQUEST['bcx'];
if($barcode)
{
	$_SESSION['bcx']	=	$barcode;
}
else
{
	$barcode	=	$_SESSION['bcx'];
}
$barcodeids	=	$AdminDAO->getrows("barcode","pkbarcodeid","barcode='$barcode'");
$barcodeid	=	$barcodeids[0]['pkbarcodeid'];
/****************** the start ********************/
$labels = array("ID","Units Sent","Remaining Units","Retail Price","Cost Price","Trade Price $defaultcurrency","Expiry");
$fields = array("pkstockid","quantity","unitsremaining","retailprice","costprice","priceinrs","expiry");
$dest 	= 	'bcstock.php';
$div	=	"bcstock";
$form 	= 	"stockdetails";
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
				pkstockid,
				quantity,
				unitsremaining,
				round(retailprice,2) as retailprice,
				round(costprice,2) as costprice,
				round(priceinrs,2) as priceinrs,
				IF(expiry='0','--------',FROM_UNIXTIME(expiry,'%d-%m-%y')) as expiry
			FROM 
				$dbname_detail.stock
			WHERE
				fkstoreid = '$storeid' AND 
				fkbarcodeid = '$barcodeid'
			";

					
$navbtn="";
/********** END DUMMY SET ***************/
?>
<p>
<div style="position:absolute;margin:101px 0 0 33px;width:921px;z-index:1000;border:#999;background:#FFF;border:2px solid #f70;">
<?php
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>