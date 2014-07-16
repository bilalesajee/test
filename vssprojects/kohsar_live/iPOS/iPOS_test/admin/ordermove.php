<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['id'];
$arg	=	$id;	// for argument of addshipment function
$id		=	ltrim($id,",");

$oid	=	$_GET['oid'];
if($oid)
{
	$id		=	ltrim($oid,",");
}
if(isset($_GET['shipid']))
{
	$selected_shipment	=	$_GET['shipid'];	
}
$ids	=	explode(",",$id);

// shipments
$srcshipments		=	$AdminDAO->getrows("shipment","*","shipmentdeleted<>1 and fkstatusid not in (2,9) ORDER BY pkshipmentid DESC");
$shipmentsel			=	"<select name=\"shipment\" id=\"shipment\" style=\"width:200px;\"><option value=\"\">Select Shipment</option>";
for($i=0;$i<sizeof($srcshipments);$i++)
{
	$shipmentname	=	$srcshipments[$i]['shipmentname'];
	$shipmentid		=	$srcshipments[$i]['pkshipmentid'];
	$select		=	"";
	if($shipmentid == $selected_shipment)
	{
		$select = "selected=\"selected\"";
	}
	$shipmentsel2	.=	"<option value=\"$shipmentid\" $select>$shipmentname</option>";
}
$shipments			=	$shipmentsel.$shipmentsel2."</select>";
// end shipments

?>
<script language="javascript" type="text/javascript">
function addshipment(oid)
{
	jQuery('#subsection').load('addshipment.php?id=-1&param=move&oid='+oid);
}
/*function responsenewshipment(text)
{
	document.getElementById('newshipment').innerHTML='text';
}*/
function movelist()
{
	options	=	{	
					url : 'ordermoveaction.php',
					type: 'POST',
					success: response
				}
	jQuery('#ordermove').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Order has been moved to selected shipment.',0,3000);
		document.getElementById('subsection').innerHTML='';
		jQuery('#maindiv').load('manageshiplist.php');
	}
	else
	{
		adminnotice(text,0,3000);
	}
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="movelistdiv" style="display: block;">
<form id="ordermove" style="width: 920px;" action="moveshiplist.php" class="form">
<fieldset>
<legend>Select Shipment</legend>
<div style="width: 600px;"><legend>You can only move those Orders which are in Request Status.</legend></div>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="movelist();">
        <img src="../images/tick.png" alt=""/> 
        Move
    </button>
    <button type="button" onclick="hidediv('movelistdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </button>
</span>
</div>
<table width="100%">
<tr>
 <td width="10%">Shipment: <span class="redstar" title="This field is compulsory">*</span></td>
 <td width="90%">
 	<?php echo $shipments;?> &nbsp;&nbsp;OR&nbsp;
    <button type="button" style="background-color:#F5F5F5;border:1px solid #C6D880;color:#529214;" class="positive" onclick="addshipment('<?php echo $arg;?>');">
        <img src="../images/tick.png" alt=""/> 
        Add New Shipment
    </button> &nbsp;&nbsp;OR&nbsp;
    <span id="newshipment"></span>
 </td>
</tr>
<tr>
 <td colspan="2">
 <div class="buttons">
    <button type="button" class="positive" onclick="movelist();">
        <img src="../images/tick.png" alt=""/> 
        Move
    </button>
    <button type="button" onclick="hidediv('movelistdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </button>
</div>
 </td>
</tr>
</table>
<input type="hidden" name="id" value ="<?php echo $id;?>"/>
</fieldset>	
</form>
<br />
<br />
</div>