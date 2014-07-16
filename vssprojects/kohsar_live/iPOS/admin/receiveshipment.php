<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id			=	$_REQUEST['id'];
//selecting damage types
$damagesarr	=	$AdminDAO->getrows("damagetype","*","1");
$d1			=	"<select name=\"damagetype[]\" id=\"damagetype[]\" style=\"width:80px;\">";
for($i=0;$i<sizeof($damagesarr);$i++)
{
	$d2		.=	"<option value = \"".$damagesarr[$i][pkdamagetypeid]."\">".$damagesarr[$i][damagetype]."</option>";
}
$damages	=	$d1.$d2;
//end damage types

//selecting return types
$returnsarr	=	$AdminDAO->getrows("returntype","pkreturntypeid,returntype","returntypedeleted<>1");
$r1			=	"<select name=\"returntype[]\" id=\"returntype[]\" style=\"width:80px;\">";
for($i=0;$i<sizeof($returnsarr);$i++)
{
	$r2		.=	"<option value = \"".$returnsarr[$i][pkreturntypeid]."\">".$returnsarr[$i][returntype]."</option>";
}
$returns	=	$r1.$r2;
//end return types

//selecting items
$query	=	"SELECT
				pkreceivingid,
				fkpurchaseid,
				concat(firstname,' ',lastname) name,
				b.barcode,
				fkbarcodeid, 
				b.itemdescription,
				purchaseprice,
				currencysymbol,
				currencyrate,
				receivedquantity quantity,
				damageqty,
				damagetype,
				returnqty,
				returntype,
				batch,
				expiry,
				FROM_UNIXTIME(receivetime,'%d-%m-%Y %h:%i:%s %p') rtime,
				fksupplierid,
				sl.fkagentid,
				sl.fkcountryid
			FROM 
				shiplist sl,receiving r, purchase p LEFT JOIN currency ON fkcurrencyid=pkcurrencyid, barcode b, damagetype, returntype, addressbook
			WHERE
				p.fkshiplistid	=	pkshiplistid AND
				fkpurchaseid	=	pkpurchaseid AND
				fkbarcodeid		=	pkbarcodeid	AND
				r.fkshipmentid	=	'$id' AND
				r.fkstoreid		=	'$storeid' AND 
				receivedby		=	pkaddressbookid AND 
				fkdamagetypeid	=	pkdamagetypeid AND 
				fkreturntypeid	=	pkreturntypeid AND (receivedquantity <>0 OR returnqty<>0 OR damageqty <> 0)
			";
