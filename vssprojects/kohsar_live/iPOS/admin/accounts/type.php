<?php
session_start();
include_once("../../includes/security/adminsecurity.php");
global $AdminDAO,$V;
$qs		=	$_SESSION['qstring'];
$id 	=	$_REQUEST['id'];
if($id!=-1)
{
	$row 		=	$AdminDAO->getrows('type',"*"," id= '$id'");
	for($i = 0;$i<sizeof($row);$i++)
	{
		$name 			=	$row[0]['name'];
		$description	=  	$row[0]['description'];
		$category_id	=	$row[0]['category_id'];
	}
}
else
{
	$creation_date	=	date("d-M-Y");
}
/********************************Categories***********************************/
$categories	=	$AdminDAO->getrows('accountcategory','*','1');
/****************************************************************************/
?>
<div id="error" class="notice" style="display:none"></div>
<div id="typediv">
<form name="type" id="type" style="width:920px;" class="form">
<fieldset>
<legend>
<?php 
if($id=='-1')
{echo "Add Type";}
else
{echo "Edit Type: $name";}
?>
</legend>
<?php buttons("accounts/inserttype.php?id=$id","type","6","accounts/types.php",'1');?>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td>Type Name:</td><td>
	<input type="text" name="name" value="<?php echo $name;?>" />
</td>
</tr>
<tr>
<td>Type Description:</td><td>
	<textarea name="description"><?php echo $description;?></textarea>
</td>
</tr>
<tr>
<td>Account Category:</td>
<td>
	<select name="categories">
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
<tr >
  <td>Type Creation Date:</td>
  <td>
	<?php
		echo $creation_date;
	?>
  </td>
</tr>
<tr >
  <td colspan="2" align="center">
 <?php buttons("accounts/inserttype.php?id=$id","type","6","accounts/types.php",'0');?>
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