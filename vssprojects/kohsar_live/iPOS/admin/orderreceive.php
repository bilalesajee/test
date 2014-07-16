<?php
session_start();
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
$damages		=	$d1.$d2;
//end damage types
//coding by jafer balti
function damtype($dtid=0)
{
	global $AdminDAO;
	$returnsarr	=	$AdminDAO->getrows("damagetype","pkdamagetypeid,damagetype","pkdamagetypeid='$dtid'");
	$damtypeid	=	$returnsarr[0]['pkdamagetypeid'];
	$damtype	=	$returnsarr[0]['damagetype'];
	return $damtype."<input type=\"hidden\" name=\"damagetype[]\" id=\"damagetype[]\" value=\"$damtypeid\" />";
}
function retype($rtid=0)
{
	global $AdminDAO;
	$returnsarr	=	$AdminDAO->getrows("returntype","pkreturntypeid,returntype","pkreturntypeid='$rtid'");
	$retypeid	=	$returnsarr[0]['pkreturntypeid'];
	$retype		=	$returnsarr[0]['returntype'];	
	return $returnsarr[0]['returntype']."<input type=\"hidden\" name=\"returntype[]\" id=\"returntype[]\" value=\"$retypeid\" />";
}
//coding by jafer balti
//selecting return types
$returnsarr	=	$AdminDAO->getrows("returntype","pkreturntypeid,returntype","returntypedeleted<>1");
$r1			=	"<select name=\"returntype[]\" id=\"returntype[]\" style=\"width:80px;\">";
for($i=0;$i<sizeof($returnsarr);$i++)
{
	$r2		.=	"<option value = \"".$returnsarr[$i][pkreturntypeid]."\">".$returnsarr[$i][returntype]."</option>";
}
$returns		=	$r1.$r2;
//end return types

//selecting items
$query	=	"SELECT 
				sl.barcode, 
				sl.itemdescription,
				sl.quantity as oquantity,
				p.quantity,
				p.pkorderpurchaseid,
				p.quantity purchased,
				p.fkorderid
			FROM 
				orderpurchase p, `order` sl
			WHERE
				fkorderid	=	pkorderid	AND
				`sl`.`fkshipmentid`	=	'$id'
			";
