<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
//getting default currency
 $id	=	$_GET['id'];
 
 $constatus		=	$AdminDAO->getrows("$dbname_detail.supplierinvoice","invoice_status","pksupplierinvoiceid='$id'");
  $invc_status	=	$constatus[0]['invoice_status'];

 $sql			=	"SELECT s.*,d.fkdamagetypeid,d.quantity as damage_quantity,d.pkdamageid,b.barcode,b.itemdescription,FROM_UNIXTIME(s.expiry,'%d-%m-%Y') expiry  from $dbname_detail.stock  s
 
 left join barcode b on b.pkbarcodeid = s.fkbarcodeid
 left join $dbname_detail.damages d on d.fkstockid = s.pkstockid
 
where  fksupplierinvoiceid = '$id' ";
   $result2			=	$AdminDAO->queryresult($sql);
 $counter = count($result2);

  $damagetypeid	=	$result2[0]['fkdamagetypeid'];
/*  $totalitems	=	$result2[0]['fksupplierinvoiceid'];
  $totalitems	=	$result2[0]['fksupplierinvoiceid'];
  $totalitems	=	$result2[0]['fksupplierinvoiceid'];
  $totalitems	=	$result2[0]['fksupplierinvoiceid'];*/

$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];

$originprice	=	$_GET['originprice'];
//$totalitems		=	$_GET['fksupplierinvoiceid'];
$damagesarr		=	$AdminDAO->getrows("damagetype","*","1");
$d1				=	"<select name=\"detail[damagetype][]\" id=\"damagetype[]\" style=\"width:50px;\">";
for($i=0;$i<sizeof($damagesarr);$i++)
{
		$select		=	"";
	if($damagetypeid==$damagesarr[$i]['pkdamagetypeid'])
	{
		$select	=	"selected=\"selected\"";
	}
	$d2			.=	"<option value = \"".$damagesarr[$i]['pkdamagetypeid']."\" $select>".$damagesarr[$i]['damagetype']."</option>";
	
}
$damages		=	$d1.$d2."</select>";









?>


<form  name="invoiceform" id="invoiceform" style="width:920px;" onSubmit="addform(); return false;" class="form">
<fieldset>
<legend>Adjust Invoice Stock</legend>
<div  style="float:right;">
<span class="buttons">
<button type="button" class="positive" onclick="submitfrm();">
    <img src="../images/tick.png" alt=""/> 
    Update
</button>
 <a href="javascript:void(0);" onclick="closefrm();" class="negative">
    <img src="../images/cross.png" alt=""/>
    Close
</a>
</span>
</div>

<div id="loaditemscript"></div>
<span id="priceatorigin" style="display:none;"></span>
<div id="error" class="notice" style="display:none"></div>
<div id="shippercentdiv"></div>
<div id="baseprice" style="display:none"></div>
<div id="baseexpense" style="display:none"></div>
<div id="shipvalue" style="display:none"></div>
<div id="minusshipment" style="display:none"></div>
<div id="plusshipment" style="display:none"></div><br /><br /><br />
<table width="100%" cellspacing="0">
<tr>
	
	<th>Barcode</th>
    <th>Item</th>
    <th>Qty</th>
    <th>Damaged</th>
    <th>Damage Type</th>
    <th>Price @ Origin <?php echo $originprice;?></th>
    <th>Price in <?php echo $defaultcurrency;?></th>
    <th>Shipment %age</th>
    <th>Shipment Charges</th>
    <th>Cost Price</th>
    <th>Sale Price</th>
 <th>%age</th>
    <th>Box Price</th>
    <th>Batch</th>
    <th>Expiry</th>
