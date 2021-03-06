<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$id				=	$_REQUEST['id'];
//selectin supplier
$itemname			=	$AdminDAO->getrows("barcode","itemdescription","pkbarcodeid='$id'");
$itemdescription	=	$itemname[0]['itemdescription'];
//selecting items
$query			=	"SELECT 
						pkstockid,
						quantity,
						unitsremaining,
						FROM_UNIXTIME(expiry,'%d-%m-%y') expiry,
						FROM_UNIXTIME(updatetime,'%d-%m-%y') updatetime,
						itemdescription,
						barcode,
						fksupplierinvid,
						fkbarcodeid,
						srcstoreid,
						fksupplierid
						
				FROM
						$dbname_detail.stock,barcode
				WHERE 	
						fkbarcodeid			=	pkbarcodeid AND
						fkbarcodeid			=	'$id'
						
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
$returns		=	$d1.$d2."</select>";
// selecting previous returns

?>
<script language="javascript" type="text/javascript">
function submitfrm(thj)
{
	loading('System is Saving The Data....');
	options	=	{	
					url : 'stockprocessreturnsact.php?check_button='+thj,
					type: 'POST',
					success: response
				}
	jQuery('#adstockfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Records updated successfully.',0,5000);
		jQuery('#maindiv').load('managestocks.php');
		hidediv('stockprocessreturns');
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
<div id="stockprocessreturns">
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
<legend>Process Returns for < <?php echo $itemdescription;?> >
</legend><div  style="float:right;">
<span class="buttons">
<button type="button" class="positive" onclick="submitfrm(1);">
    <img src="../images/tick.png" alt=""/> 
    Return 
</button><!--&nbsp;
<button type="button" class="positive" onclick="submitfrm(2);">
    <img src="../images/tick.png" alt=""/> 
    Return To Store
</button>-->
 <a href="javascript:void(0);" onclick="hidediv('stockprocessreturns');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
</a>
</span>
</div>
<br /><br />
<table>
    <tr>
        <th>Serial</th>
        <th>Barcode</th>
        <th>Item</th>
        <th>Expiry</th>
        <th>Units</th>
        <th>Remaining</th>
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
		$fksuplierinvid		=	$stocksdata[$i]['fksupplierinvid'];
		$fkbarid			=	$stocksdata[$i]['fkbarcodeid'];
		$srcid			=	$stocksdata[$i]['srcstoreid'];
		$sup_id			=	$stocksdata[$i]['fksupplierid'];
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
	if($remaining>0)
	{
	?>
    <tr>
    	<td><?php echo $serial; ?></td>
        <td><?php echo $barcode; ?></td>
        <td><?php echo $itemdescription; ?></td>
        <td><?php echo $expiry; ?></td>
        <td align="right"><?php echo $totalunits; ?></td>
        <td align="center"><?php echo $remaining; ?></td>
        <td><?php echo $updatetime; ?></td>
        <td><?php echo $returns; ?></td>
        <td><select name="returnstatus[]" id="returnstatus"  style="width:150px;"><option value="c">Confirmed</option><option value="p">Pending</option></select></td>
        <td><input type="text" class="text" name="return[]" id="return<?php echo $i+1;?>" value="<?php //echo $retqty;?>" maxlength="10" /><input type="hidden" name="stockid[]" id="stockid<?php echo $i+1;?>" value="<?php echo $pkstockid;?>" /><input type="hidden" name="totalrecs" id="totalrecs" value="<?php echo $totalrecs;?>" /><input type="hidden" name="fkinvid[]" id="fkinvid" value="<?php echo $fksuplierinvid;?>" /><input type="hidden" name="fkbarid[]" id="fkbarid" value="<?php echo $fkbarid;?>" /><input type="hidden" name="srcid[]" id="srcid" value="<?php echo $srcid;?>" /><input type="hidden" name="supid[]" id="supid" value="<?php echo $sup_id;?>" /> </td>
    </tr>
    <?php
	}//for
	}//if?>
</table>
<div class="buttons">
<button type="button" class="positive" onclick="submitfrm(1);">
    <img src="../images/tick.png" alt=""/> 
    Return 
</button><!--&nbsp;<button type="button" class="positive" onclick="submitfrm(2);">
    <img src="../images/tick.png" alt=""/> 
    Return To Store
</button>-->

 <a href="javascript:void(0);" onclick="hidediv('stockprocessreturns');" class="negative">
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