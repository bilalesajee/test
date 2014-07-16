<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$attributeid	=	$_GET['id'];
$productid		=	$_GET['param'];
$qstring=$_SESSION['qstring'];
$attrow	=	$AdminDAO->getrows("attribute","*"," pkattributeid='$attributeid'");
$attributename	=	$attrow[0]['attributename'];
if($productid!='-1')
{
	$productattrib	=	$AdminDAO->getrows('productattribute','*',"fkproductid = '$productid' AND fkattributeid = '$attributeid'");
	$attributetype	=	$productattrib[0]['attributetype'];
}
?>
<script language="javascript">
function addform(id)
{
	options	=	{	
					url : 'insertattribute.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#attfrm').ajaxSubmit(options);
}

function addattform()//from main, edit by ahsan 14/02/2012
{
	options	=	{	
					url : 'insertattribute.php',
					type: 'POST',
					success: response
				}
	jQuery('#attfrm').ajaxSubmit(options);
}//end edit
	
function response(text)
{
	//alert(text.length);
	if(text=='')
	{
		//alert(text);
		loading('Attribute Saved..');
		document.getElementById('productdiv').style.display	=	'none';
		adminnotice('Attribute data has been saved.',0,5000);
		<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
			jQuery('#sugrid').load('manageattributes.php?'+'<?php echo $qstring?>&id=<?php echo $productid;?>');
		<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
			jQuery('#sugrid').load('manageattributes.php?id='+<?php echo $productid;?>);
		<?php } //end edit?>
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function hideform3()
{
	document.getElementById('susection').style.display='none';
}
</script>
<div id="productdiv" style="display:block">
<br />
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added if condition ?>
	<form  name="attfrm" id="attfrm" style="width:920px;" action="insertattribute.php?id=<?php echo $id;?>" class="form">
<?php }elseif($_SESSION['siteconfig']!=3){ //from main, edit by ahsan 14/02/2012?>
	<form  name="attfrm" id="attfrm" style="width:920px;" action="insertattribute.php" class="form">
<?php } //end edit?>
<fieldset>
<legend>
    Add Attribute</legend>
<div style="float:right">
    <span class="buttons">
        <?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
            <button type="button" class="positive" onclick="addform('<?php echo $attributeid; ?>');">
        <?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
            <button type="button" class="positive" onclick="addattform();">
        <?php } //end edit?>
                <img src="../images/tick.png" alt=""/> 
                <?php if($attributeid=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('productdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </span>
</div>          
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	<tr >
	  <td>Attribute Name:<?php if($_SESSION['siteconfig']!=3){ //from main, edit by ahsan 14/02/2012?><span class="redstar" title="This field is compulsory">*</span><?php } //end edit?></td>
	  <td>
	    <table cellpadding="0" cellspacing="0" width="100%">
	      <tr>
	        <td><?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>Attribute Name<?php } ?></td>
	        </tr>
	     
	      <tr>
	        <td><input type="text" name="attributename" id="attributename" value="<?php echo $attributename;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}"/>
            <?php
			if($productid!='-1')
			{
				?>
            Single<input type="radio" name="attribute" value="s" <?php if($attributetype=='s'){ echo "checked=checked";} if($attributetype==''){print"checked=checked";}?> />
            Multiple<input type="radio" name="attribute" value="m" <?php if($attributetype=='m') {echo "checked=checked";}?> />
            <?php
			}
			?>
            </td>
	        </tr>          
	      </table>
	    </td>
	  </tr>
	<tr >
	  <td colspan="2"  align="left">
        <div class="buttons">
          <?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
            <button type="button" class="positive" onclick="addform('<?php echo $attributeid; ?>');">
          <?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
            <button type="button" class="positive" onclick="addattform();">
          <?php } //end edit?>
                <img src="../images/tick.png" alt=""/> 
                <?php if($attributeid=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('productdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="id" value ="<?php echo $attributeid;?>" />
<input type="hidden" name="productid" value="<?Php echo $productid;?>" id="productid"/>
</form>
<?php
if($id=='-1')
{
	$_SESSION['qstring']='';
}
?>
</div>
<script language="javascript" type="text/javascript">
	document.getElementById('attributename').focus();
</script>