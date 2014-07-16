<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$id				=	$_REQUEST['id'];
$selected_store	=	$_SESSION['storeid'];
$shipcurrency	=	$AdminDAO->getrows("shipment","exchangerate","pkshipmentid='$id'");
$rate			=	$shipcurrency[0]['exchangerate'];
//this is the number of MAX items that can be added to the stock
$itemcount	=	10;
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];
//selecting damages
$damagesarr	=	$AdminDAO->getrows("damagetype","*","1");
$d1			=	"<select name=\"damagetype[]\" id=\"damagetype[]\" style=\"width:80px;\">";
for($i=0;$i<sizeof($damagesarr);$i++)
{
	$d2			.=	"<option value = \"".$damagesarr[$i][pkdamagetypeid]."\">".$damagesarr[$i][damagetype]."</option>";
}
$damages		=	$d1.$d2."</select>";
//end damages

if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	// stores
	$stores			=	$AdminDAO->getrows("store","storename,pkstoreid","storedeleted<>1");
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
	
	//shiplist data
	$shiplistdata	=	$AdminDAO->getrows("shiplist,shiplistdetails","*","pkshiplistid=fkshiplistid AND fkshipmentid='$id'");
	$num			=	sizeof($shiplistdata);
	//end shiplist data
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	$default_store	=	$_SESSION['storeid'];
	$stores			=	$AdminDAO->getrows("store","storename,pkstoreid,storedb","storedeleted<>1 AND storestatus=1 ");
	$storesel		=	"<select name=\"store\" id=\"store\" style=\"width:100px;\" readonly><option value=\"\">Location</option>";//
	for($i=0;$i<sizeof($stores);$i++)
	{
		$storename	=	$stores[$i]['storename'];
		$storeid	=	$stores[$i]['pkstoreid'];
		$storedb	=	$stores[$i]['storedb'];
		$select		=	"";
		if($storeid == $default_store)
		{
			$select = "selected=\"selected\"";
		}
		$storesel2	.=	"<option value=\"$storeid|$storedb\" $select>$storename</option>";
	}
	$stores			=	$storesel.$storesel2."</select>";
	// end stores
	// end stores
	
	//shiplist data
	$shiplistdata	=	$AdminDAO->getrows("shiplist,shiplistdetails","*","pkshiplistid=fkshiplistid AND fkshipmentid='$id' AND shiplistdetails.quantity <> received+damaged");
	$num			=	sizeof($shiplistdata);
}//end edit
?>
<script language="javascript" type="text/javascript">
jQuery().ready(function() 
{
	getshipmentgroup(<?php echo $id;?>);
	for(i=1;i<=<?php echo $num; ?>;i++)
	{
		var pprice	=	document.getElementById('pp'+i).value;
		if(pprice!='')
		{
			document.getElementById('pr'+i).value	=	pprice*<?php echo $rate;?>;
		}
		else
		{
			document.getElementById('pr'+i).value	=	'';
		}
		//setting expiries
		$("#expiry"+i).mask("99-99-9999");
	}
});
function getshipmentgroup(id)
{
	if(id!='')
	{
		//jQuery('#shipmentgroup').load('loadshipmentgroup.php?id='+id+'&type=shipgroup');
		jQuery('#currency').load('loadcurrency.php?id='+id);
		jQuery('#priceatorigin').load('loadcurrency.php?p=1&id='+id);
		jQuery('#shippercentdiv').load('listshipmentgroup.php?shipid='+id);
		//document.getElementById('barcode1').focus();

	}
}
function getbrandsupplier(id)
{
	//alert(id);
	if(id!='')
	{
		if(document.getElementById('locksupplier').checked == false)
		{
			jQuery('#brandsupplier').load('loadshipmentgroup.php?id='+id+'&type=brandsupplier');
			document.getElementById('brandsupplier2').style.display	= 'none';
		}
	}
}
function addinstancestock()
{
	
	loading('System is Saving The Data....');
	options	=	{	
					url : 'insertstock.php',
					type: 'POST',
					success: addstockresponse
				}
	jQuery('#adstockfrm').ajaxSubmit(options);

}
function addproductinstance(productid,barcode)
{
	jQuery('#instance').load('addproductinstancefrm.php?id='+productid+'&bc='+barcode);
}
function addstockresponse(text)
{
	if(text=='')
	{
		adminnotice('Stock data has been saved.',0,8000);
		//clearforms();
		//document.getElementById('barcode1').focus();
		jQuery('#maindiv').load('manageshipment.php');
	}
	else
	{
		adminnotice(text,0,8000);	
	}
}
function hidediv(divid)
{
	document.getElementById(divid).style.display='none';	
}

