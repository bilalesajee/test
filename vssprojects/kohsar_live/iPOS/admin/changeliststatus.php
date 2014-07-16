<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['id'];
$id		=	ltrim($id,",");
$ids	=	explode(",",$id);

//start selection
$shiplist		=	$AdminDAO->getrows("shiplist","*","pkshiplistid IN ($id)");
//end selection

// statuses
$statuses		=	$AdminDAO->getrows("orderstatuses","pkstatusid,statusname","1");
$statussel		=	"<select name=\"status\" id=\"status\" style=\"width:100px;\"><option value=\"\">All</option>";
for($i=0;$i<sizeof($statuses);$i++)
{
	$statusname	=	$statuses[$i]['statusname'];
	$statusid	=	$statuses[$i]['pkstatusid'];
	$select		=	"";
	if($statusid == $liststatus)
	{
		$select = "selected=\"selected\"";
	}
	$statussel2	.=	"<option value=\"$statusid\" $select>$statusname</option>";
}
$statuses			=	$statussel.$statussel2."</select>";
// end statuses

?>
<script language="javascript" type="text/javascript">
function movelist()
{
	//loading('System is saving data....');
	options	=	{	
					url : 'liststatusact.php',
					type: 'POST',
					success: response
				}
	jQuery('#movelistform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Status changed successfully.',0,5000);
		jQuery('#maindiv').load('manageshiplist.php');
	}
	else
	{
		adminnotice(text,0,5000);
		jQuery('#maindiv').load('manageshiplist.php');
	}
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="movelistdiv" style="display: block;">
<form id="movelistform" style="width: 920px;" action="moveshiplist.php" class="form">
<fieldset>
<legend>
	Move Items
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="movelist();">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('movelistdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
</span>
</div>
<table width="100%">
<tr>
 <td>Status</td>
 <td colspan="2"><?php echo $statuses;?></td>
</tr>
<tr>
    <th>Barcode</th>
    <th>Item</th>
</tr>
<?php
for($i=0;$i<sizeof($shiplist);$i++)
{
?>
<tr>
    <td><?php echo $shiplist[$i]['barcode'];?></td>
    <td><?php echo $shiplist[$i]['itemdescription'];?></td>
</tr>
<?php
}
?>
<tr>
    <td colspan="2" align="center">
    <div class="buttons">
      <button type="button" class="positive" onclick="movelist();">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
        </button>
      <a href="javascript:void(0);" onclick="hidediv('movelistdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
        </a>
      </div>
    </td>				
</tr>
</table>
<input type="hidden" name="id" value ="<?php echo $id;?>"/>
</fieldset>	
</form>
<br />
<br />
</div>