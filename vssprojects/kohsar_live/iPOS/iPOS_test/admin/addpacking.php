<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id			=	$_GET['id'];
$shipmentid	=	$_GET['param'];
$qstring=$_SESSION['qstring'];
if($id!='')
{
	$packs = $AdminDAO->getrows("packinglist","pkpackinglistid, fkpurchaseid, fkshipmentid, packnumber, packtime, packedby, quantity"," pkpackinglistid='$id'");
	foreach($packs as $pack)
	{
		$packnumber 		= $pack['packnumber'];
		$quantity 		= $pack['quantity'];
		$oldquantity 	= $pack['quantity'];
		$fkshipmentid	= $pack['fkshipmentid'];
		$fkpurchaseid 	= $pack['fkpurchaseid'];
	}
}
?>
<div id="error" class="notice" style="display:none"></div>
<div id="packfrmdiv" style="display: block;">
<br>
<form id="packform" style="width: 920px;" action="insertpacking.php?id=-1" class="form">
<fieldset>
<legend>
	<?php
    if($id!="-1")
    { echo "Edit Package"." ".$packnumber;}
    else
    { echo "Add Package";}	
    ?>
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="addpack(-1);">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>
    <button type="button" onclick="hidediv('sugrid');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </button>
</span>
</div>
<table cellpadding="0" cellspacing="2" width="100%">
	<tbody>
	<tr>
		<td>Packing Quantity: </td>
		<td>
            <input name="quantity" 		id="quantity" value="<?php echo $quantity; ?>" onkeydown="javascript:if(event.keyCode==13) {addpack(); return false;}" type="text">           
        </td>
   	</tr>
	<tr>   
    <td>Packing Number: </td> 
        <td>
			<input name="packnumber" 		id="packnumber" value="<?php echo $packnumber; ?>" onkeydown="javascript:if(event.keyCode==13) {addpack(); return false;}" type="text">       
        </td>
	</tr>
	<tr>
	  <td colspan="2" align="center">
  <!--	    <input value="Save" onclick="addnote(-1)" type="button"><input name="btnsubmit" value="Cancel" onclick="hideform()" type="button">-->
	    <div class="buttons">
	      <button type="button" class="positive" onclick="addpack(-1);">
	        <img src="../images/tick.png" alt=""/> 
	        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
	        </button>
	      <button type="button" onclick="hidediv('sugrid');" class="negative">
	        <img src="../images/cross.png" alt=""/>
	        Cancel
	      </button>
	      </div>
	    </td>				
	  </tr>
	</tbody>
</table>
<input type="hidden" name="shipmentid" value ="<?php echo $shipmentid;?>"/>
            <input type="hidden" name="id" value ="<?php echo $id;?>"/>
</fieldset>	
</form>
</div>
<script language="javascript">
function addpack(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insertpacking.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#packform').ajaxSubmit(options);
}
	
function response(text)
{
	if(text=='')
	{
		loading('Note Saved...');
		adminnotice('Packing has been saved.',0,5000);
		jQuery('#subsection').load('managepacking.php?id='+'<?php echo $fkshipmentid?>');
		document.getElementById('packfrmdiv').style.display	=	'none';
		
	}
	else
	{
		document.getElementById('error').innerHTML		=	text;	
		document.getElementById('error').style.display	=	'block';
	}
}
function hideform()
{
	document.getElementById('packfrmdiv').style.display	=	'none';	
}
</script>
<script language="javascript">
	focusfield('quantity');
</script>