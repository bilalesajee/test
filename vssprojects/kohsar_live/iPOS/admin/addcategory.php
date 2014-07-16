<?php
include("../includes/security/adminsecurity.php");
require_once("../OpenCrypt/ajax_tree.php");
global $AdminDAO;
$id	=	$_GET['id'];
$categories 	= 	$AdminDAO->getrows("category","*"," 1");
$menu			=	array();
if($id!='')
{
	$catdata		=	$AdminDAO->getrows("category","*"," pkcategoryid = '$id'");
	$name			=	$catdata[0]['name'];
	$description	=	$catdata[0]['description'];
	$categoryimage	=	$catdata[0]['categoryimage'];	
	$subcatdata		=	$AdminDAO->getrows("subcategory","*"," fkcategoryid = '$id'");
	foreach($subcatdata as $catarr)
	{
		$cat[]		=	$catarr['fkparentid'];
	}
	$menu[0]	=	$id;
	getchildern($id);
}
?>
<script src="../includes/js/ajaxfileupload.js" type="text/javascript"></script>
<script language="javascript">
function addcat()
{
	loading('System is saving data....');
	options	=	{	
					url : 'insertcategory.php?id='+'<?php echo $id?>',
					type: 'POST',
					success: response
				}
	jQuery('#catform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Category data has been saved.',0,5000);
		jQuery('#maindiv').html('');
		jQuery('#maindiv').load('managecategories.php?'+'<?php echo $qs?>');		
	}
	else
	{
		adminnotice(text,0,5000);	
	}
	
	/*document.getElementById('error').innerHTML		=	text;	
	document.getElementById('error').style.display	=	'block';	
	if(text=='')
	{
		jQuery('#maindiv').load('addcategory.php?'+'<?php// echo $qs?>');		
	}
	*/
	//hideform();
}
function hideform()
{
	
	document.getElementById('catdiv').style.display='none';
}
function ajaxFileUpload()
{
		$("#loading")
		.ajaxStart(function(){
			$(this).show();
		})
		.ajaxComplete(function(){
			$(this).hide();
			var f	=	document.getElementById('imageField').value.replace(/\\/g, "\\\\");
			document.getElementById('prvimage').src="../categoryimage/"+f;
		});

		$.ajaxFileUpload
		(
			{
				url:'catimageupload.php',
				secureuri:false,
				fileElementId:'imageField',
				dataType: 'json',
				success: function (data, status)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							//alert(data.error);
						}else
						{
							//alert(data.msg);
						}
					}
				},
				error: function (data, status, e)
				{
					//alert(e);
				}
			}
		)
		return false;
}
function isValidImage()
{
	var imagename	=	document.getElementById('imageField').value.replace(/\\/g, "\\\\");
	var oldimage	=	document.getElementById('oldimage').value;
	if(oldimage!='')
	{
		if(!confirm("There an image exists with this product. This will be replaced with new one! are you sure"))
		{
			return false;
		}
	}
	imagefile_value = imagename;
	var checkimg = imagefile_value.toLowerCase();
	if (!checkimg.match(/(\.jpg|\.gif|\.png|\.JPG|\.GIF|\.PNG|\.jpeg|\.JPEG)$/))
	{
		alert("Please upload a valid image i.e .jpg, .gif, .png, .jpeg");
		return false;
	}else
	{
		ajaxFileUpload();
	}
}
</script>
<div id="loading" class="loading" style="display:none;">
</div>
<div id="catdiv">
<br />
<form enctype="multipart/form-data" name="catform" id="catform" style="width:920px;" class="form">
<fieldset>
<legend>
    <?php 
	if($id =='-1')
	{
    	echo"Add Category";
	}
	else
	{
		echo "Edit Category: $name";	
	}
	?>
</legend>
<div style="float:right">
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
    <span class="buttons">
                <button type="button" class="positive" onclick="addcat();">
                    <img src="../images/tick.png" alt=""/> 
                    <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
                </button>
                 <a href="javascript:void(0);" onclick="hidediv('catdiv');" class="negative">
                    <img src="../images/cross.png" alt=""/>
                    Cancel
                </a>
              </span>
<?php }?>
		  <?php if($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012
	 buttons('insertcategory.php','catform','maindiv','managecategories.php',$place=1,$formtype)
?> 
</div>          
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	<tr >
		<td>Category Name: <?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?> <span class="redstar" title="This field is compulsory">*</span><?php }//end edit?></td>
		<td><input name="categoryname" size="45" id="categoryname" type="text" value="<?php echo $name;?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"></td>
	</tr>
	<tr >
		<td>Category Image: </td>
		<td><input type="file" name="imageField" id="imageField" onchange="isValidImage()" /><?php if($categoryimage!=""){?><img src="../categoryimage/<?php echo $categoryimage; ?>" width="48" height="48" id="prvimage"/><?php }else{?><img src="../images/noimage.jpg" width="48" height="48" id="prvimage"/><?php } ?>
        <input type="hidden" name="oldimage" value="<?php echo $categoryimage?>" id="oldimage"/>
        </td>
	</tr>
	<tr >
		<td>Description: </td>				
		<td> <textarea name="description" id="description" cols="45" rows="5"><?php echo stripslashes($description);?></textarea>
		</td>
	</tr>
	<tr >
	  <td>Parent Category:</td>
	  <td>
      <?php
	  	
		echo ajax_tree(1,0,0,$menu);
		

	  /*
	  foreach($categories as $category)
	  {
       ?>
       	<input name="categories[]" value="<?php echo $category['pkcategoryid'];?>" type="checkbox"
		<?php if(@in_array($category['pkcategoryid'],$cat)) {echo "checked=checked";} ?> />
        <?php echo $category['name']; }
		*/
		?>
        
	  </td>
	  </tr>
	<tr >
	  <td colspan="2"  align="left">
           <?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
           <div class="buttons">
            <button type="button" class="positive" onclick="addcat();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('catdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
          <?php }?>
		   <?php if($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012
				 buttons('insertcategory.php','catform','maindiv','managecategories.php',$place=0,$formtype)
			?> 
        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type=hidden name="id" value ="<?php echo $id; ?>" />	
</form>
</div>
<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
<script language="javascript">
	focusfield('categoryname');
</script>
<?php }//end edit?>