function calculateprice(val)
{
	var shipmentid	=	document.getElementById('shipment').value;
	if(shipmentid!='')
	{
		var currency	=	document.getElementById('shipmentcurrency').value;
		var rate		=	document.getElementById('exchangerate').value;
		var total		=	rate*val;
		document.getElementById('priceinrs').value=total;
	}
	else
	{
	 alert("Please select a Shipment first");
	 document.getElementById('shipment').focus();
	}
}
function hidethis(id)
{
	id = parseInt(id);
	if(id == 10)
	{
		document.getElementById('btn'+id).style.display = 'none';
	}
	document.getElementById('btn'+id).style.display = 'none';
	id	=	id+1;
	document.getElementById(id).style.display = 'block';
	document.getElementById(id).style.display = 'table-row';
}
function submitfrm()
{
	loading('System is Saving The Data....');
	options	=	{	
					url : 'receivelistact.php',
					type: 'POST',
					success: addstockresponse
				}
	jQuery('#adstockfrm').ajaxSubmit(options);

}
function addproductinstance(productid,barcode)
{
	jQuery('#instance').load('addproductinstancefrm.php?id='+productid+'&bc='+barcode);
}

function checkunits(num)
{
	var dnum	=	parseInt(document.getElementById('damaged'+num).value);
	var unum	=	parseInt(document.getElementById('units'+num).value);
	if(dnum>unum)
	{
		alert('Damaged Units can not be more than Total Units');
		document.getElementById('damaged'+num).focus();
		return false;
	}
}
function calcprice(pid)
{
	var shipid	=	document.getElementById('shipment').value;
	var pprice	=	document.getElementById('pp'+pid).value;
	if(shipid == '')
	{
		alert('Select Shipment to continue');
		return false;
	}
	else
	{
		pvalue	=	document.getElementById('hprice').value;
		if(pprice!='')
		{
			document.getElementById('pr'+pid).value	=	pprice*pvalue;
		}
		else
		{
			document.getElementById('pr'+pid).value	=	'';
		}
	}
}

// The formula
// Shipment Value
/* 
Value	=	(Total Value) minus ([price*(units-damaged)])
*/
// Shipment Cost
/*
Cost	=	(Total Cost) minus ([price*(units)*percentage]) Plus ([damaged*price])
*/

// The Percentage

