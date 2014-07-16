<?php
error_reporting(0);
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$id				=	$_REQUEST['id'];
//checking consignment status to place a check
$constatus		=	$AdminDAO->getrows("consignment","fkstatusid","pkconsignmentid='$id'");
$currentstatus	=	$constatus[0]['fkstatusid'];
$disabled		=	"onclick=\"submitfrm();\"";
//selecting damages
$damagesarr	=	$AdminDAO->getrows("damagetype","*","1");
//$d1			=	"<select name=\"damagetype[]\" id=\"damagetype[]\" style=\"width:80px;\">";
for($i=0;$i<sizeof($damagesarr);$i++)
{
	$damagetypes		.=	"<option value = \"".$damagesarr[$i][pkdamagetypeid]."\">".$damagesarr[$i][damagetype]."</option>";
}
//$damages		=	$d2;
//end damages
// stores
$src_store	=	$AdminDAO->getrows("store,consignment","storename,storedb,consignmentname","pkstoreid = fkstoreid AND pkconsignmentid = '$id'");
$dest_store	=	$AdminDAO->getrows("store,consignment","storename,pkstoreid,storedb,storeip,username,password,consignmentname","pkstoreid = fkdeststoreid AND pkconsignmentid = '$id'");
$pkconsignmentdetailids	=	$AdminDAO->getrows("consignmentdetail","pkconsignmentdetailid,consignmentdetailstatus,fkbarcodeid","fkconsignmentid='$id'");
$dest_storeid	=	$dest_store[0]['pkstoreid'];
$dest_storeid	=	"";
$dest_storedb 	=	$dest_store[0]['storedb'];
$dest_storeip	=	$dest_store[0]['storeip'];
$dest_username	=	$dest_store[0]['username'];
$dest_password	=	$dest_store[0]['password'];
// creating a barcode array to retrieve remote data
$barcodeid_array	=	array();
for($x=0;$x<sizeof($pkconsignmentdetailids);$x++)
{
	$barcodeid_array[$x]	=	$pkconsignmentdetailids[$x]['fkbarcodeid'];
}
// merging array into comma separated string
$barcodestr	=	implode(",",$barcodeid_array);
//retrieving remote price info
if(mysql_connect($dest_storeip,$dest_username,$dest_password))
{
	$dbsel		=	mysql_select_db($dest_storedb);
	// 2. retrive local prices
	// retrieving price information from store pricechange
	$query		=	"SELECT fkbarcodeid,price FROM $dest_storedb.pricechange WHERE fkbarcodeid IN ($barcodestr)";
	$results	=	mysql_query($query);
	while ($first_array	=	@mysql_fetch_assoc($results))
	{
		// array 1 contains barcodeid
		$firstarr[$first_array['fkbarcodeid']]	=	$first_array['price'];
		$bcodes[]	=	$first_array['fkbarcodeid'];
	}
	// rebuilding entries from barcode $barcodeid_array
	for($y=0;$y<sizeof($barcodeid_array);$y++)
	{
		if(!(in_array($barcodeid_array[$y],$bcodes)))
	   {
		   $newbarcode_array[]	=	$barcodeid_array[$y];
	   }
	}
	// the new barcode string is
	$newbarcodestring	=	implode(",",$newbarcode_array);
	// retrieving price information from store stock
	$query2		=	"SELECT fkbarcodeid,MAX(retailprice) retailprice FROM $dest_storedb.stock WHERE fkbarcodeid IN ($newbarcodestring) GROUP BY fkbarcodeid";
	$results2	=	mysql_query($query2);
	while ($second_array	=	@mysql_fetch_assoc($results2))
	{
		// array 1 contains barcodeid
		$secondarr[$second_array['fkbarcodeid']]	=	$second_array['retailprice'];
	}
}
else
{
	$remoteserver	=	$dest_store[0]['storename']." server can not be connected";
}
/*echo "<pre>";
print_r($firstarr);
print_r($secondarr);
print_r($newbarcode_array);
echo "</pre>";*/
?>
<script language="javascript" type="text/javascript">
function hidediv(divid)
{
	document.getElementById(divid).style.display='none';	
}

