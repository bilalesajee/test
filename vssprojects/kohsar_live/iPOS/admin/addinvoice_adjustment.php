<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;

$id	=	$_GET['id'];
$qstring=$_SESSION['qstring'];
if($id =="-1")
{
$sql			=	"SELECT max(pkadjustmentid) as pkadjustmentid  from $dbname_detail.adjustment ";
   $result2			=	$AdminDAO->queryresult($sql);
   
 $pkadjustmentid	=	$result2[0]['pkadjustmentid']+1;
} 
else
{
	$adjustment = $AdminDAO->getrows("$dbname_detail.adjustment","pkadjustmentid,FROM_UNIXTIME(addtime,'%d-%m-%Y') as addtime,remarks,fksupplierid,status"," pkadjustmentid='$id'");
	
	$supplierid 	= 	$adjustment[0]['fksupplierid'];
	$addtime		=	$adjustment[0]['addtime'];
	$remarks			=	$adjustment[0]['remarks'];
	$status			=	$adjustment[0]['status'];
	
 $pkadjustmentid			=	$adjustment[0]['pkadjustmentid'];
	
   
  
	
	
	$res				=	$AdminDAO->getrows("$dbname_detail.adjustment_detail,barcode","pkpurchasereturndetailid,barcode,itemdescription,quantity,price,value","fkbarcodeid=pkbarcodeid AND fkadjustmentid='$id'");
/*$res="select r.* from $dbname_detail.purchase_return_detail p 

left join barcode b on b.pkbarcodeid = p.fkbarcodeid
left join barcode s on s.pksupplierid = p.fksupplierid
where p.pkpurchasereturndetailid='$id'
 "*/

	 $fkbarcodeid			=	$res[0]['fkbarcodeid'];
	 $itemdescription	=	$res[0]['itemdescription'];
	$quantity	=	$res[0]['quantity'];
	$price			=	$res[0]['price'];
	$value				=	$res[0]['value'];
	$pkpurchasereturndetailid				=	$res[0]['pkpurchasereturndetailid'];
	

}

//selecting supplier
$suppliersarray		= 	$AdminDAO->getrows("supplier","*", "supplierdeleted<>1 order by companyname");
$firstsupplierid	=	$suppliersarray[0]['pksupplierid'];
$d1		=	"<select name=\"supplier\" id=\"supplier\" style=\"width:150px;\" onchange=\"getinvoices(this.value);\" >";
for($i=0;$i<sizeof($suppliersarray);$i++)
{
	
		$select		=	"";
	if($supplierid==$suppliersarray[$i]['pksupplierid'])
	{
		$select	=	"selected=\"selected\"";
	}
	$d2			.=	"<option value = \"".$suppliersarray[$i]['pksupplierid']."\" $select>".$suppliersarray[$i]['companyname']."</option>";
	
	
}
$suppliers			=	$d1.$d2."</select>";


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//selecting supplier
$invarr		= 	$AdminDAO->getrows("$dbname_detail.supplierinvoice","*", "invoice_status=1 order by pksupplierinvoiceid desc");
$firstinvid	=	$invarr[0]['pksupplierinvoiceid'];

//end customers




//end customers
?>

<script language="javascript">

jQuery(function($)
{	

$("#addtime").datepicker({dateFormat: 'dd-mm-yy'});

});

function addpurchasereturn(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insertadjstment_inv.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#quoteform').ajaxSubmit(options);
}
	
