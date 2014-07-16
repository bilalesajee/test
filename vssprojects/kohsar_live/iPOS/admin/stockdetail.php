<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];

global $AdminDAO;
$rights	 	=	$userSecurity->getRights(5);
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
$labels = 	array("ID","Added by","Units Sent","Remaining Units","Retail Price","Cost Price","Trade Price $defaultcurrency","Date Added","Expiry",'Supplier','Location','Invoice Number','Bill Number' );
$fields = 	array("pkstockid","name","quantity","unitsremaining","retailprice","costprice","priceinrs","addtime","expiry",'supplier','storename','fksupplierinvoiceid','billnumber');
$actions 	=	$rights['actions'];

$dest 	= 	'stockdetail.php';
$div	=	"stockdetailsdiv";
$form 	= 	"stockdetails";
define(IMGPATH,'../images/');

	$query 	= 	"SELECT
				CONCAT(firstname, ' ' , lastname) name,
				storename,
				pkstockid,
				stk.quantity,
				fksupplierinvoiceid,
				 billnumber,
				unitsremaining,
				round(retailprice,2) as retailprice,
				round(costprice,2) as costprice,
				round(priceinrs,2) as priceinrs,
				IF(expiry='0','--------',FROM_UNIXTIME(expiry,'%d-%m-%y')) as expiry,
				IF(stk.addtime='0','--------',FROM_UNIXTIME(addtime,'%d-%m-%y')) as addtime,
				IF(pkadjustmentdetailid > '0','Yes','No') as adjustment,
				companyname as supplier
			FROM 
				$dbname_detail.stock stk LEFT JOIN supplier ON (pksupplierid = fksupplierid) LEFT JOIN $dbname_detail.supplierinvoice ON (pksupplierinvoiceid = fksupplierinvoiceid) LEFT JOIN addressbook ON (fkemployeeid=pkaddressbookid) left join $dbname_detail.stock_adjustment_detail sad on (pkstockid=sad.fkstockid) left join store on (pkstoreid	=	fkstoreid) 
					WHERE
				stk.fkbarcodeid 		= '$barcodeid'
				$where group by pkstockid
			";


					
$navbtn	=	"";
if(in_array('11',$actions))
{
 $navbtn.="<a class=\"button2\" id=\"editstock\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'editstock.php','movestock','stockdetailsdiv') title=\"Edit Stock\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			&nbsp;";		
}
					
$navbtn.="<a href=\"javascript: showpage(1,document.$form.checks,'movestock.php','movestock','stockdetailsdiv') \" title=\"Move Stock\"><b>Move Stock</b></a>&nbsp;|
			&nbsp;  <a href=\"javascript: showpage(1,document.$form.checks,'loadunits.php','movestock','stockdetailsdiv') \" title=\"View stock Damages\"><b>View Damages</b></a>&nbsp;|
			&nbsp;  <a href=\"javascript: showpage(1,document.$form.checks,'movedamagestock.php','movestock','stockdetailsdiv') \" title=\"Add stock Damages\"><b>Add Damages</b></a>&nbsp;";			
			
/********** END DUMMY SET ***************/
$totals	=	array("quantity","unitsremaining","retailprice","costprice","priceinrs");
?>
<div id="movestock"></div>
<div id="<?php echo $div;?>">
<div class="breadcrumbs" id="breadcrumbs">Stock Details for <div id="desc"><?php echo $barcode;?> <?php echo $itemdescription;?></div> </div>
<?php
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>
