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
				p.quantity,
				p.pkpurchaseid
			FROM 
				purchase p, shiplist sl
			WHERE
				fkshiplistid	=	pkshiplistid	AND
				p.fkshipmentid	=	'$id'
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
<div id="shipfrmdiv" style="display:block;">R= Requested&nbsp;A= Alloted <br>
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
                <th>Units Purchased</th>
                <th>Damaged</th>
                <th>Damage Type</th>
                <th>Returned</th>
                <th>Return Type</th>
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
				  $purchaseid	=	$receivedata[$i]['pkpurchaseid'];
				  // building statuses for further processing
				  $statusres	=	$AdminDAO->getrows("receiving,damagetype,returntype,addressbook","concat(firstname,' ',lastname) receivedby,damageqty,returnqty,damagetype,returntype","fkpurchaseid='$purchaseid' AND receivedby=pkaddressbookid AND fkdamagetypeid=pkdamagetypeid AND fkreturntypeid=pkreturntypeid");
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
              <tr bgcolor="<?php echo $bgcolor;?>">
                <td><?php echo $i+1;?></td>
                <td><?php echo $receivedata[$i]['barcode'];?></td>
                <td><?php echo $receivedata[$i]['itemdescription'];?></td>
                <td><?php echo $receivedata[$i]['quantity'];?></td>
                <td><?php if($status==1){ echo $statusres[0]['damageqty'];} else{;?><input type="text" name="damaged[]" onkeypress="return isNumberKey(event)" id="damaged"  size="5" value="" /><?php }?></td>
                <td><?php if($status==1) {echo $statusres[0]['damagetype']; } else { echo $damages;}?></td>
                <td><?php if($status==1){ echo $statusres[0]['returnqty'];} else{;?><input type="text" name="returned" onkeypress="return isNumberKey(event)" id="returned"  size="5" value="" /><?php }?></td>
                <td><?php if($status==1) {echo $statusres[0]['returntype'];; } else { echo $returns;}?></td>
                <?php
				for($k=0;$k<sizeof($storesinfo);$k++)
				{
					$storeid	=	$storesinfo[$k]['pkstoreid'];
					// if received then calculating received quantity for each store
					if($status==1)
					{
						$receivequery	=	"SELECT 
												sum(receivedquantity) quantity 
											FROM 
												receiving
											WHERE
												fkstoreid	=	'$storeid' AND
												fkpurchaseid	=	'$purchaseid'
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
											distribute
										WHERE
											fkstoreid		=	'$storeid' AND
											fkpurchaseid	=	'$purchaseid'
										";
						$distres	=	$AdminDAO->queryresult($distquery);
						$forced		=	$distres[0]['quantity'];
						
						// calculating requested
						$reqquery	=	"SELECT 
											sum(sl.quantity) quantity 
										FROM 
											shiplist sl, purchase p
										WHERE
											fkshiplistid	=	pkshiplistid AND
											sl.fkstoreid	=	'$storeid' AND
											pkpurchaseid	=	'$purchaseid'
										";
						$reqres		=	$AdminDAO->queryresult($reqquery);
						$requested	=	$reqres[0]['quantity'];
					}
				?>
                <td><?php if($status==1){ echo $received;} else {?>R = <?php echo $requested;?><br />A = <?php echo $forced;?><br /><input type="text" size="5" name="store<?php echo $storeid;?>[]" onkeypress="return isNumberKey(event)" value="<?php echo $forced;?>" id="<?php echo $storeid;?>" /><?php } ?></td>
				<?php 
					// creating a string of stores array
					$storestringarr[]	=	$storeid;
				}
				$storestring		=	implode(",",array_unique($storestringarr));
                ?>
                <td><?php if ($status==1){ echo "Prepared by ".$statusres[0]['receivedby'];} else { echo "Not Prepared";}?> <input type="hidden" name="purchaseid[]" value="<?php echo $purchaseid;?>" /></td>
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
function receiveshipment(id)
{
	options	=	{	
					url : 'receiveshipment.php?id='+id,
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