function response(text)
{
	if(text=='')
	{
		loading('Purchase Return  Saved...');
		jQuery('#maindiv').load('managepurchasereturn_without_invoices.php?param=undefined&id='+'<?php echo $id; ?>');
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
///////////////////////////////////////HERE IS FUNCTION///////////////////////////////////////////
function getstock(invid){
	
	alert(invid);
	options	=	{	
					dataType: 'json',  
					url : 'getbarcode_inv.php?id='+invid,
					type: 'post',
					success: responsebar
				}
	jQuery('#tquoteform').ajaxSubmit(options);
	
	}
	function responsebar(barar){
	var obj = jQuery.parseJSON(barar);
alert(obj.pkstockid);
				}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
</script>
<div id="iteminfo"  style="display:none;" ></div>
<div id="error" class="notice" style="display:none"></div>
<div id="quotefrmdiv" style="display: block;">
<br>
<form id="tquoteform" >
</form>
<form id="quoteform" style="width: 920px;" action="insertadjstment_inv.php?id=-1" class="form">
<fieldset>
<legend>
	<?php
    if($id!="-1")
    { echo "Adjust Invoices"." ";}
    else
    { echo "Add  Invoice for Adjustment";}	
    ?>
</legend>
<table cellpadding="0" cellspacing="2" width="100%">
  <tbody>
	
	<!--<tr>
	  <td>Purchaseid:</td>
	  <td><input name="pkadjustmentid" id="pkadjustmentid" type="text" disabled="disabled" size="5" value="<?php// echo $pkpurchasereturnid; ?>" >&nbsp;</td>
	  </tr>-->
      <tr>
		<td>Invoices:</td>
		<td><select name="inv" id="inv" style="width:150px;" onchange="getstock(this.value);" >
<option >Select Invoice</option>
<?php for($i=0;$i<sizeof($invarr);$i++){?>
	<option value = "<?php echo $invarr[$i]['pksupplierinvoiceid']?>" ><?php echo $invarr[$i]['pksupplierinvoiceid']?></option>
<?php } ?>
</select></td>
	</tr>
	
    <tr>
		<td>Date:</td>
		<td><input type="text" id="addtime" name="addtime" size="10" maxlength="10" value="<?php echo $addtime;?>" /></td>
	</tr>
  
    <tr>
		<td height="37">Remarks:</td>
		<td><textarea id="remarks" name="remarks"><?php echo $remarks;?></textarea></td>
	</tr>
	<tr>
	  
	  <td colspan="2"><fieldset>
	    <legend> Detail</legend>
	    <div id="iteminfo"  style="display:none;" ></div>
	    <div style="float:right"></div>
	    <table cellpadding="0" cellspacing="2" width="986">
	      <tbody>
	        <?php 
	  for($i=0;$i<=10;$i++)
	{
	  ?>
	        <tr >
	          <td >Barcode: </td><input type="hidden" name="detail[pkpurchasereturndetailid][]" id="pkpurchasereturndetailid_<?php echo $i;?>" value="<?php echo $res[$i]['pkpurchasereturndetailid']; ?>" />
	          <td style="width:105px"><input type="text" class="text" id="barcode" name="detail[barcode][]"  onkeydown="javascript:if(event.keyCode==13) {loaditeminfo(this.value,<?php echo $i;?>); return false;}" value="<?php echo $res[$i]['barcode'];?>" onfocus="this.select();" /></td>
	          <td  >Description:</td>
	          <td width="105" style="width:105px"><input name="detail[itemdescription][]" type="text" class="text" id="itemdescription_<?php echo $i;?>" onfocus="this.select();"  value="<?php echo $res[$i]['itemdescription']?>" onkeyup="autocomplete_call(<?php echo $i;?>)" size="37" autocomplete="off" /></td>
	          <td  >Quantity:</td>
	          <td  style="width:105px"><input name="detail[quantity][]"   type="text" id="quantity_<?php echo $i;?>" onchange="multiply(this.value,<?php echo $i;?>)" value="<?php echo $res[$i]['quantity']?>" size="10" /></td>
	          <td >Price</td>
	          <td  style="width:105px"><input name="detail[price][]"  type="text" id="price_<?php echo $i;?>" onchange="multiply(this.value,<?php echo $i;?>)" value="<?php echo $res[$i]['price']?>" size="10" /></td>
	          <td  >Total:</td>
	          <td  style="width:105px"><input name="detail[value][]"  readonly="readonly" type="text"  id="value_<?php echo $i;?>" value="<?php echo $res[$i]['value']?>" size="10" /></td>
	          <td  style="width:105px"><?php if($status==0){?><button type="button" class="positive" onclick="submi(<?php echo $pkpurchasereturndetailid;?>);">Delete </button><?php }?></td>
	          </tr>
	        <?php } ?>
	        <tr>
	          <td colspan="11" align="left">
	            <div class="buttons">
                <?php if($status==0){?>
	              <button type="button" class="positive" onclick="addpurchasereturn(-1);">
	                <img src="../images/tick.png" alt=""/> 
	                <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
	                </button>
                    <?php } ?>
	              <a href="javascript:void(0);" onclick="hidediv('quotefrmdiv');" class="negative">
	                <img src="../images/cross.png" alt=""/>
	                Cancel
	                </a>
	              </div></td>
	          </tr>
	        </tbody>
	      </table>
	    <p>
         <input type="hidden" name="id" id="id" value ="<?php echo $id;?>"/>
	      <input type="hidden" name="barcodeid" id="barcodeid" value="" />
          
	      </p>
	    <p>&nbsp;</p>
	    </fieldset></td>
	  </tr>
  
	<tr>
	  <td colspan="2" align="center">
<!--	    <input value="Save" onclick="addnote(-1)" type="button"><input name="btnsubmit" value="Cancel" onclick="hideform()" type="button">-->
      <!--  <div class="buttons">
            <button type="button" class="positive" onclick="addpurchasereturn(-1);">
                <img src="../images/tick.png" alt=""/> 
                <?php// if($id=='-1') {echo "Save";} else {echo "Update";} ?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('quotefrmdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>-->
        </td>				
	  </tr>
	</tbody>
</table>
<input type="hidden" name="detailid" id="detailid" value ="<?php if($detailid) {echo $detailid;} else {echo "-1";}?>"/>
<input type="hidden" name="id" value ="<?php echo $id;?>" />
<input type="hidden" name="pkpurchasereturndetailid" value ="<?php echo $pkpurchasereturndetailid;?>" />
<input type="hidden" name="pkadjustmentid" value ="<?php echo $pkadjustmentid;?>" />
</fieldset>	
</form>
</div>
