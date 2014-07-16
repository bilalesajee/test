<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component;
//$counter_array	=	$AdminDAO->getrows('counter','countername');
?>
<script language="javascript" type="text/javascript">
 jQuery(function($)
 {
	 $("#sdate").datepicker({dateFormat: 'yy-mm-dd'});
	 $("#edate").datepicker({dateFormat: 'yy-mm-dd'});
 });

/*function viewreport(id)
{
	options	=	{	
					url : 'showreport.php',
					type: 'POST',
					success: response
				}
	jQuery('#frmreport').ajaxSubmit(options);
}*/
function disablearr()
{
	if(document.getElementById('arrangement').disabled==false)
		document.getElementById('arrangement').disabled	=	true;
	else
		document.getElementById('arrangement').disabled	=	false;		
}
function showreport()
{
	//document.getElementById('displayreport').innerHTML=text;
	//doWin();
	sd	=	document.getElementById('sdate').value;
	ed	=	document.getElementById('edate').value;
	arr	=	document.frmreport.arrangement.value;
	ord	=	document.frmreport.sortorder.value;
	pro	=	document.getElementById('productname').value;
	if(document.getElementById('productcat').checked==true)
	{
		var cat	=	1;
	}
	window.open('showitemreport.php?sdate='+sd+'&edate='+ed+'&barcode='+barcode,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px");
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="reportsdiv">
<form name="frmreport" id="frmreport" style="width:920px;" class="form">
<fieldset>
<legend>
	Item Sale Report</legend>
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
        	<td>
            	Barcode</td>
        	<td>
            	<input type="text" class="text" id="barcode" name="barcode" />
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