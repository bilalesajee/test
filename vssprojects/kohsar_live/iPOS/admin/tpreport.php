<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
?>
<script language="javascript" type="text/javascript">
 jQuery(function($)
 {
	 $("#sdate").datepicker({dateFormat: 'yy-mm-dd'});
	 $("#edate").datepicker({dateFormat: 'yy-mm-dd'});
 });
function showreport()
{
	sd	=	document.getElementById('sdate').value;
	ed	=	document.getElementById('edate').value;
	window.open('showtpreport.php?sdate='+sd+'&edate='+ed,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px");
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="reportsdiv">
<form name="frmreport" id="frmreport" style="width:920px;" class="form">
<fieldset>
<legend>
	Trade Price Report</legend>
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
        	<td width="8%">
            	From Date: 
            </td>
            <td>
            <input type="text" class="text" name="sdate" id="sdate" value="<?php echo date('Y-m-d',time())?>">
            </td>
        </tr>
        <tr>
            <td>
                End Date: 
            </td>
            <td>
            <input type="text" class="text"  name="edate"id="edate" value="<?php echo date('Y-m-d',time())?>">
            </td>
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