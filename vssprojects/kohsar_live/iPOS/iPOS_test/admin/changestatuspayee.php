<?php
include_once("../includes/security/adminsecurity.php");
$id	=	$_GET['id'];
if($id)
{   
	$res			=	$AdminDAO->getrows("$dbname_detail.account","status","id = '$id'");
	$status			=	$res[0]['status'];
}
?>

<script language="javascript">
function adduser(id)
{
	loading('System is saving data....');
	options	=	{	
					url : 'changestatusp.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#userform').ajaxSubmit(options);
}
	
function response(text)
{
	//alert(text.length);
	if(text=='')
	{
		//alert(text);
		adminnotice("The Account payee status has been changed",0,3000);
		jQuery('#maindiv').load('managepayee.php');
		document.getElementById('subsection').style.display	=	'none';
		
	}
	else
	{
		adminnotice(text,0,3000);
	}
}
function hideform3()
{
	document.getElementById('subsection').style.display='none';
}
</script>
<div id="userdiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<div id="adduserdiv">
<form enctype="multipart/form-data" name="userform" id="userform" style="width:920px;" onSubmit="adduser(); return false;" class="form">
<fieldset>
<legend>Change Status</legend>
<div style="float:right">
   <?php
	   	 buttons('changestatusp.php','userform','maindiv','managepayee.php',$place=1,$formtype)
	   ?>
</div>
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	<tr > <!--Added by jafer on 19-12-2011-->
	  <td>Status:</td>
	  <td>
          <select name="status" id="status">
          <option value="1" <?php if($status==1){echo "selected='selected'";}?>>Active</option>
          <option value="0" <?php if($status==0){echo "selected='selected'";}?>>Inactive</option>
          </select>
      </td>
	  </tr>  <!--Added by jafer on 19-12-2011-->     
	<tr >
	  <td colspan="2"  align="center">
		   <?php
	   	 buttons('changestatusp.php','userform','maindiv','managepayee.php',$place=0,$formtype)
	   ?>
        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>
<input type="hidden" name="id" value ="<?php echo $id;?>" />
</form>
</div>
</div>
<script language="javascript" type="text/javascript">
	document.getElementById('status').focus();
</script>