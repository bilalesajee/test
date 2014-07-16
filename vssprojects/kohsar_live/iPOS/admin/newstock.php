<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$addressbookid		=	$_SESSION['addressbookid'];
// selecting shipments
$shipmentsarray		= 	$AdminDAO->getrows("shipment","*", "shipmentdeleted<>1");
$shipmentsel		=	"<select name=\"shipment\" id=\"shipment\" style=\"width:150px;\" onchange=\"getshipmentgroup(this.value)\" ><option value=\"\">Select Shipment</option>";
for($i=0;$i<sizeof($shipmentsarray);$i++)
{
	$shipmentname	=	$shipmentsarray[$i]['shipmentname'];
	$shipmentid		=	$shipmentsarray[$i]['pkshipmentid'];
	$select			=	"";
	if($shipmentid==$selected_shipments)
	{
		$select = "selected=\"selected\"";
	}
	$shipmentsel2	.=	"<option value=\"$shipmentid\" $select>$shipmentname</option>";
}
$shipments			=	$shipmentsel.$shipmentsel2."</select>";
// end shipments
// selecting suppliers
$suppliersarray		= 	$AdminDAO->getrows("supplier","*", "supplierdeleted<>1");
$firstsupplierid	=	$suppliersarray[0]['pksupplierid'];
$suppliersel		=	"<select name=\"supplier\" id=\"supplier\" style=\"width:150px;\" onchange=\"getinvoices(this.value);\" >";
for($i=0;$i<sizeof($suppliersarray);$i++)
{
	$suppliername	=	$suppliersarray[$i]['companyname'];
	$supplierid		=	$suppliersarray[$i]['pksupplierid'];
	$select			=	"";
	if(@in_array($supplierid,$selected_suppliers))
	{
		$select = "selected=\"selected\"";
	}
	$suppliersel2	.=	"<option value=\"$supplierid\" $select>$suppliername</option>";
}
$suppliers			=	$suppliersel.$suppliersel2."</select>";
// end suppliers
// last added stock
$query		=	"SELECT 
					quantity,
					FROM_UNIXTIME(updatetime,'%d-%m-%Y') as updatetime,
					FROM_UNIXTIME(expiry,'%d-%m-%Y') as expiry,
					itemdescription,
					barcode 
				FROM 
					$dbname_detail.stock,
					barcode, 
					addressbook,
					employee
				WHERE 
					fkbarcodeid=pkbarcodeid AND
					fkemployeeid=pkemployeeid AND
					employee.fkaddressbookid=pkaddressbookid AND
					pkaddressbookid='$addressbookid'
				ORDER BY pkstockid DESC LIMIT 0,1";
$arraylast	=	$AdminDAO->queryresult($query);
// end last added stock
?>
<link href="../includes/css/autocomplete.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/autocomplete/proautocomplete.js"></script>
<script language="javascript" type="text/javascript">
jQuery(function($)
{
	getinvoices(<?php echo $firstsupplierid;?>);
	document.getElementById('shipment').focus();
});
function getinvoices(sid)
{
	$('#invoices').load('getinvoices.php?sid='+sid);
}
function submitfrm()
{
	loading('System is Saving The Data....');
	options	=	{	
					url : 'newinsertstock.php',
					type: 'POST',
					success: addstockresponse
				}
	jQuery('#adstockfrm').ajaxSubmit(options);

}
function addstockresponse(text)
{
	if(text=='')
	{
		adminnotice('Stock data has been saved.',0,8000);
		jQuery('#maindiv').load('managestocks.php');
	}
	else if (text=='locked')
	{
		adminnotice('Stock data has been saved.',0,8000);
		document.getElementById('shipment').focus();
		document.getElementById('loadstockfrm').innerHTML	=	'';
	}
	else
	{
		adminnotice(text,0,8000);	
	}
}
function getshipmentgroup(id)
{
	if(id!='')
	{
		jQuery('#currency').load('loadcurrency.php?id='+id);
		jQuery('#priceatorigin').load('loadcurrency.php?p=1&id='+id);
		jQuery('#shippercentdiv').load('loadshipmentcharges.php?shipid='+id);
	}
}
function loadstockfrm()
{
	document.getElementById('loadstockfrm').style.display	=	'block';
	items		=	document.getElementById('totalitems').value;
	originprice	=	document.getElementById('priceatorigin').innerHTML;
	$('#loadstockfrm').load('stockfrm.php?rows='+items+'&originprice'+originprice);
}
</script>
<div id="addstockdiv">
<div id="loaditemscript"></div>
<span id="priceatorigin" style="display:none;"></span>
<div id="error" class="notice" style="display:none"></div>
<div id="shippercentdiv"></div>
<div id="baseprice" style="display:none"></div>
<div id="baseexpense" style="display:none"></div>
<div id="shipvalue" style="display:none"></div>
<div id="minusshipment" style="display:none"></div>
<div id="plusshipment" style="display:none"></div>
<?php
if(count($arraylast)>0)
{
?>
<div id="lastadded" class="topheading">Last Added Item</div>
<table width="100%" cellspacing="0">
<tr>
    <th>Barcode</th>
	<th>Item Description</th>
    <th>Quantity</th>
    <th>Expiry</th>
    <th>Update Time</th>
</tr>
<tr bgcolor="#FFFFE1">
    <td align="center" style="border-left:1px solid #D8D8D8;"><?php echo $arraylast[0]['barcode'];?></td>
    <td align="center"><?php echo $arraylast[0]['itemdescription'];?></td>
    <td align="center"><?php echo $arraylast[0]['quantity'];?></td>
    <td align="center"><?php echo $arraylast[0]['expiry'];?></td>
    <td align="center" style="border-right:1px solid #D8D8D8;"><?php echo $arraylast[0]['updatetime'];?></td>
</tr>
</table>
<div class="bottomimage" style="height:6px;"><!-- --></div>
<br />
<?php
}
?>
<form id="adstockfrm">
<div id="addnewstock" class="topheading">Add New Stock</div>
<table width="100%" cellspacing="0">
<tr>
	<th>Lock</th>
	<th>Shipment</th>
    <th>Currency</th>
    <th>%age</th>
    <th>Supplier</th>
    <th>Invoice</th>
    <th>Total Items</th>
    <th>&nbsp;</th>
</tr>
<tr bgcolor="#FFFFE1">
	<td align="center" style="border-left:1px solid #D8D8D8;"><input type="checkbox" name="lock" id="lock" value="locked" /></td>
	<td><?php echo $shipments;?></td>
    <td><div id="currency"></div></td>
    <td><span id="percentagediv"></span>%</td>
	<td align="center"><div id="brandsupplierdiv"><div id="brandsupplier2"><?php echo $suppliers; ?></div></div><div id="brandsupplier"></div></td>
	<td align="center"><div id="invoices"><?php echo $invoices;?></div></td>
	<td align="center"><input type="text" value="1"  onfocus="this.select()" name="totalitems" id="totalitems" class="text" onkeypress="return isNumberKey(event)" onkeydown="javascript:if(event.keyCode==13) {loadstockfrm(); return false;}" /></td>
    <td style="border-right:1px solid #D8D8D8;">
        <input type="button" name="loadstk" id="loadstk" onclick="loadstockfrm();" value="Load Form">
	</td>
</tr>
</table>
<div class="bottomimage" style="height:6px;"><!-- --></div>
<br />
<div id="loadingdiv" class="loading" style="display:none"></div>
<div id="loadstockfrm" style="display:none;"></div>
</form>
<br />
</div>