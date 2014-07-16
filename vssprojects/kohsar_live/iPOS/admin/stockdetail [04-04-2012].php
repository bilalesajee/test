<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];

global $AdminDAO;
$barcodeid	=	$_REQUEST['id'];
if($groupid!=$ownergroup)
{
	//$where=" AND fkstoreid='$storeid' ";
}

/*if($barcode)
{
	$_SESSION['bcx']	=	$barcode;
}
else
{
	$barcode	=	$_SESSION['bcx'];
}*/
$barcodeids			=	$AdminDAO->getrows("barcode","barcode,itemdescription","pkbarcodeid='$barcodeid'");
$barcode			=	$barcodeids[0]['barcode'];
$itemdescription	=	$barcodeids[0]['itemdescription'];
/****************** the start ********************/
$labels = array("ID","Units Sent","Remaining Units","Retail Price","Cost Price","Trade Price $defaultcurrency","Expiry",'Location');
$fields = array("pkstockid","quantity","unitsremaining","retailprice","costprice","priceinrs","expiry",'storename');
$dest 	= 	'stockdetail.php';
$div	=	"stockdetailsdiv";
$form 	= 	"stockdetails";
define(IMGPATH,'../images/');

	$query 		= 	"SELECT 
					storename,
					CONCAT('$storeid',':',pkstockid) as pkstockid,
					quantity,
					unitsremaining,
					round(retailprice,2) as retailprice,
					round(costprice,2) as costprice,
					round(priceinrs,2) as priceinrs,
					IF(expiry='0','--------',FROM_UNIXTIME(expiry,'%d-%m-%y')) as expiry
				FROM 
					$dbname_detail.stock,
					store
				WHERE
					pkstoreid	=	fkstoreid AND
					fkbarcodeid = '$barcodeid' AND
				 	fkstoreid='$storeid' 
				";


					
$navbtn="<a class=\"button2\" id=\"editstock\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'editstock.php','movestock','stockdetailsdiv') title=\"Edit Stock\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			&nbsp;  <a href=\"javascript: showpage(1,document.$form.checks,'movestock.php','movestock','stockdetailsdiv') \" title=\"Move Stock\"><b>Move Stock</b></a>&nbsp;|
			&nbsp;  <a href=\"javascript: showpage(1,document.$form.checks,'loadunits.php','movestock','stockdetailsdiv') \" title=\"View stock Damages\"><b>View Damages</b></a>&nbsp;";
/********** END DUMMY SET ***************/
?>
<div id="movestock"></div>
<div id="<?php echo $div;?>">
<div class="breadcrumbs" id="breadcrumbs">Stock Details for <div id="desc"><?php echo $barcode;?> <?php echo $itemdescription;?></div> </div>
<?php
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>