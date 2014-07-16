<?php 
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$id				=	$_GET['id'];
// stores
$stores			=	$AdminDAO->getrows("store","storename,pkstoreid","storedeleted<>1 AND storestatus=1");
$storesel		=	"<select name=\"store\" id=\"store\" style=\"width:186px;\"><option value=\"\">Location</option>";
for($i=0;$i<sizeof($stores);$i++)
{
	$storename	=	$stores[$i]['storename'];
	$storeid	=	$stores[$i]['pkstoreid'];
	$select		=	"";
	if($storeid == $selected_store)
	{
		$select = "selected=\"selected\"";
	}
	$storesel2	.=	"<option value=\"$storeid\" $select>$storename</option>";
}
$stores			=	$storesel.$storesel2."</select>";
// end stores

?>
<script language="javascript">
$().ready(function() 
	{
		$("#deadline").mask("99-99-9999");
		document.getElementById('barcode').focus();
		function findValueCallback(event, data, formatted) 
		{
			if(data[1]=='typebarcode')
			{
				document.getElementById('barcode').value=data[2];
				document.getElementById('barcodeid').value=data[3];
				document.getElementById('quantity').focus();			
			}
			else if(data[1]=='typeclient')
			{
				document.getElementById('clientid').value=data[2];
			}
			else if(data[1]=='typebrand')
			{
				document.getElementById('brandid').value=data[2];
			}
			else if(data[1]=='typesupplier')
			{
				document.getElementById('supplierid').value=document.getElementById('supplierid').value+','+data[2];
			}
			else if(data[1]=='typecountry')
			{
				document.getElementById('countryid').value=data[2];
			}
			$("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
		}
		function formatItem(row) 
		{
			return row[0] + " (<strong>id: " + row[0] + "</strong>)";
		}
		function formatResult(row) 
		{
			return row[0].replace(/(<.+?>)/gi,'');
		}
		$("#itemdescription").autocomplete("orderproductautocomplete.php") ;
		$(":text, textarea").result(findValueCallback).next().click(function() 
		{
			$(this).prev().search();
		});
		$("#clear").click(function() 
		{
			$(":input").unautocomplete();
		});			
		$("#clientinfo").autocomplete("orderclientautocomplete.php");
		$("#country").autocomplete("ordercountryautocomplete.php");
		$("#brand").autocomplete("orderbrandautocomplete.php");
		//$("#supplier").autocomplete("ordersupplierautocomplete.php");
		$("#supplier").autocomplete("ordersupplierautocomplete.php", {
			multiple: true,
			mustMatch: true,
			autoFill: true
		});

});
function getitemdetails(bc,itm,ed)
{
	if(bc=='')
	{
		alert("Please enter Barcode.");
		return false;
	}
	bc = trim(bc);
	jQuery('#loaditemscript').load('getshipitemdata.php?bc='+bc+'&item='+itm+'&edit='+ed);
	document.getElementById('quantity').focus();
}
function ajaxFileUpload()
{
	$("#loading")
	.ajaxStart(function(){
		$(this).show();
	})
	.ajaxComplete(function(){
						   
		$(this).fadeOut(4000);
		document.getElementById('msgdiv').style.display='block';
		var f	=	document.getElementById('image').value.replace(/\\/g, "\\\\");
		document.getElementById('prvimage').src="../orderimage/"+f;
	});
	$.ajaxFileUpload
	(
		{
			url:'orderfileupload.php',
			secureuri:false,
			fileElementId:'image',
			dataType: 'html',
			success: function (data, status)
			{
			},
			error: function (data, status, e)
			{
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
<div id="loaditemscript" style="display:none"></div>
<div id="addorderdiv" style="display: block;">
<br />
<form id="orderform" style="width: 920px;" action="" class="form"  enctype="multipart/form-data">
    <fieldset>
      <legend>
      Add Order
      </legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="showpage(1,'','orderimport.php','subsection','maindiv','','$formtype');">
            <img src="../images/file_excel.png" alt=""/>
            Import Orders
        </button>       
        <button type="button" class="positive" onclick="addshiporder();"> <img src="../images/tick.png" alt=""/>
        	Save
        </button>
        <button type="button" onclick="hidediv('addorderdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </button> </span> </div>
<table width="100%">
  <tr>
    <td style="display:none;">Lock Screen:</td>
    <td align="left" style="display:none;"><input type="checkbox" name="lock" checked="checked" value="1"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="11%">Barcode:</td>
    <td width="36%" align="left"><input name="barcode" id="barcode" class="text" size="30" value="" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,0); return false;}" type="text" autocomplete="off" onfocus="this.select()" ></td>
    <td width="11%">Brand:</td>
    <td width="42%"><input name="brand" id="brand" class="text" size="30" onFocus="this.select();" value="" type="text" autocomplete="off" /></td>
  </tr>
  <tr>
    <td>Item Name: <span class="redstar" title="This field is compulsory">*</span></td>
    <td><textarea name="itemdescription" id="itemdescription" style="width:174px; height:50px;" onFocus="this.select();"></textarea></td>
    <td>Supplier:</td>
    <td><textarea name="supplier" id="supplier" style="width:174px; height:50px;"></textarea></td>
  </tr>
  <tr>
    <td rowspan="2">Description:</td>
    <td rowspan="2"><textarea name="description" id="description" style="width:174px; height:50px;"></textarea></td>
    <td>Country:</td>
    <td><input name="country" id="country" class="text" size="30" onFocus="this.select();" value="" type="text" autocomplete="off" /></td>
  </tr>
  <tr>
    <td>Client:</td>
    <td><input name="clientinfo" id="clientinfo" class="text" size="30" onFocus="this.select();" value="" type="text" autocomplete="off" /></td>
  </tr>
  <tr>
    <td>Quantity: <span class="redstar" title="This field is compulsory">*</span></td>
    <td><input name="quantity" id="quantity" class="text" size="30" value="" onKeyDown="javascript:if(event.keycode==13){addshiporder(); return false;}" type="text" onkeypress="return isNumberKey(event);" ></td>
    <td>Price Limit:</td>
    <td><input name="pricelimit" id="pricelimit" class="text" size="30" value="" type="text" autocomplete="off" onkeypress="return isNumberKey(event);" /></td>
  </tr>
  <tr>
    <td>Price:</td>
    <td><input name="price" id="price" class="text" size="30" value="" type="text" autocomplete="off" onkeypress="return isNumberKey(event);" /></td>
    <td>Agreed Price:</td>
    <td><input name="agreedprice" id="agreedprice"  class="text" size="30" value="" type="text" autocomplete="off" onkeypress="return isNumberKey(event);" /></td>
  </tr>
  <tr>
    <td>Weight / Volume:</td>
    <td><input name="weight" id="weight" class="text" size="30" value="" onKeyDown="javascript:if(event.keycode==13){addshiplist(); return false;}" type="text" onkeypress="return isNumberKey(event);" >&nbsp;
<select name="unit" id="unit" style="width:105px;">
	<option value="g" <?php if($unit=='g') echo "selected='selected'";?>>grams(g)</option>
	<option value="kg" <?php if($unit=='kg') echo "selected='selected'";?>>kilograms(kg)</option>
    <option value="oz" <?php if($unit=='oz') echo "selected='selected'";?>>ounce(oz)</option>
	<option value="lb" <?php if($unit=='lb') echo "selected='selected'";?>>pounds(lb)</option>
    <option value="ml" <?php if($unit=='ml') echo "selected='selected'";?>>milliliter(ml)</option>    
    <option value="L" <?php if($unit=='L') echo "selected='selected'";?>>litres(L)</option>     
</select>    
    </td>
    <td rowspan="2">Comments:</td>
    <td rowspan="2"><textarea name="comments" id="comments" style="width:174px; height:50px;"></textarea></td>
  </tr>
  <tr>
    <td>Deadline:</td>
    <td><input name="deadline" id="deadline" class="text" size="30" value="" onkeydown="javascript:if(event.keyCode==13) {return false;}" type="text" ></td>
  </tr>
  <tr>
    <td>Image:</td>
    <td>
    	<input type="file" name="image" id="image" onchange="isValidImage();" />
        <span id="msgdiv" style="display:none">Image uploaded successfully.</span>
        <img  src="../images/loading.gif" id="loading" style="display:none">
    	</td>
    <td>Source:</td>
    <td><?php echo $stores; ?></td>
  </tr>
  <tr>
    <td colspan="4" align="center"><div class="buttons">
    <button type="button" class="positive" onclick="showpage(1,'','orderimport.php','subsection','maindiv','','$formtype');">
    	<img src="../images/file_excel.png" alt=""/>
    	Import Orders
    </button>      
  	<button type="button" class="positive" onclick="addshiporder();"> <img src="../images/tick.png" alt=""/>
   		Save
  	</button>
  	<button type="button" onclick="hidediv('addorderdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </button> </div>
      </td>
  </tr>
</table>
 </fieldset>
 <input type="hidden" value="<?php echo $id;?>" name="id" id="id" />
 <input type="hidden" value="" name="brandid" id="brandid" />
 <input type="hidden" value="<?php echo $barcodeid;?>" name="barcodeid" id="barcodeid" />
 <input type="hidden" value="" name="supplierid" id="supplierid" />
 <input type="hidden" value="" name="countryid" id="countryid" />
 <input type="hidden" value="" name="clientid" id="clientid" />
 <input type="hidden" value="" name="oldimage" id="oldimage" />
</form>
</div>
<!--<span class="buttons" style="float:right;margin-top:-20px;">
<button type="button" class="positive" onclick="viewhistory();"> <img src="../images/add.png" alt=""/>
View History
</button>
</span>-->
<div id="similaritemsdiv"></div>
<script language="javascript" type="text/javascript">
/*function viewhistory()
{
	bc	=	document.getElementById('barcode').value;
	$('#similaritemsdiv').load('orderhistory.php?bcid='+bc);
}*/
function addshiporder(id)
{
	options	=	{	
					url : 'ordershipaddaction.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#orderform').ajaxSubmit(options);
}
function response(text)
{
	if(text==1)
	{
		adminnotice('Order has been saved.',0,5000);
		document.getElementById('subsection').innerHTML='';
		jQuery('#maindiv').load('manageshipment.php?lock=1');
	}
	else if(text=='in process')
	{
		adminnotice('Order has been saved.',0,5000);
		document.getElementById('subsection').innerHTML='';
		jQuery('#maindiv').load('manageshipmentinprocess.php?lock=1');		
	}
	else if(text=="same barcodein process")
	{
		adminnotice("Order Quantity Updated..Order of a same barcode has already been requested.",0,5000);
		document.getElementById('subsection').innerHTML='';
		jQuery('#maindiv').load('manageshipmentinprocess.php');	
	}
	else if(text=="same barcode1")
	{
		adminnotice("Order Quantity Updated..Order of a same barcode has already been requested.",0,5000);
		document.getElementById('subsection').innerHTML='';
		jQuery('#maindiv').load('manageshipment.php');	
	}
	else
	{
		adminnotice(text,0,5000);
		jQuery('#maindiv').load('manageshipment.php');
	}
}
</script>