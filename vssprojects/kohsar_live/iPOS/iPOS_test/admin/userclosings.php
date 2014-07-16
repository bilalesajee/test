<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$userid	 	= $_GET['id'];

$qs		=	$_SESSION['qstring'];
$today	=	date("d-m-Y",time());
/****************************************************************************/
$sql="select 
				firstname,
				lastname 
			from 
				addressbook,
				employee 
			where 
				pkaddressbookid=fkaddressbookid and 
				pkemployeeid='$userid'";
$emparr	=	$AdminDAO->queryresult($sql);
$employeename	=	$emparr[0]['firstname'].' '.$emparr[0]['lastname'];
?>

<script language="javascript">
$().ready(function() 
	{
		$("#fromdate").mask("99-99-9999");
		$("#todate").mask("99-99-9999");
		document.getElementById('fromdate').focus();
	});
function hideform()
{
	
	document.getElementById('brandiv').style.display='none';
}
</script>
<div id="report"></div>
<div id="brandiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="reportform" id="reportform" onSubmit="showreport(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
	User Closing Report&rsaquo;&rsaquo; <?php print"$employeename";?>
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="showreport();">
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
	<tr style="display:none">
	  <td>Report Type: </td>				
	  <td valign="top"><div id="error3" class="error" style="display:none; float:right;"></div><div id="suppliers">
	    <select name="reporttype" id="reporttype" onchange="chcktype()">
	      <option value="1">Credit Account Summary</option>
	      <option value="2">Credit Accounts Statement</option>
	      <option value="3" >Sales Tax Invoice</option>
	      </select>
	    </div></td>
	  <!--<td valign="top"><input name="nsupplier" id="nsupplier" type="text" value="Add New" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" onFocus="if(this.value=='Add New')this.value='';"/></td>-->
	  </tr>
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
<input type="hidden" name="userid" value='<?php echo $userid?>' id="userid"/>	
</form>
</div><br />
<script language="javascript">
function showreport()
{
	var fromdate		=	document.getElementById('fromdate').value;
	var todate			=	document.getElementById('todate').value;
	var reporttype		=	document.getElementById('reporttype').value;
	var userid		=	document.getElementById('userid').value;
	
	
	var wid				=	800;
	var hig				=	600;
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width='+wid+',height='+hig+',left=100,top=25';
 	window.open('userclosingreport.php?fromdate='+fromdate+'&todate='+todate+'&reporttype='+reporttype+'&userid='+userid,'Closing',display); 	 
}
</script>