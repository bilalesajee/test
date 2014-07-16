<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$stockid	=	$_GET['id'];
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
	$idarr		=	explode(":",$stockid);
	$srcstoreid		=	$idarr[0];
	$stockid		=	$idarr[1];
	$sql="SELECT storedb,storename from store where pkstoreid='$srcstoreid'";
	$starr		=	$AdminDAO->queryresult($sql);
	$dbstore	=	$starr[0]['storedb'];
	$storenamesource	=	$starr[0]['storename'];
}//end edit
$qstring	=	$_SESSION['qstring'];
echo "THIS IS STORE ID".$storeid;
if($stockid!='')
{
	if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
		$storerow 	= 	$AdminDAO->getrows("store","*","  pkstoreid<>'$storeid' ");
		$stores		=	$Component->makeComponent("d","storeiddest",$storerow,"pkstoreid","storename",1,$selected_store);
		
		$damagerow 	= 	$AdminDAO->getrows("damagetype","*");
		$damages	=	$Component->makeComponent("d","damage",$damagerow,"pkdamagetypeid","damagetype",1);
	
		$stockrow 	= 	$AdminDAO->getrows("$dbname_detail.stock","quantity,unitsremaining,expiry,fkbarcodeid,fkbrandid"," pkstockid='$stockid'");
		$quantity	=	$stockrow[0]['quantity'];
		$unitsremaining=$stockrow[0]['unitsremaining'];
		$expiry		=	$stockrow[0]['expiry'];
		$codeid		=	$stockrow[0]['fkbarcodeid'];
		$fkbrandid	=	$stockrow[0]['fkbrandid'];
		$barcodearray 	= 	$AdminDAO->getrows("barcode b","barcode"," pkbarcodeid='$codeid'");
		$barcodemove	=	$barcodearray[0]['barcode'];
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
		$stores			=	$AdminDAO->getrows("store","storename,pkstoreid,storedb","storedeleted<>1 AND pkstoreid<>'$srcstoreid'");
		$storesel		=	"<select name=\"storeiddest\" id=\"storeiddest\" style=\"width:100px;\"><option value=\"\">Location</option>";
		for($i=0;$i<sizeof($stores);$i++)
		{
			$storename	=	$stores[$i]['storename'];
			$pkstoreid	=	$stores[$i]['pkstoreid'];
			$storedb	=	$stores[$i]['storedb'];
			$select		=	"";
			$storesel2	.=	"<option value=\"$pkstoreid|$storedb\">$storename</option>";
		}
		$stores			=	$storesel.$storesel2."</select>";
		// end stores
		
		$damagerow 	= 	$AdminDAO->getrows("damagetype","*");
		$damages	=	$Component->makeComponent("d","damage",$damagerow,"pkdamagetypeid","damagetype",1);
	
		$stockrow 	= 	$AdminDAO->getrows("$dbstore.stock","quantity,unitsremaining,expiry,fkbarcodeid,fkbrandid"," pkstockid='$stockid'");
		$quantity	=	$stockrow[0]['quantity'];
		$unitsremaining=$stockrow[0]['unitsremaining'];
		$expiry		=	$stockrow[0]['expiry'];
		$codeid		=	$stockrow[0]['fkbarcodeid'];
		$fkbrandid	=	$stockrow[0]['fkbrandid'];
		$barcodearray 	= 	$AdminDAO->getrows("barcode b","barcode"," pkbarcodeid='$codeid'");
		$barcodemove	=	$barcodearray[0]['barcode'];
	}//end edit
}
?>

<script language="javascript" type="text/javascript">
	function checklevel()
	{	
		var rem	=0;
		var move=0;
		rem		=	document.getElementById('unitsremaining').value;
		move	=	document.getElementById('moveunits').value;	
		var storeid	=	document.getElementById('storeiddest').value;
		if(storeid=='')
		{
			if(document.getElementById('storeiddest').disabled==false)
			{
				alert('Please select destination store first.');
				document.getElementById('storeiddest').focus();
				return false;
			}
		}	
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
						url : 'insertmovestock.php',
						type: 'POST',
						success: movestockresponse
					}
		jQuery('#movestockform').ajaxSubmit(options);
	}
	function movestockresponse(text)
	{
		
		if(text=='')
		{
			<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition?>
				adminnotice("The stock has been Moved to destination location.","0",5000);
				jQuery('#maindiv').load('managestocks.php?qs='+'<?php echo $qs;?>','',function(){loaddetails();});
				//hidediv('movestock');
			<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012?>
				adminnotice("The stock has been Moved to destination location.","0",5000);
				hidediv('movestock');
				jQuery('#maindiv').load('managestocks.php?qs='+'<?php //echo $qs;?>');
				
				//jQuery('#movestock').load('movestock.php?qs='+'<?php //echo $qs;?>');
				hidediv('stockdetailsdiv');
			<?php }//end edit?>
		}else
		{
			adminnotice(text,"0",5000);
		}
		
	}
	<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition?>
		function loaddetails()
		{
			jQuery('#stockdetailsdiv').load('stockdetail.php?nobrand=-1&id=<?php echo $codeid;?>');	
		}
		//ui date picker
		jQuery(function($)
		{
		
			$("#deadline").datepicker({dateFormat: 'yy-mm-dd'});
		});
	<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012?>
		//ui date picker
		jQuery(function($)
		{
		
			$("#deadline").datepicker({dateFormat: 'yy-mm-dd'});
		});
	<?php }//end edit?>
	function disablesupplier(val)
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
		
	}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="movestockdiv">
<form  name="movestockform" id="movestockform" style="width:920px;" class="form">
<fieldset>
<legend>
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition?>
    Move Stock Of <?php echo $barcodemove;?></legend>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012?>
    Move Stock Of <?php echo $barcodemove.' From '.$storenamesource?></legend>
<?php }//end edit?>    
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
	<tr id="deststore">
	  <td> Destination Stores</td>
	  <td>
      	<?php
			echo $stores;
		?>
      </td>
	  </tr>
	
	<tr>
	  <td>Move To</td>
	  <td><input type="radio" name="action" id="action" value="demand" checked="checked" onclick="disablesupplier(this.value)"/>
	    Move 
	      <input type="radio" name="action" id="action" value="damage" onclick="disablesupplier(this.value)"/>
	      Damage</td>
	  </tr>
      <tr id="damagerow" style="display:none">
	  <td width="25%"> Damage Type</td>
	  <td width="75%">
      	<?php
			echo $damages;
		?>
      </td>
	  </tr>
	<tr>
	  <td>Dead Line</td>
	  <td><input name="deadline" id="deadline" type="text"  readonly="readonly"  value="<?php if($deadline==''){echo date('Y-m-d');}else{echo $deadline;}?>"/></td>
	  </tr>
	<tr>
    	<td>
 			Total Units    
        </td>
        <td>
        	<input name="quantity" id="quantity" type="text" value="<?php echo $quantity;	?>" readonly="readonly"/>
        </td>
       
    </tr>
	<tr>
	  <td> Units Remaining</td>
	  <td>
      	<input name="unitsremaining" id="unitsremaining" type="text" value="<?php echo $unitsremaining;	?>" readonly="readonly"/>
       </td>
	  </tr>

    <tr>
	  <td> Units to Move</td>
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
<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012?>
	<input type="hidden" name="srcstoreid" id="srcstoreid" value="<?php echo $srcstoreid;?>" />
<?php }//end edit ?>
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