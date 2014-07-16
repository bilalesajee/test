<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$shipmentid		=	$_GET['id'];
$updateid		=	$_GET['upid'];
$packingdata	=	$AdminDAO->getrows("packing p,packinglist pl,shiplist","*","pl.fkpackingid=pkpackingid AND p.fkshipmentid='$shipmentid' AND pkshiplistid=pl.fkshiplistid ORDER BY pl.fkpackingid");
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];
/*echo "<pre>";
print_r($packingdata);
echo "</pre>";*/
?>
<script language="javascript" type="text/javascript">
</script>
<form name="packinglistitems" id="packinglistitems">
<table width="100%">
   <?php
	for($i=0;$i<sizeof($packingdata);$i++)
	{
		$boxname		=	$packingdata[$i]['packingname'];
		$packinglist	=	$packingdata[$i]['pkpackinglistid'];
		$received		=	$packingdata[$i]['received'];
		$reserveditems	=	$packingdata[$i]['reserved'];
		$remaining		=	$reserveditems-$received;
		$y2="";
		$y1	=	"<select name=\"packlistitem[]\">";	
		for($j=$remaining;$j>0;$j--)
		{
			$val	=	$j."_".$packinglist;
			$y2.=	"<option value=\"$val\" >$j</option>";
		}
		$receivedunits	=	$y1.$y2."</select>";
		if($i>0 && $packingdata[$i]['packingname']==$packingdata[$i-1]['packingname'])
		{
			?>
           	&nbsp;
            <?php
		}
        else
        {
        ?>
       	<tr>
		   	<th width="100%" colspan="10"><?php echo $boxname; ?></th>
        </tr>
        <tr>
            <th width="18%">Item</th>
            <th width="10%">Barcode</th>
			<th width="8%">Expiry</th>
            <th width="8%">Purchase Price</th>
            <th width="8%">Sales Tax</th>
            <th width="8%">Surcharge</th>
            <th width="8%">Charges</th>
            <th width="8%">Charges in <?php echo $defaultcurrency;?></th>
            <th width="8%">Quantity</th>
            <th width="8%">Received</th>
        </tr>
   		<?php
		}
		?>
        <tr>
        <td colspan="10">
            <table width="100%" height="10">
                <tr id="tr_<?php echo $packinglist; ?>_packinglist" onmousedown="highlight('<?php echo $packinglist; ?>','even','row','packinglist')" class="even">
                	<td width="18%"><input onclick="highlight('<?php echo $packinglist; ?>','even','chk','packinglist')" name="checks" id="cb_<?php echo $packinglist; ?>_packinglist" value="<?php echo $packinglist; ?>" type="checkbox">	<?php echo $packingdata[$i]['itemdescription'];?></td>
                	<td width="10%"><?php echo $packingdata[$i]['barcode'];?></td>
                	<td width="8%"><?php echo implode("-",array_reverse(explode("-",$packingdata[$i]['expiry'])));?></td>
                    <td width="8%"><?php echo $packingdata[$i]['purchaseprice'];?></td>
                    <td width="8%"><?php echo $packingdata[$i]['salestax'];?></td>
                    <td width="8%"><?php echo $packingdata[$i]['surcharge'];?></td>
                    <td width="8%"><?php echo $packingdata[$i]['charges'];?></td>
                    <td width="8%"><?php echo $packingdata[$i]['chargesinrs'];?></td>
                    <td width="8%"><?php echo $packingdata[$i]['reserved'];?></td>
                    <td width="8%"><?php echo $packingdata[$i]['received'];?></td>
                </tr>
                <tr>
        			<td colspan="10" align="right"><?php echo $receivedunits; ?><input type="hidden" name="shipmentid" id="shipmentid" value="<?php echo $shipmentid; ?>"  /></td>
        		</tr>
            </table>
        </td>
        </tr>
		<?php
        }
        ?>
        <tr>
        <td colspan="10"><br />
        <span class="buttons">
        <button type="button" class="positive" onclick="editselected();">
            <img src="../images/pencil.png" alt=""/> 
            Edit
        </button>
        <button type="button" class="negative" onclick="deleteselected();">
            <img src="../images/delete.png" alt=""/> 
            Delete
        </button>
        <button type="button" class="positive" onclick="updateselected();">
            <img src="../images/pencil.png" alt=""/> 
            Update
        </button>
        <a href="javascript:void(0);" onclick="hidediv('packingboxes');" class="negative">
            <img src="../images/cross.png" alt=""/>
            Cancel
        </a>
        </span>
		</td>
        </tr>
</table>
</form>