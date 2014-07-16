<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id	=	$_GET['id'];
//select products
$productsarr	=	$AdminDAO->getrows("product","pkproductid,productname","productstatus='a'");
$p1				=	"<select name=\"product[]\" id=\"product[]\" style=\"width:120px;\">";
for($i=0;$i<sizeof($productsarr);$i++)
{
	$p2		.=	"<option value = \"".$productsarr[$i][pkproductid]."\">".$productsarr[$i][productname]."</option>";
}
$products	=	$p1.$p2;
// products
$bgcolor=	'#DFEFFF';
?>
<script language="javascript">
function insertpurchase(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insertpurchase.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#purchasefrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Items purchased and packed successfully.',0,5000);
		jQuery('#subsection').load('purchaseitems.php?param=undefined&id='+'<?php echo $id; ?>');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>
<div id="loaditemscript"> </div>
<div id="error" class="notice" style="display:none"></div>
<div id="shipfrmdiv" style="display: block;"> <br>
  <form id="purchasefrm" style="width: 920px;" action="insertconsignmentitem.php?id=-1" class="form">
    <fieldset>
      <legend>
      <?php
     echo "Purchase Items for >> $shipmentname";	
    ?>
      </legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="insertpurchase(-1);"> 
        <img src="../images/tick.png" alt=""/>
        <?php echo "Save"; ?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span> </div>
      <table width="100%">
        <tr>
            <td colspan="12">Default Product:&nbsp;<?php echo $products;?></td>
        </tr>
        <tr>
          <td height="10" valign="top"><div class="topimage2" style="height:6px;">
              <!-- -->
            </div>
              <table cellpadding="2" cellspacing="0" width="100%">
              <tr>
              	<th>&nbsp;</th>
                <th>Supplier</th>
<!--                <th>Box Barcode</th>-->
                <th>Barcode</th>
                <th>Item</th>
                <th>Weight</th>
                <th>Last Purchase Price</th>
                <th>Purchase Price</th>
                <th>Batch</th>
                <th>Qty</th>
                <th>Box No</th>
                <th>Items</th>
                <th>Expiry</th>
              </tr>
              <?php
			  // selecting items for purchase
			  $purchase	=	"SELECT
								sd.fkshiplistid,
								sd.pkshiplistdetailsid,
								shipmentcurrency,
								exchangerate,
			  					barcode,
								itemdescription,
								shiplist.weight,
								SUM(sd.quantity) quantity,
								lastpurchaseprice,
								currencysymbol,
								(SELECT GROUP_CONCAT(fksupplierid) FROM shiplistsupplier sl WHERE fkshiplistid=pkshiplistid) as suppliers,
								CONCAT(firstname,' ',lastname) name
							FROM
								shiplist LEFT JOIN currency ON (pkcurrencyid=fkcurrencyid),shiplistdetails sd,addressbook,shipment
							WHERE
								sd.fkshipmentid		=	'$id' AND
								sd.fkshipmentid		=	pkshipmentid AND
								fkshiplistid		=	pkshiplistid AND
								sd.fkaddressbookid	=	pkaddressbookid
							GROUP BY
								pkshiplistid
			  				";
			  $purchaseresults	=	$AdminDAO->queryresult($purchase);
			  $purchaselen		=	sizeof($purchaseresults);
			  for($i=0;$i<sizeof($purchaseresults);$i++)
			  {
				$suppliers			=	"";
				$suppliersarr		=	$purchaseresults[$i]['suppliers'];
				$shiplistid			=	$purchaseresults[$i]['fkshiplistid'];
				$shipcurrency		=	$purchaseresults[$i]['shipmentcurrency'];
				$exchangerate		=	$purchaseresults[$i]['exchangerate'];
				$shiplistdetailsid	=	$purchaseresults[$i]['pkshiplistdetailsid'];
				$barcode			=	$purchaseresults[$i]['barcode'];
				$itemdescription	=	$purchaseresults[$i]['itemdescription'];
				$quantity			=	$purchaseresults[$i]['quantity'];
				$weight				=	$purchaseresults[$i]['weight'];
				$purchaseprice		=	$purchaseresults[$i]['lastpurchaseprice'];
				$currencysymbol		=	$purchaseresults[$i]['currencysymbol'];
				$description		=	$purchaseresults[$i]['description'];
				$name				=	$purchaseresults[$i]['name'];
				// 1. selecting suppliers
				$selected_suppliers	=	explode(",",$suppliersarr);
				$supplierarr		=	$AdminDAO->getrows("supplier","pksupplierid,companyname","supplierdeleted<>1");
				$suppliersel		=	"<select name=\"supplierid[]\" id=\"supplierid\" style=\"width:65px;\" >";
				for($j=0;$j<sizeof($supplierarr);$j++)
				{
					$suppliername	=	$supplierarr[$j]['companyname'];
					$supplierid		=	$supplierarr[$j]['pksupplierid'];
					$select			=	"";
					if(in_array($supplierid,$selected_suppliers))
					{
							$select = "style=\"font-weight:bold;background-color:#39F;\"";
					}
					$suppliersel2	.=	"<option value=\"$supplierid\" $select>$suppliername</option>";
				}
				$suppliers			=	$suppliersel.$suppliersel2."</select>";
				if($bgcolor	==	'#DFEFFF')
				{
					$bgcolor	=	'';
				}
				else
				{
					$bgcolor	=	'#DFEFFF';
				}
			  ?>
              <tr bgcolor="<?php echo $bgcolor;?>">
              	<td><input type="checkbox" name="check<?php echo $i;?>" value="1" /></td>
				<td><?php echo $suppliers;?><input type="hidden" id="shiplistid" name="shiplistid[]" value="<?php echo $shiplistid;?>" /><input type="hidden" id="shiplistdetailsid" name="shiplistdetailsid[]" value="<?php echo $shiplistdetailsid;?>" /><input type="hidden" id="shipcurrency" name="shipcurrency[]" value="<?php echo $shipcurrency;?>" /><input type="hidden" id="exchangerate" name="exchangerate[]" value="<?php echo $exchangerate;?>" onkeypress="return isNumberKey(event)" /></td>
<!--                <td><input name="boxbarcode[]" type="text" value="" size="8" ></td>-->
                <td><input name="barcode[]" type="text" value="<?php echo $barcode;?>" size="8" /></td>
                <td><input name="itemdescription[]" type="text" value="<?php echo $itemdescription;?>" size="10" /></td>
                <td><input name="weight[]" type="text" value="<?php echo $weight;?>" size="5" onkeypress="return isNumberKey(event)" /></td>
                <td><input name="lasttradeprice[]" type="text" value="<?php echo $purchaseprice;?>" size="10" onkeypress="return isNumberKey(event)" /></td>
                <td><input name="purchaseprice[]" type="text" value="" size="10" onkeypress="return isNumberKey(event)" /></td>
                <td><input name="batch[]" type="text" value="" size="3" onfocus="this.select()" /></td>
                <td><input name="quantity[]" type="text" value="<?php echo $quantity;?>" size="3" /></td>
                <td><input name="box[]" type="text" value="" size="3" /></td>
                <td><input name="boxtotal[]" type="text" value="" size="3" /></td>
                <td><input name="expiry[]" id="expiry<?php echo $i+1;?>" type="text" value="" size="8" /></td>
              </tr>
              <?php
			  }
              ?>
              <tr>
                      <td colspan="13" align="center"><div class="buttons">
                          <button type="button" class="positive" onclick="insertpurchase(-1);"> <img src="../images/tick.png" alt=""/>
                          <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
                          </button><input type="hidden" id="shipmentid" name="shipmentid" value="<?php echo $id;?>" />
                          <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div></td>
              </tr>
            </table>
		  </td>
        </tr>
      </table>
	  <div id="stockdetails">
	  </div>
    </fieldset>
  </form>
</div>
<!--<div id="<?php //echo $div;?>">
	<div class="breadcrumbs" id="breadcrumbs">Purchased Items</div>
	<?php 
	//grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
	?>
</div>-->
<script language="javascript" type="text/javascript">
for(j=1;j<=<?php echo $purchaselen;?>;j++)
{
	$('#expiry'+j).mask("99-99-9999");
	$('#deadline'+j).mask("99-99-9999");
}
</script>