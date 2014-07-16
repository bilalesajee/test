<?php
include("../includes/security/adminsecurity.php");
include("../includes/classes/commonfunctions.php");
require_once("../OpenCrypt/ajax_tree.php");
global $AdminDAO;
$id	=	$_GET['id'];
$categories 	= 	$AdminDAO->getrows("category","*"," 1");
$menu			=	array();
if($id!='')
{
	$catdata		=	$AdminDAO->getrows("purchase
										   LEFT join barcode on fkbarcodeid=pkbarcodeid
										   ","itemdescription, barcode,pkpurchaseid, fkshiplistid, fkshiplistdetailsid, fkbarcodeid,fksupplierid ,
										   fkshipmentid, 	fkaddressbookid,fkcurrencyid 	,
										   purchasetime 	,quantity 	,purchaseprice 	,
										   currencyrate 	,weight 	 	,batch 	,
										   expiry 	",
										   " pkpurchaseid = '$id'");
	
	$item			=	$catdata[0]['itemdescription'];
	$barcode		=	$catdata[0]['barcode'];
	$expiry			=	$catdata[0]['expiry'];
	$expiry			=	implode("-",array_reverse(explode("-",$expiry)));
	$weight			=	$catdata[0]['weight'];
	$quantity		=	$catdata[0]['quantity'];	
	$purchaseprice	=	$catdata[0]['purchaseprice'];
	$purchasetime	=	$catdata[0]['purchasetime'];
	$batch			=	$catdata[0]['batch'];	
	$fksupplierid	=	$catdata[0]['fksupplierid'];
	$fkshipmentid	=	$catdata[0]['fkshipmentid'];
}
?>
<script src="../includes/js/ajaxfileupload.js" type="text/javascript"></script>
<script language="javascript">

jQuery(function($){
	$("#expiry").mask("99-99-9999");	
	
});

function addpur()
{
	loading('System is saving data....');
	options	=	{	
					url : 'updatepurchase.php?id='+'<?php echo $id?>',
					type: 'POST',
					success: response
				}	
	jQuery('#catform').ajaxSubmit(options);
}
function response(text)
{

	if(text=='')
	{
		adminnotice('Purchase data has been saved.',0,5000);
		jQuery('#subsection').load('managepurchases.php?id='+'<?php echo $fkshipmentid?>&param=undefined');		
	}
	else
	{
		adminnotice(text,0,5000);	
	} 
}
function hideform()
{
	
	document.getElementById('catdiv').style.display='none';
}

</script>
<div id="loading" class="loading" style="display:none;">
</div>
<div id="catdiv">
<br />
<form enctype="multipart/form-data" name="catform" id="catform" style="width:920px;" class="form">
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
            <button type="button" class="positive" onclick="addpur();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('catdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </span>
</div>          
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	
	<tr height="30px">
	  <td>Barcode : </td>
	  <td><?php echo $item;?></td>
	  </tr>
	<tr  height="30px">
		<td>Item : </td>
		<td><?php echo $barcode;?></td>
	</tr>
    <tr >
	  <td>Expiry: </td>
	  <td><input  style="width:100px;" name="expiry" size="45" id="expiry" type="text" value="<?php echo $expiry;?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
		<td>Weight: </td>
		<td><input   style="width:100px;" name="weight" size="45" id="weight" type="text" value="<?php echo $weight;?>" ></td>
	</tr>
    
    
	<tr >
	  <td>Quantity: </td>				
	  <td><input  style="width:100px;" name="quantity" size="45" id="quantity" type="text" value="<?php echo $quantity;?>" /></td>
	  </tr>
	<tr >
	  <td>Price: </td>
	  <td><input   style="width:100px;" name="purchaseprice" size="45" id="purchaseprice" type="text" value="<?php echo $purchaseprice;?>" >
	    </td>
	  </tr>
      
    <tr >
      <td>batch: </td>
      <td><input  style="width:100px;" name="batch" size="45" id="batch"  value="<?php echo $batch;?>"type="text">        
        <input type="hidden" name="fkshipmentid" id="fkshipmentid" value="<?php echo $fkshipmentid;?>"  />        
        </td>
    </tr>      
    
    
    <tr >
	  <td>Supplier: </td>
	  <td><?php echo getallsupplier($fksupplierid);?>
	    </td>
  </tr>   
	<tr >
	  <td colspan="2"  align="left">
           <div class="buttons">
            <button type="button" class="positive" onclick="addpur();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('catdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type=hidden name="id" value ="<?php echo $id; ?>" />	
</form>
</div>
<script language="javascript">
	focusfield('expiry');
</script>