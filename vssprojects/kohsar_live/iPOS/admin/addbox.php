<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$id			=	$_GET['id'];
$ids		=	$_REQUEST['param'];
$ids		=	explode("-",$ids);
$shipmentid	=	$ids[0];
$packingid	=	$ids[1];
$qstring	=	$_SESSION['qstring'];
if($id!='')
{
	$packs = $AdminDAO->getrows("packing","*"," pkpackingid='$id'");
	foreach($packs as $pack)
	{
		$packingname 		= 	$pack['packingname'];
		$selected_packing	=	$pack['fkpackingid'];
	}
}
if($packingid=='')
{
	$selected_pack[]				=	$packs[0]['fkpackingid'];
}
else
{
	$selected_pack[]				=	$packingid;
}
$packarray			= 	$AdminDAO->getrows("packing","*", "fkpackingid=''");
$packing			=	$Component->makeComponent("d","packing",$packarray,"pkpackingid","packingname",1,$selected_pack);
?>

<script language="javascript">
function addpack(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insertbox.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#packform').ajaxSubmit(options);
}
	
function response(text)
{
	
	if(text=='')
	{
		loading('Note Saved...');
		jQuery('#attdiv').load('managebox.php?retain='+'<?php echo $packingid._.$shipmentid?>');
		document.getElementById('packfrmdiv').style.display	=	'none';
		
	}
	else
	{
		document.getElementById('error').innerHTML		=	text;	
		document.getElementById('error').style.display	=	'block';
	}
}
function hideform()
{
	document.getElementById('packfrmdiv').style.display	=	'none';	
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="packfrmdiv" style="display: block;">
<br>
<form id="packform" style="width: 920px;" action="insertbox.php?id=-1" class="form">
<fieldset>
<legend>
	<?php
    if($id!="-1")
    { echo "Edit Box"." ".$packingname;}
    else
    { echo "Add Box";}	
    ?>
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="addpack(-1);">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('sugrid');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
</span>
</div>
<table cellpadding="0" cellspacing="2" width="100%">
	<tbody>
	<tr>
		<td>Box Name: </td>
		<td>

		<input name="packname" id="packname" value="<?php echo $packingname; ?>" onkeydown="javascript:if(event.keyCode==13) {addpack(); return false;}" type="text"></td>
	</tr>
	<tr>
    <tr>
		<td>Packing: </td>
		<td>
        <?php echo $packing; ?>
        </td>
	</tr>
	<tr>
	  <td colspan="2" align="center">
  <!--	    <input value="Save" onclick="addnote(-1)" type="button"><input name="btnsubmit" value="Cancel" onclick="hideform()" type="button">-->
	    <div class="buttons">
	      <button type="button" class="positive" onclick="addpack(-1);">
	        <img src="../images/tick.png" alt=""/> 
	        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
	        </button>
	      <a href="javascript:void(0);" onclick="hidediv('sugrid');" class="negative">
	        <img src="../images/cross.png" alt=""/>
	        Cancel
	        </a>
	      </div>
	    </td>				
	  </tr>
	</tbody>
</table>
<input type="hidden" name="packingid" value ="<?php echo $packingid;?>"/>
<input type="hidden" name="id" value ="<?php echo $id;?>"/>
<input type="hidden" name="shipmentid" value="<?php echo $shipmentid;?>" />
</fieldset>	
</form>
</div>
<?php if($_SESSION['siteconfig']!=3){ //from main, edit by ahsan 14/02/2012?>
<script language="javascript">
	focusfield('packname');
</script>
<?php } //end edit?>