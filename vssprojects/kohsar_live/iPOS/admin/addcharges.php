<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id	=	$_GET['id'];
if($id!='')
{
	$chdata		=	$AdminDAO->getrows("charges","*"," pkchargesid = '$id'");
	$name			=	$chdata[0]['chargesname'];
}
?>

<script language="javascript">
function addcharge()
{
	loading('System is saving data....');
	options	=	{	
					url : 'insertcharges.php?id='+'<?php echo $id?>',
					type: 'POST',
					success: response
				}
	jQuery('#chargeform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Charges data has been saved.',0,5000);
		jQuery('#charges').load('shipmentcharges.php?'+'<?php echo $qs?>');		
		document.getElementById('chargediv').style.display='none';
	}
	else
	{
		adminnotice(text,0,5000);	
	}
}
function hideform()
{
	
	document.getElementById('chargediv').style.display='none';
}
</script>
<div id="loading" class="loading" style="display:none;">
</div>
<div id="error" class="notice" style="display:none"></div>
<div id="chargediv">
<br />
<form enctype="multipart/form-data" name="chargeform" id="chargeform" style="width:920px;" class="form">
<fieldset>
<legend>
<?php 
if($id!='-1')
{
 	echo "Edit";
}
else
{
	echo "Add";
}
?>	
Charges
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onClick="addcharge();">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('chargediv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
</div>  
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	<tr class="odd">
		<td width="11%">Charge Name: </td>
		<td width="89%"><input name="chargename" id="chargename" type="text" value="<?php echo $name;?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"></td>
	</tr>
	<tr class="odd">
	  <td colspan="2"  align="left">
        <div class="buttons">
            <button type="button" class="positive" onClick="addcharge();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('chargediv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type=hidden name="id" value ="<?php echo $id; ?>" />	
</form>
</div>
<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
<script language="javascript">
	focusfield('chargename');
</script>
<?php }//end edit?>