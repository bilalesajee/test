<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
$cashiersarray		= 	$AdminDAO->getrows("employee,addressbook","CONCAT(firstname,' ',lastname) name,pkaddressbookid", "fkaddressbookid=pkaddressbookid order by firstname ");


$cashiersel		=	"<select name=\"cashiers\" id=\"cashiers\" style=\"width:120px;\" ><option value=\"\">Any</option>";
for($i=0;$i<sizeof($cashiersarray);$i++)
{
	$cashiername	=	$cashiersarray[$i]['name'];
	$cashierid		=	$cashiersarray[$i]['pkaddressbookid'];
	$cashiersel2	.=	"<option value=\"$cashierid\" >$cashiername</option>";
}
$cashiers			=	$cashiersel.$cashiersel2."</select>";
?>
<script language="javascript" type="text/javascript">
 jQuery(function($)
 {
	 $("#sdate").datepicker({dateFormat: 'dd-mm-yy'});
	 $("#edate").datepicker({dateFormat: 'dd-mm-yy'});
 });
function showreport()
{
	sdate		=	document.getElementById('sdate').value;
	edate		=	document.getElementById('edate').value;
	counter		=	document.getElementById('counter').value;
	if(document.getElementById('cashiers').value)
		cashiers	=	document.getElementById('cashiers').value;
	else
		cashiers	=	'';

	
window.open('cash_def_report.php?sdate='+sdate+'&edate='+edate+'&counter='+counter+'&cashiers='+cashiers,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px");
}
</script>
<title>Cash Difference</title>

<div id="error" class="notice" style="display:none"></div>
<div id="reportsdiv">
<form name="frmreport" id="frmreport" style="width:920px;" class="form">
<fieldset>
<legend>
	Cash Difference Reports</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="showreport();">
        <img src="../images/tick.png" alt=""/> 
        View Report
    </button>
     <a href="javascript:void(0);" onclick="hidediv('reportsdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
</span>
</div>
	<table width="100%">
       <tr>
  <td width="12%">
            	From Date: 
            </td>
          <td width="88%">
            <input type="text" class="text" name="sdate" id="sdate" value="<?php echo date('d-m-Y',time())?>">
            </td>
        </tr>
        <tr>
          <td> End Date: </td>
          <td><input type="text" class="text"  name="edate" id="edate" value="<?php echo date('d-m-Y',time())?>"></td>
        </tr>
      <tr>
		  <td width="12%">Counter:</td>
		  <td width="88%"><select name="counter" id="counter" style="width:136px;">
		    <option value="">All</option>
		    <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
           
		    </select></td>
	    </tr>
          <tr>
            <td>Employee:</td>
            <td><?php echo $cashiers;?>   &nbsp;</td>
        </tr>
        <tr>
        	<td colspan="2">
            	<div class="buttons">
                    <button type="button" class="positive" onclick="showreport();">
                        <img src="../images/tick.png" alt=""/> 
                        View Report
                    </button>
                     <a href="javascript:void(0);" onclick="hidediv('reportsdiv');" class="negative">
                        <img src="../images/cross.png" alt=""/>
                        Cancel
                    </a>
              </div>
            </td>
        </tr>
    </table>
    </fieldset>
</form>
</div>
<div id="displayreport"></div>