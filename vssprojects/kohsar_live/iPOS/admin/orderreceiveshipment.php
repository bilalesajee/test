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
/*$query	=	"SELECT
				pkorderreceiveid,
				fkorderpurchaseid,
				concat(firstname,' ',lastname) name,
				b.barcode,
				fkbarcodeid, 
				b.itemdescription,
				purchaseprice,
				currencysymbol,
				exchangerate,
				receivedquantity quantity,
				damagedquantity,
				damagetype,
				returnedquantity,
				returntype,
				batch,
				expiry,
				FROM_UNIXTIME(r.datetime,'%d-%m-%Y %h:%i:%s %p') rtime,
				fksupplierid,
				s.fkagentid,
				sl.fkcountryid
			FROM 
				`order` sl,orderreceive r, orderpurchase p, shipment s LEFT JOIN currency ON shipmentcurrency=pkcurrencyid, barcode b, damagetype, returntype, addressbook
			WHERE
				sl.fkshipmentid		=	pkshipmentid AND
				p.fkorderid			=	pkorderid AND
				fkorderpurchaseid	=	pkorderpurchaseid AND
				fkbarcodeid			=	pkbarcodeid	AND
				r.fkshipmentid		=	'$id' AND
				r.fkstoreid			=	'$storeid' AND 
				r.fkaddressbookid		=	pkaddressbookid AND 
				fkdamagetypeid		=	pkdamagetypeid AND 
				fkreturntypeid		=	pkreturntypeid AND (receivedquantity <>0 OR returnedquantity<>0 OR damagedquantity <> 0)
			";*/
$query			=	"SELECT
						r.fkorderpurchaseid,
						o.barcode barcode,
						p.fkbarcodeid fkbarcodeid,
						p.purchaseprice purchaseprice,
						o.itemdescription itemdescription,
						r.clientquantity clientquantity,
						DATE_FORMAT(p.expiry,'%d-%m-%Y') expiry,
						p.quantity quantity,
						sum(r.damagedquantity) damaged,
						damagetype,
						fkdamagetypeid,
						sum(r.returnedquantity) returned,
						returntype,
						fkreturntypeid,
						sum(r.receivedquantity) received,
						p.fksupplierid fksupplierid,
						s.companyname companyname,
						c.companyname client,
						sh.exchangerate exchangerate,
						o.fkcountryid fkcountryid,
						sh.fkagentid fkagentid,
						r.pkorderreceiveid pkorderreceiveid,
						batch
					FROM
						`order` o LEFT JOIN customer ON fkcustomerid = pkcustomerid, 
						orderpurchase p, 
						orderreceive r LEFT JOIN returntype ON fkreturntypeid=pkreturntypeid LEFT JOIN damagetype ON fkdamagetypeid = pkdamagetypeid, 
						supplier s, 
						customer c, 
						shipment sh
					WHERE
						p.fkorderid			=	pkorderid AND
						p.fkshipmentid		=	pkshipmentid AND
						p.fkshipmentid		=	'$id' AND
						r.fkorderpurchaseid	=	pkorderpurchaseid AND
						p.fksupplierid 		= 	pksupplierid AND
						r.fkstoreid			=	'$storeid'
					GROUP BY
						pkorderpurchaseid
					";//o.fkstoreid			=	'$storeid'
