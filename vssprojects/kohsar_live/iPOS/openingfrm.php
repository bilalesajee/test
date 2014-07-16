<?php
include_once("includes/security/adminsecurity.php");
global $AdminDAO;
?>
<script language="javascript">
document.getElementById('amount').focus();
function openingprocess()
{   //added by Yasir -- 18-07-11
	document.openingfrm.button.disabled = "disabled";	
	//
	options	={	
			url : 'insertopening.php',
			type: 'POST',
			success: processopening
		}
		jQuery('#openingfrm').ajaxSubmit(options);
}
function processopening(text)
{
	if(text!=''){
		notice(text,'',5000);
		//added by Yasir -- 18-07-11
		document.openingfrm.button.disabled = "";	
		//
	}
	else
		document.getElementById('openingfrmdiv').style.display	=	'none';
}
</script>
<form  name="openingfrm" id="openingfrm" method="post">
<table id="useralertsdiv2" class="epos">
<tr>
<th colspan="2">Opening Form</th>
</tr>
<tr>
<td>Opening Amount</td>
<td><input type="text" name="amount" class="text" id="amount" autocomplete="off" onkeypress="return isNumberKey(event)" onkeydown="javascript:if(event.keyCode==13) {return false;}"/></td>
</tr>
<tr>
<td colspan="2">
<span class="buttons" style="font-size:12px;float:left;padding:2px;margin-left:115px;">
            <button type="button" name="button" id="button" onclick="openingprocess();" title="CTRL+S">
                <img src="images/disk.png" alt=""/> 
               Save
            </button>
            <button type="button" name="button2" id="button2" onclick="hidediv('openingfrmdiv');" title="Cancel Opening">
                <img src="images/cross.png" alt=""/> 
               Cancel
            </button> 
</span>          
            </td>
</tr>
</table>
</form>