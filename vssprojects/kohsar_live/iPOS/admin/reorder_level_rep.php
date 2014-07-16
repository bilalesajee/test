<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;

?>
<script language="javascript" type="text/javascript">
 
function showreport()
{
	
	barcode		=	document.getElementById('barcode').value;
	reorderlevel		=	document.getElementById('reorderlevel').value;
	
	window.open('reorder_level_report.php?barcode='+barcode+'&reorderlevel='+reorderlevel,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px");
}
</script>
<title>Reorder Level Report</title>

<div id="error" class="notice" style="display:none"></div>
<div id="reportsdiv">
<form name="frmreport" id="frmreport" style="width:920px;" class="form">
<fieldset>
<legend>
	Reorder Level Report</legend>
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
          <td width="12%">Barcode:</td>
          <td width="88%"><input type="text" class="text" name="barcode" id="barcode" value="" /></td>
        </tr>
        <tr>
          <td>Reorder Level:</td>
          <td><input name="reorderlevel" type="text" class="text" id="reorderlevel" value="" size="10" /></td>
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