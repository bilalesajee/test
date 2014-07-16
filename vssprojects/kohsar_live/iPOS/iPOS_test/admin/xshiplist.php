<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['id'];
$id		=	ltrim($id,",");
$ids	=	explode(",",$id);
$chargesarray	=	$AdminDAO->getrows("charges","*","chargesdeleted<>1");
foreach($chargesarray as $charr)
{
	$charge[]	=	$charr['pkchargesid'];;
}
$chargestring	=	@implode(",",$charge);
// calculating colspan
$chargesize		=	sizeof($charge);
$colspan		=	$chargesize+10;
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
					url : 'updateslist.php',
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
		jQuery('#maindiv').load('manageshiplist.php');
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
<table cellpadding="2" cellspacing="0" width="100%">
<tr>
<th>Barcode</th>
<th>Item</th>
<th>Weight</th>
<th>Price</th>
<th>Quantity</th>
<th>Box Number</th>
<th>Items</th>
<th>Expiry</th>
<th>Supplier</th>
<?php
for($i=0;$i<sizeof($chargesarray);$i++)
{
?>
<th><?php echo $chargesarray[$i]['chargesname'];?></th>
<?php
}
?>
<th>&nbsp;</th>
</tr>
<?php
for($x=0;$x<sizeof($ids);$x++)
{
	$sid			=	$ids[$x];
	$shiplistdata	=	$AdminDAO->getrows("shiplist","*","pkshiplistid='$sid'");
	$barcode		=	$shiplistdata[0]['barcode'];
	$itemdescription=	$shiplistdata[0]['itemdescription'];
	$quantity		=	$shiplistdata[0]['quantity'];
	// selecting suppliers
	$suppliersarray		= 	$AdminDAO->getrows("shiplistsupplier,supplier","*", "fksupplierid=pksupplierid AND fkshiplistid='$sid'");
	$suppliersel2		=	"";
	$suppliersel		=	"<select name=\"supplier_$sid\" id=\"supplier_$sid\" style=\"width:100px;\" ><option value=\"\">Select Supplier</option>";
	for($i=0;$i<sizeof($suppliersarray);$i++)
	{
		$suppliername	=	$suppliersarray[$i]['companyname'];
		$supplierid		=	$suppliersarray[$i]['pksupplierid'];
		$suppliersel2	.=	"<option value=\"$supplierid\">$suppliername</option>";
	}
	$suppliers			=	$suppliersel.$suppliersel2."</select>";
	// end suppliers
	//items in box
	
	//end items in box
?>
    <tr class="even">
    <td><?php echo $barcode; ?></td>
    <td><?php echo $itemdescription; ?></td>
    <td><input type="text" name="weight_<?php echo $sid; ?>" value="<?php echo $weight; ?>" class="text" size="5" onKeyDown="javascript:if(event.keycode==13){updatelist(); return false;}" /></td>
    <td><input type="text" name="price_<?php echo $sid; ?>" value="<?php echo $price; ?>" class="text" size="5" onKeyDown="javascript:if(event.keycode==13){updatelist(); return false;}" /></td>
    <td><input type="text" name="quantity_<?php echo $sid; ?>" value="<?php echo $quantity; ?>" class="text" size="5" onKeyDown="javascript:if(event.keycode==13){updatelist(); return false;}" /></td>
    <td align="right"><input type="text" name="box_<?php echo $sid; ?>" value="<?php echo $items; ?>" class="text" size="5" onKeyDown="javascript:if(event.keycode==13){updatepack(); return false;}" /></td>
    <td align="right"><input type="text" name="boxtotal_<?php echo $sid; ?>" value="<?php echo $totalitems; ?>" class="text" size="5" onKeyDown="javascript:if(event.keycode==13){updatepack(); return false;}" />
    <input type="hidden" name="quantity_<?php echo $sid; ?>" value="<?php echo $quantity;?>" />
    </td>
    <td><input type="text" id="expiry_<?php echo $sid; ?>" name="expiry_<?php echo $sid; ?>" value="<?php echo $expiry; ?>" class="text" size="8" onKeyDown="javascript:if(event.keycode==13){updatelist(); return false;}" /></td>
    <td><?php echo $suppliers; ?></td>
    <?php
    for($i=0;$i<sizeof($chargesarray);$i++)
    {
		$chargeid	=	$chargesarray[$i]['pkchargesid'];
    ?>
    <td><input type="text" name="charges_<?php echo $sid."_".$chargeid; ?>" value="" class="text" size="5" onKeyDown="javascript:if(event.keycode==13){updatelist(); return false;}" /></td>
    <?php
    }
    ?>
    <td><img src="../images/max.GIF" width="12" height="12" onclick="viewlistdetails('<?php echo $sid; ?>')" title="View Item Details"/></td>
    </tr>
    <tr>
    	<td colspan="<?php echo $colspan;?>">
        <?php
			$res	=	$AdminDAO->getrows("shiplist,shiplistdetails sd LEFT JOIN supplier ON (sd.fksupplierid=pksupplierid)","sd.pkshiplistdetailsid,sd.weight,sd.price,sd.quantity,sd.expiry,companyname as supplier","fkshiplistid	= '$sid' AND	pkshiplistid	=	fkshiplistid GROUP BY pkshiplistdetailsid");
			$res2	=	$AdminDAO->getrows("shiplist,packing,packinglist pl","packingname,reserved","fkshiplistid = '$sid' AND pkpackingid = pl.fkpackingid AND pkshiplistid	= fkshiplistid ");
			?>
			<p>
			<span style="display:none;" id="<?php echo $sid; ?>_detail">
            <table width="100%">
            <tr>
                <th>Weight</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Expiry</th>
                <th>Supplier</th>
            </tr>
			<?php
			for($g=0;$g<sizeof($res);$g++)
			{
			?>
	           	<tr>
                    <td align="right"><?php echo $res[$g]['weight'];?></td>
                    <td align="right"><?php echo $res[$g]['price'];?></td>
                    <td align="right"><?php echo $res[$g]['quantity'];?></td>
                    <td align="center"><?php echo implode("-",array_reverse(explode("-",$res[$g]['expiry'])));?></td>
                    <td align="center"><?php echo $res[$g]['supplier'];?></td>
                </tr>
            <?php
			}
			?>
            </table>
            <table width="100%">
            <tr>
            	<th>Box</th>
                <th>Quantity</th>
            </tr>
			<?php
			for($j=0;$j<sizeof($res2);$j++)
			{
			?>
	           	<tr>
                    <td><?php echo $res2[$j]['packingname'];?></td>
                    <td><?php echo $res2[$j]['reserved'];?></td>
                </tr>
            <?php
			}
			?>
            </table>
			</span>
			</p>
        </td>
    </tr>
<?php
}
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
<input type="hidden" name="chargestr" value="<?php echo $chargestring;?>" />
<input type="hidden" name="ids" value="<?php echo $id;?>" />
</fieldset>
</form>
</div>