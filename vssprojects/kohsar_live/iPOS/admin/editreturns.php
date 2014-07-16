<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$returnid = $_GET['id'];
$returnsarray	=	$AdminDAO->getrows("$dbname_detail.returns r,$dbname_detail.stock,barcode","pkreturnid,pkstockid,barcode,itemdescription,r.quantity qty,returnstatus,fkreturntypeid"," pkreturnid = '$returnid' AND fkstockid=pkstockid AND fkbarcodeid=pkbarcodeid");
$pkreturnid			=	$returnsarray[0]['pkreturnid'];
$pkstockid			=	$returnsarray[0]['pkstockid'];
$barcode			=	$returnsarray[0]['barcode'];
$itemdescription	=	$returnsarray[0]['itemdescription'];
$qty				=	$returnsarray[0]['qty'];
$fkreturntypeid		=	$returnsarray[0]['fkreturntypeid'];
$returnstatus		=	$returnsarray[0]['returnstatus'];
if($id!='-1')
{
	//selecting return types
	$returnsarr	=	$AdminDAO->getrows("returntype","*","1");
	$d1			=	"<select name=\"returntype\" id=\"returntype\" style=\"width:150px;\">";
	for($i=0;$i<sizeof($returnsarr);$i++)
	{
		$returntypeid	=	$returnsarr[$i]['pkreturntypeid'];
		if($fkreturntypeid==$returntypeid)
		{
			$select	=	"selected=\"selected\"";
		}
		$d2			.=	"<option value = \"".$returnsarr[$i]['pkreturntypeid']."\" $select>".$returnsarr[$i]['returntype']."</option>";
		$select	=	"";
	}
	$returns		=	$d1.$d2."</select>";
}
/****************************************************************************/
?>
<script language="javascript">
function addform()
{
	loading('System is saving data....');
	options	=	{	
					url : 'editreturnsact.php',
					type: 'POST',
					success: response
				}
	jQuery('#returnfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Returns data has been saved.',0,5000);
		jQuery('#maindiv').load('managesupplierinvoices.php','',function(){loaddetails();})
	}
	else
	{
		adminnotice(text,0,5000);	
	}
	//hideform();
}
function loaddetails()
{
	jQuery('#editreturns').load('editreturns.php?id=<?php echo $returnid;?>');	
}
function hideform()
{
	document.getElementById('editstock').style.display='none';
}
</script>
<?php
?>
<div id="editreturn">
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="returnfrm" id="returnfrm" onSubmit="addform(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
Edit Returns</legend>
<div style="float:right">
<span class="buttons">
<button type="button" class="positive" onclick="addform();">
    <img src="../images/tick.png" alt=""/> 
    Update
</button>
 <a href="javascript:void(0);" onclick="hidediv('editreturn');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
</a>
</span>
</div>
<table width="529">
	<tbody>
	<tr>
	  <td>Barcode:</td>
	  <td valign="top"><?php echo $barcode; ?></td>
	  </tr>
	<tr>
	  <td>Item:</td>
	  <td valign="top"><?php echo $itemdescription;?></td>
	  </tr>
	<tr>
	  <td>Return Type:</td>
	  <td valign="top"><?php echo $returns;?></td>
	  </tr>
	<tr>
	  <td width="135">Status:</td>
	  <td width="382" colspan="2"><select name="returnstatus" id="returnstatus" style="width:150px;"><option value="c" <?php if($returnstatus=='c'){echo "selected=\"selected\"";}?>>Confirmed</option><option value="p" <?php if($returnstatus=='p'){echo "selected=\"selected\"";}?>>Pending</option></select></td>
	  </tr>
	<tr>
		<td>Quantity:</td>
		<td colspan="2"><input name="qty" id="qty" onfocus="this.select();" type="text" value="<?php echo $qty; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"></td>
	</tr>
	<tr>
	  <td colspan="3"  align="left"> 
	    <div class="buttons">
	      <button type="button" class="positive" onclick="addform();">
	        <img src="../images/tick.png" alt=""/> 
				Update
	        </button>
	      <a href="javascript:void(0);" onclick="hidediv('editstock');" class="negative">
	        <img src="../images/cross.png" alt=""/>
	        Cancel
	        </a>
	      </div>
	    </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="returnid" id="returnid" value = <?php echo $pkreturnid;?> />
<input type="hidden" name="stockid" id="stockid" value = <?php echo $pkstockid;?> />
<input type="hidden" name="original" id="original" value = <?php echo $qty;?> />
</form>
</div>
<script language="javascript">
document.getElementById('qty').focus();
loading('Loading Form...');
</script>