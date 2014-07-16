<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$stockid	=	$_GET['id'];
$qstring	=	$_SESSION['qstring'];
$storeid;
if($stockid!='')
{
	/*$storerow 	= 	$AdminDAO->getrows("store","*","  pkstoreid<>'$storeid' ");
	$stores		=	$Component->makeComponent("d","storeiddest",$storerow,"pkstoreid","storename",1,$selected_store);*/
	
	$damagerow 	= 	$AdminDAO->getrows("damagetype","*");
	$damages	=	$Component->makeComponent("d","damage",$damagerow,"pkdamagetypeid","damagetype",1);

	$stockrow 	= 	$AdminDAO->getrows("$dbname_detail.stock","quantity,unitsremaining,expiry,fkbarcodeid,fkbrandid"," pkstockid='$stockid'");
	$quantity	=	$stockrow[0]['quantity'];
	$unitsremaining=$stockrow[0]['unitsremaining'];
	//$expiry		=	$stockrow[0]['expiry'];
	$codeid		=	$stockrow[0]['fkbarcodeid'];
	//$fkbrandid	=	$stockrow[0]['fkbrandid'];
	$barcodearray 	= 	$AdminDAO->getrows("barcode b","barcode"," pkbarcodeid='$codeid'");
	$barcodemove	=	$barcodearray[0]['barcode'];
}
?>

<script language="javascript" type="text/javascript">
	function checklevel()
	{	
		var rem	=0;
		var move=0;
		rem			=	document.getElementById('unitsremaining').value;
		move		=	document.getElementById('moveunits').value;	
		/*var storeid	=	document.getElementById('storeiddest').value;
		if(storeid=='')
		{
			if(document.getElementById('storeiddest').disabled==false)
			{
				alert('Please select destination store first.');
				document.getElementById('storeiddest').focus();
				return false;
			}
		}	*/
		if(move=='')
		{
			alert('Please enter Units qauntity to move.');
			document.getElementById('moveunits').focus();
			return false;
		}
		//alert('To Move='+move+'rem='+rem);
		
		var chk	=rem-move;
		if(chk < 0)
		{
			
		
			alert('You can not move more than ( '+rem+' )');
			document.getElementById('moveunits').focus();
			return false;
		}
		movestock();
		return false;
	}
	function movestock()
	{
		loading('Syetem is Saving The Data....');
		options	=	{	
						url : 'insertdamages.php',
						type: 'POST',
						success: movestockresponse
					}
		jQuery('#movestockform').ajaxSubmit(options);
	}
	function movestockresponse(text)
	{
		
		if(text=='')
		{
			adminnotice("The stock has been Moved to  Damages.","0",5000);
			jQuery('#maindiv').load('managestocks.php?qs='+'<?php echo $qs;?>','',function(){loaddetails();});
			//hidediv('movestock');
	
		}else
		{
			adminnotice(text,"0",5000);
		}
		
	}
	function loaddetails()
	{
		jQuery('#stockdetailsdiv').load('stockdetail.php?nobrand=-1&id=<?php echo $codeid;?>');	
	}
 	//ui date picker
	jQuery(function($)
	{
	
		$("#deadline").datepicker({dateFormat: 'yy-mm-dd'});
	});
	/*function disablesupplier(val)
	{
		if(val=='damage')
		{
			// $('#storeiddest :input').attr('disabled', true);
			document.getElementById('storeiddest').disabled=true;
			document.getElementById('damage').disabled=false;
			jQuery('#damagerow').show();
			//jQuery('#input:storeiddest').disable();
			
			
		}
		if(val=='demand')
		{
			//alert(demand);
			document.getElementById('storeiddest').disabled=false;
			document.getElementById('damage').disabled=true;
			jQuery('#damagerow').hide();
		}
		
	}*/
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="movestockdiv">
<form  name="movestockform" id="movestockform" style="width:920px;" class="form">
<fieldset>
<legend>
    Add Damages for <?php echo $barcodemove;?></legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="checklevel();">
        <img src="../images/tick.png" alt=""/> 
        <?php if($shipmentid=='-1'){echo 'Save';}else{echo 'Update';}?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('movestockdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
</div>
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	<tr>
	  <td width="25%">
	    Total Units    
	    </td>
	  <td width="75%">
	    <?php echo $quantity;	?>
        <input type="hidden" value="<?php echo $quantity;	?>" name="quantity" id="quantity" />
	    </td>
	  
	  </tr>
	<tr>
	  <td> Units Remaining</td>
	  <td>
      	<?php echo $unitsremaining;	?>
        <input type="hidden" value="<?php echo $unitsremaining;	?>" name="unitsremaining" id="unitsremaining" />
       </td>
	  </tr>

    <tr>
	  <td> Damaged Units</td>
	  <td><input name="moveunits" id="moveunits" type="text" value="" onkeypress="return numbersonly(event, false)"/></td>
	  </tr>
	<tr>
	  <td>Comments</td>
	  <td>
      	<textarea name="comments" id="comments"></textarea>
       </td>
	  </tr>
	<tr>
	  <td>&nbsp;</td>
	  <td>
		  <div class="buttons">
            <button type="button" class="positive" onclick="checklevel();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($shipmentid=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('movestockdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
      </td>
	  </tr>

	</tbody>
</table>
<input type="hidden" name="stockid" id="stockid" value="<?php echo $stockid;?>" />
<input type="hidden" name="codeid" id="codeid" value="<?php echo $codeid;?>" />
<input type="hidden" name="fkbrandid" id="fkbrandid" value="<?php echo $fkbrandid;?>" />
</fieldset>	
</form>
</div>
<div id="instancediv"></div>
<script language="javascript">
	document.getElementById('storeiddest').focus();
</script>