function hidethis(id)
{
	id = parseInt(id);
	if(id == 10)
	{
		document.getElementById('btn'+id).style.display = 'none';
	}
	document.getElementById('btn'+id).style.display = 'none';
	id	=	id+1;
	document.getElementById(id).style.display = 'block';
	document.getElementById(id).style.display = 'table-row';
}
function submitfrm()
{
	loading('System is Saving The Data....');
	options	=	{	
					url : 'receiveconsignmentact.php',
					type: 'POST',
					success: response
				}
	jQuery('#adstockfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Consignment has been received.',0,5000);
		jQuery('#maindiv').load('manageconsignments.php');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function stopfrm()
{
	<?php
	$currentstatus	=	$constatus[0]['fkstatusid'];
	if($currentstatus!=9)
	{
	?>
		alert('The movement status must be approved before you can receive it.');
		$('#maindiv').load('manageconsignments.php');
	<?php
	}
	else if($dest_storeid!=3)
	{
		?>
		if(document.getElementById('updateprice').checked==true)
		{
			conf	=	confirm('Are you sure, you want to update the prices');
			if(conf)
			{
				submitfrm();
			}
			else
				return false;
		}
		else
		{
			submitfrm();
		}
	<?php
	}
	else
	{
	?>
	submitfrm();
	<?php
	}
	?>
}
function checkunits(num,t)
{
	if(t =='d')
	{
		var dnum	=	parseInt(document.getElementById('damaged'+num).value);
		var unum	=	parseInt(document.getElementById('units'+num).value);
		if(dnum>unum)
		{
			alert('Damaged Units can not be more than Total Units');
			document.getElementById('damaged'+num).focus();
			return false;
		}
	}//if
	else
	{
		var dnum	=	parseInt(document.getElementById('received'+num).value);
		var unum	=	parseInt(document.getElementById('units'+num).value);
		var dam		=	parseInt(unum - dnum);
		document.getElementById('damaged'+num).value = dam;
	}//else
}
</script>
<div id="loaditemscript">
</div>
<div id="currency" style="display:none;"></div>
<div id="cur">
</div>
<div id="instancediv">
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
<input type="hidden" value="<?php echo $id;?>" id="id" name="id" />
<fieldset>
<legend>Receiving Consignment < <?php echo $src_store[0]['consignmentname'];?> > From < <?php echo $src_store[0]['storename']; ?> > To < <?php echo $dest_store[0]['storename']; ?> >
</legend><div  style="float:right;">
<span class="buttons">
<button type="button" class="positive" onclick="stopfrm();">
    <img src="../images/tick.png" alt=""/> 
    Save
</button>
 <a href="javascript:void(0);" onclick="hidediv('instancediv');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
</a>
</span>
</div>
<br /><br />
<table>
	<tr>
    	<th colspan="6" align="right" style="background-color:#F00">Update Price</th>
    	<td colspan="7" style="background-color:#F00"><?php if($dest_storeid!=3) {?><input type="checkbox" name="updateprice" id="updateprice" checked="checked" value="1" /><?php } else { echo "Disabled";} ?></td>
    </tr>
    <tr>
        <th>Serial</th>
        <th>Barcode</th>
        <th>Item</th>
        <th>Expiry</th>
        <th>Units Sent</th>
        <th>Units Received</th>
        <th>Damaged</th>
        <th>Damage Type</th>
        <th>Cost Price</th>
        <th>Sale Price</th>
        <th>New Sale Price</th>
        <th>Store Sale Price</th>
        <th>Status</th>
    </tr>
    <?php
	$serial	=	0;
	$bgcolor=	'#DFEFFF';
	$dest_storedb =	$dest_store[0]['storedb'];
	for($i=0;$i<sizeof($pkconsignmentdetailids);$i++)
	{
		$serial	=	$serial	+1;
		$cdid 	=	$pkconsignmentdetailids[$i]['pkconsignmentdetailid'];
		$status	=	$pkconsignmentdetailids[$i]['consignmentdetailstatus'];
		
		$stock_details				=	$AdminDAO->getrows("barcode b,consignmentdetail cd",
											   "barcode,itemdescription,cd.*",
											   "fkbarcodeid = pkbarcodeid AND cd.pkconsignmentdetailid = '$cdid'");
		$barcode		=	$stock_details[0]['barcode'];
		$itemdescription=	$stock_details[0]['itemdescription'];
		$quantity		=	$stock_details[0]['quantity'];
		$expiry			=	$stock_details[0]['expiry'];
		$retailprice	=	$stock_details[0]['retailprice'];
		$newretailprice	=	$stock_details[0]['newretailprice'];
		$costprice		=	$stock_details[0]['costprice'];
		$receivedquantity	=	$stock_details[0]['receivedquantity'];
		$fkdamagetypeid		=	$stock_details[0]['fkdamagetypeid'];
		$fkbarcodeid		=	$stock_details[0]['fkbarcodeid'];
		$damagetype		=	$AdminDAO->getrows("damagetype","damagetype","pkdamagetypeid= '$fkdamagetypeid'");
		$damagetype		=	$damagetype[0]['damagetype'];
		$receivetime	=	$stock_details[0]['receivetime'];
		$receivedby		=	$stock_details[0]['receivedby'];
		$receivedby		=	$AdminDAO->getrows("addressbook","concat(firstname,' ',lastname) receivedby","pkaddressbookid= '$receivedby'");
		$receivedby		=	$receivedby[0]['receivedby'];
		//$cdid			=	$stock_details[0]['cdid'];
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
		$storeprice	=	$firstarr[$fkbarcodeid];
		if($storeprice	==	'')
		{
			$storeprice	=	$secondarr[$fkbarcodeid];
		}
	?>
    <tr id="<?php echo $i;?>" bgcolor="<?php echo $bgcolor;?>" >
    <td>
    	<?php echo $serial;?>
    </td>
    <td>
    <?php echo $barcode;?>
    <input type="hidden" name="cdid[]" id="cdid<?php echo $i;?>" value="<?php echo $cdid;?>" />
    </td>
    <td>
    	<?php echo $itemdescription;?>
    </td>
    <td>
        	<?php echo date("d/m/y",$expiry);?>
        </td>    
            <td align="center">
            	<?php
					if($status==1)
					{
						echo $quantity;
					}
					else
					{
					
				?>
				<input type="text" name="units[]" readonly="readonly" onkeypress="return isNumberKey(event)" class="text"  id="units<?php echo $i;?>"  size="5" value="<?php echo $quantity;?>" onblur="checkunits('<?php echo $i;?>','r');" />
               <?php
				}//else
			   ?>
            </td>
        <td align="center">
        	<?php
            if($status==1)
            {
                echo $receivedquantity;
            }
            else
            {
			?>
            <input type="text" name="received[]" onkeypress="return isNumberKey(event)" class="text"  id="received<?php echo $i;?>"  size="5" value="<?php echo $quantity;?>" onblur="checkunits('<?php echo $i;?>','r');" />
           	<?php
			}
			?>
        </td>
      
        	<?php
       	 	if($status==1)
            {
			?>
            	<td align="center">
                	<?php
                    $d	=	 ($quantity - $receivedquantity);
					echo $d;
					?>
                </td>
            <?php
			}
			else
			{
			?>
        		<input type="text" name="damaged[]"  readonly="readonly" value="0" onkeypress="return isNumberKey(event)" class="text" id="damaged<?php echo $i;?>" size="5" onblur="checkunits('<?php echo $i;?>','d');" /></td>
                 
           	<?php
			}
			
			?>
            <?php
       	 	if($status==1)
            {?>
				<td><?php echo $damagetype;?></td>
			<?php
            }
			else
			{
			?>
				<td align="left">
                <select name="damagetypes<?php echo $i;?>" style="width:80px;">
				<?php echo $damagetypes;?>
                </select>
                </td>
             <?php
			}//else
			 ?>
       
        <td align="center"><?php echo $costprice;?> </td>
        <td align="center"><?php echo $retailprice;?></td>
        
        <td align="center">
        <?php
         if($status==1)
            {
				echo $newretailprice;
			}
			else
			{
			?>
        <input type="text" name="newsaleprice[]" onkeypress="return isNumberKey(event)" class="text" id="newsp<?php echo $i;?>" value="<?php echo $retailprice;?>" size="5" /></td>
        <?php
			}//else
		?>
        <?php /*?><td align="center"><?php echo $storeprice;?></td><?php */?>
        <td align="center"><?php if($storeprice==''){ echo $remoteserver;} else { echo $storeprice; }?></td>
        <td>
        	<?php
				$receivetime	=	date('d/m/y h:i:s a',$receivetime);
				if($status==1)
				{
					echo "$receivedby Received $receivedquantity on $receivetime";
				}
				else
				{
					echo "Not Received";
				}
			?>
        </td>
    </tr>
    <?php
	}//for
	?>
</table>
<div class="buttons">
<button type="button" class="positive" <?php echo $disabled;?>>
    <img src="../images/tick.png" alt=""/> 
    Save
</button>
 <a href="javascript:void(0);" onclick="hidediv('instancediv');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
</a>
</div>
</fieldset>
</form>
<br />
</div>
</div>
<script language="javascript" type="text/javascript">
//document.getElementById('barcode1').focus();
</script>