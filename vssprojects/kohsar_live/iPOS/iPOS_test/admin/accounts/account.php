<?php
session_start();
include_once("../../includes/security/adminsecurity.php");
global $AdminDAO,$V;
$qs		=	$_SESSION['qstring'];
$id 	=	$_REQUEST['id'];
if($id!=-1)
{
	$row 		=	$AdminDAO->getrows("$dbname_detail.account","*"," id= '$id'");
	for($i = 0;$i<sizeof($row);$i++)
	{
		$title 			=	$row[0]['title'];
		$code 			=  	$row[0]['code'];
		$category_id	=	$row[0]['category_id'];
		$creation_date	=	date("d-M-Y",$row[0]['creationdate']);
		$status			=	$row[0]['status'];		
		$type_id		= 	$row[0]['type_id'];
	}
}else{
	$creation_date	=	date("d-M-Y");
}
/********************************Categories***********************************/
$categories	=	$AdminDAO->getrows('accountcategory','*','1');
/****************************************************************************/
?>
<script language="javascript" type="text/javascript">
function gettypes(catid,typeid)
{
	$('#types').load('accounts/gettypes.php?catid='+catid+'&typeid='+typeid);
}
$(document).ready(function() {
 gettypes(<?php echo "'$category_id'";?>,<?php echo "'$type_id'";?>);
});
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="accountdiv">
<form name="account" id="account" style="width:920px;" class="form">
<fieldset>
<legend>
<?php 
if($id=='-1')
{echo "Add Account";}
else
{echo "Edit Account: $title";}
?>
</legend>
<?php buttons("accounts/insertaccount.php?id=$id","account","60","accounts/accounts.php",'1');?>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td>Account Title:</td><td>
	<input type="text" name="title" value="<?php echo $title;?>" />
</td>
</tr>
<tr>
<td>Account Code:</td><td>
	<input type="text" name="code" value="<?php echo $code;?>" />
</td>
</tr>
<tr>
<td>Account Category:</td>
<td>
	<select name="categories" onchange="javascript:gettypes(this.value,'<?php echo $type_id;?>');"  onblur="javascript:gettypes(this.value,'<?php echo $type_id;?>');">
    	<?php
			for($i=0;$i<sizeof($categories);$i++)
			{
		?>
	        <option  <?php if($category_id == $categories[$i]['id']) {echo "SELECTED=SELECTED";} ?>  value="<?php echo $categories[$i]['id'];?>"><?php echo $categories[$i]['name'];?></option>
       	<?php
			}//for
		?>
    </select>
</td>
</tr>
<tr>
<td>Account Type:</td>
<td>
	<span id="types"></span>
</td>
</tr>
<tr >
  <td>Status:</td>
  <td>
  	<input type="radio" name="status" id="status" value="1" <?php if($status !=0){echo 'checked=checked';}?> /> Active
    <input type="radio" name="status" id="status" value="0" <?php if($status ==0){echo 'checked=checked';}?> /> In Active
  
  </td>
</tr>
<tr >
  <td>Account Creation Date:</td>
  <td>
	<?php
		echo $creation_date;
	?>
  </td>
</tr>
<tr >
  <td colspan="2" align="center">
 <?php buttons("accounts/insertaccount.php?id=$id","account","60","accounts/accounts.php",'0');?>
  </td>
  </tr>
</table>
</fieldset>
</form>
</div>
<script language="javascript">
//document.addstore.storename.focus();
loading('Loading Form...');
</script>
<br />