</tr>
<?php
for($i=0;$i<$counter;$i++)
{
	  $barcode	=	$result2[0]['barcode'];
  $temdescription	=	$result2[0]['itemdescription'];
  $stockid	=	$result2[0]['pkstockid'];
	//Clearing the session by jafer
	$j	=	$i+1;
	$_SESSION['stock'][$j]					=	'';
	$_SESSION['pricechange'][$j]			=	'';
	$_SESSION['pricechangehistory'][$j]		=	'';
	$_SESSION['stockadjustment'][$j]		=	'';
	$_SESSION['damages'][$j]				=	'';
	$_SESSION['instancestock'][$j]			=	'';
?>
<tr id='tr<?php echo $i+1;?>'>
	
	<td>
    <input type="hidden" name="detail[pkstockid][]" id="pkstockid_<?php echo $i;?>" value="<?php echo $result2[$i]['pkstockid']; ?>" />
      <input type="hidden" name="detail[pkdamageid][]" id="pkdamageid_<?php echo $i;?>" value="<?php echo $result2[$i]['pkdamageid']; ?>" />
    <input name="detail[barcode1][]" id="barcode<?php echo $i+1;?>" class="text" type="text" autocomplete="off" onfocus="this.select()" size="8" value="<?php echo $result2[$i]['barcode']; ?>" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,0,'<?php echo $i+1;?>'); return false;}" ><input type="hidden" name="bc[]" id="bc<?php echo $i+1;?>" /></td>
	<td><input name="detail[productname][]" id="productname<?php echo $i+1;?>" type="text" onkeyup="suggestnow(event,'<?php echo $i+1;?>')" class="text" autocomplete="off" size="10" value="<?php echo $result2[$i]['itemdescription']; ?>" onfocus="this.select(); addresults(this.id);" onkeydown="javascript:if(event.keyCode==13) {return false;}"/><div id="res<?php echo $i+1;?>"></div></td>
	<td><input type="text" name="detail[units][]" value="<?php echo $result2[$i]['quantity']; ?>" onkeypress="return isNumberKey(event)" class="text" id="units<?php echo $i+1;?>" size="3" onfocus="this.select()" /></td>
	<td><input type="text" name="detail[damaged][]" onkeypress="return isNumberKey(event)" value="<?php echo $result2[$i]['damage_quantity']; ?>" class="text" id="damaged<?php echo $i+1;?>" size="3" onfocus="this.select()" onblur="checkunits('<?php echo $i+1;?>');" /></td>
	<td><select name="detail[damagetype][]" id="damagetype[]" style="width:100px;">
<?php for($e=0;$e<sizeof($damagesarr);$e++)
{
	?>
	<option value = "<?php echo $damagesarr[$e]['pkdamagetypeid']?>" <?php if($damagesarr[$e]['pkdamagetypeid']==$result2[$i]['fkdamagetypeid']){ echo 'selected';}?>><?php echo $damagesarr[$e]['damagetype']?></option>
	
<?php } ?></select></td>
	<td><input type="text" name="detail[purchaseprice][]" onkeypress="return isNumberKey(event)" class="text" id="pp<?php echo $i+1;?>" size="3" onfocus="this.select()" onblur="calcprice('<?php echo $i+1;?>');" value="<?php echo $result2[$i]['purchaseprice']; ?>" /></td>
	<td><input type="text" name="detail[priceinrs][]" value="<?php echo $result2[$i]['priceinrs']; ?>"  onkeypress="return isNumberKey(event)" class="text" id="pr<?php echo $i+1;?>" size="3" readonly="readonly" /></td>
	<td><input type="text" name="detail[shipmentpercentage][]" value="<?php echo $result2[$i]['shipmentpercentage']; ?>" onkeypress="return isNumberKey(event)" class="text" id="sch<?php echo $i+1;?>" size="3" onfocus="this.select()" onblur="calculatethis();" /></td>
	<td><input type="text" name="detail[shipmentcharges][]" value="<?php echo $result2[$i]['shipmentcharges']; ?>" onkeypress="return isNumberKey(event)" class="text" id="shipmentcharges<?php echo $i+1;?>" size="3" readonly="readonly" /></td>
	<td><input type="text" name="detail[costprice][]"  value="<?php echo $result2[$i]['costprice']; ?>" onkeypress="return isNumberKey(event)" class="text" id="cp<?php echo $i+1;?>" size="3" readonly="readonly" /></td>
	<td><input type="text" name="detail[saleprice][]" value="<?php echo $result2[$i]['retailprice']; ?>" onkeypress="return isNumberKey(event)" class="text" id="sp<?php echo $i+1;?>" size="3" onfocus="this.select()" onblur="itempercentage(<?php echo $i+1;?>)" /></td>
<td><div id="itempercentage<?php echo $i+1;?>"></div></td>
	<td><input type="text" name="detail[boxprice][]" value="<?php echo $result2[$i]['boxprice']; ?>"  onkeypress="return isNumberKey(event)" class="text" id="boxprice<?php echo $i+1;?>" size="3" onfocus="this.select()" /></td>
	<td><input type="text" name="detail[batch][]" value="<?php echo $result2[$i]['batch']; ?>" class="text" id="batch<?php echo $i+1;?>" size="3" onfocus="this.select()" /></td>
    <td><input type="text" name="detail[expiry][]" value="<?php echo $result2[$i]['expiry']; ?>" class="text" id="expiry<?php echo $i+1;?>" size="8" onblur="alertdate(this.value,this.id);" /><input type="hidden" name="totalitems" id="totalitems" value="<?php echo $totalitems;?>" /><input type="hidden" name="id" id="id" value="<?php echo $id;?>" /></td>
