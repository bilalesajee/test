<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
/****************************************************************************/
?>
<script language="javascript">
function addform()
{
	options	=	{	
					url : 'itemprice.php',
					type: 'POST',
					success: response
				}
	jQuery('#brandform').ajaxSubmit(options);
}
function response(text)
{
	var bc		=	document.getElementById('barcode').value;
	bc	=	 bc.replace(/^\s+|\s+$/g,"");
	if(document.getElementById('newprice'))
	{
		var np		=	document.getElementById('newprice').value;
		var bcid	=	document.getElementById('barcodeid').value;
		var pcid	=	document.getElementById('pkpricechangeid').value;
		
	}
	
	jQuery('#itemprice').load('itemprice.php?barcode='+bc+'&np='+np+'&bcid='+bcid+'&pcid='+pcid);
}
function hideform()
{
	
	document.getElementById('brandiv').style.display='none';
}
</script>
<?php


?>
<div id="brandiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="brandform" id="brandform" onSubmit="addform(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
Change Price
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="addform();">
        <img src="../images/tick.png" alt=""/> 
        Save
    </button>
     <a href="javascript:void(0);" onclick="hidediv('brandiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
</div>
<table width="452">
	<tbody>
	<tr>
		<td>Barcode:</td>
		<td colspan="2"><input name="barcode" id="barcode" type="text" value="" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"></td>
	</tr>
    	
	</tbody>
</table>
<div id="itemprice"></div>
</fieldset>
</form>
</div><br />
<script language="javascript">
</script>