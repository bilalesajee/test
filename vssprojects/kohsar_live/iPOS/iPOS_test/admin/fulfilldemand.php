<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$demanddetailid	=	$_GET['id'];
$qstring	=	$_SESSION['qstring'];
if($demanddetailid!='')
{
	$query	=" SELECT 
				b.itemdescription,
				s.pkstockid,
				pkbarcodeid,
				dd.units,
				d.demandstatus,
				(select storename from store where pkstoreid=d.fkstoreid) as storename
				
			FROM 
				barcode b, demanddetails dd,stock s,demand d
			WHERE
				b.pkbarcodeid =	dd.fkbarcodeid AND
				dd.pkdemanddetailid='$demanddetailid' AND
				s.fkbarcodeid	=	pkbarcodeid AND
				dd.fkdemandid=d.pkdemandid
				";
	$demanddetailrow	= 	$AdminDAO->queryresult($query);
	$stockid			=	$demanddetailrow[0]['pkstockid'];
	$barcodeid			=	$demanddetailrow[0]['pkbarcodeid'];
	$pkdemanddetailid	=	$demanddetailid; //$demanddetailrow[0]['pkdemanddetailid'];
	$demandstatus	=	$demanddetailrow[0]['demandstatus'];

	if($demandstatus=='f')
	{
		
		print'<script>adminnotice("This demad has been already fulfilled.","0",5000);</script>';
		exit;
	}
	if($stockid!='')
	{
		$and=" AND
					fkbarcodeid='$barcodeid' ";
	}
	else
	{
		/*$and=" AND
					pkstockid='$barcodeid' ";*/
	}
	$query="SELECT pkstockid,
					quantity,
					unitsremaining,
					unitsreserved,
					from_unixtime(expiry,'%m-%d-%y') as expiry,
					fkbarcodeid,
					fkbrandid,
					shipmentdate
				FROM 
					stock s, shipment sh
				WHERE 
					fkshipmentid=pkshipmentid AND
					s.fkstoreid='$storeid'
					
					$and
					
					";
					//AND s.fkstoreid <> '$storeid'
	$stockrow 	= 	$AdminDAO->queryresult($query);
}
?>

<script language="javascript" type="text/javascript">
	
	function movestock()
	{
		loading('System is Saving The Data....');
		options	=	{	
						url : 'insertfullfilldemand.php',
						type: 'POST',
						success: movestockresponse
					}
		jQuery('#movestockform').ajaxSubmit(options);
	}
	function movestockresponse(text)
	{
		
		if(text=='')
		{
			//jQuery("#error").show().html(text);
			adminnotice("Demand has been successfully updated.","0",5000);
			jQuery('#maindiv').load('managedemands.php');
			hidediv('sugridchild');
			hidediv('sugrid');
		}else
		{
			//jQuery("#error").show().html(text);
			adminnotice(text,"0",5000);
		}
		
	}
 	//ui date picker
	
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="fulfilldemand">
<form  name="movestockform" id="movestockform" style="width:920px;" class="form">
<fieldset>
<legend>
  <?php echo $demanddetailrow[0]['demandname'];?>  Full Fill Demand for " <?php echo $demanddetailrow[0]['itemdescription'];?> "</legend>
<div style="float:right">
<span class="buttons">
<button type="button" class="positive" onclick="movestock();">
    <img src="../images/tick.png" alt=""/> 
    Fulfill Demand
</button>
 <a href="javascript:void(0);" onclick="hidediv('fulfilldemand');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
</a>
</span>
</div>
<table cellpadding="0" cellspacing="2" width="50%">
	<tbody>
	<tr id="deststore">
	  <td><b>Destination Stores</b></td>
	  <td colspan="4">
      	<?php echo $demanddetailrow[0]['storename'];?>
        <input type="hidden" name="deststore" id="deststore" value="<?php echo $demanddetailrow[0]['fkstoreid'];?>" />
      </td>
	  </tr>
	<tr>
	  <td><b>Dead Line</b></td>
	  <td colspan="4">
      	<?php echo date("d-m-y",$demanddetailrow[0]['deadline']);?>
      </td>
	  </tr>
	<tr>
    	<td>
 			<b>Demanded Units</b>
        </td>
        <td colspan="4">
        	<?php echo $demanddetailrow[0]['units'];?>
        	<input type="hidden" name="demandunits" id="demandunits" value="<?php echo $demanddetailrow[0]['units'];?>" />
        </td>
       
    </tr>
	<tr>
    <td colspan="4">&nbsp;</td>
    </tr>
	<tr>
	  <td width="15%" align="center"><strong>Total Units</strong></td>
	  <td width="10%" align="center"><strong>Remaining</strong></td>
	  <td width="15%" align="center"><strong>Exp/Arrival</strong></td>
	  <td width="10%" align="center"><strong>To Move</strong></td>
	  </tr>
	<?php
	//dump($stockrow);
	
	for($s=0;$s<count($stockrow);$s++)
	{
	$unitsremaining =	$stockrow[$s]['unitsremaining'];
	$unitsreserved	=	$stockrow[$s]['unitsreserved'];
	$expiry			=	$stockrow[$s]['expiry'];
	$codeid			=	$stockrow[$s]['fkbarcodeid'];
	$quantity		=	$stockrow[$s]['quantity'];
	$shipmentdate	=	$stockrow[$s]['shipmentdate'];
	$stockid		=	$stockrow[$s]['pkstockid'];
	?>
    <tr>
	  <td width="8%" align="center">
      	<input name="totalunits[]" type="text" id="totalunits[]" value="<?php echo $quantity;?>" size="5" readonly="readonly"/>
       </td>
	  <td align="center"><input name="unitsremaining[]" type="text" id="unitsremaining[]" value="<?php echo $unitsremaining+$unitsreserved;?>" size="5" readonly="readonly"/></td>
	  <td align="center">
      	<?php
		if($expiry>0)
		{
		?>
        <input name="expiry[]" type="text" id="expiry[]" value="<?php echo $expiry;	?>" size="15" readonly="readonly"/>
     	<?php
		}
		else
		{
		?>
			 <input name="shipmentdate[]" type="text" id="shipmentdate[]" value="<?php echo $shipmentdate;	?>" size="15" readonly="readonly"/>
		<?php
        }
		?>
      </td>
	  <td align="center">
      
      <input name="unitstomove[]" type="text" id="unitstomove[]"  size="5" value="0"/>
      <input name="stockid[]" type="hidden" id="stockid[]"  size="5" value="<?php echo $stockid;?>"/>
      
      </td>
	</tr>
	<?php
	}
	?>
	<tr>
	  <td colspan="5">&nbsp;</td>
	  </tr>
	<tr>
	  <td>&nbsp;</td>
	  <td colspan="4">
          <div class="buttons">
            <button type="button" class="positive" onclick="movestock();">
                <img src="../images/tick.png" alt=""/> 
                Fulfill Demand
            </button>
             <a href="javascript:void(0);" onclick="hidediv('fulfilldemand');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
        </td>
	  </tr>

	</tbody>
</table>

<input type="hidden" name="pkdemanddetailid" id="pkdemanddetailid" value="<?php echo $pkdemanddetailid;?>" />
<input type="hidden" name="unitsreserved" id="unitsreserved" value="<?php echo $unitsreserved;?>" />
<input type="hidden" name="codeid" id="codeid" value="<?php echo $codeid;?>" />

</fieldset>	
</form>
</div>
<div id="instancediv"></div>
<script language="javascript">
	//document.getElementById('unitstomove[0]').focus();
</script>