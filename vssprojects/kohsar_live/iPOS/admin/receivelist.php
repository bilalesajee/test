<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['id'];
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];
?>
<script language="javascript">
jQuery().ready(function() 
{
	<?php
	for($x=0;$x<sizeof($ids);$x++)
	{
		$sid			=	$ids[$x];
		?>
		$("#expiry_"+'<?php echo $sid;?>').mask("99-99-9999");
	<?php
	}
	?>
});
function updatelist()
{
	options	=	{	
					url : 'receivelistact.php',
					type: 'POST',
					success: response
				}
	jQuery('#shiplistfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Wish List has been saved.',0,5000);
		jQuery('#maindiv').load('managereceivelist.php');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function viewlistdetails(id)
{
	if(document.getElementById(id+'_detail').style.display=='none')
	{
		document.getElementById(id+'_detail').style.display	=	'block';
	}
	else
	{
		document.getElementById(id+'_detail').style.display	=	'none';
	}
}
function calculatethis()
{
	var remunitsprice=0, damagedunitsprice=0,percentageonprice=0, pprice=0;
	for(i=1;i<=10;i++)
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
</script>
<div id="xshipdiv">
<form  name="shiplistfrm" id="shiplistfrm" style="width:920px;" onSubmit="updatelist(); return false;" class="form" >
    <fieldset>
      <legend>
      <?php if($id=='-1'){echo 'Add';}else{echo 'Update';}?>
      Wish List</legend>
      <div  style="float:right;"><span class="buttons">
        <button type="button" class="positive" onclick="updatelist();"> <img src="../images/tick.png" alt=""/>
        <?php if($shipmentid=='-1'){echo 'Save';}else{echo 'Update';}?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('xshipdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a></span></div>
<table width="100%">
<tr>
<td height="10" valign="top">
<div class="topimage2" style="height:6px;"><!-- --></div>
<div id="shippercentdiv"></div>
<div id="baseprice" style="display:none"></div>
<table cellpadding="2" cellspacing="0" width="100%">

<tr align="left">
<th>Barcode</th>
<th>Item</th>
<th>Quantity</th>
<th>Received</th>
<th>Damaged</th>
<th>Damage Type</th>
<th>Price in <?php echo $defaultcurrency;?></th>
<th>Shipment Group%</th>
<th>Shipment Charges</th>
<th>Sale Price</th>
<?php
for($i=0;$i<sizeof($chargesarray);$i++)
{
?>
<th><?php echo $chargesarray[$i]['chargesname'];?></th>
<?php
}
?>
</tr>
<?php
$shiplistdata	=	$AdminDAO->getrows("shiplist,shiplistdetails","*","pkshiplistid=fkshiplistid AND fkshipmentid='$id'");
for($i=0;$i<sizeof($shiplistdata);$i++)
{
		$barcode		=	$shiplistdata[$i]['barcode'];
		$itemdescription=	$shiplistdata[$i]['itemdescription'];
		$quantity		=	$shiplistdata[$i]['quantity'];
		$detailsid		=	$shiplistdata[$i]['pkshiplistdetailsid'];
		$darray[]		=	$detailsid;
?>
    <tr class="<?php if($x%2 == 0) {echo "even";} else {echo "odd";}?>">
    <td><?php echo $barcode; ?></td>
    <td><?php echo $itemdescription; ?></td>
    <td><?php echo $quantity; ?></td>
    <td><input type="text" onfocus="this.select()" name="received_<?php echo $detailsid; ?>" value="<?php echo $quantity; ?>" class="text" size="5" onKeyDown="javascript:if(event.keycode==13){updatelist(); return false;}" /></td>
    <td><input type="text" onfocus="this.select()" name="damaged_<?php echo $detailsid; ?>" value="<?php echo $weight; ?>" class="text" size="5" onkeydown="javascript:if(event.keycode==13){updatelist(); return false;}" /></td>
    <td><input type="text" name="priceinrs" onkeypress="return isNumberKey(event)" class="text" id="priceinrs" size="4" onblur="calculatethis();" /></td>
     <td><input type="text" name="shipmentgroup" onkeypress="return isNumberKey(event)" class="text" id="shipmentgroup" size="4" onblur="calculatethis();" /></td>
      <td><input type="text" name="shipmentcharges" onkeypress="return isNumberKey(event)" class="text" id="shipmentcharges" size="4" onblur="calculatethis();" /></td>
       <td><input type="text" name="saleprice" onkeypress="return isNumberKey(event)" class="text" id="saleprice" size="4" onblur="calculatethis();" /></td>
    </tr>
<?php
}
//$dstring	=	implode(",",$darray);
?>
	<tr>
	  	<td colspan="<?php echo $colspan; ?>">
	    <div class="buttons">
	      <button type="button" class="positive" onclick="updatelist();">
	        <img src="../images/tick.png" alt=""/> 
	        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
	        </button>
	      <a href="javascript:void(0);" onclick="hidediv('xshipdiv');" class="negative">
	        <img src="../images/cross.png" alt=""/>
	        Cancel
	        </a>
	      </div>
	    </td>				
	</tr>
</table>
</td>
</tr>
</table>
<input type="hidden" name="dstring" value="<?php echo $dstring;?>" />
<input type="hidden" name="ids" value="<?php echo $id;?>" />
</fieldset>
</form>
</div>