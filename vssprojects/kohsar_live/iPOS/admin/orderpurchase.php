<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['id'];
$orders	=	$AdminDAO->getrows("`order`,ordersupplier","distinct pkorderid,fkbrandid,barcode,itemdescription,description,quantity,lastsaleprice,pricelimit,agreedprice,comments","fkshipmentid='$id' AND fkorderid=pkorderid","fkbrandid","ASC");
?>
<script language="javascript">
$().ready(function() 
	{
		function findValueCallback(event, data, formatted) 
		{
			if(data[1]=='typesupplier')
			{
				document.getElementById('supplierid_'+data[3]).value=	data[2];
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
		total	=	document.getElementById('totalrecs').value;
		for(i=1;i<=total;i++)
		{
			$("#supplier_"+i).autocomplete("ordersupplierautocomplete.php", {extraParams: {cid: function() { return $("#ccid").val(); } }});
			$("#expiry_"+i).mask("99-99-9999");
		}
		
	});
function addpurchase()
{
	loading('System is saving data....');
	options	=	{	
					url : 'orderpurchaseaction.php?id='+'<?php echo $id?>',
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
		document.getElementById('scharges').innerHTML='';
		jQuery('#maindiv').load('manageshipmentinprocess.php');		
	}
	else
	{
		adminnotice(text,0,5000);	
	} 
}
function addnewsupplier(id)
{
	document.getElementById('addsupp'+id).style.display="block";
	document.getElementById('supplier_'+id).style.display="none";
	document.getElementById('addbrands'+id).style.display='none';
}
</script>
<div id="loading" class="loading" style="display:none;">
</div>
<div id="purchasediv">
<br />
<form name="purchaseform" id="purchaseform" style="width:920px;" class="form">
<fieldset>
<legend>
    Save Purchase
</legend>
<div style="float:right">
<span class="buttons">
            <button type="button" class="positive" onclick="addpurchase();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
            <button type="button" onclick="hidediv('purchasediv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </button>
          </span>
</div><br /><br />
<!--<table width="100%">
<tr>
<td width="10%">Default Product</td>
<td width="90%" align="left"><?php// $AdminDAO->dropdown("productid","product","pkproductid","productname");?></td>
</tr>
</table>-->
<table width="100%">
    <tr>
      <td height="6" valign="top"><div class="topimage2" style="height:6px;"><!-- --></div>
		<table width="100%" cellpadding="1" cellspacing="0">
            <tbody>
            <tr>
                <th>&nbsp;</th>
                <th align="left"><input type="checkbox" onclick="toggleChecked(this.checked)" id="chkAllreorder" name="chkAllreorder"></th>
                <th>Order ID</th>
                <th>Barcode</th>
                <th>Item</th>
                <th>Description</th>
                <th>Order Qty</th>
                <th>Purchased</th>
                <th>Purchase Qty<span style="color:#F00;">*</span></th>
                <th>Weight</th>
                <th>Suggested Supplier</th>
                <th>Actual Supplier</th>
                <th>Src Price</th>
                <th>Price Limit</th>
                <th>Agreed Price</th>
                <th>Price<span style="color:#F00;">*</span></th>
                <th>Batch</th>
                <th>Expiry</th>
                <th style="display:none;">Mark</th>
                <th>Comments</th>
            </tr>
            <?php
            for($i=0;$i<sizeof($orders);$i++)
            {
                $orderid			=	$orders[$i]['pkorderid'];
                $brandid			=	$orders[$i]['fkbrandid'];
                $barcode			=	$orders[$i]['barcode'];
                $itemdescription	=	$orders[$i]['itemdescription'];
				$description		=	$orders[$i]['description'];
                $quantity			=	$orders[$i]['quantity'];
                $sourceprice		=	$orders[$i]['lastsaleprice'];
                $pricelimit			=	$orders[$i]['pricelimit'];
                $agreedprice		=	$orders[$i]['agreedprice'];
                $comments			=	$orders[$i]['comments'];
                //fetching suppliers
                // selecting suppliers
                $suppliersarray		= 	$AdminDAO->getrows("supplier,ordersupplier","pksupplierid,companyname", "fkorderid='$orderid' AND fksupplierid=pksupplierid");
                /*echo "<pre>";
                print_r($suppliersarray);
                echo "</pre>";*/
                $suppliersel		=	"";
                $suppliersel2		=	"";
                $suppliersel		=	"<select name=\"suppliers[]\" id=\"suppliers\" style=\"width:60px;\">";
                for($s=0;$s<sizeof($suppliersarray);$s++)
                {
                    $suppliername	=	$suppliersarray[$s]['companyname'];
                    $supplierid		=	$suppliersarray[$s]['pksupplierid'];
                    $select			=	"";
                    if(@in_array($supplierid,$selected_suppliers))
                    {
                        $select = "selected=\"selected\"";
                    }
                    $suppliersel2	.=	"<option value=\"$supplierid\" $select>$suppliername</option>";
                }
                $suppliers			=	$suppliersel.$suppliersel2."</select>";
                // end suppliers
                // fetching previous purchases
                $purchaseqty		=	$AdminDAO->getrows("orderpurchase","IF(sum(quantity) IS NULL,0,sum(quantity)) qty","fkorderid='$orderid'");
                $purchased			=	$purchaseqty[0]['qty'];
                // fetch barcodeid
                $barcodeidarr		=	$AdminDAO->getrows("barcode","pkbarcodeid","barcode='$barcode'");
                $barcodeid			=	$barcodeidarr[0]['pkbarcodeid'];
            ?>
            <tr>
                <td><?php echo $i+1;?></td>
                <td><input type="checkbox" name="check<?php echo $i;?>" id="<?php echo $i;?>" value="1" class="checkbox" /></td>
                <td><?php echo $orderid;?><input type="hidden" name="brand[]" id="brand" value="<?php echo $brandid;?>" /></td>
                <td><?php echo $barcode;?><input type="hidden" name="barcode[]" id="barcode" value="<?php echo $barcode;?>" /><input type="hidden" name="barcodeid[]" id="barcodeid" value="<?php echo $barcodeid;?>" /></td>
                <td><?php echo $itemdescription;?><input type="hidden" name="itemdescription[]" id="itemdescription" value="<?php echo $itemdescription;?>" /></td>
                <td><?php echo $description;?><input type="hidden" name="description[]" id="description" value="<?php echo $description;?>" />
                </td>
                <td align="right"><?php echo $quantity;?><input type="hidden" name="quantity[]" id="quantity" value="<?php echo $quantity;?>" /></td>
                <td align="right"><?php echo $purchased;?></td>
                <td><input type="text" name="purchasequantity[]" id="purchasequantity" size="1" value="<?php echo $purchasequantity;?>" class="text" onkeypress="return isNumberKey(event);" /></td>
                <td><input type="text" name="weight[]" id="weight" size="1" value="<?php echo $weight;?>" class="text" onkeypress="return isNumberKey(event);" /></td>
                <td><?php echo $suppliers;?></td>
                <td><input type="text" name="supplier[]" id="supplier_<?php echo $i+1;?>" size="2" class="text" onfocus="document.getElementById('ccid').value='<?php echo $i+1;?>'" value="" title="<?php echo $suppliername;?>" /><input type="hidden" name="supplierid[]" id="supplierid_<?php echo $i+1;?>" value="" />
                <a class='button2' id='addbrands<?php echo $i+1;?>' href="javascript:addnewsupplier('<?php echo $i+1;?>');" title='Add New Supplier'><span class='addrecord'>&nbsp;</span></a>
                
                
                <div id="addsupp<?php echo $i+1;?>" style="display:none;"><input class="text" size="8" type="text" name="newsupp[]" id="newsupp<?php echo $i+1;?>" value="" /></div>
                
                </td>
                <td align="right"><?php echo $sourceprice;?></td>
                <td align="right"><?php echo $pricelimit;?></td>
                <td align="right"><?php echo $agreedprice;?></td>
                <td><input type="text" name="purchaseprice[]" id="purchaseprice" size="1" value="<?php echo $purchaseprice;?>" class="text" autocomplete="off" onkeypress="return isNumberKey(event);" /></td>
                <td><input type="text" size="1" name="batch[]" id="batch" value="<?php echo $batch;?>" class="text" /></td>
                <td><input type="text" size="8" name="expiry[]" id="expiry_<?php echo $i+1;?>" value="<?php echo $expiry;?>" class="text" /></td>
                <td style="display:none;"><input type="text" size="1" name="mark[]" id="mark" value="<?php echo $mark;?>" class="text" /></td>
                <td><?php echo $comments;?></td><input type="hidden" name="orderid[]" id="orderid" value ="<?php echo $orderid; ?>" />        
            </tr>
            <?php
            }
            ?>
            <tr >
              <td colspan="14"  align="left">
                   <div class="buttons">
                    <button type="button" class="positive" onclick="addpurchase();">
                        <img src="../images/tick.png" alt=""/> 
                        <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
                    </button>
                    <button type="button" onclick="hidediv('purchasediv');" class="negative">
                        <img src="../images/cross.png" alt=""/>
                        Cancel
                    </button>
                  </div>
                </td>				
              </tr>
            </tbody>
        </table>
		</td>
    </tr>
</table>
<input type="hidden" name="ccid" id="ccid" value="" />
<input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
<input type="hidden" name="totalrecs" id="totalrecs" value="<?php echo sizeof($orders);?>" />
</fieldset>
</form>
</div>