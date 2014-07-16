<?php
include("../../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;

$reportid = $_GET['id'];

$param	  =	$_GET['param'];

$qs	=	$_SESSION['qstring'];

$today	=	date("d-m-Y",time());

/****************************************************************************/
?>

<script language="javascript">
$().ready(function() 
	{
		$("#fromdate").mask("99-99-9999");
		$("#todate").mask("99-99-9999");
		$("#invoicedate").mask("99-99-9999");
		document.getElementById('fromdate').focus();
	});
function addform()
{
	loading('Please wait while your report is generated ...');
	options	=	{	
					url : 'accounts/displayreport.php',
					type: 'POST',
					success: response
				}
	jQuery('#reportform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Report data has been saved.',0,5000);
		//jQuery('#maindiv').load('managebrands.php?'+'<?php //echo $qs?>');
		hidediv('brandiv');
	}
	else
	{
		jQuery('#report').html(text);
		//adminnotice(text,0,5000);	
	}
	//hideform();
}
function hideform()
{
	
	document.getElementById('brandiv').style.display='none';
}
</script>
<div id="report"></div>
<div id="brandiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="reportform" id="reportform" onSubmit="addform(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
	Report
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="addform();">
        <img src="../images/tick.png" alt=""/> 
        Generate Report
    </button>
     <a href="javascript:void(0);" onclick="hidediv('brandiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
</div>
<table>
	<tbody>
	<tr>
	  <td>From Date: </td>
	  <td colspan="2"><div id="error1" class="error" style="display:none; float:right;"></div>
	    <input name="fromdate" id="fromdate" type="text" value="<?php echo $fromdate; ?>" onkeydown="javascript:if(event.keyCode==13) {todate.focus(); return false;}" size="8"> dd-mm-yyyy</td>
	  </tr>
	<tr>
		<td>To Date </td>
		<td colspan="2"><div id="error2" class="error" style="display:none; float:right;"></div><input name="todate" id="todate" type="text" value="<?php echo $today; ?>" onkeydown="javascript:if(event.keyCode==13) {showreport();return false;}" size="8"> dd-mm-yyyy</td>
	</tr>
	<!-- -->
    <!-- -->
	<tr>
	  <td colspan="3"  align="left">
	    <div class="buttons">
	      <button type="button" class="positive" onclick="showreport();">
	        <img src="../images/tick.png" alt=""/> 
	        Generate Report            </button>
	      <a href="javascript:void(0);" onclick="hidediv('brandiv');" class="negative">
	        <img src="../images/cross.png" alt=""/>
	        Cancel            </a>          </div>        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="id" value = <?php echo $reportid?> />	
<input type="hidden" name="param" value = <?php echo $param?> />	
</form>
</div><br />
<script language="javascript">
function showreport()
{
	var fromdate		=	document.getElementById('fromdate').value;
	var todate			=	document.getElementById('todate').value;
	
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=800,height=800,left=100,top=25';
	window.open('accounts/displayreport.php?id=<?php echo $reportid?>&fromdate='+fromdate+'&todate='+todate+'&param=<?php echo $param?>','Invice',display);
}
</script>