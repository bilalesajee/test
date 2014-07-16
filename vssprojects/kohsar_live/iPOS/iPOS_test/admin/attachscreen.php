<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$sectionid = $_GET['id'];
// selecting screen data
$screendata	=	$AdminDAO->getrows("screen","pkscreenid","fksectionid='$sectionid'");
foreach($screendata as $sectiondata)
{
	$selected_screens[]	=	$sectiondata['pkscreenid'];
}
/*echo "<br><bR><BR><pre>";
print_r($selected_screens);
echo "</pre>";*/
// selecting screens
$screenarray		= 	$AdminDAO->getrows("screen","pkscreenid,screenname", "fksectionid=0 OR fksectionid='$sectionid' ORDER BY screenname ASC");
$screensel		=	"<select name=\"screen[]\" id=\"screen\" style=\"width:250px;\" multiple=multiple size=20>";
for($i=0;$i<sizeof($screenarray);$i++)
{
	$screenname		=	$screenarray[$i]['screenname'];
	$pkscreenid		=	$screenarray[$i]['pkscreenid'];
	$select			=	"";
	if(@in_array($pkscreenid,$selected_screens))
	{
		$select = "selected=\"selected\"";
	}
	$screensel2	.=	"<option value=\"$pkscreenid\" $select>$screenname</option>";
}
$screens			=	$screensel.$screensel2."</select>";
// end screens
if($sectionid)
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
					url : 'updatescreen.php',
					type: 'POST',
					success: response
				}
	jQuery('#sectionform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Screens updated successfully.',0,5000);
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
			print"Working on: $sectionname";
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
	   		 buttons('updatescreen.php','sectionform','maindiv','managesections.php',$place=1,$formtype)
	 	?> 
</div>
<table>
	<tbody>
	<tr>
		<td valign="top"><strong>Select Screens:  <span class="redstar" title="This field is compulsory">*</span></strong> </td>
		<td><div id="error1" class="error" style="display:none; float:right;"></div>
		<?php echo $screens;?></td>
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
	   		 buttons('updatescreen.php','sectionform','maindiv','managesections.php',$place=0,$formtype)
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
document.sectionform.screen.focus();
loading('Loading Form...');
</script>