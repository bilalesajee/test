<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id	=	$_GET['id'];
$qstring=$_SESSION['qstring'];
if($id!='')
{
	$notes = $AdminDAO->getrows("note","*"," pknoteid='$id'");
	foreach($notes as $note)
	{
		$notename = $note['title'];
		$description = $note['description'];
		$status	 = $note['status'];
	}
}
?>

<script language="javascript">
function addnote(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insertnote.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#noteform').ajaxSubmit(options);
}
	
function response(text)
{
	if(text=='')
	{
		loading('Note Saved...');
		jQuery('#maindiv').load('managenotes.php?'+'<?php echo $qstring?>');
		document.getElementById('notefrmdiv').style.display	=	'none';
		
	}
	else
	{
		document.getElementById('error').innerHTML		=	text;	
		document.getElementById('error').style.display	=	'block';
	}
}
function hideform()
{
	document.getElementById('notefrmdiv').style.display	=	'none';	
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="notefrmdiv" style="display: block;">
<br>
<form id="noteform" style="width: 920px;" action="insertnote.php?id=-1" class="form">
<fieldset>
<legend>
	<?php
    if($id!="-1")
    { echo "Edit Note"." ".$notename;}
    else
    { echo "Add Note";}	
    ?>
</legend>
<div style="float:right">
<?php /*//add comment by ahsan 24/02/2012//if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added if condition?>
<span class="buttons">
    <button type="button" class="positive" onclick="addnote(-1);">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('notefrmdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
</span>
<?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012*///add comment by ahsan 24/02/2012//
	   	 buttons('insertnote.php','noteform','maindiv','managenotes.php',$place=1,$formtype)
	   //end edit?>
</div>
<table cellpadding="0" cellspacing="2" width="100%">
	<tbody>
	<tr>
		<td>Note Title: <?php //add comment by ahsan 24/02/2012//if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?><span class="redstar" title="This field is compulsory">*</span><?php //add comment by ahsan 24/02/2012//}//end edit?></td>
		<td>

		<input name="notename" id="notename" value="<?php echo $notename; ?>" onkeydown="javascript:if(event.keyCode==13) {addnote(); return false;}" type="text"></td>
	</tr>
	<tr>
		<td>Description: </td>
		<td>
		      <textarea name="description" id="description" cols="45" rows="5"><?php echo stripslashes($description);?></textarea>
</td>
	</tr>
	<tr>
	  
	  <td>Status: </td>
	  <td>Active <input name="status" type="radio" value="1" <?php if($status==1) echo "checked"; ?>> Inactive <input name="status" type="radio" value="0" <?php if($status==0) echo "checked"; ?>></td>
	  </tr>
	<tr>
	  <td colspan="2" align="center">
<!--	    <input value="Save" onclick="addnote(-1)" type="button"><input name="btnsubmit" value="Cancel" onclick="hideform()" type="button">-->
        <?php /*//add comment by ahsan 24/02/2012//if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added if condition?>
        <div class="buttons">
            <button type="button" class="positive" onclick="addnote(-1);">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('notefrmdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
 		  <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012*///add comment by ahsan 24/02/2012//
	   	 buttons('insertnote.php','noteform','maindiv','managenotes.php',$place=0,$formtype)
	   ?>
       </td>				
	  </tr>
	</tbody>
</table>
<input type="hidden" name="id" value ="<?php echo $id;?>" />
</fieldset>	
</form>
</div>
<?php //add comment by ahsan 24/02/2012//if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
<script language="javascript">
	focusfield('notename');
</script>
<?php //add comment by ahsan 24/02/2012//}//end edit?>