$receivedata	=	$AdminDAO->queryresult($query);
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
/*echo "<pre>";
print_r($storesinfo);
echo "</pre>";*/
//echo $storesize	=	sizeof($storesinfo);
$colspan		=	7+sizeof($storesinfo);
$bgcolor=	'#DFEFFF';
?>
<div id="shipfrmdiv" style="display:block;">
<div class="breadcrumbs" id="breadcrumbs">Shipment Receiving</div>
<br>
R= Requested&nbsp;A= Alloted <br>
  <form id="shipmentfrm" style="width: 920px;" action="receiveshipment.php?id=-1" class="form"  enctype="multipart/form-data">
    <fieldset>
      <legend>
      Receive Shipment
      </legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="receiveshipment(-1);"> <img src="../images/tick.png" alt=""/>
        Receive
        </button>
        <button type="button" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </button></span> </div>
 <table width="100%">
        <tr>
          <td height="12" valign="top"><div class="topimage2" style="height:6px;"><!-- --></div>
            <table style="width:895px;" cellpadding="2" cellspacing="0">
              <tbody>
              <tr>
              	<th>&nbsp;</th>
                <th align="left"><input type="checkbox" onclick="toggleChecked(this.checked)" id="chkAllreorder" name="chkAllreorder"></th>
                <th>Order ID</th>
                <th>Barcode</th>
                <th>Item</th>
                <th>Ordered Quantity</th>
                <th>Units Purchased</th>
                <th>Damaged</th>
                <th>Damage Type</th>
                <th>Returned</th>
                <th>Return Type</th>
                <th>Client Qty</th>
                <?php
				for($j=0;$j<sizeof($storesinfo);$j++)
				{
				?>
                <th><?php echo $storesinfo[$j]['storename'];?></th>
                <?php
				}
				?>
                <th>Status</th>
              </tr>
				<?php
                for($i=0;$i<sizeof($receivedata);$i++)
                {
					$status			=	0;
					$purchaseid		=	$receivedata[$i]['pkorderpurchaseid'];
					$orderid		=	$receivedata[$i]['fkorderid'];
					// building statuses for further processing
					$statusres		=	$AdminDAO->getrows("orderreceive,damagetype,returntype,addressbook","concat(firstname,' ',lastname) receivedby,damagedquantity,returnedquantity,fkdamagetypeid,fkreturntypeid,clientquantity,SUM(receivedquantity) receivedquantity","fkorderpurchaseid='$purchaseid' AND fkaddressbookid=pkaddressbookid AND fkdamagetypeid=pkdamagetypeid AND fkreturntypeid=pkreturntypeid AND fkshipmentid='$id' GROUP BY fkorderpurchaseid");
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
						//$bgcolor	=	"red";
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
					//echo " $purchaseid -----". $receivedata[$i]['purchased']." <= ". $statusres[0]['receivedquantity']."--";
              ?>
              <tr bgcolor="<?php echo $bgcolor;?>">
              	<td><?php echo $i+1;?></td>
                <td><input <?php if($status==1){ echo "disabled='disabled'";}?> type="checkbox" name="check<?php echo $i;?>" id="<?php echo $i;?>" value="1" class="checkbox" /></td>
                <td><?php echo $orderid;?></td>
                <td><?php echo $receivedata[$i]['barcode'];?></td>
                <td><?php echo $receivedata[$i]['itemdescription'];?></td>
                <td align="right" style="padding-right: 20px;"><?php echo $receivedata[$i]['oquantity'];?></td>
                <td align="right" style="padding-right: 20px;"><?php echo $receivedata[$i]['quantity'];?><input type="hidden" name="purchasedquan[]" id="purchasedquan" value="<?php echo $receivedata[$i]['quantity'];?>" /></td>
                <td><?php if($status==1){ echo $statusres[0]['damagedquantity'];?><input type="hidden" name="damaged[]" onkeypress="return isNumberKey(event)" id="damaged"  size="5" value="<?php echo $statusres[0]['damagedquantity'];?>" /><?php }else{?><input type="text" name="damaged[]" onkeypress="return isNumberKey(event)" id="damaged"  size="5" value="<?php echo $statusres[0]['damagedquantity'];?>" /><?php }?></td>
                <td><?php if($status==1) {echo damtype($statusres[0]['fkdamagetypeid']);  } else { echo $damages;}?></td>
                <td><?php if($status==1){ echo $statusres[0]['returnedquantity'];?><input type="hidden" name="returned[]" onkeypress="return isNumberKey(event)" id="returned"  size="5" value="<?php echo $statusres[0]['returnedquantity'];?>" /><?php }else{;?><input type="text" name="returned[]" onkeypress="return isNumberKey(event)" id="returned"  size="5" value="<?php echo $statusres[0]['returnedquantity'];?>" /><?php }?></td>
                <td><?php if($status==1) {echo retype($statusres[0]['fkreturntypeid']); } else { echo $returns;}?></td>
                <td><?php if($status==1){ echo $statusres[0]['clientquantity'];?><input type="hidden" name="clientquantity[]" onkeypress="return isNumberKey(event)" id="clientquantity"  size="5" value="<?php echo $statusres[0]['clientquantity'];?>" /><?php } else{;?><input type="text" name="clientquantity[]" onkeypress="return isNumberKey(event)" id="clientquantity"  size="5" value="<?php echo $statusres[0]['clientquantity'];?>" /><?php }?></td>
                <?php
				for($k=0;$k<sizeof($storesinfo);$k++)
				{
					$storeid	=	$storesinfo[$k]['pkstoreid'];
					// if received then calculating received quantity for each store
					if($status==1)
					{
						$receivequery	=	"SELECT 
												receivedquantity quantity 
											FROM 
												orderreceive
											WHERE
												fkstoreid			=	'$storeid' AND
												fkorderpurchaseid	=	'$purchaseid' AND
												fkshipmentid		=	$id	
											";
						$receiveres		=	$AdminDAO->queryresult($receivequery);
						$received		=	$receiveres[0]['quantity'];
					}
					else
					{
						// calculating forced
						$storeid	=	$storesinfo[$k]['pkstoreid'];
						$distquery	=	"SELECT 
											sum(quantity) quantity 
										FROM 
											orderallot
										WHERE
											fkstoreid			=	'$storeid' AND
											fkorderpurchaseid	=	'$purchaseid'
										";
						$distres	=	$AdminDAO->queryresult($distquery);
						$forced		=	$distres[0]['quantity'];
						
						// calculating requested
						$reqquery	=	"SELECT 
											sum(sl.quantity) quantity 
										FROM 
											`order` sl, orderpurchase p
										WHERE
											fkorderid			=	pkorderid AND
											sl.fkstoreid		=	'$storeid' AND
											pkorderpurchaseid	=	'$purchaseid'
										";
						$reqres		=	$AdminDAO->queryresult($reqquery);
						$requested	=	$reqres[0]['quantity'];
					}
				?>
                <td><?php if($status==1){ echo $received;?><input type="hidden" size="5" name="store<?php echo $storeid;?>[]" onkeypress="return isNumberKey(event)" value="<?php if($status==1) echo $received; else echo $forced;?>" id="<?php echo $storeid;?>" /><?php } else {?>R = <?php echo $requested;?><br />A = <?php echo $forced;?><br /><input type="text" size="5" name="store<?php echo $storeid;?>[]" onkeypress="return isNumberKey(event)" value="<?php if($status==1) echo $received; else echo $forced;?>" id="<?php echo $storeid;?>" /><?php } ?></td>
				<?php 
					// creating a string of stores array
					$storestringarr[]	=	$storeid;
				}
				$storestring		=	implode(",",array_unique($storestringarr));
                ?>
                <td><?php if ($status==1){ echo "Prepared by ".$statusres[0]['receivedby'];} else { echo "Not Prepared";}?> <input type="hidden" name="purchaseid[]" value="<?php echo $purchaseid;?>" /><input type="hidden" name="orderid[]" value="<?php echo $orderid;?>" /></td>
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
          <button type="button" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </button></div></td>
    </tr>
</table>
</fieldset>
<input type="hidden" name="storeids" id="storeids" value="<?php echo $storestring;?>" />
<input type="hidden" name="shipmentid" id="shipmentid" value="<?php echo $id;?>" />
</form>
<script language="javascript" type="text/javascript">
function receiveshipment(id)
{
	options	=	{	
					url : 'orderreceiveact.php?id='+id,
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
		hidediv('shipfrmdiv');
		jQuery('#maindiv').load('manageshipmentclosed.php');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>