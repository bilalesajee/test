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
$dest 	= 	'manageshipment.php';
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
						IF(shipmentdate='0','--------',FROM_UNIXTIME(sh.shipmentdate, '%d-%m-%y-%H:%i:%s')) as shipmentdate,
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
						sh.shipmentdeleted <> 1	AND sh.fkstatusid=1		
						$where
					GROUP BY
						pkshipmentid DESC
					";//and	pkstatusid not in (5,7,11) 	
					//round((Select SUM(s.purchaseprice*d.quantity*sh.exchangerate) as damages from $dbname_detail.stock s LEFT JOIN 																										$dbname_detail.damages d ON (fkstockid = pkstockid), shipment where s.fkshipmentid = sh.pkshipmentid GROUP by fkshipmentid),2) as totalcharges,
//*******************************************************************
//echo $query;
$navbtn	=	"";
if($_SESSION['siteconfig'] != 3){//edit by ahsan on 08/03/2012
	if(in_array('24',$actions))
	{
	//	print"Hello";
		$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addshipment.php','subsection','maindiv','','$formtype')\" title='Add Shipment'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
	}
	if(in_array('25',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addshipment.php','subsection','maindiv','','$formtype') title=\"Edit Shipment\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('26',$actions))
	{
		$navbtn .="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Shipments\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('149',$actions))
	{
		$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'ordershipadd.php','subsection','maindiv') \" title=\"Add Order\"><b>Add Order</b></a>&nbsp;";
	}
	if(in_array('68',$actions))
	{
		$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'vieworders.php','subsection','maindiv') \" title=\"View Orders\"><b>View Orders</b></a>&nbsp;";
	}
	if(in_array('155',$actions))
	{
		$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'changestatus.php','subsection','maindiv') \" title=\"Change Status\"><b>Change Status</b></a>&nbsp;";
	}
	
	$qstring	=	$_SERVER['QUERY_STRING'];
		//$navbtn .="	<a href=\"javascript: printlist('$qstring') \" title=\"Print Shipment\"><span class=\"printrecord\">&nbsp;</span></a>&nbsp;";
	if(in_array('27',$actions))
	{
		$navbtn .="|&nbsp;<a href=\"javascript:showpage('1',document.$form.checks,'manageshipmentcharges.php','subsection','maindiv') \" title=\"Manage Shipment Charges\"><b>Charges</b></a>";
	}
	if(in_array('28',$actions))
	{
		$navbtn .="<a href=\"javascript:showpage(2,'','manageshipmentgroups.php','subsection','maindiv') \" title=\"View Shipment groups\"><b>Groups</b></a>&nbsp;";
	}
	if(in_array('59',$actions))
	{
		$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'managepacking.php','subsection','maindiv') \" title=\"Packing\"><b>Packing</b></a>";
	}
	if(in_array('72',$actions))
	{
		$navbtn .="<a href=\"javascript:showpage(1,document.$form.checks,'managetranslist.php','subsection','maindiv') \" title=\"Packing\"><b> Transit List</b></a>&nbsp;|";
	}
	if(in_array('73',$actions))
	{
		$navbtn .=" <a href=\"javascript:showpage(1,document.$form.checks,'managepackinglist.php','subsection','maindiv') \" title=\"Packing\"><b> Packing List</b></a>&nbsp;|";
	}
	if(in_array('75',$actions))
	{
		$navbtn .=" <a href=\"javascript:showpage(1,document.$form.checks,'managedeliverynote.php','subsection','maindiv') \" title=\"Delivery Note\"><b> Delivery Note</b></a>&nbsp;|";
	}
	if(in_array('120',$actions))
	{
	
		$navbtn .=" <a href=\"javascript: printshoppinglist() \" title=\"Shopping List\"><b>List</b></a>&nbsp;";
	}
	if(in_array('110',$actions))
	{
		$navbtn .="|&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'purchaseitems.php','subsection','maindiv') \" title=\"Purchase Items\"><b>Purchase</b></a>";
	}
	if(in_array('111',$actions))
	{
		$navbtn .=" |&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'orderpurchases.php','scharges','maindiv') \" title=\"Purchased Items\"><b>Purchased</b></a>&nbsp;";
	}
	
	//$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'orderpacks.php','scharges','maindiv') \" title=\"Packing\"><b>Packed</b></a>&nbsp;";
	
	if(in_array('112',$actions))
	{
		$navbtn .=" |&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'managedistribution.php','subsection','maindiv') \" title=\"Distribute Items\"><b>Allot</b></a>";
	}
	if(in_array('113',$actions))
	{
		$navbtn .=" |&nbsp;<a href=\"javascript: shipmentlabels() \" title=\"Shipment Labels\"><b>Label</b></a>&nbsp;";
	}
	if(in_array('114',$actions))
	{
		$navbtn .="|&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'orderimportinvoice.php','subsection','maindiv','','$formtype') \" title=\"Import Invoice\"><b>Import</b></a>&nbsp;";
	}
	if(in_array('115',$actions))
	{
		$navbtn .=" |&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'managereceiving.php','subsection','maindiv') \" title=\"Receive Items\"><b>Receive</b></a>";
	}
	if(in_array('116',$actions))
	{
		$navbtn .=" |&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'managepricing.php','subsection','maindiv') \" title=\"Price Items\"><b>Price</b></a> ";
	}
	if(in_array('76',$actions))
	{
			$navbtn .=" |&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'shipmentlabels.php','subsection','maindiv') \" title=\"Shipment Labels\"><b>Label</b></a>";
		/*$navbtn .=" <a href=\"javascript:showpage(1,document.$form.checks,'manageprintlist.php','subsection','maindiv') \" title=\"Print List\"><b>Shopping List</b></a>&nbsp;";*/
	}
	if(in_array('91',$actions))
	{
		$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'stocklist.php','subsection','maindiv') \" title=\"Receive List\"><b>Receive List</b></a>&nbsp;";
	}
	if(in_array('92',$actions))
	{
		$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'receiveditems.php','subsection','maindiv') \" title=\"Received Items\"><b>Received</b></a>&nbsp;";	
	}
	if(in_array('117',$actions))
	{
		$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'managequote.php','subsection','maindiv') \" title=\"Received Items\"><b>Quote</b></a>&nbsp;";	
	}
	if(in_array('118',$actions))
	{
		$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'managepacking.php','subsection','maindiv') \" title=\"Received Items\"><b>Pack</b></a>&nbsp;";	
	}
	if(in_array('119',$actions))
	{
		$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'manageshipmentdetails.php','subsection','maindiv','product') \" title=\"Manage Attributes\"><b>Items</b></a>";
	}
	if(in_array('150',$actions))
	{
	$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,document.$form.checks,'orderpurchase.php','subsection','maindiv','order','$formtype') \" title=\"Purchase\"><b>Purchase</b></a>&nbsp;";
	}
	if(in_array('118',$actions))
	{
		$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,'','orderpack.php','subsection','maindiv','','$formtype') \" title=\"Pack Items\"><b> Pack</b></a>&nbsp;";
	}
}//Edit by Ahsan on 08/03/2012
if($_SESSION['siteconfig'] != 1){//edit by ahsan on 08/03/2012
	$navbtn .=" <a href=\"javascript:showpage(1,document.$form.checks,'receiveshipment.php','subsection','maindiv') \" title=\"Receive Shipment\"><b> Receive Shipment</b></a>&nbsp;";
	$navbtn .=" |<a href=\"javascript:showpage(1,document.$form.checks,'orderreceiveshipment.php','subsection','maindiv') \" title=\"Receive Shipment\"><b> Receive Shipment New</b></a>&nbsp;";
}//Edit by Ahsan on 08/03/2012

//$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,'','orderallot.php','subsection','maindiv','','$formtype') \" title=\"Allot Items\"><b>Allot</b>&nbsp;</a>";
//$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,'','orderreceive.php','subsection','maindiv','','$formtype') \" title=\"Receive Items\"><b> Receive</b></a>&nbsp;";
//$navbtn .="|&nbsp;<a href=\"javascript:showpage(1,'','orderprice.php','subsection','maindiv','','$formtype') \" title=\"Price Items\"><b>Price</b></a>&nbsp;";
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