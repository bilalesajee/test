<?php 
session_start();
//echo $_SERVER['QUERY_STRING'];
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(68);
if($groupid != $ownergroup)
{
	// $where=" AND sh.fkstoreid='$storeid' ";
}
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$deltype	=	'delshipment';
include_once("delete.php"); 
$dest 	= 	'manageshipmentclosed.php';
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
						sh.shipmentdeleted <> 1	AND sh.fkstatusid NOT IN (1,3)						
						$where
					GROUP BY
						pkshipmentid DESC
					";//and	pkstatusid not in (5,7,11) 	
					//round((Select SUM(s.purchaseprice*d.quantity*sh.exchangerate) as damages from $dbname_detail.stock s LEFT JOIN 																										$dbname_detail.damages d ON (fkstockid = pkstockid), shipment where s.fkshipmentid = sh.pkshipmentid GROUP by fkshipmentid),2) as totalcharges,
//*******************************************************************
//echo $query;
$navbtn	=	"";
if(in_array('153',$actions))
{
	$navbtn .="&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'vieworders.php','subsection','maindiv') \" title=\"View Orders\"><b>View Orders</b></a>&nbsp;";	
}
if(in_array('27',$actions))
{
	$navbtn .="|&nbsp;<a href=\"javascript:showpage('1',document.$form.checks,'manageshipmentcharges.php','subsection','maindiv') \" title=\"Shipment Charges\"><b>Charges</b></a>&nbsp;";
}
if(in_array('112',$actions))
{
	//$navbtn .=" |&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'managedistribution.php','subsection','maindiv') \" title=\"Distribute Items\"><b>Allot</b></a>";
	$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,'','orderallot.php','subsection','maindiv','','$formtype') \" title=\"Allot Items\"><b>Allot</b>&nbsp;</a>";
}
if(in_array('118',$actions))
{
	//$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'managepacking.php','subsection','maindiv') \" title=\"Received Items\"><b>Pack</b></a>&nbsp;";	
	$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'orderpack.php','subsection','maindiv') \" title=\"Received Items\"><b>Pack</b></a>&nbsp;";	
}
if(in_array('73',$actions))
{
	$navbtn .=" <a href=\"javascript:showpage(1,document.$form.checks,'managepackinglist.php','subsection','maindiv') \" title=\"Packing\"><b> Packing List</b></a>&nbsp;|";
}
if(in_array('152',$actions))
{	
	$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'orderpacks.php','subsection','maindiv') \" title=\"Packing\"><b>Packed</b></a>&nbsp;";
}
if(in_array('115',$actions))
{
	//$navbtn .=" |&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'managereceiving.php','subsection','maindiv') \" title=\"Receive Items\"><b>Receive</b></a>";
	$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,'','orderreceive.php','subsection','maindiv','','$formtype') \" title=\"Receive Items\"><b>Receive</b></a>&nbsp;";
}
if(in_array('92',$actions))
{
	//$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'receiveditems.php','subsection','maindiv') \" title=\"Received Items\"><b>Received</b></a>&nbsp;";	
}
if(in_array('116',$actions))
{
	//$navbtn .=" |&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'managepricing.php','subsection','maindiv') \" title=\"Price Items\"><b>Price</b></a> ";
	$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,'','orderprice.php','subsection','maindiv','','$formtype') \" title=\"Price Items\"><b>Price</b></a>&nbsp;";
}
if(in_array('28',$actions))
{
	$navbtn .="<a href=\"javascript:showpage(2,'','manageshipmentgroups.php','subsection','maindiv') \" title=\"View Shipment groups\"><b>Groups</b></a>&nbsp;";
}
		/*$navbtn .=" |&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'shipmentlabels.php','subsection','maindiv') \" title=\"Shipment Labels\"><b>Label</b></a>";*/
	/*$navbtn .=" <a href=\"javascript:showpage(1,document.$form.checks,'manageprintlist.php','subsection','maindiv') \" title=\"Print List\"><b>Shopping List</b></a>&nbsp;";*/

if(in_array('91',$actions))
{
	$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'stocklist.php','subsection','maindiv') \" title=\"Receive List\"><b>Receive List</b></a>&nbsp;";
}

//$navbtn .=" <a href=\"javascript:showpage(1,document.$form.checks,'managedeliverynote.php','subsection','maindiv') \" title=\"Delivery Note\"><b> Delivery Note</b></a>&nbsp;|";
//$navbtn .=" <a href=\"javascript: printshoppinglist() \" title=\"Shopping List\"><b>List</b></a>&nbsp;";
//$navbtn .=" <a href=\"javascript: printshipreport('')\" title=\"Shipment Report\"><b> Report </b></a>&nbsp;|";
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