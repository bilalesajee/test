<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$shipmentid		=	$_GET['id'];
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];
?>
<script language="javascript">
function viewnotes()
{
	if(document.getElementById('itemdescription').checked==true)
	{	
		var itm	=	document.getElementById('itemdescription').value;
	}
	if(document.getElementById('barcode').checked==true)
	{	
		var bc	=	document.getElementById('barcode').value;
	}
	if(document.getElementById('purchasequantity').checked==true)
	{	
		var pq	=	document.getElementById('purchasequantity').value;
	}
	if(document.getElementById('expiry').checked==true)
	{	
		var ex	=	document.getElementById('expiry').value;
	}
	if(document.getElementById('lastpurchaseprice').checked==true)
	{	
		var lpp	=	document.getElementById('lastpurchaseprice').value;
	}
	if(document.getElementById('purchaseprice').checked==true)
	{	
		var pp	=	document.getElementById('purchaseprice').value;
	}
	if(document.getElementById('salestax').checked==true)
	{	
		var st	=	document.getElementById('salestax').value;
	}
	if(document.getElementById('surcharge').checked==true)
	{	
		var sc	=	document.getElementById('surcharge').value;
	}
	if(document.getElementById('weight').checked==true)
	{	
		var wt	=	document.getElementById('weight').value;
	}
	if(document.getElementById('chargesinrs').checked==true)
	{	
		var ci	=	document.getElementById('chargesinrs').value;
	}
var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=350,height=600,left=100,top=25';
 	window.open('deliverynote.php?shipmentid=<?php echo $shipmentid;?>&barcode='+bc+'&itemdescription='+itm+'&purchasequantity='+pq+'&expiry='+ex+'&lastpurchaseprice='+lpp+'&purchaseprice='+pp+'&salestax='+st+'&surcharge='+sc+'&weight='+wt+'&chargesinrs='+ci,'deliverynote',display); 
}
function hideform()
{
	document.getElementById('deliveryfrmdiv').style.display	=	'none';	
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="deliveryfrmdiv" style="display: block;">
<br>
<form id="deliveryform" style="width: 920px;" action="deliverynote.php?id=-1" class="form">
<fieldset>
<legend>
	Delivery Note
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="viewnotes();">
        <img src="../images/tick.png" alt=""/> 
        View Note
    </button>
     <a href="javascript:void(0);" onclick="hidediv('deliveryfrmdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
</span>
</div>
<table border="0">
 <tr>
    <th>Item Name</th>
    <th>Barcode</th>
    <th>Quantity</th>
    <th>Expiry</th>
    <th>Last Purchase Price</th>
    <th>Purchase Price</th>
    <th>Sales Tax</th>
    <th>Surcharge</th>
    <th>Weight</th>
    <th>Charges in <?php echo $defaultcurrency;?></th>                    
  </tr>
  <tr>
    <td><input type="checkbox" name="itemdescription" id="itemdescription" value="itemdescription" checked="checked" /></td>
    <td><input type="checkbox" name="barcode" id="barcode" value="barcode" checked="checked" /></td>
    <td><input type="checkbox" name="purchasequantity" id="purchasequantity" value="purchasequantity" checked="checked" /></td>
    <td><input type="checkbox" name="expiry" id="expiry" value="expiry" checked="checked" /></td>
    <td><input type="checkbox" name="lastpurchaseprice" id="lastpurchaseprice" value="lastpurchaseprice" checked="checked" /></td>
    <td><input type="checkbox" name="purchaseprice" id="purchaseprice" value="purchaseprice" checked="checked" /></td>
    <td><input type="checkbox" name="salestax" id="salestax" value="salestax" checked="checked" /></td>
    <td><input type="checkbox" name="surcharge" id="surcharge" value="surcharge" checked="checked" /></td>
    <td><input type="checkbox" name="weight" id="weight" value="weight" checked="checked" /></td>
    <td><input type="checkbox" name="chargesinrs" id="chargesinrs" value="chargesinrs" checked="checked" /></td>
  </tr>
</table>
</fieldset>	
</form>
</div>