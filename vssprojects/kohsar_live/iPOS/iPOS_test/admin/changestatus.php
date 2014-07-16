<?php 
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['id'];
$resarray	=	$AdminDAO->getrows("shipment",'fkstatusid',"pkshipmentid = '$id'");
$shipstatus	=	$resarray[0]['fkstatusid'];
if($shipstatus==1)
$status	=	3;
else if(($shipstatus==3))
$status	=	9;
?>
<script language="javascript" type="text/javascript">
var r=confirm("Are you sure you want to move the shipment to the Next Step?");
if (r==true)
  {
  changestatus(<?php echo $id;?>,<?php echo $status;?>);
  }
else
  { // alert("You pressed Cancel!");  
  }
function changestatus(id,status)
{
	$.post('changestatusaction.php',{id:id,stat:status},function(data){
		if(data=='')
		{
			adminnotice('Selected shipment has been moved to Next State.',0,5000);
			if(status==3)
			jQuery('#maindiv').load('manageshipment.php');
			else
			jQuery('#maindiv').load('manageshipmentinprocess.php');
		}
		else
		{	
			adminnotice(data,0,5000);
		}
	})
}
</script>
<?php
/*include_once("../includes/security/adminsecurity.php");
global $AdminDAO;

$id		=	ltrim($id,",");
$ids	=	explode(",",$id);

// shipments
$srcshipments		=	$AdminDAO->getrows("shipment","*","shipmentdeleted<>1 and fkstatusid in (1,5,6) ORDER BY pkshipmentid DESC");
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
*/
?>
<!--<div id="error" class="notice" style="display:none"></div>
<div id="movelistdiv" style="display: block;">
<form id="ordermove" style="width: 920px;" action="moveshiplist.php" class="form">
<fieldset>
<legend>Change Shipment Status</legend>
<div style="width: 600px;"><legend>You can only move those Orders which are in Request Status.</legend></div>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="movelist();">
        <img src="../images/tick.png" alt=""/> 
        Move Shipment
    </button>
    <button type="button" onclick="hidediv('movelistdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </button>
</span>
</div>
<table width="100%">
<tr>
 <td width="15%">Shipment Status :</td>
 <td width="85%"><?php//echo $shipments;?></td>
</tr>
<tr>
 <td colspan="2">
 <div class="buttons">
    <button type="button" class="positive" onclick="movelist();">
        <img src="../images/tick.png" alt=""/> 
        Move Shipment
    </button>
    <button type="button" onclick="hidediv('movelistdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </button>
</div>
 </td>
</tr>
</table>
<input type="hidden" name="id" value ="<?php//echo $id;?>"/>
</fieldset>	
</form>
<br />
<br />
</div>-->