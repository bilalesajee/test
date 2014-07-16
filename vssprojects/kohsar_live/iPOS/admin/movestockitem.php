<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$id					=	$_GET['id'];
$qstring			=	$_SESSION['qstring'];
if($id!='-1')
{
	$shiplist = $AdminDAO->getrows("shiplist LEFT JOIN currency ON (fkcurrencyid=pkcurrencyid)","*"," pkshiplistid='$id'");
/*	echo "<pre>";
	print_r($shiplist);
	echo "</pre>";*/
	foreach($shiplist as $slist)
	{
		$barcode 			= 	$slist['barcode'];
		$itemdescription 	= 	$slist['itemdescription'];
		$quantity 			= 	$slist['quantity'];
		$weight				=	$slist['weight'];
		$suppliers			=	$AdminDAO->getrows("shiplistsupplier","*","fkshiplistid='$id'");
		foreach($suppliers as $supplier)
		{
			$selected_suppliers[]	=	$supplier['fksupplierid'];
		}
		$selected_country	=	$slist['fkcountryid'];
		$selected_store		=	$slist['fkstoreid'];
		$selected_emp		=	$slist['fkaddressbookid'];
		$currencyid			=	$slist['pkcurrencyid'];
		$currencysymbol		=	$slist['currencysymbol'];
		$lastpurchaseprice 	= 	$slist['lastpurchaseprice'];
		$deadline			=	implode("-",array_reverse(explode("-",$slist['deadline'])));
	}
}
else
{
	$selected_store	=	$_SESSION['storeid'];
	$selected_emp	=	$_SESSION['addressbookid'];
}

// stores
if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
	$stores			=	$AdminDAO->getrows("store","storename,pkstoreid","storedeleted<>1");
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
	$stores			=	$AdminDAO->getrows("store","storename,pkstoreid","storedeleted<>1 AND storestatus=1");
}//end edit
$storesel		=	"<select name=\"store\" id=\"store\" style=\"width:100px;\"><option value=\"\">Location</option>";
for($i=0;$i<sizeof($stores);$i++)
{
	$storename	=	$stores[$i]['storename'];
	$storeid	=	$stores[$i]['pkstoreid'];
	$select		=	"";
	if($storeid == $selected_store)
	{
		$select = "selected=\"selected\"";
	}
	$storesel2	.=	"<option value=\"$storeid\" $select>$storename</option>";
}
$stores			=	$storesel.$storesel2."</select>";
// end stores

?>
<script language="javascript">
jQuery().ready(function() 
	{
		$("#expiry").mask("99-99-9999");
		$("#deadline").mask("99-99-9999");
		function findValueCallback(event, data, formatted) 
		{
			var barcode=document.getElementById('barcode').value=data[1];
			document.getElementById('quantity').focus();
			getitemdetails(document.getElementById('barcode').value,1);
			//getinstance('instancediv',barcode);
			jQuery("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
		}
		function formatItem(row) 
		{
			return row[0] + " (<strong>id: " + row[0] + "</strong>)";
		}
		function formatResult(row) 
		{
			return row[0].replace(/(<.+?>)/gi,'');
		}
			jQuery("#itemdescription").autocomplete("productautocomplete.php") ;
			jQuery(":text, textarea").result(findValueCallback).next().click(function() 
			{
				$(this).prev().search();
			});
			jQuery("#clear").click(function() 
			{
				jQuery(":input").unautocomplete();
			});
			//document.adstockfrm.reset(); 
});
function getitemdetails(bc,itm)
{
	if(bc=='')
	{
		alert("Please enter Barcode.");
		return false;
	}
	bc = trim(bc);
	jQuery('#loaditemscript').load('getmoveitemdata.php?bc='+bc+'&item='+itm);
	document.getElementById('quantity').focus();
}
function addshiplist(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insertshiplist.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#shiplistform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Wish List has been saved.',0,5000);
		jQuery('#maindiv').load('manageshiplist.php');
		
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function getexpitem(id)
{
	jQuery('#quantitydiv').load('getitemquantity.php?id='+id);
//	$("input#textbox").val($(this).html());
}
</script>
<div id="loaditemscript">
</div>
<div id="error" class="notice" style="display:none"></div>
<div id="shipfrmdiv" style="display: block;">
<br>
<form id="shiplistform" style="width: 920px;" action="insertshiplist.php?id=-1" class="form">
<fieldset>
<legend>
	<?php
    if($id!="-1")
    { echo "Edit Item"." ".$packingname;}
    else
    { echo "Add Item";}	
    ?>
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="addshiplist(-1);">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
</span>
</div>
<table width="100%">
<tr>
<td height="10" valign="top">
<div class="topimage2" style="height:6px;"><!-- --></div>
<table cellpadding="2" cellspacing="0" width="100%" >
<tbody>
    <tr>
        <th>Barcode</th>
        <th>Item</th>
        <th>Weight</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Expiry</th>
        <th>Supplier</th>
        <th>Store</th>
    </tr>
    <tr class="even">
        <td><input name="barcode" id="barcode" class="text" size="10" value="<?php echo $barcode; ?>" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,0); return false;}" type="text" autocomplete="off" onfocus="this.select()" ></td>
        <td><input name="itemdescription" id="itemdescription" class="text" size="20" value="<?php echo $itemdescription; ?>" onKeyDown="javascript:if(event.keycode==13){addshiplist(); return false;}" type="text" ></td>
        <td><input name="weight" id="weight" class="text" size="5" value="<?php echo $weight; ?>" onKeyDown="javascript:if(event.keycode==13){addshiplist(); return false;}" type="text" ></td>
        <td><span id="currency"><?php echo $currencysymbol;?></span><span id="lastpurchaseprice"><?php echo $lastpurchaseprice;?></span></td>
        <td id="quantitydiv"><input name="quantity" id="quantity" class="text" size="5" value="<?php echo $quantity; ?>" onKeyDown="javascript:if(event.keycode==13){addshiplist(); return false;}" type="text" onkeypress="return isNumberKey(event)" ></td>
        <td><div id="expdate"></div></td>
        <td><input name="suppliername" id="suppliername" class="text" size="15" value="<?php echo $suppliername; ?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" type="text"></td>
        <td><?php echo $stores; ?></td>
    </tr>
    <tr>
	  <td colspan="9" align="center">
	    <div class="buttons">
	      <button type="button" class="positive" onclick="addshiplist(-1);">
	        <img src="../images/tick.png" alt=""/> 
	        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
	        </button>
	      <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative">
	        <img src="../images/cross.png" alt=""/>
	        Cancel
	        </a>
	      </div>
	    </td>				
	  </tr>
</tbody>    
</table>
</td>
</tr>
</table>
<input type="hidden" name="id" value ="<?php echo $id;?>"/>
<input type="hidden" name="lastpprice" id="lastpprice" value ="<?php echo $lastpurchaseprice;?>"/>
<input type="hidden" name="addressbookid" id="addressbookid" value="<?php echo $selected_emp; ?>" />
<input type="hidden" name="currencyid" id="currencyid" value="<?php echo $currencyid; ?>" />
</fieldset>	
</form>
</div>