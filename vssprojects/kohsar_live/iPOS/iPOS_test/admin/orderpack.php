<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['id'];
$orders	=	$AdminDAO->getrows("`order`,ordersupplier","distinct pkorderid,fkbrandid,barcode,itemdescription,description,quantity,lastsaleprice,pricelimit,agreedprice,comments","fkshipmentid='$id' AND fkorderid=pkorderid");
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
		//total	=	document.getElementById('totalrecs').value;
		//for(i=1;i<=total;i++)
		//{
		//	$("#supplier_"+i).autocomplete("ordersupplierautocomplete.php", {extraParams: {cid: function() { return $("#ccid").val(); } }});
		//}
		//$("#expiry").mask("99-99-9999");
	});
function addpack()
{
	loading('System is saving data....');
	options	=	{	
					url : 'orderpackaction.php?id='+'<?php echo $id?>',
					type: 'POST',
					success: response
				}	
	jQuery('#packform').ajaxSubmit(options);
}
function response(text)
{

	if(text=='')
	{
		adminnotice('Packing data has been saved.',0,5000);
		document.getElementById('subsection').innerHTML='';
		jQuery('#maindiv').load('manageshipmentclosed.php');		
	}
	else
	{
		adminnotice(text,0,5000);	
	} 
}
</script>
<div id="loading" class="loading" style="display:none;">
</div>
<div id="packdiv">
<br />
<form name="packform" id="packform" style="width:920px;" class="form">
<fieldset>
<legend>
  Save Packing
</legend>
<div style="float:right">
<span class="buttons">
            <button type="button" class="positive" onclick="addpack();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
            <button type="button" onclick="hidediv('packdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </button>
          </span>
</div><br /><br />
<table width="100%">
    <tr>
      <td height="6" valign="top"><div class="topimage2" style="height:6px;"><!-- --></div>
		<table width="100%" cellpadding="1" cellspacing="0">
            <tbody>
            <tr>
                <th>&nbsp;</th>
                <th align="left"><input type="checkbox" onclick="toggleChecked(this.checked)" id="chkAllreorder" name="chkAllreorder"></th>
                <th align="left">Order ID</th>
                <th align="left">Barcode</th>
                <th>Item</th>
                <th align="right">Order Qty</th>
                <th align="right">Purchased</th>
                <th align="right">Packed</th>
                <th align="right">Remainig</th>
                <th>Box #</th>
                <th>Pack Qty</th>
                <!--<th>Items/Pack</th>-->
                </tr>
            <?php
            for($i=0;$i<sizeof($orders);$i++)
            {
                $orderid			=	$orders[$i]['pkorderid'];
                $barcode			=	$orders[$i]['barcode'];
                $itemdescription	=	$orders[$i]['itemdescription'];
                $quantity			=	$orders[$i]['quantity'];
                // fetching previous purchases
                $purchaseqty		=	$AdminDAO->getrows("orderpurchase","IF(sum(quantity) IS NULL,0,sum(quantity)) qty","fkorderid='$orderid'");
                $purchased			=	$purchaseqty[0]['qty'];
                // fetch barcodeid
                $barcodeidarr		=	$AdminDAO->getrows("barcode","pkbarcodeid","barcode='$barcode'");
                $barcodeid			=	$barcodeidarr[0]['pkbarcodeid'];
				// fetching previous packing info
				$packqty			=	$AdminDAO->getrows("orderpack","IF(sum(quantity) IS NULL,0,sum(quantity)) qty","fkorderid='$orderid'");
                $packed				=	$packqty[0]['qty'];
				
				$remaining			=	$purchased-$packed;
            ?>
            <tr>
                <td><?php echo $i+1;?></td>
                <td><input type="checkbox" <?php if($remaining==0){echo 'disabled="disabled"'; }?> name="check<?php echo $i;?>" id="<?php echo $i;?>" value="1"  class="checkbox" /></td>
                <td><?php echo $orderid;?></td>
                <td><?php echo $barcode;?></td>
                <td><?php echo $itemdescription;?></td>
                <td align="right"><?php echo $quantity;?></td>
                <td align="right"><?php echo $purchased;?></td>
                <input type="hidden" name="orderid[]" id="orderid" value ="<?php echo $orderid; ?>" /> 
                <td align="right" style="padding-right: 10px;"><?php echo $packed;?></td>    
                <td align="right" style="padding-right: 10px;"><?php echo $remaining;?></td>       
                <td align="right" style="padding-right: 10px;"><input name="box[]" type="text" value="" size="3" /></td>
                <td align="right" style="padding-right: 10px;"><input name="quantity[]" type="text" value="" size="3" />
                <input name="boxtotal[]" type="hidden" value="<?php echo $remaining;?>" size="3" /></td>                
                <!--<td><input name="boxtotal[]" type="text" value="" size="3" /></td>-->
            </tr>
            <?php
            }
            ?>
            <tr >
              <td colspan="11"  align="left">
                   <div class="buttons">
                    <button type="button" class="positive" onclick="addpack();">
                        <img src="../images/tick.png" alt=""/> 
                        <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
                    </button>
                    <button type="button" onclick="hidediv('packdiv');" class="negative">
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
<input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
</fieldset>
</form>
</div>