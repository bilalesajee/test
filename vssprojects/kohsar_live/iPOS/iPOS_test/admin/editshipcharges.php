<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$shipmentid			=	$_GET['id'];
// shipments
//$srcshipments		=	$AdminDAO->getrows("shipment","*","shipmentdeleted<>1 and fkstatusid not in (2) ORDER BY pkshipmentid DESC");
$chargesarray		= 	$AdminDAO->getrows("charges","*","chargesdeleted<>1");
$chargesize			=	sizeof($chargesarray);
foreach($chargesarray as $chargeid)
{
	$charge[]	=	$chargeid['pkchargesid'];
}

?>
<script language="javascript" type="text/javascript">
function movelist()
{
	options	=	{	
					url : 'editshipchargesaction.php',
					type: 'POST',
					success: response
				}
	jQuery('#ordermove').ajaxSubmit(options);
}
function response(text)
{
		adminnotice('Shipment Charges has been updated Successfully.',0,5000);
		document.getElementById('subsection33').innerHTML='';
		jQuery('#subsection').load('manageshipmentcharges.php?id='+text);
		$('#maindiv').load('manageshipmentclosed.php');
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="movelistdiv" style="display: block;">
<form id="ordermove" style="width: 920px;" class="form">
<fieldset>
<legend>Edit Shipment Charges(Rs)</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="movelist();">
        <img src="../images/tick.png" alt=""/> 
        Update
    </button>
    <button type="button" onclick="hidediv('movelistdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </button>
</span>
</div>
<table width="100%">
<tr>
          <?php
	 $i=1;
	  foreach($chargesarray as $charges)
	  {
	  	$shipmentcharges			= 	$AdminDAO->getrows("shipmentcharges","*"," fkshipmentid='$shipmentid' AND 	fkchargesid='".$charges['pkchargesid']."'");
	  ?>
          <tr >
            <td width="14%"><?php echo $charges['chargesname'];?>:
              <input type="hidden" name="chargesid[]" value="<?php echo $shipmentcharges[0]['pkshipmentchargesid'];?>" /></td>
            <td><input name="charges_<?php echo $charges['pkchargesid'];?>" id="c_<?php echo $i;?>" type="text" value="<?php echo $shipmentcharges[0]['chargesinrs'];?>" />
              <div id="error3" class="error" style="display:none; float:right;"></div></td>
          </tr>
          <?php
	  //$totalcharges		+=	$shipmentcharges[0]['totalcharges'];
	  //$chargesinrs		+=	$shipmentcharges[0]['chargesinrs'];	  
	  $i++;
	  }
	  ?>
</tr>
<tr>
 <td colspan="2">
 <input type="hidden" name="shipmentid" value="<?php echo $shipmentid;?>" />
 <div class="buttons">
    <button type="button" class="positive" onclick="movelist();">
        <img src="../images/tick.png" alt=""/> 
        Update
    </button>
    <button type="button" onclick="hidediv('movelistdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </button>
</div>
 </td>
</tr>
</table>
</fieldset>	
</form>
<br />
<br />
</div>