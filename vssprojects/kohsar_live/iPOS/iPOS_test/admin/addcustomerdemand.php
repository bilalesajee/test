<?php
include_once("../includes/security/adminsecurity.php");
global $adminDAO,$Component;
$demandid		=	$_GET['id'];
$demandrow		= 	$AdminDAO->getrows("customerdemands","*"," pkcustomerdemandsid	=	'$demandid'");
$productname					=	$demandrow[0]['productname'];
$advance						=	$demandrow[0]['advance'];	
$deadline						=	$demandrow[0]['deadline'];
$customerinfo					=	$demandrow[0]['customerinfo'];
$description					=	$demandrow[0]['description'];
// geting shipment groupid from shipmentgroupjunc

?>
<script language="javascript" type="text/javascript">
	
function addcustomerdemands()
{
	    loading('Syetem is Saving The Data....');
		options	=	{	
						url : 'insertcustomerdemand.php',
						type: 'POST',
						success: demandresponse
					}
		jQuery('#customerdemandfrm').ajaxSubmit(options);
}
function demandresponse(res)
{
	//alert(res);
	if(res!='')
	{
		
		adminnotice(res,0,8000);
		//jQuery('#maindiv').load('customerdemands.php?qs='+'<?php echo $qs;?>');
		
	}else
	{
		jQuery('#sugrid').load('customerdemands.php?qs='+'<?php echo $qs;?>');
		
	}
}


jQuery().ready(function() 
	{
		
		$("#deadline").datepicker({dateFormat: 'yy-mm-dd'});
		function findValueCallback(event, data, formatted) 
		{
			document.getElementById('deadline').focus();
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
			jQuery("#productname").autocomplete("productautocomplete.php") ;
			
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
<div id="catdiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form  name="customerdemandfrm" id="customerdemandfrm" style="width:650px;" onSubmit="addcustomerdemands(); return false;" action= method="post">
<fieldset>
<legend>
    <?php if($demandid=='-1'){echo 'Add';}else{echo 'Update';}?> Customer Demand
</legend>
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	<tr class="odd">
		<td width="30%">Product  Name: </td>
		<td width="23%"><input name="productname" id="productname" type="text" value="<?php echo $productname?>"onkeydown="javascript:if(event.keyCode==13) { return false;}" autocomplete="off" /></td>
		<td width="68%"><div id="error1" class="error" style="display:none; float:right;"></div></td>
	</tr>
	<tr class="even">
	  <td> Deadline: </td>
	  <td><input name="deadline" id="deadline" type="text" value="<?php if($deadline===''){echo $deadline;}else{echo date('Y-m-d');}?>" onkeydown="javascript:if(event.keyCode==13) {addcustomerdemands(); return false;}" /></td>
	  <td><div id="error3" class="error" style="display:none; float:right;"></div></td>
	  </tr>
	<tr class="even">
	  <td> Advance Deposit: </td>
	  <td><input name="advance" id="advance" type="text" value="<?php echo $advance;?>" onkeydown="javascript:if(event.keyCode==13) { return false;}" onkeypress="return isNumberKey(event)"/></td>
	  <td><div id="error4" class="error" style="display:none; float:right;"></div></td>
	  </tr>
	<tr class="even">
	  <td> Customer Information: </td>
	  <td><textarea name="customerinfo"><?php echo $customerinfo;?></textarea></td>
	  <td><div id="error5" class="error" style="display:none; float:right;"></div></td>
	  </tr>
	<tr class="even">
	  <td> Description: </td>
	  <td><textarea name="description" ><?php echo $description;?></textarea></td>
	  <td><div id="error2" class="error" style="display:none; float:right;"></div></td>
	  </tr>
	

      <tr class="even">
        <td colspan="3"  align="center"><input type="submit" value="<?php if($demandid=='-1'){echo 'Save';}else{echo 'Update';}?>" />
          <input name="demandid" type="hidden" value="<?php echo $demandid;?>" />
          <input name="submit2" type="button" value="Cancel" onclick="hidediv('customerdemandfrm')" /></td>
      </tr>
    </tbody>
  </table>
  </fieldset>
</form>
</div>
<script language="javascript">
	document.getElementById('productname').focus();
</script>