/*
	Percentage	=	Cost/Value*100 
*/
function calculatethis()
{
	var remunitsprice=0, damagedunitsprice=0,percentageonprice=0, pprice=0;
	for(i=1;i<=<?php echo $num;?>;i++)
	{
		units	=	document.getElementById('units'+i).value;
		damaged	=	document.getElementById('damaged'+i).value;
		pprice	=	document.getElementById('pr'+i).value;
		percent	=	document.getElementById('sch'+i).value;
		retail	=	document.getElementById('cp'+i).value;
		shipch	=	document.getElementById('shipmentcharges'+i).value;
				
		//1. remaining units for shipment value
		
		remunits	=	units-damaged;
		remunitsprice	=	remunitsprice + (remunits*pprice);
		
		// to be deducted from shipment value
		document.getElementById('shipvalue').innerHTML	=	remunitsprice;
		
		// to be deducted from the shipment cost
		percentageonprice	=	percentageonprice	+	((remunits*pprice)*(percent/100));
		document.getElementById('minusshipment').innerHTML	=	percentageonprice;
				
		//2. damaged units for shipment cost
		
		// to be added to the shipment cost
		damagedunitsprice	=	damagedunitsprice + (damaged*pprice);
		document.getElementById('plusshipment').innerHTML = damagedunitsprice;
		
		// putting the retail price	
		if(pprice!='')
		{
			document.getElementById('cp'+i).value	=	parseFloat(pprice)+(pprice*percent/100);
		}
		else
		{
			document.getElementById('cp'+i).value	=	'';
		}
		
		if(pprice!='')
		{
			document.getElementById('shipmentcharges'+i).value	=	parseFloat(pprice)*percent/100;
		}
		else
		{
			document.getElementById('shipmentcharges'+i).value	=	'';
		}
	}
	basevalue	=	document.getElementById('baseprice').innerHTML;
	basevalue	=	parseFloat(basevalue);
	basecost	=	document.getElementById('baseexpense').innerHTML;
	basecost	=	parseFloat(basecost);
	
	finalvalue	=	basevalue	-	parseFloat(document.getElementById('shipvalue').innerHTML);
	finalcost	=	basecost	-	parseFloat(document.getElementById('minusshipment').innerHTML)+parseFloat(document.getElementById('plusshipment').innerHTML);
	remainingpercentage	=	finalcost/finalvalue*100;
	document.getElementById('percentagediv').innerHTML	=	remainingpercentage;
}
function alertdate(id)
{
	yy	=	document.getElementById(id).value;
	if(yy!="")
	{
		year	=	parseInt(yy)+2000;
		newid	=	id.substr(2,2);
		mm		=	document.getElementById('mm'+newid).value;
		dd		=	document.getElementById('dd'+newid).value;
		dateval	=	year+'-'+mm+'-'+dd;
		if(dateval<"<?php echo date('Y-m-d')?>")
		{
			alert("The item has already expired, please correct the date.");
		}
		valid	=	dd+'-'+mm+'-'+year;
		if(!isValidDate(valid))
		{
			alert("The date you entered is not valid. Correct format is: day-month-year (dd-mm-yy)");
		}
	}
}
</script>
<div id="loaditemscript">
</div>
<div id="currency" style="display:none;"></div>
<div id="cur">
</div>
<div id="instancediv">
<div id="stockitem">
<br />
<div id="error" class="notice" style="display:none"></div>
<div id="shippercentdiv"></div>
<div id="baseprice" style="display:none"></div>
<div id="baseexpense" style="display:none"></div>
<div id="shipvalue" style="display:none"></div>
<div id="minusshipment" style="display:none"></div>
<div id="plusshipment" style="display:none"></div>
<form id="adstockfrm" class="form">
<fieldset>
<legend>Add Stock</legend>
<div  style="float:right;">
<span class="buttons">
<button type="button" class="positive" onclick="submitfrm();">
    <img src="../images/tick.png" alt=""/> 
    Save
</button>
 <a href="javascript:void(0);" onclick="hidediv('instancediv');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
</a>
</span>
</div>
<br />
<table width="14%">
<tr>
	<th>Destination Store</th>
    <td><?php echo $stores; ?></td>
  	<th>Shipment Percentage</th>
  	<td><span id="percentagediv"></span>%</td>
