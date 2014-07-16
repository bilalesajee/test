<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;

$saleid	=	$_GET['id'];
//end customers
$saledetail	=	$AdminDAO->getrows("$dbname_detail.saledetail sd left join $dbname_detail.stock st on (sd.fkstockid=pkstockid)left join $dbname_main.barcode b on (st.fkbarcodeid=pkbarcodeid)","b.itemdescription,sd.saleprice,sd.quantity,pksaledetailid","sd.fksaleid='$saleid'");
$customer_id	=	$AdminDAO->getrows("$dbname_detail.sale","fkaccountid,datetime,countername","pksaleid='$saleid'");
$query_cust 	= 	$AdminDAO->getrows("customer","pkcustomerid,CONCAT(firstname,' ', lastname) name","location=3 ");

?>

<script language="javascript">

jQuery(function($)
{	

$("#addtime").datepicker({dateFormat: 'dd-mm-yy'});

});

function addpurchasereturn()
{
	//loading('System is saving data....');
	options	=	{	
					url : 'update_sale_invoice.php',
					type: 'POST',
					success: response
				}
	jQuery('#quoteform').ajaxSubmit(options);
}
	
function response(text)
{
	if(text=='')
	{
		loading('Sale Invoice Edited...');
		jQuery('#maindiv').load('managecollections.php');
		document.getElementById('quotefrmdiv').style.display	=	'none';
		
	}
	else
	{
		document.getElementById('error').innerHTML		=	text;	
		document.getElementById('error').style.display	=	'block';
	}
}
function loaditeminfo(val,counter)
{

	$('#iteminfo').load('loaditem2.php?bc='+val+'&cntr='+counter);
}
$().ready(function() 
{
	document.getElementById('invoice_no').focus();
	function findValueCallback(event, data, formatted) 
	{
		document.getElementById('barcode').value=data[1];
		document.getElementById('itemdescription').value=data[0];
		document.getElementById('barcodeid').value=data[2];
		document.getElementById('btn2').focus();			
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
	
});
function autocomplete_call(itrator)
{$("#itemdescription_"+itrator).autocomplete("itemautocomplete.php") ;}
//////////////////////////////////////////////////////////////////////////////

function hideform()
{
	document.getElementById('quotefrmdiv').style.display	=	'none';	
}

function multiply(val,ct)
{
  
   var quantity = document.getElementById('quantity_'+ct).value;
   var price = document.getElementById('price_'+ct).value;
 
   document.getElementById('value_'+ct).value = quantity * price;
 
}


function submi(pkpurchasereturndetailid)
{
	//alert(pkpurchasereturndetailid);
	var data = 'pkpurchasereturndetailid='+pkpurchasereturndetailid;
	$.ajax({
  type: "POST",
  url: 'delete2.php',
  data: data,
  success: function(response) {  
    if(response == 'yes')
	{alert('Record Deleted successfully');}
	jQuery('#maindiv').load('addpurchase_return_inv.php?param=undefined&id='+'<?php echo $id; ?>');
    
  }  
  
});
	
}

</script>
<div id="iteminfo"  style="display:none;" ></div>
<div id="error" class="notice" style="display:none"></div>
<div id="quotefrmdiv" style="display: block;">
<br>
<form id="quoteform" style="width: 920px;" action="insertpurchase_return_inv.php?id=-1" class="form">
<fieldset>
<legend>
	<?php
    echo "Edit Sale Invoice";
    ?>
</legend>
<table cellpadding="0" cellspacing="2" width="100%">
  <tbody>
	<tr>
	  
	  <td colspan="2"><fieldset>
	    <legend> Bill No :<?php echo $saleid;?></legend>
	    <div id="iteminfo"  style="display:none;" ></div>
	    <div style="float:right"></div>
        <?php if($customer_id[0]['fkaccountid'] > 0){?>
           <table cellpadding="0" cellspacing="2" width="986">
	      <tbody>
          <tr >
	          <td ><b>Customer :   </b>  </td>
	          <td  style="width:900px"> <select id="cust" name="cust">   <?php for($i=0;$i<sizeof($query_cust);$i++){?>
              
              <option value="<?php echo $query_cust[$i]['pkcustomerid']?>" <?php if($query_cust[$i]['pkcustomerid']==$customer_id[0]['fkaccountid']){?> selected="selected" <?php }?>><?php echo $query_cust[$i]['name']?></option>
               <?php }?>
               </select>
               </td>
          </tbody></table>
          
          <?php }?>
	    <table cellpadding="0" cellspacing="2" width="986">
	      <tbody>
    
	        <?php 
			$ccount=count($saledetail);
	  for($i=0;$i<$ccount;$i++)
	{
	  ?>
	        <tr >
	          <td >Item:     </td>
	          <td  style="width:300px"><?php echo $saledetail[$i]['itemdescription']; ?></td>
              <td  >Quantity:</td>
	          <td  style="width:105px"><input name="quantity[]"  type="text"  readonly id="quantity_<?php echo $i;?>"   onchange="multiply(this.value,<?php echo $i;?>)" value="<?php echo $saledetail[$i]['quantity']?>" size="10" /></td>
	          <td >Price:</td>
	          <td  style="width:105px"><input name="price[]"  type="text" id="price_<?php echo $i;?>"   onchange="multiply(this.value,<?php echo $i;?>)" value="<?php echo $saledetail[$i]['saleprice']?>" size="10" /></td>
	          <td  >Amount:</td>
	          <td  style="width:105px"><input name="detail[value][]" readonly="readonly"   type="text"  id="value_<?php echo $i;?>" value="<?php echo $saledetail[$i]['saleprice']*$saledetail[$i]['quantity']?>" size="10" /></td>
	       <input type="hidden" name="detailid[]" id="detailid_<?php echo $i;?>" value="<?php echo $saledetail[$i]['pksaledetailid']; ?>" />
           
          
	          </tr>
	        <?php } ?>
             <input type="hidden" name="saleid"  value="<?php echo $saleid; ?>" />
             <input type="hidden" name="billdate"  value="<?php echo $customer_id[0]['datetime']; ?>" />
               <input type="hidden" name="countr"  value="<?php echo $customer_id[0]['countername']; ?>" />
	        <tr>
	          <td colspan="11" align="left">
	            <div class="buttons">
	              <button type="button" class="positive" onclick="addpurchasereturn();">
	                <img src="../images/tick.png" alt=""/> 
	                <?php echo "Update"; ?>
	                </button>
                     <a href="javascript:void(0);" onclick="hidediv('quotefrmdiv');" class="negative">
	                <img src="../images/cross.png" alt=""/>
	                Cancel
	                </a>
	              </div></td>
	          </tr>
	        </tbody>
	      </table>
	    </fieldset></td>
	  </tr>
  
	<tr>
	  <td colspan="2" align="center">
        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
</form>
</div>