$receivedata	=	$AdminDAO->queryresult($query);
/*echo "<pre>";
print_r($receivedata);
echo "</pre>";*/
$colspan		=	17+sizeof($storesinfo);
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
                <th>Client</th>
                <th>Expiry</th>
                <th>Units Sent</th>
                <th>Received</th>
                <th>Damaged</th>
                <th>Damage Type</th>
                <th>Return</th>
                <th>Return Type</th>
                <th>Cost Price</th>
                <th>Sale Price</th>
                <th>New Sale Price</th>
                <th>Received by</th>
                <th>Receive Time</th>
              </tr>
              <?php
              for($i=0;$i<sizeof($receivedata);$i++)
              {
					$purchaseid		=	$receivedata[$i]['fkorderpurchaseid'];
					$receivestatus	=	$AdminDAO->getrows("$dbname_detail.stock,addressbook","1,CONCAT(firstname, ' ' , lastname) name,FROM_UNIXTIME(updatetime,'%d-%m-%Y %h:%i:%s %p') rtime","fkpurchaseid='$purchaseid' AND fkemployeeid = pkaddressbookid");
					$status			=	$receivestatus[0][0];
					$employee		=	$receivestatus[0]['name'];
					$rtime			=	$receivestatus[0]['rtime'];
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
				$priceres		=	$AdminDAO->getrows("orderprice,addressbook","concat(firstname,' ',lastname) name,DATE_FORMAT(`datetime`,'%d-%m-%Y') datetime,costprice,retailpercentage,retailprice,shipmentcharges,shipmentpercentage,DATE_FORMAT(datetime,'%d-%m-%Y')  ptime","fkorderpurchaseid='$purchaseid' AND fkstoreid='$storeid' AND fkaddressbookid=pkaddressbookid");
				// calculating costprice
				// costprice = currencyrate*(purchaseprice+shipmentcharges)
				$costprice	=	$receivedata[$i]['exchangerate']*($priceres[$i]['costprice']+$priceres[0]['shipmentcharges']);
				//selecting damages
				$damagetypes	=	"";
				$damagesarr	=	$AdminDAO->getrows("damagetype","*","1");
				for($d=0;$d<sizeof($damagesarr);$d++)
				{
					$sel	=	"";
					if($receivedata[$i]['fkdamagetypeid']==$damagesarr[$d]['pkdamagetypeid'])
					{
						$sel	=	"selected=\"selected\"";
					}
					$damagetypes		.=	"<option value = \"".$damagesarr[$d]['pkdamagetypeid']."\" $sel>".$damagesarr[$d]['damagetype']."</option>";
				}
				//selecting returns
				$returntypes	=	"";
				$returnsarr	=	$AdminDAO->getrows("returntype","*","1");
				for($r=0;$r<sizeof($returnsarr);$r++)
				{
					$sel	=	"";
					if($receivedata[$i]['fkreturntypeid']==$returnsarr[$d]['pkreturntypeid'])
					{
						$sel	=	"selected=\"selected\"";
					}
					$returntypes		.=	"<option value = \"".$returnsarr[$r]['pkreturntypeid']."\" $sel>".$returnsarr[$r]['returntype']."</option>";
				}
			?>
              <tr bgcolor="<?php echo $bgcolor;?>">
                <td><?php echo $i+1;?></td>
                <td><?php echo $receivedata[$i]['barcode'];?></td>
                <td><?php echo $receivedata[$i]['itemdescription'];?></td>
                <td><?php echo $receivedata[$i]['client'];?></td>
                <td><?php echo $receivedata[$i]['expiry'];?></td>
                <td align="center"><?php echo $receivedata[$i]['quantity'];?></td>
                <td align="center">
				<?php if($status!=1)
				{ 
				?>
                <input type="text" name="received[]" onkeypress="return isNumberKey(event)" class="text" id="received<?php echo $i;?>" value="<?php echo $receivedata[$i]['received'];?>" size="2" />
                <?php
				}
				else
				{
					echo $receivedata[$i]['received'];
                }
                ?>
                </td>
				<td>
                <?php if($status!=1)
				{ 
				?>
                <input type="text" name="damaged[]" onkeypress="return isNumberKey(event)" class="text" id="damaged<?php echo $i;?>" value="<?php echo $receivedata[$i]['damaged'];?>" size="2" />
                <?php
				}
				else
				{
					echo $receivedata[$i]['damaged'];
				}
				?>
                </td>
                <td>
                <?php if($status!=1)
				{ 
				?>
                <select name="damagetypes[]" style="width:80px;">
				<?php echo $damagetypes;?>
                </select>
                <?php
				}
				else
				{
					echo $receivedata[$i]['damagetype'];
				}
				?>
                </td>
                <td>
                 <?php if($status!=1)
				{ 
				?>
                <input type="text" name="returned[]" onkeypress="return isNumberKey(event)" class="text" id="returned<?php echo $i;?>" value="<?php echo $receivedata[$i]['returned'];?>" size="2" />
                <?php
				}
				else
				{
					echo $receivedata[$i]['returned'];
				}
				?>
                </td>
                <td>
                <?php if($status!=1)
				{ 
				?>
                <select name="returntypes[]" style="width:80px;">
				<?php echo $returntypes;?>
                </select>
                <?php
				}
				else
				{
					echo $receivedata[$i]['returntype'];
				}
				?>
                </td>
                <td><?php echo $costprice;?></td>
				<td><?php echo $priceres[0]['retailprice'];?></td>
                <td>
                <?php if($status!=1)
				{ 
				?>
                <input type="text" name="newsaleprice[]" onkeypress="return isNumberKey(event)" class="text" id="newsp<?php echo $i;?>" value="<?php echo $priceres[0]['retailprice'];?>" size="2" />
                <?php
				}
				else
				{
					echo $priceres[0]['retailprice'];
				}
				?>
                </td>
                <td>
                 <?php if($status==1)
				{ 
					echo $employee;
				}
				else
				{
					echo "Not Received";
				}
				?>
                </td>
                <td>
                <?php if($status==1)
				{ 
					echo $rtime;
				}
				?>
                </td>
                <?php if($status!=1)
				{
					?>
				<tr>
                <td>
                <input type="hidden" name="barcodeid[]" value="<?php echo $receivedata[$i]['fkbarcodeid'];?>" />
                <input type="hidden" name="purchaseprice[]" value="<?php echo $receivedata[$i]['purchaseprice'];?>" />
                <input type="hidden" name="shipmentpercentage[]" value="<?php echo $priceres[0]['shipmentpercentage'];?>" />
                <input type="hidden" name="shipmentcharges[]" value="<?php echo $priceres[0]['shipmentcharges'];?>" />
                <input type="hidden" name="costprice[]" value="<?php echo $costprice;?>" />
                <input type="hidden" name="retailpercentage[]" value="<?php echo $priceres[0]['retailpercentage'];?>" />
                <input type="hidden" name="retailprice[]" value="<?php echo $priceres[0]['retailprice'];?>" />
                <input type="hidden" name="quantity[]" value="<?php echo $receivedata[$i]['quantity'];?>" />
                <input type="hidden" name="damagetype[]" value="<?php echo $receivedata[$i]['damagetype'];?>" />
                <input type="hidden" name="damageqty[]" value="<?php echo $receivedata[$i]['damaged'];?>" />
                <input type="hidden" name="returntype[]" value="<?php echo $receivedata[$i]['returntype'];?>" />
                <input type="hidden" name="returnqty[]" value="<?php echo $receivedata[$i]['returned'];?>" />
                <input type="hidden" name="receivingid[]" value="<?php echo $receivedata[$i]['pkorderreceiveid'];?>" />
                <input type="hidden" name="purchaseid[]" value="<?php echo $purchaseid;?>" />
                <input type="hidden" name="batch[]" value="<?php echo $receivedata[$i]['batch'];?>" />
                <input type="hidden" name="expiry[]" value="<?php echo $receivedata[$i]['expiry'];?>" />
                <input type="hidden" name="supplier[]" value="<?php echo $receivedata[$i]['fksupplierid'];?>" />
                <input type="hidden" name="agent[]" value="<?php echo $receivedata[$i]['fkagentid'];?>" />
                <input type="hidden" name="country[]" value="<?php echo $receivedata[$i]['fkcountryid'];?>" />
                <input type="hidden" name="currencyrate[]" value="<?php echo $receivedata[$i]['exchangerate'];?>" />
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
					url : 'orderreceiveshipmentact.php?id='+id,
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