<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$sectionid = $_GET['id'];
if($sectionid!=-1)
{
	$sectiondata	=	$AdminDAO->getrows("section","pksectionid,sectionname,status","pksectionid='$sectionid'");
	$sectionname	=	$sectiondata[0]['sectionname'];
	$status			=	$sectiondata[0]['status'];
}
/****************************************************************************/
?>
<script language="javascript">
function addform()
{
	options	=	{	
					url : 'insertsection.php',
					type: 'POST',
					success: response
				}
	jQuery('#sectionform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Section data has been saved.',0,5000);
		jQuery('#maindiv').load('managesections.php?'+'<?php echo $qs?>');
		hideform();
	}
	else
	{
		adminnotice(text,0,5000);	
	}
}
/*function hideform()
{
	document.getElementById('sectiondiv').style.display='none';
}*/
</script>
<div id="sectiondiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="sectionform" id="sectionform" onSubmit="addform(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
	<?php 
		if($sectionid == '-1')
		{
			print"Adding New Section";
		}
		else
		{
			print"Editing: $sectionname";
		}
	?>
</legend>
<div style="float:right">
<?php /*?><span class="buttons">
    <button type="button" class="positive" onclick="addform();">
        <img src="../images/tick.png" alt=""/> 
        <?php if($sectionid=='-1'){echo 'Save';}else{echo 'Update';}?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('sectiondiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span><?php */?>
   		<?php
	   		 buttons('insertsection.php','sectionform','maindiv','managesections.php',$place=1,$formtype)
	 	?> 
</div>
<table>
	<tbody>
	<tr>
		<td>Section Name:  <span class="redstar" title="This field is compulsory">*</span></td>
		<td><div id="error1" class="error" style="display:none; float:right;"></div>
		<input name="sectionname" id="sectionname" type="text" value="<?php echo $sectionname; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"></td>
	</tr>
	<tr>
		<td>Status:</td>
		<td>
        <select name="status" id="status" style="width:150px;">
        	<option value="1" <?php if($status==1){ echo "selected=\"selected\"";}?>>Active</option>
            <option value="0" <?php if($status==0 && $sectionid!=-1){ echo "selected=\"selected\"";}?>>Inactive</option>
        </select>
        </td>
	</tr>
	<tr>
	  <td colspan="2"  align="left">
	    <?php /*?><div class="buttons">
	      <button type="button" class="positive" onclick="addform();">
	        <img src="../images/tick.png" alt=""/> 
	        <?php if($sectionid=='-1'){echo 'Save';}else{echo 'Update';}?>
	        </button>
	      <a href="javascript:void(0);" onclick="hidediv('sectiondiv');" class="negative">
	        <img src="../images/cross.png" alt=""/>
	        Cancel
	        </a>
	      </div><?php */?>
		  <?php
	   	 buttons('insertsection.php','sectionform','maindiv','managesections.php',$place=0,$formtype)
	 	?>   
	    </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type=hidden name="sectionid" value = <?php echo $sectionid?> />	
</form>
</div><br />
<script language="javascript">

loading('Loading Form...');
</script>
<script language="javascript">
	focusfield('sectionname');
</script>