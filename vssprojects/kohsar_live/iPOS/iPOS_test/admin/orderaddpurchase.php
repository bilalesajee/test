<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id	=	$_GET['id'];
if($id!='')
{
	$catdata		=	$AdminDAO->getrows("orderpurchase LEFT join barcode on fkbarcodeid=pkbarcodeid LEFT JOIN supplier ON fksupplierid=pksupplierid","fkorderid, itemdescription, barcode, fkbarcodeid, companyname, fksupplierid, fkshipmentid, orderpurchase.fkaddressbookid, datetime, quantity, purchaseprice, weight, batch,DATE_FORMAT(expiry,'%d-%m-%Y') expiry"," pkorderpurchaseid = '$id'");
	$orderid		=	$catdata[0]['fkorderid'];
	$barcodeid		=	$catdata[0]['fkbarcodeid'];
	$item			=	$catdata[0]['itemdescription'];
	$barcode		=	$catdata[0]['barcode'];
	$expiry			=	$catdata[0]['expiry'];
	$weight			=	$catdata[0]['weight'];
	$quantity		=	$catdata[0]['quantity'];	
	$purchaseprice	=	$catdata[0]['purchaseprice'];
	$purchasetime	=	$catdata[0]['purchasetime'];
	$batch			=	$catdata[0]['batch'];	
	$fksupplierid	=	$catdata[0]['fksupplierid'];
	$companyname	=	$catdata[0]['companyname'];
	$fkshipmentid	=	$catdata[0]['fkshipmentid'];
}
?>
<script language="javascript">
jQuery(function($){
	$("#expiry").mask("99-99-9999");
	document.getElementById('barcode').focus();
		function findValueCallback(event, data, formatted) 
		{
			if(data[1]=='typesupplier')
			{
				document.getElementById('supplierid').value	=	data[2];
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
		$("#supplier").autocomplete("ordersupplierautocomplete.php");
});
function addpurchase()
{
	loading('System is saving data....');
	options	=	{	
					url : 'orderupdatepurchase.php?id='+'<?php echo $id?>',
					type: 'POST',
					success: response
				}	
	jQuery('#purchaseform').ajaxSubmit(options);
}
function response(text)
{

	if(text=='')
	{
		adminnotice('Purchase data has been saved.',0,5000);
		document.getElementById('orderpurchasediv').innerHTML='';
		jQuery('#subsection').load('orderpurchases.php?id='+'<?php echo $fkshipmentid?>&param=undefined');		
	}
	else
	{
		adminnotice(text,0,5000);	
	} 
}
function hideform()
{
	
	document.getElementById('orderpurchasediv').style.display='none';
}

</script>
<div id="loading" class="loading" style="display:none;">
</div>
<div id="orderpurchasediv">
<br />
<form name="purchaseform" id="purchaseform" style="width:920px;" class="form">
<fieldset>
<legend>
    <?php 
	if($id =='-1')
	{
    	echo"Add Purchase";
	}
	else
	{
		echo "Edit Purchase ";	
	}
	?>
</legend>
<div style="float:right">
<span class="buttons">
            <button type="button" class="positive" onclick="addpurchase();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hideform();" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </span>
</div>          
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	
	<tr height="30px">
	  <td width="9%">Barcode : </td>
	  <td width="91%"><?php echo $barcode;?></td>
	  </tr>
	<tr  height="30px">
		<td>Item : </td>
		<td><?php echo $item;?></td>
	</tr>
    <tr >
	  <td>Expiry: </td>
	  <td align="left"><input name="expiry" id="expiry" type="text" class="text" value="<?php echo $expiry;?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
		<td>Weight: </td>
		<td align="left"><input name="weight" id="weight" type="text" class="text" value="<?php echo $weight;?>" ></td>
	</tr>
    
    
	<tr >
	  <td>Quantity: </td>				
	  <td align="left"><input name="quantity" id="quantity" type="text" class="text" value="<?php echo $quantity;?>" /></td>
	  </tr>
	<tr >
	  <td>Price: </td>
	  <td align="left"><input name="purchaseprice" id="purchaseprice" type="text" class="text" value="<?php echo $purchaseprice;?>" >
	    </td>
	  </tr>
    <tr >
      <td>batch: </td>
      <td align="left"><input name="batch" id="batch"  value="<?php echo $batch;?>" type="text" class="text" /></td>
    </tr>      
    <tr >
	  <td>Supplier: </td>
	  <td align="left"><input type="text" name="supplier" id="supplier" value="<?php echo $companyname;?>" class="text" autocomplete="off" /></td>
  </tr>   
	<tr >
	  <td colspan="2"  align="left">
           <div class="buttons">
            <button type="button" class="positive" onclick="addpurchase();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hideform();" class="negative">
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
<input type="hidden" name="orderid" value="<?php echo $orderid;?>" />
<input type="hidden" name="barcodeid" value="<?php echo $barcodeid;?>" />
<input type="hidden" name="supplierid" value ="<?php echo $fksupplierid;?>" />
<input type="hidden" name="fkshipmentid" value="<?php echo $fkshipmentid;?>" />
</form>
</div>