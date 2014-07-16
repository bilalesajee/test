<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$id				=	$_GET['id'];
$param			=	$_GET['param'];
if($param==1) //locking case
{ 
	$brandid		=	$_GET['brandid'];
	//selecting brand
	if($brandid)
	{
		$branddata		=	$AdminDAO->getrows("brand","brandname","pkbrandid='$brandid'");
		$brandname		=	$branddata[0]['brandname'];
	}
	$clientid		=	$_GET['clientid'];
	//selecting customer
	if($clientid)
	{
		$clientdata		=	$AdminDAO->getrows("customer,addressbook","CONCAT(firstname,' ',lastname) companyname","pkcustomerid='$clientid' AND fkaddressbookid = pkaddressbookid");
		$customername	=	$clientdata[0]['companyname'];
	}
	$countryid		=	$_GET['countryid'];
	//selecting country
	if($countryid)
	{
		$countrydata	=	$AdminDAO->getrows("countries","code3","pkcountryid='$countryid'");
		$countryname	=	$countrydata[0]['code3'];
	}
	$deadline		=	implode("-",array_reverse(explode("-",$_GET['deadline'])));
	$supplierids	=	$_GET['supplierids'];
	if($supplierids)
	{
		//selecting supplier
		$sdata	=	$AdminDAO->getrows("supplier","pksupplierid,companyname","pksupplierid IN ($supplierids)");
		foreach($sdata as $sarr)
		{
			$supplierid		.=	",".$sarr['pksupplierid'];
			$suppliername	.=	",".$sarr['companyname'];
		}
	}
}
if($id!="-1") //edit case
{
	$query			=	"SELECT 
								fkstoreid,
								fkcustomerid,
								fkshipmentid,
								barcode,
								itemdescription,
								quantity,
								DATE_FORMAT(deadline,'%d-%m-%Y') deadline,
								lastsaleprice,
								pricelimit,
								agreedprice,
								weight,
								fkbrandid,
								fkcountryid,
								description,
								comments,
								productimage,
								clientinfo,
								fkstatusid,
								unit
						FROM 	
								`order`
						WHERE 
								pkorderid='$id'
						";
	$orderdata		=	$AdminDAO->queryresult($query);
	$fkshipmentid	=	$orderdata[0]['fkshipmentid'];
	$barcode		=	$orderdata[0]['barcode'];
	$itemdescription=	$orderdata[0]['itemdescription'];
	$description	=	$orderdata[0]['description'];
	$clientinfo		=	$orderdata[0]['clientinfo'];
	$quantity		=	$orderdata[0]['quantity'];
	$pricelimit		=	$orderdata[0]['pricelimit'];
	$price			=	$orderdata[0]['lastsaleprice'];
	$agreedprice	=	$orderdata[0]['agreedprice'];
	$weight			=	$orderdata[0]['weight'];
	$comments		=	$orderdata[0]['comments'];
	$deadline		=	$orderdata[0]['deadline'];
	$defaultimage	=	$orderdata[0]['productimage'];
	$selected_store	=	$orderdata[0]['fkstoreid'];
	$brandid		=	$orderdata[0]['fkbrandid'];
	$supplierid		=	$orderdata[0]['fksupplierid'];
	$countryid		=	$orderdata[0]['fkcountryid'];
	$clientid		=	$orderdata[0]['fkcustomerid'];
	$fkstatusid		=	$orderdata[0]['fkstatusid'];
	$unit			=	$orderdata[0]['unit'];
	//selecting customer
	if($clientid)
	{
		$clientdata		=	$AdminDAO->getrows("customer,addressbook","CONCAT(firstname,' ',lastname) companyname","pkcustomerid='$clientid' AND fkaddressbookid = pkaddressbookid");
		$customername	=	$clientdata[0]['companyname'];
	}
	//selecting shipment
	if($fkshipmentid)
	{
		$shipmentdata	=	$AdminDAO->getrows("shipment","shipmentname","pkshipmentid='$fkshipmentid'");
		$shipmentname	=	$shipmentdata[0]['shipmentname'];
	}
	//selecting brand
	if($brandid)
	{
		$branddata		=	$AdminDAO->getrows("brand","brandname","pkbrandid='$brandid'");
		$brandname		=	$branddata[0]['brandname'];
	}
	//selecting supplier
	$supplierdata	=	$AdminDAO->getrows("ordersupplier,supplier","fksupplierid,companyname","fksupplierid=pksupplierid AND fkorderid='$id'");
	foreach($supplierdata as $supplierarr)
	{
		$supplierid		.=	",".$supplierarr['fksupplierid'];
		$suppliername	.=	",".$supplierarr['companyname'];
	}
	//selecting country
	if($countryid)
	{
		$countrydata	=	$AdminDAO->getrows("countries","code3","pkcountryid='$countryid'");
		$countryname	=	$countrydata[0]['code3'];
	}
}
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
<style>
#preview
{
	position:absolute;
	border:1px solid #ccc;
	background:#333;
	padding:5px;
	display:none;
	color:#fff;
	z-index:5000;
}
ul
{
	list-style-type:none;
}
</style>
<script language="javascript">
/*
 * Image preview script 
 * powered by jQuery (http://www.jquery.com)
 * 
 * written by Alen Grakalic (http://cssglobe.com)
 * 
 * for more info visit http://cssglobe.com/post/1695/easiest-tooltip-and-image-preview-using-jquery
 *
 */
 
