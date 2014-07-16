<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id	=	$_GET['id'];
$qstring=$_SESSION['qstring'];
if($id!='')
{
	$prow = $AdminDAO->getrows("product","*"," pkproductid='$id'");
	$productname	=	$prow[0]['productname'];
	$description	=	$prow[0]['productdescription'];
	$imagepath		=	$prow[0]['defaultimage'];
	$catrow = $AdminDAO->getrows("subcategory sc,productcategory pc","pksubcatid"," sc.fkcategoryid= pc.fkcategoryid AND fkproductid='$id'");
	foreach($catrow as $categoriesarray)
	{
		$cat[]	=	$categoriesarray['pksubcatid'];	
	}
	$multiattributes = $AdminDAO->getrows("attribute,productattribute","pkattributeid,attributename","
																											fkattributeid	=	pkattributeid AND 
																											fkproductid		=	'$id'	AND
																											attributetype	=	'm'	AND
																											attributedeleted<>1	
																											","attributename","ASC");
	$singleattributes = $AdminDAO->getrows("attribute,productattribute","pkattributeid,attributename","
																											fkattributeid	=	pkattributeid AND 
																											fkproductid		=	'$id'	AND
																											attributetype	=	's'		AND
																											attributedeleted<>1	
																											","attributename","ASC");
	//$allattributes = $AdminDAO->getrows("attribute","*"," attributedeleted<>1","attributename","ASC");
	// repeating attribute table
	$query	= "SELECT attributename,pkattributeid FROM attribute WHERE
				pkattributeid NOT IN (
									  SELECT pkattributeid 
											FROM 
												attribute,
												productattribute
											WHERE
												fkproductid		=	'$id' AND 
												fkattributeid	=	pkattributeid AND 
												attributetype	!=	'n')
											ORDER BY attributename
		
				";
	$systemattributes	=	$AdminDAO->queryresult($query);
}//if
else
{
	$systemattributes	=	$AdminDAO->getrows("attribute","*"," attributedeleted<>1","attributename","ASC");
}
?>
<script type="text/javascript">
$().ready(function() {
	$('#addS').click(function() {
		return !$('#systemattributes option:selected').remove().appendTo('#singleattributes');
	});
	$('#removeS').click(function() {
		return !$('#singleattributes option:selected').remove().appendTo('#systemattributes');
	});
	
	$('#addM').click(function() {
		return !$('#systemattributes option:selected').remove().appendTo('#multipleattributes');
	});
	$('#removeM').click(function() {
		return !$('#multipleattributes option:selected').remove().appendTo('#systemattributes');
	});
});

function addform(id)
{
	//alert(id);
	//loading('System is saving data....');
	$('#singleattributes option').each(function(i) {
		$(this).attr("selected", "selected");
	});
	$('#multipleattributes option').each(function(i) {
		$(this).attr("selected", "selected");
	});
	options	=	{	
					url : 'insertproduct.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#prodform').ajaxSubmit(options);
}
	
function response(text)
{
	//alert(text);
	if(text=='')
	{
		//loading('Product Saved...');
		adminnotice('Product data has been saved.',0,5000);
		jQuery('#maindiv').load('manageproducts.php?'+'<?php echo $qstring?>');
		//document.getElementById('productdiv').style.display	=	'none';
		
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function hideform()
{
	document.getElementById('productfrmdiv').style.display	=	'none';	
}
function ajaxFileUpload()
	{
		//var f	=	document.getElementById('image').value.replace(/\\/g, "\\\\");
		$("#loading")
		.ajaxStart(function(){
			$(this).show();
		})
		.ajaxComplete(function(){
							   
			$(this).fadeOut(4000);
			document.getElementById('msgdiv').style.display='block';
			var f	=	document.getElementById('image').value.replace(/\\/g, "\\\\");
			document.getElementById('prvimage').src="../productimage/"+f;
			//alert(f);
		});

		$.ajaxFileUpload
		(
			{
				url:'fileupload.php',
				secureuri:false,
				fileElementId:'image',
				dataType: 'html',
				success: function (data, status)
				{
					//alert(data);
					//alert(status);
					/*if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							//alert(data.error);
						}else
						{
							//alert(data.msg);
						}
					}*/
				},
				error: function (data, status, e)
				{
					
					//alert(data.image);
					//alert(status);
					//alert(status);
				}
			}
		)
		
		return false;
	}
	

function isValidImage()
{
	var imagename	=	document.getElementById('image').value.replace(/\\/g, "\\\\");
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
<div class="notice" style="display:none" id="gridnotice">
<div id="error">
</div>
<a href=javascript:void(0) onclick=jQuery('#gridnotice').fadeOut()>
    <img src='../images/min.GIF' />
</a>
</div>
<div id="productfrmdiv" style="display:block">
<br />
<form enctype="multipart/form-data" name="prodform" id="prodform" style="width:920px;" action="insertproduct.php?id=<?php echo $id;?>" class="form">
<fieldset>
<legend>
    <?php 
	if($id =='-1')
	{
    	echo"Add Product";
	}
	else
	{
		echo "Edit Product: $productname";	
	}
	?>
 </legend>
<div style="float:right">
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
<span class="buttons">
            <button type="button" class="positive" onclick="addform('<?php echo $id; ?>');">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('productfrmdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </span>    
<?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012
	 buttons('insertproduct.php','prodform','maindiv','manageproducts.php',$place=1,$formtype)
//end edit?> 
</div>          
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	<tr >
		<td width="11%">Product Name: </td>
		<td width="89%">
		<input name="productname" id="productname" type="text" value="<?php echo $productname; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"></td>
	</tr>
	<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added if condition?>
    <tr >
		<td>Description: </td>
		<td>
		      <textarea name="description" id="description" cols="45" rows="5"><?php echo stripslashes($description); ?></textarea>
</td>
	</tr>
	<tr >
	  <td>Image:</td>
	  <td><input type="file" name="image" id="image" onchange="isValidImage();"/>
      <span id="msgdiv" style="display:none">Image uploaded successfully.</span>
     
      	<img  src="../images/loading.gif" id="loading" style="display:none">
	 
	  <?php if($imagepath!="")
	  	{
		?>
        <img src="../productimage/<?php echo $imagepath; ?>" width="48" height="48" id="prvimage"/>
		<?php 
		}else
		{?>
			<img src="../images/noimage.jpg" width="48" height="48" id="prvimage"/>
		<?php
        }
		?>
      <input type="hidden" name="oldimage" value="<?php echo $imagepath?>" id="oldimage"/>
      </td>
	  </tr>
      <?php }//end edit if statement?>
     </tbody>
     </table>
     <table width="920">
	<tr >
	  <th width="38%">
       Attributes: <?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?><span class="redstar" title="This field is compulsory">*</span><?php }//end edit?>
      </th> 
      </tr>
	<tr>
	  <td valign="top">
	  <table cellpadding="0" cellspacing="0" width="85%" >
		 <tr align="center" valign="top">
        	<th >
			System Attributes<br />
             <select multiple="multiple"  size="12" name="systemattributes[]" id="systemattributes" >
            <?php
			for($j = 0; $j<sizeof($systemattributes);$j++)
			{
				$attid 		= $systemattributes[$j]['pkattributeid'];
				$attribute = $systemattributes[$j]['attributename'];
			?>
            <option value="<?php echo $attid;?>">
            	<?php echo $attribute; ?>
            </option>
		  <?php 
			}
		  ?>
           </select>
           </th>
		<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
        	<td align="center" valign="middle">
            	<b>
            	<a href="javascript:void(0);" id="addS">Attach Single </a>
					<br /><br />
            	<a href="javascript:void(0);" id="removeS">Remove Single </a>
                <br /><br />
            	<a href="javascript:void(0);" id="addM">Attach Multiple </a>
					<br /><br />
            	<a href="javascript:void(0);" id="removeM">Remove Multiple </a>
                </b>
            </td>
          <?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
		   <td width="22%" rowspan="2" align="center" valign="middle">
            	
           <div class="buttons">
				<a href="javascript:void(0);" id="addS">Attach Single&gt;&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
					<br />
					<br />
            	<a href="javascript:void(0);" id="removeS">&lt;&lt;Remove Single &nbsp;&nbsp;&nbsp;</a>
                <br />
                <br />
            	<a href="javascript:void(0);" id="addM">Attach Multiple&gt;&gt; &nbsp;&nbsp;</a>
					<br />
					<br />
           	  <a href="javascript:void(0);" id="removeM">&lt;&lt;Remove Multiple </a>  
			   </div>
		                           
		</td>
          
          <?php } //end edit?>      
			<th>
            	Single Attributes<br />
              <select multiple="multiple" size="5" name="singleattributes[]" id="singleattributes">
            <?php
			if($id!='-1')
			{
				// repeating attribute table
				for($j = 0; $j<sizeof($singleattributes);$j++)
				{
					$attid 		= $singleattributes[$j]['pkattributeid'];
					$attribute = $singleattributes[$j]['attributename'];
				?>
				<option value="<?php echo $attid;?>">
					<?php echo $attribute; ?>
				</option>
			  <?php 
				}
			}//if
		  ?>
           </select>
			<hr />
        	Multiple Attributes<br />
              <select multiple="multiple"  size="5" name="multipleattributes[]" id="multipleattributes">
            <?php
			// repeating attribute table
			if($id !='-1')
			{
				for($j = 0; $j<sizeof($multiattributes);$j++)
				{
					$attid 		= $multiattributes[$j]['pkattributeid'];
					$attribute = $multiattributes[$j]['attributename'];
				?>
				<!--<option value="<?php //echo $attid;//commented line to replace it from main, edit by ahsan 14/02/2012?>">-->
				<option value="<?php echo $attid;?>" selected="selected"> <?php /*?>Added  selected="selected" by yasir 24-08-11<?php *///copied line from?>
					<?php echo $attribute; ?>
				</option>
			  <?php 
				}//for
			  
			}//if
			?>
        	</select>
</th>
</tr>          
 </table>
</td>
</tr>
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added if condition?>
<tr>
	<th>Categories</th> 
 </tr>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012 ?>
<tr>
	<td><a href="javascript:void(0);" onclick="toggleitem('cattreediv');" > <img src="../images/max.gif"  id="cattreediv_img" width="15" height="15"> <strong>Categories</strong>&nbsp;&nbsp; (Click here to Show/Hide the Categories)</a></td> 
 </tr>
<?php }?>
 <tr>	    
	  <td valign="top">
     <div id="cattreediv" style="display:none;"><?php //added div from main, by ahsan 14/02/2012?>
      		 <?php
	  require_once("../OpenCrypt/ajax_tree.php");
	  echo ajax_tree(1,1,0,$menu);
	  ?>
	  </div><?php //closed div added by ahsan 14/02/2012?>
      </td>
	  </tr>
	<tr >
	  <td colspan="2"  align="left">
        <?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added if condition?>
        <div class="buttons">
            <button type="button" class="positive" onclick="addform('<?php echo $id; ?>');">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('productfrmdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
          <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012
			 buttons('insertproduct.php','prodform','maindiv','manageproducts.php',$place=0,$formtype)
			//end edit?>
        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="id" value ="<?php echo $id;?>" />	
<input type="hidden" name="attributes" value ="<?php echo $radio;?>" />	
</form>
<?php
if($id=='-1')
{
	$_SESSION['qstring']='';
}
?>
</div>
<script language="javascript" type="text/javascript">
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added ?>
	document.getElementById('productname').focus();
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
	focusfield('productname');
<?php }?>
</script>