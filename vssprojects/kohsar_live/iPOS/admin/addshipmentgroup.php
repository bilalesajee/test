<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO, $attributeid;
$id			=	$_REQUEST['id'];
$qstring	=	$_SESSION['qstring'];
if($id!='-1')
{
	$grprow = $AdminDAO->getrows("shipmentgroups","*"," pkshipmentgroupid='$id' AND shipmentgroupsdeleted<>1");
	$shipmentgroupname	=	$grprow[0]['shipmentgroupname'];
	$percentage			=	$grprow[0]['percentage'];
}
?>

<script language="javascript">
function addform(id)
{
	loading('Syetem is Saving The Data....');
	options	=	{	
					url : 'insertshipmentgroup.php',
					type: 'POST',
					success: response
				}
	jQuery('#optionsform').ajaxSubmit(options);
}
	
function response(text)
{
	//alert(text.length);
	if(text=='')
	{
		//alert(text);
		loading('Shipmen Group Saved..');
		adminnotice("Shipment group data has been saved.",0,5000);
		jQuery('#subsection').load('manageshipmentgroups.php?'+'<?php echo $qstring?>');
		hidediv('sgroups');
		//document.getElementById('optiondiv').style.display	=	'none';
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
<form  name="optionsform" id="optionsform" style="width:650px;" action="insertshipmentgroup.php?id=<?php echo $id;?>" onsubmit="addform()">
<fieldset>
<legend>
    Add Shipment Group</legend>
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	<tr class="even">
	  <td>Group Name:</td>
	  <td><input type="text" name="groupname" id="groupname" onkeydown="javascript:if(event.keyCode==13) {return false;}" value="<?php echo $shipmentgroupname;?>"/></td>
	  </tr>
	<tr class="even">
	  <td width="26%">Group Percentage:</td>
	  <td width="74%"><input type="text" name="percentage" id="percentage" onkeydown="javascript:if(event.keyCode==13) {return false;}" value="<?php echo $percentage;?>"/></td>
	  </tr>
	<tr class="even">
	  <td colspan="2"  align="center">
	    <input type="button" value="Save" onclick="addform(<?php echo $id; ?>)"><input name="btnsubmit" type="button" value="Cancel" onclick="hideform()" /></td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="id" value ="<?php echo $id;?>" />
</form>
<?php
if($id=='-1')
{
	$_SESSION['qstring']='';
}
?>
</div>
<script language="javascript" type="text/javascript">
	document.getElementById('groupname').focus();
</script>