<div id="barcodeitemname">
<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$bcid	=	filter($_GET['id']);
$query	=	"SELECT pkbarcodeid,itemdescription,shortdescription FROM barcode WHERE barcode='$bcid'";
$res	=	$AdminDAO->queryresult($query);
if(sizeof($res)>0)
{
	$barcodeid	=	$res[0]['pkbarcodeid'];
	$itemdesc	=	$res[0]['itemdescription'];
	$shortdesc	=	$res[0]['shortdescription'];
	?>
	<form name="newname" id="newname" style="width:920px;" class="form">
	<fieldset>
	<table>
		<tbody>
		<tr>
			<td>Item Name:</td>
			<td><?php echo $itemdesc;?></td>
		</tr>
		<tr>
			<td>Item Description:</td>
	<input type="text" name="shortdesc" id="shortdesc" value="<?php echo $shortdesc;?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" size="100" onfocus="this.select();" maxlength="75"/>
	<input type="hidden" name="pkbarcodeid" id="pkbarcodeid" value="<?php echo $barcodeid;?>" /></td>
	</tr>
	<tr>
		<td colspan="2">
		<div class="buttons">
		<button type="button" class="positive" onclick="addform();">
			<img src="../images/tick.png" alt="Update"/> 
			Update
		</button>
        <a href="javascript:void(0);" onclick="hi('barcodeitemname');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
		</div>
		</td>
	</tr>
	</tbody>
	</table>
	</fieldset>
	</form>
	<script language="javascript" type="text/javascript">
	document.getElementById('shortdesc').focus();
	</script>
<?php
}
else
{
	?>
	<script language="javascript" type="text/javascript">
	adminnotice('Barcode not found',0,5000);
	</script>
    <?php
}
?>
</div>