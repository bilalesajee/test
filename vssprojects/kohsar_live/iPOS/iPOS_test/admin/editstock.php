<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$stockid = $_GET['id'];
$qs	=	$_SESSION['qstring'];
$stocksarray	=	$AdminDAO->getrows("$dbname_detail.stock","*"," pkstockid = '$stockid'");
$barcodeid		=	$stocksarray[0]['fkbarcodeid'];
$storeid		=	$stockarray[0]['fkstoreid'];
$brandid		=	$stocksarray[0]['fkbrandid'];
$supplierid		=	$stocksarray[0]['fksupplierid'];
if($id!='-1')
{
	/********************************COUNTRIES***********************************/
	$selected_brand	=	$AdminDAO->getrows('brand','*', "pkbrandid = '$brandid'");
	$selectedbrand	=	array($selected_brand[0]['pkbrandid']);
	$brands_array	=	$AdminDAO->getrows('brand, barcodebrand, countries',"pkbrandid, CONCAT(brandname,' ',countryname) AS brandname", "branddeleted <> 1 AND fkcountryid=pkcountryid AND fkbrandid=pkbrandid AND fkbarcodeid='$barcodeid'");
	$brands			=	$Component->makeComponent('d','brands',$brands_array,'pkbrandid','brandname',1,$selectedbrand);
	/***********************************************SUPPLIER***********************************/
	$selected_supplier	=	$AdminDAO->getrows('supplier','*', "pksupplierid = '$supplierid'");
	$selectedsupplier	=	array($selected_supplier[0]['pksupplierid']);
	
	$suppliers_array	=	$AdminDAO->getrows('supplier','*', ' supplierdeleted <> 1');
	$suppliers			=	$Component->makeComponent('d','suppliers',$suppliers_array,'pksupplierid','companyname',1,$selectedsupplier);
}
/****************************************************************************/
//$expirydate			=	$stocksarray[0]['expiry'];
$expirydate			=	fdate($stocksarray[0]['expiry']);
?>
<script language="javascript">
jQuery(function($)
 {
	$("#stockdate").datepicker({dateFormat: 'yy-mm-dd'});
 });
function addform()
{
	loading('System is Saving The Data....');
	options	=	{	
					url : 'editstockdetail.php',
					type: 'POST',
					success: response
				}
	jQuery('#stockfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Stock data has been saved.',0,5000);
		jQuery('#maindiv').load('managestocks.php','',function(){loaddetails();})
	}
	else
	{
		adminnotice(text,0,5000);	
	}
	//hideform();
}
function loaddetails()
{
	jQuery('#stockdetailsdiv').load('stockdetail.php?nobrand=-1&id=<?php echo $barcodeid;?>');	
}
function hideform()
{
	document.getElementById('editstock').style.display='none';
}
</script>
<?php
?>
<div id="editstock">
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="stockfrm" id="stockfrm" onSubmit="addform(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
Edit Stock
</legend>
<div style="float:right">
<span class="buttons">
<button type="button" class="positive" onclick="addform();">
    <img src="../images/tick.png" alt=""/> 
    <?php if($shipmentid=='-1'){echo 'Save';}else{echo 'Update';}?>
</button>
 <a href="javascript:void(0);" onclick="hidediv('editstock');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
</a>
</span>
</div>
<table width="529">
	<tbody>
	<tr>
	  <td>Brand: </td>
	  <td valign="top"><?php echo $brands; ?></td>
	  </tr>
	<tr>
	  <td>Supplier: </td>
	  <td valign="top"><?php echo $suppliers;?></td>
	  </tr>
	<tr>
	  <td>Batch: </td>
	  <td valign="top"><input name="batch" id="batch" type="text" value="<?php echo $stocksarray[0]['batch']; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" ></td>
	  </tr>
	<tr>
	  <td width="135">Total Units: </td>
	  <td width="382" colspan="2"><input name="totalunits" id="totalunits" type="text" value="<?php echo $stocksarray[0]['quantity']; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"></td>
	  </tr>
	<tr>
		<td>Remaining Units: </td>
		<td colspan="2"><input name="remainingunits" id="remainingunits" type="text" value="<?php echo $stocksarray[0]['unitsremaining']; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"></td>
	</tr>
    <tr>
      <td>Trade Price: </td>				
      <td valign="top"><input name="purchaseprice" id="purchaseprice" type="text" value="<?php echo $stocksarray[0]['purchaseprice']; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" ></td>
    </tr>
    <tr>
		<td>Price (Rs): </td>				
		<td valign="top"><input name="priceinrs" id="priceinrs" type="text" value="<?php echo $stocksarray[0]['priceinrs']; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" ></td>
	</tr>
    <tr>
		<td>Shipment Charges: </td>				
		<td valign="top"><input name="shipmentcharges" id="shipmentcharges" type="text" value="<?php echo $stocksarray[0]['shipmentcharges']; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" ></td>
	</tr>
    <tr>
		<td>Cost Price: </td>				
		<td valign="top"><input name="costprice" id="costprice" type="text" value="<?php echo $stocksarray[0]['costprice']; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" ></td>
	</tr>
    <tr>
		<td>Retail Price: </td>				
		<td valign="top"><input name="retailprice" id="retailprice" type="text" value="<?php echo $stocksarray[0]['retailprice']; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" ></td>
	</tr>
	<tr>
	  <td>Expiry Date:</td>
	  <td valign="top"><input name="stockdate" id="stockdate" type="text" value="<?php echo $expirydate; ?>" readonly="readonly"></td>
	  </tr>
	<tr>
	  <td colspan="3"  align="left">
           <div class="buttons">
            <button type="button" class="positive" onclick="addform();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($shipmentid=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('editstock');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
	    </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="stockid" value = <?php echo $stockid;?> />	
</form>
</div>
<script language="javascript">
document.getElementById('batch').focus();
loading('Loading Form...');
</script>