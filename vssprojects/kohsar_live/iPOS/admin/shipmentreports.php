<?php 
session_start();

//echo $_SERVER['QUERY_STRING'];
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(8);
if($groupid != $ownergroup)
{
	// $where=" AND sh.fkstoreid='$storeid' ";
}
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$deltype	=	'delshipment';
include_once("delete.php"); 
$dest 	= 	'shipmentreports.php';
$div	=	'maindiv';
$form 	= 	"frm1";	
define(IMGPATH,'../images/');
//***********************sql for record set**************************
					$query 	= 	"SELECT 
						sh.pkshipmentid,
						sh.shipmentname,
						currencysymbol,
						storename,
						round(sh.exchangerate,2) as exchangerate,
						round(sh.totalvalue,2) as totalvalue,
						round(sh.amountinrs,2) as amountinrs,
						(SELECT COUNT(pkshiplistdetailsid) FROM shiplistdetails WHERE fkshipmentid=sh.pkshipmentid) totalorders,
						round((Select SUM(s.purchaseprice*d.quantity*sh.exchangerate) as damages from $dbname_detail.stock s LEFT JOIN 																										$dbname_detail.damages d ON (fkstockid = pkstockid), shipment where s.fkshipmentid = sh.pkshipmentid GROUP by fkshipmentid),2) as totalcharges,
						IF(shipmentdate='0','--------',FROM_UNIXTIME(sh.shipmentdate, '%d-%m-%y')) as shipmentdate,
						IF(shipmentdate='0','--------',FROM_UNIXTIME(sh.shipmentdate, '%Y-%m-%d')) as sortingdate,
						c.countryname
					FROM
						store,
						shipment sh 
							LEFT JOIN currency ON (pkcurrencyid = shipmentcurrency) 
							LEFT JOIN countries c ON (sh.fkcountryid = c.pkcountryid)
					WHERE
						pkstoreid = fkdeststoreid AND 
						sh.shipmentdeleted <> 1
						$where
					GROUP BY
						pkshipmentid
					";
//*******************************************************************
//echo $query;
$navbtn	=	"";
$navbtn .="			<a href=\"javascript: showpage(1,document.$form.checks,'viewshipmentreporter.php','subsection','maindiv', 'supplier') \" title=\"\"><b>Supplier</b></a>&nbsp;|";
$navbtn .="&nbsp;	<a href=\"javascript: showpage(1,document.$form.checks,'viewshipmentreporter.php','subsection','maindiv','brand') \" title=\"\"><b>Brand</b>&nbsp;|</a>";
$navbtn .="&nbsp;	<a href=\"javascript: viewshiplist('') \" title=\"\"><b>Whole</b>&nbsp;|</a>";
$navbtn .="&nbsp;	<a href=\"javascript: viewshiplist('Received') \" title=\"\"><b>Received</b>&nbsp;|</a>";
$navbtn .="&nbsp;	<a href=\"javascript: viewshiplist('Not Purchased') \" title=\"\"><b>Not Purchased</b>&nbsp;|</a>";
$navbtn .="&nbsp;	<a href=\"javascript: showpage(1,document.$form.checks,'viewshipmentreporter.php','subsection','maindiv','price') \" title=\"\"><b>Price</b>&nbsp;|</a>";
$navbtn .="&nbsp;	<a href=\"javascript: showpage(1,document.$form.checks,'viewshipmentreporter.php','subsection','maindiv','product') \" title=\"\"><b>Product</b>&nbsp;|</a>";
$navbtn .="&nbsp;	<a href=\"javascript: showpage(1,document.$form.checks,'viewshipmentreporter.php','subsection','maindiv','source') \" title=\"\"><b>Source</b>&nbsp;|</a>";
$navbtn .="&nbsp;	<a href=\"javascript: viewshiplist('Return') \" title=\"\"><b>Return</b>&nbsp;|</a>";
$navbtn .="&nbsp;	<a href=\"javascript: viewshiplist('Expired') \" title=\"\"><b>Expire</b>&nbsp;|</a>";
$navbtn .="&nbsp;	<a href=\"javascript: viewshiplist('Extra') \" title=\"\"><b>Extra</b>&nbsp;|</a>";
$navbtn .="&nbsp;	<a href=\"javascript: viewshiplist('Damages') \" title=\"\"><b>Damages</b></a>";
/********** END DUMMY SET ***************/
//showpage(1,document.$form.checks,'shipreport.php','subsection','maindiv')
?>
</head>
<div id="sgroups"></div>
<div id="scharges"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Shipments</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>
<br />
<br />
<div id="sugrid"></div>
<script language="javascript">
function viewshiplist(status)
{
	var ids	=	selectedstring.split(',');
	var	id	=	(ids[1]);
	//if(status=='received'){
		param="status="+status;
	//}
	if(id=='undefined')
	{
		alert('Please select at least one record to display.');
	}
	else
	{
		var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1';
 		window.open('supplierreport.php?shipmentid='+ids[1]+'&'+param,'Ship List',display); 
	}
}
</script>