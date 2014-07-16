<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$shipmentid		=	$_GET['id'];
$items			=	$AdminDAO->getrows("shiplist","*","fkshipmentid='$shipmentid'");
$len			=	sizeof($items);
?>
<script language="javascript">
jQuery(function($){
	for(i=0;i<<?php echo $len;?>;i++)
	{
   		$("#expiry"+i).mask("99-99-9999");
	}
});
function addtranslist(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'inserttranslist.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#translistform').ajaxSubmit(options);
}
function response(text)
{
	if(text!='')
	{
		document.getElementById('error').innerHTML		=	text;	
		document.getElementById('error').style.display	=	'block';
	}
	else
	{
		document.getElementById('error').innerHTML		=	'Shipment List Updated Successfully.';
		document.getElementById('error').style.display	=	'block';
	}
}
</script>
<div id="loaditemscript">
</div>
<div id="error" class="notice" style="display:none"></div>
<div id="shipfrmdiv" style="display: block;">
<br>
<form id="translistform" style="width: 920px;" action="inserttranslist.php?id=-1" class="form">
<fieldset>
<legend>
   	Transit List
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="addtranslist(-1);">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
</span>
</div>
<div id="transdata" style="margin-top:25px;">
<table>
  <tr>
    <th>Barcode</th>
    <th>Item</th>
    <th>Last P.Price</th>
    <th>Qty</th>
    <th>Purchased Qty</th>
    <th>Expiry</th>
    <th>Currency & Rate</th>
    <th>Purchase Price</th>
    <th>Sales Tax</th>
    <th>Surcharge</th>
    <th>Weight(gms)</th>
    <th>Charges</th>
  </tr>
<?php
for($i=0;$i<sizeof($items);$i++)
{
	$aexpiry	=	explode("-",$items[$i]['expiry']);
	$iexpiry	=	array_reverse($aexpiry);
	$pexpiry	=	implode("-",$iexpiry);

?>
  <tr>
    <td><input type="text" name="barcode[]" id="barcode" onkeypress="return isNumberKey(event)" size="15" class="text" value="<?php echo html_entity_decode($items[$i]['barcode']); ?>" /></td>
    <td><input type="text" name="itemdescription[]" id="itemdescription" onkeypress="return isNumberKey(event)" size="20" class="text" value="<?php echo html_entity_decode($items[$i]['itemdescription']); ?>" /></td>
    <td><input type="text" name="lastpurchaseprice[]" id="lastpurchaseprice" onkeypress="return isNumberKey(event)" size="4" class="text" value="<?php echo $items[$i]['currencysymbol'].$items[$i]['lastpurchaseprice']; ?>" readonly="readonly" /></td>
    <td><input type="text" name="quantity[]" id="quantity" onkeypress="return isNumberKey(event)" size="4" class="text" value="<?php echo $items[$i]['quantity']; ?>" readonly="readonly" /></td>
    <td><input type="text" name="purchasequantity[]" id="purchasequantity" onkeypress="return isNumberKey(event)" size="4" class="text" value="<?php echo $items[$i]['purchasequantity']; ?>" /></td>
    <td><input type="text" name="expiry[]" id="expiry<?php echo $i;?>" onkeypress="return isNumberKey(event)" size="8" class="text" value="<?php echo  $pexpiry;?>" /></td>
    <td><input type="text" name="currency[]" id="currency" onkeypress="return isNumberKey(event)" size="4" class="text" value="<?php echo $items[$i]['currencysymbol']; ?>" /></td>
    <td><input type="text" name="[]" id="purchaseprice" onkeypress="return isNumberKey(event)" size="4" class="text" value="<?php echo $items[$i]['purchaseprice']; ?>" /></td>
    <td><input type="text" name="salestax[]" id="salestax" onkeypress="return isNumberKey(event)" size="4" class="text" value="<?php echo $items[$i]['salestax']; ?>" /></td>
    <td><input type="text" name="surcharge[]" id="surcharge" onkeypress="return isNumberKey(event)" size="4" class="text" value="<?php echo $items[$i]['surcharge']; ?>" /></td>
    <td><input type="text" name="weight[]" id="weight" onkeypress="return isNumberKey(event)" size="4" class="text" value="<?php echo $items[$i]['weight']; ?>" /></td>
    <td>
    <input type="text" name="charges[]" id="charges" onkeypress="return isNumberKey(event)" size="4" class="text" value="<?php echo $items[$i]['charges']; ?>" />
    <input type="hidden" name="shiplistid[]" id="shiplistid" value="<?php echo $items[$i]['pkshiplistid'];?>" />
    </td>
  </tr>
<?php
}
?>
</table>
<div class="buttons">
  <button type="button" class="positive" onclick="addtranslist(-1);">
    <img src="../images/tick.png" alt=""/> 
    <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>
  <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
    </a>
</div>
</div>
</fieldset>	
</form>
</div>