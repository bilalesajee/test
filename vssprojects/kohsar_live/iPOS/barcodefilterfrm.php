<?php
include("includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$userSecurity;
//print_r($chequebanks);
?>
<script language="javascript" type="text/javascript">
function savebarcode()
{
	var currentbarcode		=	document.getElementById('currentbarcode').value;
	var fixedbarcode		=	document.getElementById('fixedbarcode').value;
	
	
	if(currentbarcode=='')
	{
		alert("Please enter Current barcode.");
		document.getElementById('currentbarcode').focus();
		return false;
	}
	if(fixedbarcode=='')
	{
		alert("Please Enter Fixed Barcode (Correct Barcode).");
		document.getElementById('fixedbarcode').focus();
		return false;
	}
		options	=	{	
						url : 'savebarcodefilter.php',
						type: 'POST',
						success: successbf
					}
		jQuery('#barcidefilterfrm').ajaxSubmit(options);
		
}
function successbf()
{
	loadsection('main-content','barcodefilterfrm.php');
	//jQuery('#barcode').load('barcodefilterfrm.php');
}
</script>
<div id="loading" class="loading" style="display:none;"></div>
<div id="error"></div>
<div id="barcode" >
<form id="barcidefilterfrm">
    <div class="Table" >
        <div class="Row">
          <div class="Column"><label>Current Barcode</label>
              <input type="text" class="text" name="currentbarcode" id="currentbarcode" onkeydown="javascript:if(event.keyCode==13) {fixedbarcode.focus();return false;}" tabindex="1">
          </div>
          <div class="Column"><label>Fixed Barcode</label>
            <input type="text" class="text" name="fixedbarcode" id="fixedbarcode" autocomplete="off" onkeydown="javascript:if(event.keyCode==13) {savebarcode(); return false;}" >
          </div>
		
		 <!-- case 2cheque -->
		 <br />
		    <div class="Column" style="float:left; position:absolute; left: 110px; top: 126px;">
                <button type="button" onclick="savebarcode();" >
                        <img src="images/tick.png" alt=""/> 
                       Save Now</button>
                <button type="button" onclick="cancelbf()" title="Ctrl+home">
                        <img src="images/cross.png" alt=""/>Cancel
                </button>
       	  </div>
       <!-- <div class="Row">
            
        </div>-->
    </div><!--  Table -->
</form>
</div>
<br />
<br />
<br />
<br />
<div id="barcodefilter">
</div>
<script language="javascript" type="text/javascript">
	document.getElementById('currentbarcode').focus();
	jQuery('#barcodefilter').load('barcodefilter.php');

</script>
<div id="paydetailsdiv"></div>