</tr>
<?php
}
?>
</table>
<div class="buttons">
<button type="button" class="positive" onclick="submitfrm();">
    <img src="../images/tick.png" alt=""/> 
    Update
</button>
 <a href="javascript:void(0);" onclick="closefrm();" class="negative">
    <img src="../images/cross.png" alt=""/>
    Close
</a>
</div><div id="loadingdiv" class="loading" style="display:none"></div>
<div id="loadstockfrm" style="display:none;"></div>
</fieldset>
</form>
<script language="javascript" type="text/javascript">
jQuery(function($)
{
	for(j=1;j<=<?php echo $counter;?>;j++)
	{
		$('#expiry'+j).mask("99-99-9999");
	}
});


function submitfrm(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insert_invoice_stock_new.php?id='+id,
					type: 'POST',
					success: response,
					cache:false
				}
	jQuery('#invoiceform').ajaxSubmit(options);
}
	
function response(text)
{
	if(text=='')
	{
		loading('Stock Data Saved...');
		jQuery('#maindiv').load('managesupplierinvoices.php?param=undefined&id='+'<?php echo $id; ?>');
		document.getElementById('#invoiceform').style.display	=	'none';
		
	}
	else
	{
		document.getElementById('error').innerHTML		=	text;	
		document.getElementById('error').style.display	=	'block';
	}
}
function getitemdetails(bc,itm,id)
{
	//alert(itm);
	if(bc=='')
	{
		alert("Please enter Barcode.");
		return false;
	}
	bc = trim(bc);
	jQuery('#loaditemscript').load('newgetitemdata.php?bc='+bc+'&item='+itm+'&id='+id);
	document.getElementById('units'+id).focus();
	document.getElementById('barcode'+id).disabled	=	false;
	document.getElementById('productname'+id).disabled	=	false;
	document.getElementById('bc'+id).value	=	document.getElementById('barcode'+id).value;
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

	//var shipid	=	document.getElementById('shipment').value;
	var pprice	=	document.getElementById('pp'+pid).value;
	/*if(shipid == '')
	{
		alert('Select Shipment to continue');
		return false;
	}
	else
	{*/
		//pvalue	=	document.getElementById('hprice').value;
		if(pprice!='')
		{
			document.getElementById('pr'+pid).value	=	pprice
		}
		else
		{
			document.getElementById('pr'+pid).value	=	'';
		}
	//}
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
function addresults(resid)
{
	for(i=1;i<=<?php echo $counter;?>;i++)
	{
		resultid	=	resid.substring(11);
		//alert(i+'----'+resdid);
		document.getElementById('res'+i).innerHTML	=	'';
	}
	document.getElementById('res'+resultid).innerHTML	=	"<div id=\"results\" style=\"width:50px\">";
	document.getElementById('productname'+resultid).focus();
}
function calculatethis()
{
	var remunitsprice=0, damagedunitsprice=0,percentageonprice=0, pprice=0;
	for(i=1;i<=<?php echo $counter;?>;i++)
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
	remainingpercentage	=	remainingpercentage.toFixed(2);
	document.getElementById('percentagediv').innerHTML	=	remainingpercentage;
}
function itempercentage(id)
{
	cost	=	document.getElementById('cp'+id).value;
	sale	=	document.getElementById('sp'+id).value;
	if(cost=='' || sale=='')
	{
		return;	
	}
	percent	=	(sale-cost)/sale*100;
	percent	=	percent.toFixed(2);//Math.round(percent,0);
	document.getElementById('itempercentage'+id).innerHTML	=	percent+'%';
}

/*function edititem(itemid)
{
	$.post('removelastinsertions.php',{numb:itemid});
	document.getElementById('tr'+itemid).style.backgroundColor="#fff";	
	document.getElementById('expiry'+itemid).disabled		=	false;

	document.getElementById('barcode'+itemid).disabled		=	false;
	document.getElementById('barcode'+itemid).value			=	'';
	document.getElementById('bc'+itemid).value				=	'';
	document.getElementById('productname'+itemid).disabled	=	false;
	document.getElementById('productname'+itemid).value		=	'';
	document.getElementById('units'+itemid).value			=	'';
	document.getElementById('damaged'+itemid).value			=	'';
	document.getElementById('pp'+itemid).value				=	'';
	document.getElementById('pr'+itemid).value				=	'';
	document.getElementById('sch'+itemid).value				=	'';
	document.getElementById('shipmentcharges'+itemid).value	=	'';
	document.getElementById('cp'+itemid).value				=	'';
	document.getElementById('sp'+itemid).value				=	'';
	document.getElementById('boxprice'+itemid).value		=	'';
	document.getElementById('itempercentage'+itemid).innerHTML	=	'';
	document.getElementById('batch'+itemid).value			=	'';
	document.getElementById('expiry'+itemid).value			=	'';
	document.getElementById('barcode'+itemid).focus();
}*/
document.getElementById('barcode1').focus();
function loadingdelay(text)
{
	selectedstring = "";
	$("#loadingdiv").ajaxStart(function()
	{
    	document.getElementById('loadingdiv').innerHTML=text;
		$(this).show();
 	});
	$("#loadingdiv").ajaxStop(function()
	{
   		$(this).hide();
	 });
}
function closefrm()
{
		hidediv('subsection');
}
function myfunction(numb)
{
	if(document.getElementById('lock').checked)
	{
		var lock = 'locked';
	}
	else
	{
		var lock = 'unlocked';
	}
	var shipment		=	$('#shipment').val();
	var supplier		=	$('#supplier').val();
	var invoice			=	$('#invoice').val();
	var totalitems		=	$('#totalitems').val();

	var bc 				=	$('#bc'+numb).val();
	//var productname		=	$('#productname'+numb).val();
	var units			=	$('#units'+numb).val();
	var damaged			=	$('#damaged'+numb).val();

	var damagetype		=	$('#damagetype'+numb).val();
	var purchaseprice	=	$('#pp'+numb).val();
	var priceinrs		=	$('#pr'+numb).val();
	var shipmentpercent	=	$('#sch'+numb).val();

	var shipmentcharges	=	$('#shipmentcharges'+numb).val();
	var costprice		=	$('#cp'+numb).val();
	var saleprice		=	$('#sp'+numb).val(); 

	var boxprice		=	$('#boxprice'+numb).val();
	var batch			=	$('#batch'+numb).val();
	var expiry			=	$('#expiry'+numb).val();	

var msg	=	'';
if(bc=='')
{
	msg	+= 	"Please Enter Barcode to proceed.\n";
}
if(units=='')
{
	msg	+= 	"Please Enter Quantity of the Item.\n";
}
if(costprice=='')
{
	msg	+= 	"Please Enter Costprice.\n";
}
if(saleprice=='')
{
	msg	+= 	"Please Enter Saleprice.\n";
}

	if(msg!='')
	{
		alert(msg);	
		document.getElementById('barcode'+numb).focus();
	}
/*	else
	{
		$.post('processinsertstock.php?numb='+numb,{lock:lock,shipment:shipment,supplier:supplier,invoice:invoice,totalitems:totalitems,bc:bc,units:units,damaged:damaged,damagetype:damagetype,purchaseprice:purchaseprice,priceinrs:priceinrs,shipmentpercentage:shipmentpercent,shipmentcharges:shipmentcharges,costprice:costprice,saleprice:saleprice,boxprice:boxprice,batch:batch,expiry:expiry},function(data)
		{
			if(data=="success")
			{
				document.getElementById('tr'+numb).style.backgroundColor="#CFD3BA";	
				document.getElementById('expiry'+numb).disabled		=	true;
				//$('#productname'+numb).style.backgroundColor="avender";	
			}
			else
			{
				alert(data);
			}
		});
	}
*/}
function alertdate(val,id)
{
	if(val == '__-__-____')
	{	
		return;
	}
	if(val!="")
	{
		dtval	=	val.split('-');
		dateval	=	dtval[2]+'-'+dtval[1]+'-'+dtval[0];
		if(!isValidDate(val))
		{
			//document.getElementById(id).focus();
			window.setTimeout(function ()
    		{
        		document.getElementById(id).focus();
    		}, 0);
			alert("The date you entered is not valid. Correct format is: (dd-mm-yyyy)");
		}
		else if(dateval<"<?php echo date('Y-m-d')?>")
		{
			//document.getElementById(id).focus();
			window.setTimeout(function ()
    		{
        		document.getElementById(id).focus();
    		}, 0);
			alert("The item has already expired, please correct the date.");
		}
		else
		{				
			var numb	=	id.substring(6);
			myfunction(numb);
		}
	}
}
loadingdelay('Please wait ...');
</script>