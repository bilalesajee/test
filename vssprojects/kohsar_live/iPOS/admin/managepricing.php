<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id			=	$_REQUEST['id'];
//selecting items
$query	=	"SELECT 
				sl.barcode, 
				sl.itemdescription,
				p.quantity,
				p.purchaseprice,
				p.pkpurchaseid,
				currencysymbol,
				exchangerate
			FROM 
				purchase p, shiplist sl, shipment LEFT JOIN currency ON (shipmentcurrency=pkcurrencyid)
			WHERE
				p.fkshipmentid	=	pkshipmentid AND
				fkshiplistid	=	pkshiplistid	AND
				p.fkshipmentid	=	'$id'
			";
$receivedata	=	$AdminDAO->queryresult($query);
$recsize		=	sizeof($receivedata);
/*echo "<pre>";
print_r($receivedata);
echo "</pre>";*/

//fetching store wise requests
$storedata	=	"SELECT 
					pkstoreid,storename
				FROM 
					store 
				WHERE
					storestatus=1
				";
$storesinfo	=	$AdminDAO->queryresult($storedata);
$stsize		=	sizeof($storesinfo);
/*echo "<pre>";
print_r($storesinfo);
echo "</pre>";*/
//echo $storesize	=	sizeof($storesinfo);
$colspan		=	8+sizeof($storesinfo);
$bgcolor		=	'#DFEFFF';
?>
<div id="shipfrmdiv" style="display: block;"> <br>
  <form id="shipmentfrm" style="width: 920px;" action="receiveshipment.php?id=-1" class="form"  enctype="multipart/form-data">
    <fieldset>
      <legend>
      Shipment
      Pricing</legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="receiveshipment(-1);"> <img src="../images/tick.png" alt=""/>
        Receive
        </button>
        <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span> </div>
 <table width="100%">
        <tr>
          <td height="12" valign="top"><div class="topimage2" style="height:6px;"><!-- --></div>
            <table style="width:895px;" cellpadding="2" cellspacing="0">
              <tbody>
              <tr>
                <th>Sr #</th>
                <th>Barcode</th>
                <th>Item</th>
                <th>Units Purchased</th>
                <th>Purchase Price <?php echo $receivedata[0]['currencysymbol'];?></th>
                <th>Shipment %</th>
                <th>Shipment Charges</th>
                <th>Cost Price</th>
                <th> %age</th>
                <?php
				for($j=0;$j<sizeof($storesinfo);$j++)
				{
				?>
                <th><?php echo $storesinfo[$j]['storename'];?></th>
                <?php
				}
				?>
                <th>Status</th>
                <th>History</th>
              </tr>
              <?php
              for($i=0;$i<sizeof($receivedata);$i++)
              {
				  $purchaseid	=	$receivedata[$i]['pkpurchaseid'];
				  // building statuses for further processing
				  $statusres	=	$AdminDAO->getrows("pricing,addressbook","concat(firstname,' ',lastname) pricedby,costprice,retailpercentage,retailprice,shipmentcharges,shipmentpercentage","fkpurchaseid='$purchaseid' AND pricedby=pkaddressbookid");
					if(sizeof($statusres)>0)
					{
					  $status	=	1;
					}
					else
					{
					  $status	=	0;
					}
					if($status ==1)
					{
						$bgcolor	=	"#BEFAA5";
					}
					else
					{
						if($bgcolor	==	'#DFEFFF')
						{
							$bgcolor	=	'';
						}
						else
						{
							$bgcolor	=	'#DFEFFF';
						}
					}//
              ?>
              <tr bgcolor="<?php echo $bgcolor;?>"><div id="rate" style="display:none;"><?php echo $receivedata[0]['exchangerate'];?></div>
                <td><?php echo $i+1;?></td>
                <td><?php echo $receivedata[$i]['barcode'];?></td>
                <td><?php echo $receivedata[$i]['itemdescription'];?></td>
                <td><?php echo $receivedata[$i]['quantity'];?></td>
                <td><?php echo $receivedata[$i]['purchaseprice'];?></td>
                <td><?php if($status==1){ echo $statusres[0]['shipmentpercentage'];} else{;?><input type="text" size="5" name="shipmentpercentage[]" id="shipmentpercentage_<?php echo $i;?>" onkeypress="return isNumberKey(event)" onblur="cal(<?php echo $i;?>,<?php echo $receivedata[$i]['purchaseprice'];?>)"  /><?php }?></td>
                <td><?php if($status==1){ echo $statusres[0]['shipmentcharges'];} else{;?><input type="text" size="5" name="shipmentcharges[]" id="shipmentcharges_<?php echo $i;?>" onkeypress="return isNumberKey(event)" /><?php }?></td>
                <td><?php if($status==1){ echo $statusres[0]['costprice'];} else{;?><input type="text" size="5" name="costprice[]"  id="costprice_<?php echo $i;?>"  onkeypress="return isNumberKey(event)" /><?php }?></td>
                <td><?php if($status==1){ echo $statusres[0]['retailpercentage'];} else{;?><input type="text" size="5" name="retailpercentage[]" id="retailpercentage_<?php echo $i;?>"   onkeypress="return isNumberKey(event)"  onblur="rcalc(<?php echo $i;?>,this.value,<?php echo $stsize;?>)" /><?php }?></td>
                <?php
				for($k=0;$k<sizeof($storesinfo);$k++)
				{
					// if received then calculating received quantity for each store
					if($status==1)
					{
						$pricequery	=	"SELECT 
												retailprice
											FROM 
												pricing
											WHERE
												fkstoreid	=	'$storeid' AND
												fkpurchaseid	=	'$purchaseid'
											";
						$priceres		=	$AdminDAO->queryresult($pricequery);
						$storeprice		=	$priceres[0]['retailprice'];
					}
				?>
                <td><?php if($status==1){ echo $storeprice;} else {?><input type="text" size="5" name="store<?php echo $storesinfo[$k][pkstoreid];?>[]" id="store_<?php echo $i."_".$k;?>" onkeypress="return isNumberKey(event)" /><?php } ?></td>
				<?php 
					// creating a string of stores array
					$storestringarr[]	=	$storesinfo[$k][pkstoreid];
				}
				$storestring		=	implode(",",array_unique($storestringarr));
                ?>
                
                <td><?php if ($status==1){ echo "Prepared by ".$statusres[0]['pricedby'];} else { echo "Not Priced";}?><input type="hidden" name="purchaseid[]" value="<?php echo $receivedata[$i]['pkpurchaseid'];?>" /></td>
                <td><a href="#" onclick="viewhistory(<?php echo $receivedata[$i]['barcode'];?>)">View</a></td>
              </tr>
              <?php
              }
              ?>
              </tbody>
            </table>
		</td>
	</tr>
    <tr>
      <td colspan="12" align="center"><div class="buttons">
          <button type="button" class="positive" onclick="receiveshipment(-1);"> <img src="../images/tick.png" alt=""/>
			Receive
          </button>
          <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div></td>
    </tr>
