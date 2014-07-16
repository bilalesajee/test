<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
 //$id				=	$_REQUEST['id'];
/*//selectin supplier
$itemname			=	$AdminDAO->getrows("barcode","itemdescription","pkbarcodeid='$id'");
$itemdescription	=	$itemname[0]['itemdescription'];
//selecting items*/
 $query			=	"SELECT s.pkstockid as id,s.priceinrs as tradeprice,s.retailprice,s.fksupplierinvoiceid ,s.expiry,
						
						s.quantity,
						
						b.itemdescription,
						b.barcode
				FROM
						$dbname_detail.stock s
						left join barcode b on b.pkbarcodeid = s.fkbarcodeid
				WHERE 	
						(s.expiry=0 or s.fksupplierinvoiceid = 0) or (s.expiry is null or s.fksupplierinvoiceid is null)   limit 10
						
				";
$stocksdata	=	$AdminDAO->queryresult($query);

//selecting return types

?>
<script language="javascript" type="text/javascript">
jQuery(function($)
{
	
 $(".datepick").datepicker({dateFormat: 'yy-mm-dd'});

});
function submitfrm(id)
{
	
  
  //alert(stock1);
	loading('System is Saving The Data....');
	options	=	{	
					url : 'insert_orphan_stock.php',
					type: 'POST',
					
					success: response
				}
	jQuery('#adstockfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Records updated successfully.',0,5000);
		jQuery('#maindiv').load('orphan_stock.php');
		//hidediv('stockprocessreturns');
		selecttab('86_tab','orphan_stock.php')
	}
	else
	{
		adminnotice(text,0,5000);
		
	}
}
</script>
<div id="loaditemscript">
</div>
<div id="currency" style="display:none;"></div>
<div id="cur">
</div>
<div id="stockprocessreturns">
<div id="stockitem">
<br />
<div id="error" class="notice" style="display:none"></div>
<div id="shippercentdiv"></div>
<div id="baseprice" style="display:none"></div>
<div id="baseexpense" style="display:none"></div>
<div id="shipvalue" style="display:none"></div>
<div id="minusshipment" style="display:none"></div>
<div id="plusshipment" style="display:none"></div>
<form id="adstockfrm" class="form">
  <fieldset>
  <legend> Orphan Stock
</legend>
<br /><br />
<table width="97%" cellpadding="1" cellspacing="1">
    <tr>
        <th width="5%">Select</th>
        <th width="8%">Barcode</th>
        <th width="12%">Item</th>
        <th width="7%">Stock</th>
        <th width="8%">Trade Price</th>
        <th width="5%"><span id="description1">Retail Price</span></th>
        <th width="5%">Expiry</th>
        <th width="10%">InvoiceID</th>
        <th width="14%">Adjustment</th>
        <th width="13%">Trade Price</th>
        <th width="6%">Retail Price</th>
        <th width="7%">Expiry</th>
        <th width="13%">InvoiceID</th>
        <th width="10%">Save</th>
    </tr>
    <?php
	
	for($i=0;$i<sizeof($stocksdata);$i++)
	{
		
		$quantity			=	$stocksdata[$i]['quantity'];
		$pkstockid=$stocksdata[$i]['id'];
		$barcode			=	$stocksdata[$i]['barcode'];
		$itemdescription	=	$stocksdata[$i]['itemdescription'];
		
		$id	=	$stocksdata[$i]['id'];
		$tradeprice	=	$stocksdata[$i]['tradeprice'];
		$retailprice	=	$stocksdata[$i]['retailprice'];
		$expiry	=	$stocksdata[$i]['expiry'];
		$fksupplierinvoiceid	=	$stocksdata[$i]['fksupplierinvoiceid'];
	
	
	?>
    <tr>
    	<td><input type="checkbox" name="stock1_<?php echo $i;?>" id="stock1_<?php echo $i;?>" value="<?php echo $id;?>"></td>
        <td><?php echo $barcode; ?></td>
        <td><?php echo $itemdescription; ?></td>
        <td align="center"><?php echo $quantity; ?></td>
        <td align="center"><?php echo $tradeprice; ?></td>
        <td align="center"><?php echo $retailprice; ?></td>
        <td align="center"><?php echo $expiry; ?></td>
        <td align="center"><?php echo $fksupplierinvoiceid; ?></td>
        <td align="center"><input type="text" size="10" class="text" name="newstock_<?php echo $i;?>" id="newstock_<?php echo $i;?>" value="<?php //echo $retqty;?>" maxlength="10" />
          
        </td>
        <td><div  style="float:right;"></div>
          <input name="tradeprice_<?php echo $i;?>" type="text" class="text" id="tradeprice_<?php echo $i;?>" value="<?php //echo $retqty;?>" size="10" maxlength="10" /></td>
        <td><input name="retailprice_<?php echo $i;?>" type="text" class="text" id="retailprice_<?php echo $i;?>" value="<?php //echo $retqty;?>" size="10" maxlength="10" /></td>
        <td><input name="expiry_<?php echo $i;?>" type="text" class="text datepick" id="expiry_<?php echo $i;?>" value="<?php //echo $retqty;?>" size="10" maxlength="10" /></td>
            <td><input name="fksupplierinvoiceid_<?php echo $i;?>" type="text" class="text" id="fksupplierinvoiceid_<?php echo $i;?>2" value="<?php //echo $retqty;?>" size="10" maxlength="10" /></td>
        <td align="center"><span class="buttons">
          <button type="button" class="positive" onclick="submitfrm(<?php echo $id;?>);"> <img src="../images/tick.png" alt=""/> Save </button>
        </span></td>
    </tr>
     <?php
	
	}//if?>
     <tr>
       <td colspan="14">&nbsp;</td>
     </tr>
     <tr>
      <td colspan="14"><span class="buttons">
      <button type="button" class="positive" onclick="submitfrm(<?php echo $id;?>);"> <img src="../images/tick.png" alt=""/> Save </button>
      </span></td>
      </tr>
   
</table>
<div class="buttons"></div>
</fieldset>
</form>
<br />
</div>
</div>
<script language="javascript" type="text/javascript">
//document.getElementById('barcode1').focus();
</script>