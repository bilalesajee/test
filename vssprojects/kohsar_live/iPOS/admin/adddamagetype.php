<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$did = $_GET['id'];

$qs	=	$_SESSION['qstring'];
if($did=="")
{
	$did="-1";
}
else if($did !="-1")
{
	$damages = $AdminDAO->getrows('damagetype','*',"`pkdamagetypeid`='$did' AND damagetypedeleted <> '1'");
	$damagetype = $damages[0]['damagetype'];
}
?>
<script language="javascript">
function addform()
{
	loading('System is Saving The Data....');
	options	=	{	
					url : 'insertdamagetype.php',
					type: 'POST',
					success: response
				}
	jQuery('#curform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Damage Type data has been saved.',0,5000);
		jQuery('#maindiv').load('managedamagetypes.php?'+'<?php echo $qs?>');		
	}
	else
	{
		adminnotice(text,0,5000);	
	}
}
function hideform()
{
	
	document.getElementById('curdiv').style.display='none';
}
</script>
<div id="curdiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="curform" id="curform" onSubmit="addform(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
	<?php 
		if($currencyid == '-1')
		{
			print"Adding New Damage Type";
		}
		else
		{
			print"Editing: $damagetype";
		}
	?>
</legend>
<div style="float:right">
<?php /*//add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
    <span class="buttons">
        <button type="button" class="positive" onclick="addform();">
            <img src="../images/tick.png" alt=""/> 
            <?php if($did=='-1'){echo 'Save';}else{echo 'Update';}?>
        </button>
         <a href="javascript:void(0);" onclick="hidediv('curdiv');" class="negative">
            <img src="../images/cross.png" alt=""/>
            Cancel
        </a>
      </span>
<?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012*///add comment by ahsan 24/02/2012// 
	   	 buttons('insertdamagetype.php','curform','maindiv','managedamagetypes.php',$place=1,$formtype)
//end edit?>
</div>          
<table>
	<tbody>
	<tr>
		<td>Damage Type: <span class="redstar" title="This field is compulsory">*</span></td>
		<td>
		<input name="name" id="name" type="text" value="<?php echo $damagetype; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"></td>
	</tr>
	<tr>
	  <td colspan="2"  align="left">
      <?php /*//add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012?>
		   <div class="buttons">
            <button type="button" class="positive" onclick="addform();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($did=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('curdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>        
		   <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012*///add comment by ahsan 24/02/2012// 
	   	 		buttons('insertdamagetype.php','curform','maindiv','managedamagetypes.php',$place=0,$formtype)
	 		//end edit?>       
	    </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="did" value = <?php echo $did?> />	
</form>
</div><br />
<script language="javascript">
document.curfrm.brand.focus();
loading('Loading Form...');
</script>
<?php //add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
<script language="javascript">
	focusfield('name');
</script>
<?php //add comment by ahsan 24/02/2012// } //end edit?>