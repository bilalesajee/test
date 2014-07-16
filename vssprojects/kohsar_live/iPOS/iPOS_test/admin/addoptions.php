<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO, $attributeid;
$param = $_REQUEST['param'];
if($param=="attid")
{
	$attributeid		=	$_REQUEST['id'];
	//echo "here 1";
}
else
{
	$attributeid	=	$_REQUEST['attid'];
	$id				= 	$_REQUEST['id'];
	//echo "here 2";
}
//echo $attributeid."......is the attributeid";
$qstring		=	$_SESSION['qstring'];
if($id!='')
{
	$options		=	$AdminDAO->getrows("attributeoption","*"," pkattributeoptionid = '$id'");
	$optionname		=	$options[0]['attributeoptionname'];
	$attributeid	=	$options[0]['fkattributeid'];
}
?>

<script language="javascript">
function addform(id)
{
	loading('Syetem is Saving The Data....');
	options	=	{	
					url : 'insertoption.php?id='+'<?php echo $id;?>',
					type: 'POST',
					success: response
				}
	jQuery('#optionsform').ajaxSubmit(options);
}
	
function response(text)
{
	if(text=='')
	{
		loading('Option Saved..');
		adminnotice('Option data has been saved.',0,5000);
		jQuery('#susection').load('loadattributes.php?id='+'<?php echo $attributeid;?>');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function hideform(optiondiv)
{
	document.getElementById('optiondiv').style.display='none';
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="optiondiv" style="display:block">
<br />
<form  name="optionsform" id="optionsform" style="width:920px;" action="insertattribute.php?id=<?php echo $id;?>" onsubmit="addform()" class="form">
<fieldset>
<legend>
    Add Options</legend>
<div style="float:right">
    <span class="buttons">
    <button type="button" class="positive" onclick="addform();">
        <img src="../images/tick.png" alt=""/> 
        Save
    </button>
     <a href="javascript:void(0);" onclick="hidediv('optiondiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
</div>         
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	<tr>
	  <td>Add Option: <?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?><span class="redstar" title="This field is compulsory">*</span><?php }//end edit?></td>
	  <td><input type="text" name="optionname" id="optionname" value="<?php echo $optionname ?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
	  </tr>
	<tr>
	  <td colspan="2"  align="left">
        <div class="buttons">
            <button type="button" class="positive" onclick="addform();">
                <img src="../images/tick.png" alt=""/> 
                Save
            </button>
             <a href="javascript:void(0);" onclick="hidediv('optiondiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="attid" value ="<?php echo $attributeid;?>" />
<input type="hidden" name="id" value ="<?php echo $id;?>" />
</form>
<?php
if($id=='-1')
{
	$_SESSION['qstring']='';
}
?>
</div><br />
<script language="javascript" type="text/javascript">
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012?>
	document.getElementById('optionname').focus();
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
	focusfield('optionname');
<?php }//end edit?>
</script>