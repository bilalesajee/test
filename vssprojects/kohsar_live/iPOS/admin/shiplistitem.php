<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['id'];
$id		=	ltrim($id,",");
$ids	=	explode(",",$id);

// selecting shipments
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	$shipments		=	$AdminDAO->getrows("shipment","pkshipmentid,shipmentname","isopened='o'");
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	$shipments		=	$AdminDAO->getrows("shipment","pkshipmentid,shipmentname","fkstatusid IN (1,3)");
}//end edit
$shipmentsel	=	"<select name=\"shipment\" id=\"shipment\" style=\"width:100px;\" ><option value=\"\">Select Shipment</option>";
for($i=0;$i<sizeof($shipments);$i++)
{
	$shipmentname	=	$shipments[$i]['shipmentname'];
	$shipmentid		=	$shipments[$i]['pkshipmentid'];
	$shipmentsel2	.=	"<option value=\"$shipmentid\">$shipmentname</option>";
}
$shipment			=	$shipmentsel.$shipmentsel2."</select>";
// end shipments
?>
<script language="javascript">
function updatelist()
{
	options	=	{	
					url : 'shiplistact.php',
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
		jQuery('#maindiv').load('managecountrylist.php');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>
<div id="xshipdiv">
<form  name="shiplistfrm" id="shiplistfrm" style="width:920px;" onSubmit="updatelist(); return false;" class="form" >
    <fieldset>
      <legend>
      	Ship Item
      </legend>
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
        	<th>Select Shipment</th>
        </tr>
        <tr>
        <td class="even"><?php echo $shipment; ?><?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>&nbsp;<input type="button" value="New Shipment" onclick="javascript:showpage(0,'','addshipment.php','subsection','maindiv','1')"><?php }//end edit?></td>
        </tr>
        </table>
    </td>
  	</tr>
</table>
<table width="100%">
    <tr>
    <td height="10" valign="top">
    <div class="topimage2" style="height:6px;"><!-- --></div>
        <table cellpadding="2" cellspacing="0" width="100%">
            <tr>
                <th style="text-align:left;">Barcode</th>
                <th style="text-align:left;">Item</th>
            </tr>
            <?php
            for($x=0;$x<sizeof($ids);$x++)
            {
                $sid			=	$ids[$x];
                $shiplistdata	=	$AdminDAO->getrows("shiplist","*","pkshiplistid='$sid'");
                $barcode		=	$shiplistdata[0]['barcode'];
                $itemdescription=	$shiplistdata[0]['itemdescription'];
            ?>
            <tr class="<?php if($x%2 == 0) {echo "even";} else {echo "odd";}?>">
                <td><?php echo $barcode; ?></td>
                <td><?php echo $itemdescription; ?></td>
            </tr>
            <?php
            }
            ?>
        </table>
    </td>
    </tr>
    <tr>
        <td>
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
<input type="hidden" name="ids" value="<?php echo $id;?>" />
</fieldset>
</form>
</div>