<?php
session_start();
include_once("includes/security/adminsecurity.php");
global $AdminDAO;
$currencies	=	$AdminDAO->getrows("currency","*","1 ORDER BY currencyname ASC");
$key	=	md5("kstr");
?>
<script language="javascript" type="text/javascript">
function closingprocess()
{   
    // added by Yasir -- 18-07-11
	document.closingfrm.button.disabled = "disabled";	
	//
	//var declearedamount = document.getElementById('declearedamount').value;
	//jQuery('main-content').load("closingprocess.php?declearedamount="+declearedamount);
	loading('Loggin out ...');
	options	={	
			url : 'closingprocess.php',
			type: 'POST',
			success: closingres
		}
		jQuery('#closingfrm').ajaxSubmit(options,function(){return false});
}
function closingres(text)
{ 
	if(text=='')
	{
		var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=650,height=600,left=100,top=25';
		//
		//window.open('http://smk.esajee.com/sendmail.php?id='+'<?php echo $closingsession;?>&pm=<?php echo $key;?>','Closing',display); ////commented by jafer on [23-02-2012] because is sy owners ko b mail jaati ha	
		//
		//var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=650,height=600,left=100,top=25';
		//window.open('closingprint.php?closingid='+'<?php //echo $closingsession;?>','Closing',display); 
		//document.getElementById('shortcuts').style.display='none';
		//jQuery("#main-content").load("closinginfo.php");
		notice('This Closing session has been completed.','',5000);
		$('#closingclose').load('closetransactions.php');
		$('#closingfrm').hide();
		//return false;
	}
	else
	{
	  // added by yasir 22-06-11	
		notice(text,'',5000);		
		//$('#closingclose').load('closetransactions.php');
		//$('#closingfrm').hide();	
		// added by Yasir -- 18-07-11
		document.closingfrm.button.disabled = "";	
		//
	}
}
</script>
<div id="closingclose"></div>
<form  name="closingfrm" id="closingfrm" method="post">
<table class="pos">
<tr> 
<th colspan="2">Closing Form <?php if(!isset($closingsession) || $closingsession=='' || $closingsession==0)
{?><span style="color:#F00;"><?php echo "(No active closing available)";?></span><?php }?></th>
</tr>
<tr>
  <td>Password</td>
  <td><input type="password" name="password" class="text" id="password" /></td>
</tr>
<tr>
<td>Declared Amount</td>
<td><input type="text" name="declaredamount" class="text" id="declaredamount" autocomplete="off" onkeypress="return isNumberKey(event)"/></td>
</tr>
<tr>
<td>Total Cheques</td>
<td><input type="text" name="totalcheques" class="text" id="totalcheques" autocomplete="off" onkeypress="return isNumberKey(event)"/></td>
</tr>
<tr>
<td>Cheque Amount</td>
<td><input type="text" name="chequeamount" class="text" id="chequeamount" autocomplete="off" onkeypress="return isNumberKey(event)"/></td>
</tr>
<tr>
<th colspan="2">Currencies</th>
</tr>
<?php
for($i=0;$i<sizeof($currencies);$i++)
{
?>
<tr>
<td><?php echo $currencies[$i]['currencyname'];?></td>
<td><input type="hidden" name="currencyid[]" value="<?php echo $currencies[$i]['pkcurrencyid'];?>" /><input type="text" name="<?php echo $currencies[$i]['pkcurrencyid'];?>" id="<?php echo $currencies[$i]['pkcurrencyid'];?>" class="text" onkeypress="return isNumberKey(event);" autocomplete="off" /> </td>
</tr>
<?php 
}
?>
<tr>
<td colspan="2">
<span class="buttons" style="font-size:12px;float:left;padding:2px;margin-left:150px;">
            <button type="button" name="button" id="button" onclick="closingprocess();" title="CTRL+S">
                <img src="images/disk.png" alt=""/> 
               Save
            </button>
            <button type="button" name="button2" id="button2" onclick="hidediv('closingfrmdiv');" title="Cancel Closing">
                <img src="images/cross.png" alt=""/> 
               Cancel
            </button>
</span>
</td>
</tr>
</table>
</form>
<script language="javascript">
document.getElementById('password').focus();
</script>