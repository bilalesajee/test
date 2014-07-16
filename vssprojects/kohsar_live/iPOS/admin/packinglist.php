<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['id'];
$id		=	ltrim($id,",");
$ids	=	explode(",",$id);
?>
<script language="javascript">
function updatepack()
{
	options	=	{	
					url : 'updatepack.php',
					type: 'POST',
					success: response
				}
	jQuery('#packfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Packing List has been saved.',0,5000);
		//jQuery('#maindiv').load('manageshiplist.php');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function viewpackdetails(id)
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
<div id="packdiv">
<form  name="packfrm" id="packfrm" style="width:920px;" onSubmit="updatepack(); return false;" class="form" >
    <fieldset>
      <legend>
      <?php if($id=='-1'){echo 'Add';}else{echo 'Update';}?>
      Wish List</legend>
      <div  style="float:right;"><span class="buttons">
        <button type="button" class="positive" onclick="updatepack();"> <img src="../images/tick.png" alt=""/>
        <?php if($shipmentid=='-1'){echo 'Save';}else{echo 'Update';}?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('packdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a></span></div>
<table width="100%">
<tr>
<td height="10" valign="top">
<div class="topimage2" style="height:6px;"><!-- --></div>
<table cellpadding="2" cellspacing="0" width="100%">
<tr>
<th>Barcode</th>
<th>Item</th>
<th>Quantity</th>
<th>Box Number</th>
<th>Items</th>
<th>&nbsp;</th>
</tr>
<?php
for($x=0;$x<sizeof($ids);$x++)
{
	$sid			=	$ids[$x];
	$shiplistdata	=	$AdminDAO->getrows("shiplist,shiplistdetails","barcode,itemdescription,SUM(shiplistdetails.quantity) as qty","pkshiplistid='$sid' AND pkshiplistid=fkshiplistid GROUP BY pkshiplistid");
	$barcode		=	$shiplistdata[0]['barcode'];
	$itemdescription=	$shiplistdata[0]['itemdescription'];
	$quantity		=	$shiplistdata[0]['qty'];
?>
    <tr class="even">
    <td><?php echo $barcode; ?></td>
    <td><?php echo $itemdescription; ?></td>
    <td align="right"><?php echo $quantity; ?></td>
    <td align="right"><input type="text" name="box_<?php echo $sid; ?>" value="<?php echo $items; ?>" class="text" onKeyDown="javascript:if(event.keycode==13){updatepack(); return false;}" /></td>
    <td align="right"><input type="text" name="boxtotal_<?php echo $sid; ?>" value="<?php echo $totalitems; ?>" class="text" onKeyDown="javascript:if(event.keycode==13){updatepack(); return false;}" />
    <input type="hidden" name="quantity_<?php echo $sid; ?>" value="<?php echo $quantity;?>" />
    </td>
    <td><img src="../images/max.GIF" width="12" height="12" onclick="viewpackdetails('<?php echo $sid; ?>')" title="View Packing Details"/></td>
    </tr>
    <tr>
    	<td colspan="6">
        <?php
			$res	=	$AdminDAO->getrows("shiplist,packing,packinglist pl","packingname,reserved","fkshiplistid = '$sid' AND pkpackingid = pl.fkpackingid AND pkshiplistid	= fkshiplistid ");
			?>
			<p>
			<span style="display:none;" id="<?php echo $sid; ?>_detail">
            <table width="100%">
            <tr>
                <th>Box</th>
                <th>Quantity</th>
            </tr>
			<?php
			for($g=0;$g<sizeof($res);$g++)
			{
			?>
	           	<tr>
                    <td><?php echo $res[$g]['packingname'];?></td>
                    <td><?php echo $res[$g]['reserved'];?></td>
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
	  	<td colspan="6">
	    <div class="buttons">
	      <button type="button" class="positive" onclick="updatepack();">
	        <img src="../images/tick.png" alt=""/> 
	        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
	        </button>
	      <a href="javascript:void(0);" onclick="hidediv('packdiv');" class="negative">
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
<input type="hidden" name="ids" value="<?php echo $id;?>" />
</fieldset>
</form>
</div>