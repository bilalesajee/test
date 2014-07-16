<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$id = $_GET['id'];

$qs	=	$_SESSION['qstring'];
if($id=="")
{
	$id="-1";
}
else if($id !="-1")
{
	$reason = $AdminDAO->getrows('discountreason','*',"`pkreasonid`='$id' AND discountreasondeleted <> '1'");
	$name = $reason[0]['reasontitle'];
		$desc = $reason[0]['reasondiscription'];
			$status = $reason[0]['reasonsatus'];
}
?>
<script language="javascript">
function addform()
{
	loading('System is Saving The Data....');
	options	=	{	
					url : 'insertreason.php',
					type: 'POST',
					success: response
				}
	jQuery('#curform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Discount Reason data has been saved.',0,5000);
		jQuery('#maindiv').load('managereasons.php?'+'<?php echo $qs?>');		
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
			print"Adding New Discount Reason";
		}
		else
		{
			print"Editing: $name";
		}
	?>
</legend>
<div style="float:right">
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, adeed if condition?>
<span class="buttons">
    <button type="button" class="positive" onclick="addform();">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('curdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
 <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012
	   	 buttons('insertreason.php','curform','maindiv','managereasons.php',$place=1,$formtype)
	 ?>    
</div>          
<table>
	<tbody>
	<tr>
		<td>Reason Title: <?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?><span class="redstar" title="This field is compulsory">*</span><?php }//end edit?></td>
		<td colspan="2">
		<input name="name" id="name" type="text" value="<?php echo $name; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"></td>
	</tr>
	<tr>
		<td>Description: </td>
		<td colspan="2"><textarea name="desc"><?php echo $desc ?></textarea></td>
	</tr>
	<tr>
		<td>Status: </td>				
		<td valign="top">
        <select name="status">
        	<option value="a" <?php if($status == 'a') echo "selected=selected"; ?> >Active</option>
           	<option value="i" <?php if($status == 'i') echo "selected=selected"; ?>>Inactive</option>
        </select>
        </td>
	</tr>
	<tr>
		<td colspan="3"  align="left">
        <?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
			<div class="buttons">
            <button type="button" class="positive" onclick="addform();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('curdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>		
		  <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012
	   		 buttons('insertreason.php','curform','maindiv','managereasons.php',$place=0,$formtype)
		 //end edit?>   	
          
        </td>				
	</tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="id" value = <?php echo $id?> />	
</form>
</div><br />
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
<script language="javascript">
document.curfrm.brand.focus();
loading('Loading Form...');
</script>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
<script language="javascript">

loading('Loading Form...');
</script>
<script language="javascript">
	focusfield('name');
</script>
<?php }//end edit?>