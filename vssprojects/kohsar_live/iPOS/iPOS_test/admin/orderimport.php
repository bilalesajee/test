<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$shipmentid	=	$_GET['id'];
session_start();
?>
<script language="javascript" type="text/javascript">
function importform(blankrec,shipmentid)
{
	loading('System is Saving The Data....');
	if(shipmentid!=-1)
	{
		options	=	{	
						url : 'orderimportaction.php?blankrec='+blankrec+'&shipmentid='+shipmentid,
						type: 'POST',
						success: responseshipmentimport
					}
	}
	else
	{
		options	=	{	
						url : 'orderimportaction.php?blankrec='+blankrec+'&shipmentid='+shipmentid,
						type: 'POST',
						success: responseimport
					}		
	}
	jQuery('#orderimportactionform').ajaxSubmit(options);
}
function responseshipmentimport(text)
{
	if(text=='')
	{
		adminnotice('Order has been imported.',0,5000);
		loadsection('center-column','manageshipment.php');
	}
	else if(text=='missing')
	{
		adminnotice('Order has been imported with some missing informations.',0,5000);
		loadsection('center-column','manageshipment.php');
	}
	else if(text=='in process')
	{
		adminnotice('Order has been imported.',0,5000);
		loadsection('center-column','manageshipmentinprocess.php');		
	}
	else if(text=='in process but missing')
	{
		adminnotice('Order has been imported with some missing informations.',0,5000);
		loadsection('center-column','manageshipmentinprocess.php');		
	}
	else
	{
		adminnotice(text,0,5000);
	}

}
function responseimport(text)
{
	if(text=='')
	{
		adminnotice('Order has been imported.',0,5000);
		selecttab('30_tab','manageshiplist.php');
	}
	else if(text=='missing')
	{
		adminnotice('Order has been imported with some missing informations.',0,5000);
		selecttab('30_tab','manageshiplist.php');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
/********************************************/
function addform(shipmentid)
{
	if($('#orderfile').val()=='')
	{
		alert('Please Select a file to upload.');
		return;	
	}
	isValidImage();
	adminnotice('Please Wait, System is Reading The Data....',0,20000);
	//loading('Please Wait, System is Reading The Data....');	
	options	=	{	
					url : 'orderimportfile.php?shipmentid='+shipmentid,
					type: 'POST',
					success: response
				}
	jQuery('#orderimportform').ajaxSubmit(options);
}
function response(text)
{
	document.getElementById('orderimportaction').style.display="block";
	document.getElementById('orderimportaction').innerHTML=text;	
	jQuery('#msg').hide();
}
function hideform()
{
	
	document.getElementById('importorderdiv').style.display='none';
}
function ajaxFileUpload()
{
	//alert("jafer");
	$("#loading").ajaxStart(function(){
		$(this).show();
	})
	.ajaxComplete(function(){
						   
		$(this).fadeOut(4000);
		//document.getElementById('msgdiv').style.display='block';
		//var f	=	document.getElementById('image').value.replace(/\\/g, "\\\\");
		//document.getElementById('prvimage').src="../orderimport/"+f;
	});
	$.ajaxFileUpload
	(
		{
			url:'orderimportfileupload.php',
			secureuri:false,
			fileElementId:'orderfile',
			dataType: 'text/html',
			success: function (data, status)
			{
				//alert(data);
			},
			error: function (data, status, e)
			{
				alert(data,status,e);
			}
		}
	)
	return false;
}
function isValidImage()
{
	var imagename	=	document.getElementById('orderfile').value.replace(/\\/g, "\\\\");
	//var oldimage	=	document.getElementById('oldimage').value;
/*	if(oldimage!='')
	{
		if(!confirm("There an image exists with this product. This will be replaced with new one! are you sure"))
		{
			return false;
		}
	}
*/	imagefile_value = imagename;
	var checkimg = imagefile_value.toLowerCase();
	if (!checkimg.match(/(\.csv)$/))
	{
		alert("Please upload a valid csv file i.e .csv");
		document.getElementById('orderfile').value='';		
		return false;
	}else
	{
		ajaxFileUpload();
	}
}
</script>
<div id="orderimportaction"></div>
<br />
<div id="importorderdiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="orderimportform" id="orderimportform" method="post" onSubmit="addform(); return false;" style="width:920px;" class="form"   enctype="multipart/form-data">
<fieldset>
<legend>
	Import Order
</legend>
<div>
    <legend>
    Please upload a csv file with the fields barcode,item,description...
    </legend>
    <br />
</div>
<div style="float:right; margin-top:7px;">
<span class="buttons">
    <button type="button" class="positive" onclick="addform(<?php echo $shipmentid;?>);">
        <img src="../images/tick.png" alt=""/> 
        Upload File
    </button>
     <a href="javascript:void(0);" onclick="hidediv('subsection');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
</div>
<table>
	<tbody>
	<tr>
		<td>File: <span class="redstar" title="This field is compulsory">*</span></td>
		<td colspan="2">
			<input name="orderfile" id="orderfile" type="file" size="90" onchange="isValidImage();">
        </td>
	</tr>
	</tbody>
</table>

</fieldset>	
<input type="hidden" name="orderid" value="<?php echo $orderid;?>" />	
</form>
</div><br />
<script language="javascript">
loading('Loading Form...');
</script>