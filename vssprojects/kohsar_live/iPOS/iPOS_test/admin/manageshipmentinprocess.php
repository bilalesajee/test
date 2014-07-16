<?php 
session_start();
//echo $_SERVER['QUERY_STRING'];
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(67);
if($groupid != $ownergroup)
{
	// $where=" AND sh.fkstoreid='$storeid' ";
}
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$deltype	=	'delshipment';
include_once("delete.php"); 
$dest 	= 	'manageshipmentinprocess.php';
$div	=	'maindiv';
$form 	= 	"frm1";	
define(IMGPATH,'../images/');
//***********************sql for record set**************************
/*$query 	= 	"SELECT 
						sh.pkshipmentid,
						sh.shipmentname,
						currencysymbol,
						storename,
						round(sh.exchangerate,2) as exchangerate,
						round(sh.totalvalue,2) as totalvalue,
						round(sh.amountinrs,2) as amountinrs,
						round((Select SUM(s.purchaseprice*d.quantity*sh.exchangerate) as damages from $dbname_detail.stock s LEFT JOIN 																										$dbname_detail.damages d ON (fkstockid = pkstockid), shipment where s.fkshipmentid = sh.pkshipmentid GROUP by fkshipmentid),2) as totalcharges,
						IF(shipmentdate='0','--------',FROM_UNIXTIME(sh.shipmentdate, '%d-%m-%y')) as shipmentdate,
						(SELECT SUM(chargesinrs)
							FROM shipmentcharges sch LEFT JOIN charges ON (sch.fkchargesid = pkchargesid )
							WHERE chargesdeleted <>1 AND
							sch.fkshipmentid = sh.pkshipmentid
							GROUP BY sch.fkshipmentid) as charges,
						c.countryname,
						round(((SELECT SUM(chargesinrs)
							FROM shipmentcharges sch LEFT JOIN charges ON (sch.fkchargesid = pkchargesid )
							WHERE chargesdeleted <>1 AND
							sch.fkshipmentid = sh.pkshipmentid
							GROUP BY sch.fkshipmentid)/amountinrs * 100),2) as percharges
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
					";*/
					 $query 	= 	"SELECT 
						sh.pkshipmentid,
						statusname,
						sh.shipmentname,
						currencysymbol,
						IF(fkclientid<>0,name,storename) storename,
						round(sh.exchangerate,2) as exchangerate,
						round(sh.totalvalue,2) as totalvalue,
						round(sh.amountinrs,2) as amountinrs,
						(SELECT COUNT(pkorderid) FROM `order` WHERE fkshipmentid=sh.pkshipmentid) totalorders,
						IF(shipmentdate='0','--------',FROM_UNIXTIME(sh.shipmentdate, '%d-%m-%y')) as shipmentdate,
						IF(shipmentdate='0','--------',FROM_UNIXTIME(sh.shipmentdate, '%Y-%m-%d')) as sortingdate,						
						c.countryname
					FROM
						shipment sh 
							LEFT JOIN currency ON (pkcurrencyid = shipmentcurrency) 
							LEFT JOIN countries c ON (sh.fkcountryid = c.pkcountryid)
							LEFT JOIN client ON (fkclientid=pkclientid)
							LEFT JOIN store ON (pkstoreid = fkdeststoreid)
							JOIN shipstatuses ON (fkstatusid=pkstatusid)
					WHERE						
						sh.shipmentdeleted <> 1	AND sh.fkstatusid=3						
						$where
					GROUP BY
						pkshipmentid DESC
					";//and	pkstatusid not in (5,7,11) 	
					//round((Select SUM(s.purchaseprice*d.quantity*sh.exchangerate) as damages from $dbname_detail.stock s LEFT JOIN 																										$dbname_detail.damages d ON (fkstockid = pkstockid), shipment where s.fkshipmentid = sh.pkshipmentid GROUP by fkshipmentid),2) as totalcharges,
//*******************************************************************
//echo $query;
$navbtn	=	"";
if(in_array('158',$actions))
{
	$navbtn .="&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'ordershipadd.php','subsection','maindiv') \" title=\"Add Order\"><b>Add Order</b></a>&nbsp;";
}
if(in_array('154',$actions))
{
	$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'vieworders.php','subsection','maindiv') \" title=\"View Orders\"><b>View Orders</b></a>&nbsp;";	
}
if(in_array('150',$actions))
{
$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'orderpurchase.php','scharges','maindiv','order','$formtype') \" title=\"Purchase\"><b>Purchase</b></a>&nbsp;";
}
if(in_array('111',$actions))
{
	$navbtn .="|&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'orderpurchases.php','scharges','maindiv') \" title=\"Purchased Items\"><b>View Purchased</b></a>&nbsp;";
}
if(in_array('157',$actions))
{
	$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'changestatus.php','subsection','maindiv') \" title=\"Change Status\"><b>Change Status</b></a>&nbsp;";
}
//$navbtn .=" <a href=\"javascript: printsupllierreport('')\" title=\"Shipment Report\"><b> Supplier Report </b></a>&nbsp;";
/********** END DUMMY SET ***************/
//showpage(1,document.$form.checks,'shipreport.php','subsection','maindiv')
//*******************advanced data filters arrays**************************************************************//
$filter[]		=	array('shipment','Shipment',"shipmentname");//param 4 is alias name
$filter[]		=	array('countries','Country','countryname');
$filter[]		=	array('store','Requested by','storename');
//************************************************************end of advanced data filters arrays******************//
?>
</head>
<div id="sgroups"></div>
<div id="scharges"></div>
<div id="subsection"></div>
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
function printshoppinglist()
{
	var ids	=	selectedstring.split(',');
	var	id	=	(ids[1]);
	if(id==undefined)
	{
		alert('Please select at least one record to display.');
	}
	else
	{
		var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1';
 		window.open('shoppinglist.php?id='+ids[1],'Shopping List',display); 
	}
}
function shipmentlabels()
{
	var ids	=	selectedstring.split(',');
	var	id	=	(ids[1]);
	if(id==undefined)
	{
		alert('Please select at least one record to display.');
	}
	else
	{
		var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1';
 		window.open('shipmentlabels.php?id='+ids[1],'Shipment Labels',display); 
	}
}
function printlist(text)
{
	var ids	=	selectedstring;
	//alert(ids);
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=500,height=600,left=100,top=25';
 	window.open('printshipment.php?ids='+ids+'&'+text,'Print Wish List',display); 
}
function printshipreport(text)
{
	var ids	=	selectedstring;
	//alert(ids);
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=1000,height=600,left=100,top=25';
 	window.open('shipreport.php?ids='+ids+'&'+text,'Print Ship Report',display); 
}
function printsupllierreport(text)
{
	var ids	=	selectedstring;
	//alert(ids);
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=1000,height=600,left=100,top=25';
 	window.open('supplierreport.php?ids='+ids+'&'+text,'Print Ship Report',display); 
}
</script>