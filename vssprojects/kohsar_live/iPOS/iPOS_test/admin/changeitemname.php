<div id="maindiv">
<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
/****************************************************************************/
?>
<script language="javascript">
function addform()
{
	options	=	{	
					url : 'itemnameact.php',
					type: 'POST',
					success: response
				}
	jQuery('#newname').ajaxSubmit(options);
}
function response(text)
{
	adminnotice(text,0,5000);
	$('#sugrid').load('changeitemname.php');
}
function getitemname(val)
{
	val	=	trim(val);
	$('#itemnametest').load('barcodeitem.php?id='+val)
}
function hi(divid)
{
	document.getElementById(divid).style.display	=	'none';
}
</script>
<div id="brandiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="itemform" id="itemform" style="width:920px;" class="form">
<fieldset>
<legend>
Change Item Description</legend>
<table width="452">
	<tbody>
	<tr>
		<td>Barcode:</td>
		<td><input name="barcode" id="barcode" type="text" value="" onkeydown="javascript:if(event.keyCode==13) {getitemname(this.value); return false;}" onfocus="this.select();" maxlength="20" ></td>
	</tr>
	</tbody>
</table>
</fieldset>
</form>
</div>
<div id="itemnametest" style="display:block"></div>
<script language="javascript" type="text/javascript">
document.getElementById('barcode').focus();
</script>
</div>