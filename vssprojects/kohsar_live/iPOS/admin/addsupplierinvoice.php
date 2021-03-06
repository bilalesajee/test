<?php

include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 	=	$_REQUEST['id'];
if($id)
{
	//edit data
	$row 		=	$AdminDAO->getrows("$dbname_detail.supplierinvoice","fksupplierid,billnumber,FROM_UNIXTIME(datetime,'%d-%m-%Y') datetime,description,image,invoice_status"," pksupplierinvoiceid= '$id'");
	$fksupplierid	=	$row[0]['fksupplierid'];
	$billnumber		=	$row[0]['billnumber'];
	$datetime		=	$row[0]['datetime'];
	$description	=	$row[0]['description'];
	$image			=	$row[0]['image'];
	$inv_status	=	$row[0]['invoice_status'];
}
// selecting suppliers
$suppliers		=	$AdminDAO->getrows("supplier","pksupplierid,companyname","supplierdeleted=0");
$suppliersel	=	"<select name=\"supplier\" id=\"supplier\" style=\"width:180px;\" ><option value=\"\">Select Supplier</option>";
for($i=0;$i<sizeof($suppliers);$i++)
{
	$suppliername	=	$suppliers[$i]['companyname'];
	$supplierid		=	$suppliers[$i]['pksupplierid'];
	$select	=	"";
	if($supplierid==$fksupplierid)
	{
		$select	=	"selected=\"selected\"";
	}
	$suppliersel2	.=	"<option value=\"$supplierid\" $select>$suppliername</option>";
}
$supplier			=	$suppliersel.$suppliersel2."</select>";
// end suppliers
?>
<script language="javascript" src="../includes/js/ajaxfileupload.js"></script>
<script language="javascript">
jQuery(function($)
{
	$("#datetime").mask("99-99-9999");
	$('#supplier').focus();
});
function addform()
{
	loading('System is saving The Data....');
	options	=	{	
					url : 'insertsupplierinvoice.php',
					type: 'POST',
					success: response
				}
	jQuery('#invoiceform').ajaxSubmit(options);
}
function response(text)
{
	
	  if(text=="")
	  {
		//document.getElementById('error').style.display		=	'none';
		adminnotice('Invoice data has been saved.',0,5000);
		jQuery('#maindiv').load('managesupplierinvoices.php');		
	}
	else
	{
		//alert('i am in else');
		adminnotice(text,0,5000);
	}
}
function viewinvoice(p)
{
	
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
		//alert('herer');
		ajaxFileUpload();
	}
}
</script>
<div id="supplierdiv">
<form enctype="multipart/form-data" name="invoiceform" id="invoiceform" style="width:920px;" onSubmit="addform(); return false;" class="form">
<fieldset>
<legend>
    Add Invoice	</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="addform();" >
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
    </button>

     <a href="javascript:void(0);" onclick="hidediv('supplierdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>    
</div>          
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	<tr >
		<td width="14%">Supplier:</td>
		<td><?php echo $supplier;?></td>
	</tr>
	<tr >
	  <td>Bill Number:</td>
	  <td><input type="text" id="billnumber" name="billnumber" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" value="<?php echo $billnumber;?>" /></td>
	  </tr>
	<tr >
	  <td>Date:</td>
	  <td><input type="text" id="datetime" name="datetime" maxlength="8" value="<?php echo $datetime;?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Description:</td>
	  <td><textarea name="description" id="description"><?php echo html_entity_decode($description);?></textarea></td>
	  </tr>
	<tr >
	  <td>File:</td>
	  <td><input type="file" name="image" id="image" onchange="isValidImage()"/>
	   <span id="msgdiv" style="display:none">Image uploaded successfully.</span>
     
      	<img  src="../images/loading.gif" id="loading" style="display:none">
	 
	  <?php if($image!="")
	  	{
		?>
        <a href="../productimage/<?php echo $image; ?>" target="_blank">
        <img src="../productimage/<?php echo $image; ?>" width="48" height="48" id="prvimage" />
        </a>
		<?php 
		}else
		{?>
        <a href="../productimage/<?php echo $image; ?>" target="_blank">
			<img src="../images/noimage.jpg" width="48" height="48" id="prvimage" /></a>
		<?php
        }
		?>
      <input type="hidden" name="oldimage" value="<?php echo $image?>" id="oldimage"/>
	  </td>
	  </tr>
	<tr >
	  <td colspan="2"  align="left">
	    <div class="buttons">
       
	      <button type="button" class="positive" onclick="addform();" >
	        <img src="../images/tick.png" alt=""/> 
	        <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
	        </button>
           
	      <a href="javascript:void(0);" onclick="hidediv('supplierdiv');" class="negative">
	        <img src="../images/cross.png" alt=""/>
	        Cancel
	        </a>
	      </div>
	    </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="id" value ="<?php echo $id; ?>" />	
</form>
</div>