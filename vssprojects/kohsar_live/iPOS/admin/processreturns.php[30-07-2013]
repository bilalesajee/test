<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$id				=	$_REQUEST['id'];
//selectin supplier
$constatus		=	$AdminDAO->getrows("$dbname_detail.supplierinvoice,supplier","companyname,invoice_status","pksupplierinvoiceid='$id' AND fksupplierid=pksupplierid");
$companyname	=	$constatus[0]['companyname'];
$invc_status	=	$constatus[0]['invoice_status'];
//selecting items
$query		=	"SELECT 
						pkstockid,
						quantity,
						unitsremaining,
						FROM_UNIXTIME(expiry,'%d-%m-%y') expiry,
						FROM_UNIXTIME(updatetime,'%d-%m-%y') updatetime,
						itemdescription,
						barcode
				FROM
						$dbname_detail.stock,barcode
				WHERE 	
						fkbarcodeid			=	pkbarcodeid AND
						fksupplierinvoiceid	=	'$id'
						
				";
$stocksdata	=	$AdminDAO->queryresult($query);
$totalrecs	=	sizeof($stocksdata);
//selecting return types
$returnsarr	=	$AdminDAO->getrows("returntype","*","1");
$d1			=	"<select name=\"returntype[]\" id=\"returntype[]\" style=\"width:150px;\">";
for($i=0;$i<sizeof($returnsarr);$i++)
{
	$d2			.=	"<option value = \"".$returnsarr[$i]['pkreturntypeid']."\">".$returnsarr[$i]['returntype']."</option>";
}
//////////////////////////////////For Damages///////////////////////////////////////////////////////////////

$returnsarrd	=	$AdminDAO->getrows("main.damagetype","*","1");
$d2d			.=	"<option value=\"\" disabled=\"disabled\" >....Damages Types.......</option>";

for($i=$i;$i<sizeof($returnsarrd);$i++)
{
	$d2d			.=	"<option value = \"".$returnsarrd[$i]['pkdamagetypeid']."\">".$returnsarrd[$i]['damagetype']."</option>";
}

$returns		=	$d1.$d2."</select>";

/////////////////////////////////////////////////////////////////////////////////////////////////
// selecting previous returns

?>
<script language="javascript" type="text/javascript">
function submitfrm()
{
	loading('System is Saving The Data....');
	options	=	{	
					url : 'processreturnsact.php',
					type: 'POST',
					success: response2
				}
	jQuery('#adstockfrm').ajaxSubmit(options);
}
function response2(text)
{
	if(text=='')
	{
		adminnotice('Records updated successfully.',0,5000);
		jQuery('#maindiv').load('managesupplierinvoices.php');
	}
	else
	{
		adminnotice(text,0,5000);
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
<input type="hidden" value="<?php echo $id;?>" id="id" name="id" />
<fieldset>
<legend>Process Returns for < <?php echo $companyname;?> >
</legend><div  style="float:right;">
<span class="buttons">
<?php if($invc_status==1){?>
<button type="button" class="positive" onclick="submitfrm();">
    <img src="../images/tick.png" alt=""/> 
    Save
</button>
<?php }?>
 <a href="javascript:void(0);" onclick="hidediv('instancediv');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
</a>
</span>
</div><?php if($invc_status==0){?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" >
  <tr>
    <td style="font-weight:bold; color:#F00;">First Change Invoice Status Than Return Any Product[Status is Open Now]</td>
  </tr>
</table>
 <?php }else{?>
<br /><br />
<?php }?>
<table>
    <tr>
        <th>Serial</th>
        <th>Barcode</th>
        <th>Item</th>
        <th>Expiry</th>
        <th>Units</th>
        <th>Unitsremaining</th>
        <th>Add Date</th>
		<th>Return Type</th>
        <th>Return Status</th>
        <th>Returned</th>
    </tr>
    <?php
	$serial	=	0;
	$bgcolor=	'#DFEFFF';
	for($i=0;$i<sizeof($stocksdata);$i++)
	{
		$serial				=	$serial	+1;
		$pkstockid			=	$stocksdata[$i]['pkstockid'];
		//selecting previous returns for this stock
		$returnsqty			=	$AdminDAO->getrows("$dbname_detail.returns","SUM(quantity) qty","fkstockid='$pkstockid'");
		$retqty				=	$returnsqty[0]['qty'];
		$barcode			=	$stocksdata[$i]['barcode'];
		$itemdescription	=	$stocksdata[$i]['itemdescription'];
		$expiry				=	$stocksdata[$i]['expiry'];
		$totalunits			=	$stocksdata[$i]['quantity'];
		$remaining			=	$stocksdata[$i]['unitsremaining'];
		//$remaining			-=	$retqty;
		$updatetime			=	$stocksdata[$i]['updatetime'];
		if($status ==1)
		{
			$bgcolor	=	"#BEFAA5";
		}
		else
		{
			if($bgcolor	==	'#DFEFFF')
			{
				$bgcolor	=	'';
			}
			else
			{
				$bgcolor	=	'#DFEFFF';
			}
		}//
	?>
    <tr>
    	<td><?php echo $serial; ?></td>
        <td><?php echo $barcode; ?></td>
        <td><?php echo $itemdescription; ?></td>
        <td><?php echo $expiry; ?></td>
        <td align="right"><?php echo $totalunits; ?></td>
        <td align="right"><?php echo $remaining; ?></td>
        <td><?php echo $updatetime; ?></td>
        <td><?php echo $returns; ?></td>
        <td><select name="returnstatus[]" id="returnstatus"  style="width:150px;"><option value="c">Confirmed</option><option value="p">Pending</option></select></td>
        <td><input type="text" class="text" name="return[]" id="return<?php echo $i+1;?>" value="<?php //echo $retqty;?>" maxlength="10" /><input type="hidden" name="stockid[]" id="stockid<?php echo $i+1;?>" value="<?php echo $pkstockid;?>" /><input type="hidden" name="totalrecs" id="totalrecs" value="<?php echo $totalrecs;?>" /><input type="hidden" name="urem[]" id="urem" value="<?php echo $remaining;?>" /></td>
    </tr>
    <?php
	}//for
	?>
</table>
<div class="buttons">
<?php if($invc_status==1){?>
<button type="button" class="positive" onclick="submitfrm();">
    <img src="../images/tick.png" alt=""/> 
    Save
</button>
<?php }?>
 <a href="javascript:void(0);" onclick="hidediv('instancediv');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
</a>
</div>
</fieldset>
</form>
<br />
</div>
</div>
<script language="javascript" type="text/javascript">
//document.getElementById('barcode1').focus();
</script>