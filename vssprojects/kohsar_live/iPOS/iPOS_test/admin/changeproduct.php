<?php

include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$barcodeid 	=	$_REQUEST['id'];
?>
<script language="javascript">
function addform()
{
	loading('System is saving The Data....');
	options	=	{	
					url : 'changeproductaction.php',
					type: 'POST',
					success: response
				}
	jQuery('#productfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=="")
	{
		adminnotice('Product has been saved.',0,5000);
		jQuery('#maindiv').load('manageproducts.php');
		hidediv('items');
		hidediv('supplierdiv');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
jQuery().ready(function() 
{
		function findValueCallback(event, data, formatted) 
		{
			var barcode=document.getElementById('productid').value=data[1];
			//alert(barcode);
			//return false;
			//getitemdetails(document.getElementById('barcode1').value,1);
			//getinstance('instancediv',barcode);
			jQuery("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
		}
		function formatItem(row) 
		{
			return row[0] + " (<strong>id: " + row[0] + "</strong>)";
		}
		function formatResult(row) 
		{
			return row[0].replace(/(<.+?>)/gi,'');
		}
			jQuery("#productname").autocomplete("changeproductautocomplete.php") ;
			jQuery(":text, textarea").result(findValueCallback).next().click(function() 
			{
				$(this).prev().search();
			});
			jQuery("#clear").click(function() 
			{
				jQuery(":input").unautocomplete();
			});
			//document.adstockfrm.reset(); 
});
</script>
<div id="supplierdiv">
<form enctype="multipart/form-data" name="productfrm" id="productfrm" style="width:920px;" onSubmit="addform(); return false;" class="form">
<fieldset>
<legend>
    Move Item to Other Product	</legend>
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
		<td width="14%">Product Name : </td>
		<td width="86%"><div id="error1" class="error" style="display:none; float:right;"></div>
		<input name="productname" id="productname" type="text" onkeydown="javascript:if(event.keyCode==13) { return false;}"></td>
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
                Cancel            </a>          </div>        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="productid" id="productid"/>	
<input type="hidden" name="id" value ="<?php echo $barcodeid;?>" />	
</form>
</div>