this.imagePreview = function(){	
	/* CONFIG */
		
		xOffset = 10;
		yOffset = 30;
		
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result
		
	/* END CONFIG */
	$("a.preview").hover(function(e){
		this.t = this.title;
		this.title = "";	
		var c = (this.t != "") ? "<br/>" + this.t : "";
		$("body").append("<p id='preview'><img src='"+ this.href +"' alt='Image preview' />"+ c +"</p>");								 
		$("#preview")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");						
    },
	function(){
		this.title = this.t;	
		$("#preview").remove();
    });	
	$("a.preview").mousemove(function(e){
		$("#preview")
			.css("top",(e.pageY - xOffset-500) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});			
};


// starting the script on page load
$(document).ready(function(){
	imagePreview();
});


$().ready(function() 
	{
		$("#deadline").mask("99-99-9999");
		<?php
		if($id!='-1')
		{
			?>
			//getitemdetails(document.getElementById('barcode').value,0,1);
			<?php
		}
		?>
		document.getElementById('barcode').focus();
		function findValueCallback(event, data, formatted) 
		{
			if( typeof(data) == 'undefined' ){
			data = '';
			}			
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
		$(":text, textarea").result(findValueCallback).next().click(function() 
		{
			$(this).prev().search();
		});
		$("#clear").click(function() 
		{
			$(":input").unautocomplete();
		});
		$("#itemdescription").autocomplete("orderproductautocomplete.php") ;
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
function addorder(id)
{
	options	=	{	
					url : 'orderaddaction.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#orderform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Order has been saved.',0,5000);
		document.getElementById('subsection').innerHTML='';
		jQuery('#maindiv').load('manageshiplist.php?'+text);		
	}
	else if(text.indexOf("lock")==1)
	{
		adminnotice('Order has been saved.',0,5000);
		jQuery('#maindiv').load('manageshiplist.php?'+text);
	}
	else if(text=="without barcode")
	{
		adminnotice("Order has been saved without barcode",0,5000);
		document.getElementById('subsection').innerHTML='';
		jQuery('#maindiv').load('manageshiplist.php');
	}	
	else if(text=="same barcode")
	{
		adminnotice("Order Quantity Updated..Order of a same barcode has already been requested.",0,5000);
		document.getElementById('subsection').innerHTML='';
		jQuery('#maindiv').load('manageshiplist.php');	
	}
	else
	{
		adminnotice(text,0,5000);
		jQuery('#maindiv').load('manageshiplist.php');
	}
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
		<?php if($defaultimage!="")
	  	{
		?>
		document.getElementById('prvimage').src="../orderimage/"+f;
		<?php
		}
		?>
	});
	$.ajaxFileUpload
	(
		{
			url:'orderfileupload.php',
			secureuri:false,
			fileElementId:'image',
			dataType: 'text/html',
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
      <?php
    if($id!="-1")
    { echo "Edit Order";}
    else
    { echo "New Order";}	
    ?>
      </legend>
      <div style="float:right"> <span class="buttons">
    <?php if($id=='-1') {?>   
        <button type="button" class="positive" onclick="showpage(0,'','orderimport.php','subsection','maindiv','','$formtype');">
        <img src="../images/file_excel.png" alt=""/>
        Import Orders
        </button>   
	<?php }?>            
    <button type="button" class="positive" onclick="addorder();">
        <img src="../images/tick.png" alt=""/>
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>

        <a href="javascript:void(0);" onclick="hidediv('addorderdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span> </div>
<table width="100%">
  <tr>
    <td>Lock Screen:</td>
    <td align="left"><input type="checkbox" name="lock" value="1" <?php if($param==1) echo "checked=\"checked\""; ?> ></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="11%">Barcode:</td>
    <td width="36%" align="left"><input name="barcode" id="barcode" class="text" size="30" value="<?php echo $barcode; ?>" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,0); return false;}" type="text" autocomplete="off" onfocus="this.select()" ></td>
    <td width="11%">Brand:</td>
    <td width="42%"><input name="brand" id="brand" class="text" size="30" onFocus="this.select();" value="<?php echo $brandname; ?>" type="text" autocomplete="off" /> Lock <input type="checkbox" <?php if($param==1 && $brandid) echo "checked=\"checked\""; ?> name="brandlock" value="1" /></td>
  </tr>
  <tr>
    <td>Item Name: <span class="redstar" title="This field is compulsory">*</span></td>
    <td><textarea name="itemdescription" id="itemdescription" style="width:174px; height:50px;" onFocus="this.select();"><?php echo $itemdescription; ?></textarea></td>
    <td>Supplier:</td>
    <td><textarea name="supplier" id="supplier" style="width:174px; height:50px;"><?php if($suppliername) { echo trim($suppliername,",").",";} ?></textarea>
      Lock
      <input type="checkbox" <?php if($param==1 && $supplierids) echo "checked=\"checked\""; ?> name="supplierlock" value="1" /></td>
  </tr>
  <tr>
    <td rowspan="2">Description:</td>
    <td rowspan="2"><textarea name="description" id="description" style="width:174px; height:50px;"><?php echo $description; ?></textarea></td>
    <td>Country:</td>
    <td><input name="country" id="country" class="text" size="30" onFocus="this.select();" value="<?php echo $countryname; ?>" type="text" autocomplete="off" /> Lock <input type="checkbox" <?php if($param==1 && $countryid) echo "checked=\"checked\""; ?> name="countrylock" value="1" /></td>
  </tr>
  <tr>
    <td>Deadline:</td>
    <td><input name="deadline" id="deadline" class="text" size="30" value="<?php echo $deadline; ?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" type="text"  onblur="alertdate(this.value,this.id);" />
      Lock
      <input type="checkbox" <?php if($param==1 && $deadline) echo "checked=\"checked\""; ?> name="deadlinelock" value="1" /></td>
  </tr>
  <tr>
    <td>Quantity: <span class="redstar" title="This field is compulsory">*</span></td>
    <td><input name="quantity" id="quantity" class="text" size="30" value="<?php echo $quantity; ?>" onKeyDown="javascript:if(event.keycode==13){addorder(); return false;}" type="text" onkeypress="return isNumberKey(event);" ></td>
    <td>Client:</td>
    <td><input name="clientinfo" id="clientinfo" class="text" size="30" onfocus="this.select();" value="<?php echo $customername; ?>" type="text" autocomplete="off" />
      Lock
      <input type="checkbox" <?php if($param==1 && $clientid) echo "checked=\"checked\""; ?> name="clientlock" value="1" /></td>
  </tr>
  <tr>
    <td>Price:</td>
    <td><input name="price" id="price" class="text" size="30" value="<?php echo $price; ?>" type="text" autocomplete="off" onkeypress="return isNumberKey(event);" /></td>
    <td>Weight / Volume</td>
    <td><input name="weight" id="weight" class="text" size="30" value="<?php echo $weight; ?>" onkeydown="javascript:if(event.keycode==13){addshiplist(); return false;}" type="text" onkeypress="return isNumberKey(event);" />&nbsp;
<select name="unit" id="unit" style="width:105px;">
	<option value="g" <?php if($unit=='g') echo "selected='selected'";?>>grams(g)</option>
	<option value="kg" <?php if($unit=='kg') echo "selected='selected'";?>>kilograms(kg)</option>
    <option value="oz" <?php if($unit=='oz') echo "selected='selected'";?>>ounce(oz)</option>
	<option value="lb" <?php if($unit=='lb') echo "selected='selected'";?>>pounds(lb)</option>
    <option value="ml" <?php if($unit=='ml') echo "selected='selected'";?>>milliliter(ml)</option>    
    <option value="L" <?php if($unit=='L') echo "selected='selected'";?>>litres(L)</option>     
</select>        
    </td>
  </tr>
  <tr>
    <td>Price Limit:</td>
    <td><input name="pricelimit" id="pricelimit" class="text" size="30" value="<?php echo $pricelimit; ?>" type="text" autocomplete="off" onkeypress="return isNumberKey(event);" /></td>
    <td rowspan="2">Comments:</td>
    <td rowspan="2"><textarea name="comments" id="comments" style="width:174px; height:50px;"><?php echo $comments; ?></textarea></td>
  </tr>
  <tr>
    <td>Agreed Price:</td>
    <td><input name="agreedprice" id="agreedprice"  class="text" size="30" value="<?php echo $agreedprice; ?>" type="text" autocomplete="off" onkeypress="return isNumberKey(event);" /></td>
  </tr>
  <tr>
    <td>Image:</td>
    <td>
    	<input type="file" name="image" id="image" onchange="isValidImage();" />
        <span id="msgdiv" style="display:none">Image uploaded successfully.</span>
        <img  src="../images/loading.gif" id="loading" style="display:none">
    	<?php if($defaultimage!="")
	  	{
		?>
        <ul>
			<li><a href="../orderimage/<?php echo $defaultimage; ?>" class="preview"><img src="../orderimage/<?php echo $defaultimage; ?>" width="48" height="48" id="prvimage"/></a></li>
		</ul>
		<?php 
		}
		?></td>
    <td>Source:</td>
    <td><?php echo $stores; ?></td>
  </tr>
  <tr>
    <td colspan="4" align="center"><div class="buttons">
    <?php if($id=='-1') {?>   
        <button type="button" class="positive" onclick="showpage(0,'','orderimport.php','subsection','maindiv','','$formtype');">
        <img src="../images/file_excel.png" alt=""/>
        Import Orders
        </button>   
	<?php }?>    
  	<button type="button" class="positive" onclick="addorder();">
      <img src="../images/tick.png" alt=""/>
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>
     
      <a href="javascript:void(0);" onclick="hidediv('addorderdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div>
      </td>
  </tr>
</table>
 </fieldset>
 <input type="hidden" value="<?php echo $id;?>" name="id" id="id" />
 <input type="hidden" value="<?php echo $brandid;?>" name="brandid" id="brandid" />
 <input type="hidden" value="<?php echo $barcodeid;?>" name="barcodeid" id="barcodeid" />
 <input type="hidden" value="<?php echo trim($supplierid,",");?>" name="supplierid" id="supplierid" />
 <input type="hidden" value="<?php echo $countryid;?>" name="countryid" id="countryid" />
 <input type="hidden" value="<?php echo $clientid;?>" name="clientid" id="clientid" />
 <input type="hidden" value="<?php echo $defaultimage;?>" name="oldimage" id="oldimage" />
 <input type="hidden" value="<?php echo $fkstatusid;?>" name="fkstatusid" id="fkstatusid" />
 <input type="hidden" value="<?php echo $fkshipmentid;?>" name="fkshipmentid" id="fkshipmentid" />
</form>
</div>
<div class="buttons" style="float:right;margin-top:-20px;">
<button type="button" class="positive" onclick="viewhistory();"> <img src="../images/add.png" alt=""/>
View History
</button>
</div>
<div id="similaritemsdiv" style="clear:both;margin-top:5px;"></div>
<script language="javascript" type="text/javascript">
function viewhistory()
{
	bc	=	document.getElementById('barcode').value;
	$('#similaritemsdiv').load('orderhistory.php?bcid='+bc);
}
function alertdate(val,id)
{
	if(val!="")
	{
		dtval	=	val.split('-');
		dateval	=	dtval[2]+'-'+dtval[1]+'-'+dtval[0];
		if(dateval<"<?php echo date('Y-m-d')?>")
		{
			alert("The deadline can not be a past date, please correct the date.");
		}
		if(!isValidDate(val))
		{
			alert("The date you entered is not valid. Correct format is: (dd-mm-yyyy)");
		}
	}
}
</script>