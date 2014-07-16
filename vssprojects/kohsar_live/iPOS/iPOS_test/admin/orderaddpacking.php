<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['id'];
if($id!='')
{
	$packs = $AdminDAO->getrows("orderpack","pkorderpackid,packnumber,fkshipmentid,quantity"," pkorderpackid='$id'");
	foreach($packs as $pack)
	{
		$packnumber 	=	$pack['packnumber'];
		$quantity 		=	$pack['quantity'];
		$pkorderpackid	=	$pack['pkorderpackid'];
		$fkshipmentid	=	$pack['fkshipmentid'];
	}
}
?>
<div id="error" class="notice" style="display:none"></div>
<div id="packfrmdiv" style="display: block;">
<br>
<form id="packform" style="width: 920px;" action="insertpacking.php?id=-1" class="form">
<fieldset>
<legend>
	Edit Pack
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="addpack(-1);">
        <img src="../images/tick.png" alt=""/> 
        Update
    </button>
    <button type="button" onclick="hidediv('editpack');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </button>
</span>
</div>
<table cellpadding="0" cellspacing="2" width="100%">
	<tbody>
	<tr>
		<td width="11%" height="25">Pack #: </td>
		<td width="89%"><input name="packnumber" id="packnumber" value="<?php echo $packnumber; ?>" onkeydown="javascript:if(event.keyCode==13) {addpack(); return false;}" type="text" /></td>
   	</tr>
	<tr>   
    <td>Pack Quantity: </td> 
        <td>
        <input name="prevquantity" id="prevquantity" value="<?php echo $quantity; ?>" type="hidden" />
        <input name="quantity" id="quantity" value="<?php echo $quantity; ?>" onkeydown="javascript:if(event.keyCode==13) {addpack(); return false;}" type="text" />
        </td>
	</tr>
	<tr>
	  <td height="60" colspan="2" align="center">
	    <div class="buttons">
	      <button type="button" class="positive" onclick="addpack(-1);">
	        <img src="../images/tick.png" alt=""/> 
	        Update
	        </button>
	      <button type="button" onclick="hidediv('editpack');" class="negative">
	        <img src="../images/cross.png" alt=""/>
	        Cancel
	       </button>
	      </div>
	    </td>				
	  </tr>
	</tbody>
</table>
<input type="hidden" name="id" value ="<?php echo $pkorderpackid;?>"/>
</fieldset>	
</form>
</div>
<script language="javascript">
function addpack(id)
{
	options	=	{	
					url : 'orderpackupdate.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#packform').ajaxSubmit(options);
}
	
function response(text)
{
	if(text=='')
	{
		adminnotice('Packing has been saved.',0,5000);
		jQuery('#subsection').load('orderpacks.php?id='+'<?php echo $fkshipmentid?>');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function hideform()
{
	document.getElementById('packfrmdiv').style.display	=	'none';	
}
</script>