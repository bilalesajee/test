<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
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
	
	
window.open('ledger_report_detail.php?sdate='+sdate+'&edate='+edate,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=800px");
}
</script>
<title>Stock Ledger</title>

<div id="error" class="notice" style="display:none"></div>
<div id="reportsdiv">
<form name="frmreport" id="frmreport" style="width:920px;" class="form">
<fieldset>
<legend>
	Stock Ledger</legend>
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