</tr>
</table>
<table>
    <tr>
        <th>Barcode</th>
        <th>Item</th>
        <th>Units</th>
        <th>Damaged</th>
        <th>Damage Type</th>
        <th>Price @ Origin in <span id="priceatorigin"></span></th>
        <th>Price in <?php echo $defaultcurrency;?></th>
        <th>Shipment Group %</th>
        <th>Shipment Charges</th>
        <th>Cost Price</th>
        <th>Sale Price</th>
        <th>Box Sale Price</th>
        <th>Batch</th>
        <th>Expiry</th>
        <td>&nbsp;</td>
    </tr>
    <?php
	for($i=0;$i<sizeof($shiplistdata);$i++)
	{
			$barcode		=	$shiplistdata[$i]['barcode'];
			$itemdescription=	$shiplistdata[$i]['itemdescription'];
			$quantity		=	$shiplistdata[$i]['quantity'];
			$damaged		=	$shiplistdata[$i]['damaged'];
			$received		=	$shiplistdata[$i]['received'];
			$quantity		=	$quantity-($damaged+$received);
			$price			=	$shiplistdata[$i]['price'];
			$detailsid		=	$shiplistdata[$i]['pkshiplistdetailsid'];
			$shiplistid		=	$shiplistdata[$i]['pkshiplistid'];
			$expirydate		=	implode("-",array_reverse(explode("-",$shiplistdata[$i]['expiry'])));
			$brand			=	$shiplistdata[$i]['fkbrandid'];
			$brand			=	$shiplistdata[$i]['fksupplier'];
			$darray[]		=	$detailsid;
	?>
    <tr id="1">
    <td><input type="text" name="barcode[]" onkeypress="return isNumberKey(event)" class="text" id="barcode" size="15" readonly="readonly" value="<?php echo $barcode;?>"/></td>
    <td>
		<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
    		<?php echo $itemdescription;?>
		<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
    	<input type="text" name="itemdescription[]" value="<?php echo $itemdescription;?>" class="text" id="itemdescription" size="15" readonly="readonly" />
        <?php }//end edit?>
    </td>
            <td><input type="text" name="units[]" onkeypress="return isNumberKey(event)" class="text" id="units<?php echo $i+1;?>" size="4" value="<?php echo $quantity;?>" /></td>
        <td><input type="text" name="damaged[]" onkeypress="return isNumberKey(event)" class="text" id="damaged<?php echo $i+1;?>" size="4" onblur="checkunits('1');" /></td>
        <td><?php echo $damages;?></td>
        <td><input type="text" name="purchaseprice[]" onkeypress="return isNumberKey(event)" class="text" id="pp<?php echo $i+1;?>" size="4" onblur="calcprice('<?php echo $i+1;?>');" value="<?php echo $price;?>" readonly="readonly"/></td>
        <td><input type="text" name="priceinrs[]" onkeypress="return isNumberKey(event)" class="text" id="pr<?php echo $i+1;?>" size="4" readonly="readonly" /></td>
        <td><input type="text" name="shipmentpercentage[]" onkeypress="return isNumberKey(event)" class="text" id="sch<?php echo $i+1;?>" size="4" onblur="calculatethis();" /></td>
		<td><input type="text" name="shipmentcharges[]" onkeypress="return isNumberKey(event)" class="text" id="shipmentcharges<?php echo $i+1;?>" size="4" readonly="readonly" /></td>                
        <td><input type="text" name="costprice[]" onkeypress="return isNumberKey(event)" class="text" id="cp<?php echo $i+1;?>" size="4" readonly="readonly" /></td>
        <td><input type="text" name="saleprice[]" onkeypress="return isNumberKey(event)" class="text" id="sp<?php echo $i+1;?>" size="4" /></td>
        <td><input type="text" name="boxprice[]"  onkeypress="return isNumberKey(event)" class="text" id="boxprice<?php echo $i+1;?>" size="4" /></td>
        <td><input type="text" name="batch[]" class="text" id="batch<?php echo $i+1;?>" size="4" /></td>
        <td>
        <?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
            <input type="text" name="expiry[]" onkeypress="return isNumberKey(event)" size="8" class="text" id="expiry<?php echo $i+1;?>" maxlength="2" value="<?php echo $expirydate; ?>" /><input type="hidden" id="detailsid" name="detailsid[]" value="<?php echo $detailsid;?>" /><input type="hidden" name="shiplistid[]" id="shiplistid" value="<?php echo $shiplistid; ?>" /></td>    
		<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
            <input type="text" name="expiry[]" onkeypress="return isNumberKey(event)" size="8" class="text" id="expiry<?php echo $i+1;?>" value="<?php echo $expirydate; ?>" /><input type="hidden" id="detailsid" name="detailsid[]" value="<?php echo $detailsid;?>" /><input type="hidden" name="shiplistid[]" id="shiplistid" value="<?php echo $shiplistid; ?>" /></td>    
         <?php }//end edit?>
        <td>&nbsp;</td>        
    </tr>
    <?php
	}
	?>
       
   
</table>
<div class="buttons">
<button type="button" class="positive" onclick="submitfrm();">
    <img src="../images/tick.png" alt=""/> 
    Save
</button>
 <a href="javascript:void(0);" onclick="hidediv('instancediv');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
</a>
</div>
<input type="hidden" name="shipment" id="shipment" value="<?php echo $id;?>" />
</fieldset>
</form>
<br />
</div>
</div>
<script language="javascript" type="text/javascript">
//document.getElementById('barcode1').focus();
</script>