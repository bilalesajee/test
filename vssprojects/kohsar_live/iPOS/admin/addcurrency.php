<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$currencyid = $_GET['id'];
session_start();
$qs	=	$_SESSION['qstring'];
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];

if($currencyid=="")
{
	$currencyid="-1";
}
else if($currencyid !="-1")
{
	$currency = $AdminDAO->getrows('currency','*',"`pkcurrencyid`='$currencyid' AND currencydeleted <> '1'");
	$currencyname = $currency[0]['currencyname'];
	$symbol = $currency[0]['currencysymbol'];
	$rate = $currency[0]['rate'];
}
?>
<script language="javascript">
function addform()
{
	loading('System is Saving The Data....');
	options	=	{	
					url : 'insertcurrency.php',
					type: 'POST',
					success: response
				}
	jQuery('#curform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Currency data has been saved.',0,5000);
		jQuery('#maindiv').load('managecurrencies.php?'+'<?php echo $qs?>');		
	}
	else
	{
		adminnotice(text,0,6000);	
	}
}
/*function hideform()
{
	
	document.getElementById('curdiv').style.display='none';
}*/
</script>
<div id="curdiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="curform" id="curform" onSubmit="addform(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
	<?php 
		if($currencyid == '-1')
		{
			print"Adding New Currency";
		}
		else
		{
			print"Editing: $currencyname";
		}
	?>
</legend>
<div style="float:right">
<?php /*?><span class="buttons">
    <button type="button" class="positive" onclick="addform();">
        <img src="../images/tick.png" alt=""/> 
        <?php if($currencyid=='-1'){echo 'Save';}else{echo 'Update';}?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('curdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span><?php */?>
  		<?php
	   		 buttons('insertcurrency.php','curform','maindiv','managecurrencies.php',$place=1,$formtype)
	 		?>
</div>
<table>
	<tbody>
	<tr>
		<td>Currency Name: <span class="redstar" title="This field is compulsory">*</span></td>
		<td colspan="2">
		<input name="name" id="name" type="text" value="<?php echo $currencyname; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"></td>
	</tr>
	<tr>
		<td>Currency Symbol: <span class="redstar" title="This field is compulsory">*</span></td>
		<td colspan="2"><input type="text" name="symbol" id="symbol" value="<?php echo $symbol ?>" /></td>
	</tr>
	<tr>
		<td>Rate in <?php echo $defaultcurrency;?>: <span class="redstar" title="This field is compulsory">*</span></td>				
		<td valign="top"><input type="text" name="rate" id="rate" value="<?php echo $rate ?>" onkeypress="return isNumberKey(event);"/></td>
		<!--<td valign="top"><input name="nsupplier" id="nsupplier" type="text" value="Add New" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" onFocus="if(this.value=='Add New')this.value='';"/></td>-->
	</tr>
	<tr>
		<td colspan="3"  align="left">
		  <?php /*?> <div class="buttons">
            <button type="button" class="positive" onclick="addform();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($currencyid=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('curdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div><?php */?>
		    <?php
	   			 buttons('insertcurrency.php','curform','maindiv','managecurrencies.php',$place=0,$formtype)
	 		?>
        </td>				
	</tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="currencyid" value = <?php echo $currencyid?> />	
</form>
</div><br />
<script language="javascript">
//document.curfrm.name.focus();
loading('Loading Form...');
</script>
<script language="javascript">
	focusfield('name');
</script>