$receivedata	=	$AdminDAO->queryresult($query);
/*echo "<pre>";
print_r($receivedata);
echo "</pre>";*/
$colspan		=	7+sizeof($storesinfo);
$bgcolor=	'#DFEFFF';
?>
<div id="shipfrmdiv" style="display: block;"> <br>
  <form id="shipmentfrm" style="width: 920px;" action="receiveshipment.php?id=-1" class="form"  enctype="multipart/form-data">
    <fieldset>
      <legend>
      Receive Shipment
      </legend>
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
                <th>Cost</th>
                <th>Retail</th>
				<th>Quantity</th>
                <th>Batch</th>
                <th>Expiry</th>              
                <th>Prepared by</th>
                <th>Prepare Time</th>
                <th>Priced by</th>
                <th>Price Time</th>
              </tr>
              <?php
              for($i=0;$i<sizeof($receivedata);$i++)
              {
					$purchaseid		=	$receivedata[$i]['fkpurchaseid'];
					$receivestatus	=	$AdminDAO->getrows("$dbname_detail.stock","1","fkpurchaseid='$purchaseid'");
					$status			=	$receivestatus[0][0];
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
				// comparing with pricing info
				$purchaseid		=	$receivedata[$i]['fkpurchaseid'];
				$priceres		=	$AdminDAO->getrows("pricing,addressbook","concat(firstname,' ',lastname) name,pricetime,costprice,retailpercentage,retailprice,shipmentcharges,shipmentpercentage,FROM_UNIXTIME(pricetime,'%d-%m-%Y %h:%i:%s %p') ptime","fkpurchaseid='$purchaseid' AND fkstoreid='$storeid' AND pricedby=pkaddressbookid");
				// calculating costprice
				// costprice = currencyrate*(purchaseprice+shipmentcharges)
				$costprice	=	$receivedata[$i]['currencyrate']*($receivedata[$i]['purchaseprice']+$priceres[0]['shipmentcharges']);
			?>
              <tr bgcolor="<?php echo $bgcolor;?>">
                <td><?php echo $i+1;?></td>
                <td><?php echo $receivedata[$i]['barcode'];?></td>
                <td><?php echo $receivedata[$i]['itemdescription'];?></td>
                <td><?php echo $costprice;?></td>
				<td><?php echo $priceres[0]['retailprice'];?></td>
                <td><?php echo $receivedata[$i]['quantity'];?></td>
                <td><?php echo $receivedata[$i]['batch'];?></td>
                <td><?php echo implode("-",array_reverse(explode("-",$receivedata[$i]['expiry'])));?></td>
                <td><?php echo $receivedata[$i]['name'];?></td>
                <td><?php echo $receivedata[$i]['rtime'];?></td>
                <td><?php echo $priceres[0]['name'];?></td>
                <td><?php echo $priceres[0]['ptime'];?>
                <?php if($status!=1)
				{
					?>
                    <input type="hidden" name="barcodeid[]" value="<?php echo $receivedata[$i]['fkbarcodeid'];?>" />
                    <input type="hidden" name="purchaseprice[]" value="<?php echo $receivedata[$i]['purchaseprice'];?>" />
                    <input type="hidden" name="shipmentpercentage[]" value="<?php echo $priceres[0]['shipmentpercentage'];?>" />
                    <input type="hidden" name="shipmentcharges[]" value="<?php echo $priceres[0]['shipmentcharges'];?>" />
                    <input type="hidden" name="costprice[]" value="<?php echo $costprice;?>" />
                    <input type="hidden" name="retailpercentage[]" value="<?php echo $priceres[0]['retailpercentage'];?>" />
                    <input type="hidden" name="retailprice[]" value="<?php echo $priceres[0]['retailprice'];?>" />
                    <input type="hidden" name="quantity[]" value="<?php echo $receivedata[$i]['quantity'];?>" />
                    <input type="hidden" name="damagetype[]" value="<?php echo $receivedata[$i]['damagetype'];?>" />
                    <input type="hidden" name="damageqty[]" value="<?php echo $receivedata[$i]['damageqty'];?>" />
                    <input type="hidden" name="returntype[]" value="<?php echo $receivedata[$i]['returntype'];?>" />
                    <input type="hidden" name="returnqty[]" value="<?php echo $receivedata[$i]['returnqty'];?>" />
                    <input type="hidden" name="receivingid[]" value="<?php echo $receivedata[$i]['pkreceivingid'];?>" />
                    <input type="hidden" name="purchaseid[]" value="<?php echo $purchaseid;?>" />
                    <input type="hidden" name="batch[]" value="<?php echo $receivedata[$i]['batch'];?>" />
                    <input type="hidden" name="expiry[]" value="<?php echo $receivedata[$i]['expiry'];?>" />
                    <input type="hidden" name="supplier[]" value="<?php echo $receivedata[$i]['fksupplierid'];?>" />
                    <input type="hidden" name="agent[]" value="<?php echo $receivedata[$i]['fkagentid'];?>" />
                    <input type="hidden" name="country[]" value="<?php echo $receivedata[$i]['fkcountryid'];?>" />
                    <input type="hidden" name="currencyrate[]" value="<?php echo $receivedata[$i]['currencyrate'];?>" />
                <?php
				}
				?>
                </td>
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
<input type="hidden" name="shipmentid" id="shipmentid" value="<?php echo $id;?>" />
</form>
<script language="javascript" type="text/javascript">
function receiveshipment(id)
{
	options	=	{	
					url : 'receiveshipmentact.php?id='+id,
					type: 'POST',
					success: receiveresponse
				}
	jQuery('#shipmentfrm').ajaxSubmit(options);
}
function receiveresponse(text)
{
	if(text=='')
	{
		adminnotice('Items have been moved to the destination.',0,5000);
		jQuery('#maindiv').load('manageshipment.php');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>