</table>
</fieldset>
<input type="hidden" name="storeids" id="storeids" value="<?php echo $storestring;?>" />
<input type="hidden" name="shipmentid" id="shipmentid" value="<?php echo $id;?>" />
</form>
<script language="javascript" type="text/javascript">
jQuery(function($)
{
	document.getElementById('shipmentpercentage_0').focus();
});
function cal(i,pprice)
{
	exchangerate		=	document.getElementById('rate').innerHTML;
	percentage			=	document.getElementById('shipmentpercentage_'+i).value;
	cprice				=	pprice*exchangerate;
	percentval			=	cprice*percentage/100;
	percentvalr			=	percentval.toFixed(2);
	document.getElementById('shipmentcharges_'+i).value	=	percentvalr;
	costprice			=	cprice+percentval;
	costpricer			=	costprice.toFixed(2);
	document.getElementById('costprice_'+i).value	=	costpricer;
	document.getElementById('retailpercentage_'+i).focus();
//	alert(costprice);
	
}
function rcalc(i,rpercent,size)
{
	costprice	=	document.getElementById('costprice_'+i).value;
	percentval	=	costprice*rpercent/100;
	percentvalr	=	percentval.toFixed(2);
	//alert(percentvalr);
	retail		=	parseFloat(costprice)+parseFloat(percentvalr);
	//alert(retail);
	retailr		=	retail.toFixed(2);
	for(k=0;k<size;k++)
	{
		document.getElementById('store_'+i+'_'+k).value	=	retailr;
	}
	j	=	i+1;
	if(document.getElementById('shipmentpercentage_'+j))
		document.getElementById('shipmentpercentage_'+j).focus();
	//alert(percentval);
}
function receiveshipment(id)
{
	options	=	{	
					url : 'priceshipment.php?id='+id,
					type: 'POST',
					success: priceresponse
				}
	jQuery('#shipmentfrm').ajaxSubmit(options);
}
function priceresponse(text)
{
	if(text=='')
	{
		adminnotice('Items have been scheduled for pricing.',0,5000);
		jQuery('#maindiv').load('manageshipment.php');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function viewhistory(bc)
{
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1';
 		window.open('viewpricehistory.php?bc='+bc,'Price History',display); 
}
</script>