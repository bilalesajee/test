<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$AdminDAO2,$Component;
//$counter_array	=	$AdminDAO->getrows('counter','countername');

?><title>Stock Adjustment Report</title>

<script language="javascript" type="text/javascript">
jQuery(function($)
{	


document.getElementById('sdate').focus();

$("#sdate").datepicker({dateFormat: 'dd-mm-yy'});
$("#edate").datepicker({dateFormat: 'dd-mm-yy'});
});





function showreport()
{
	sd		=	document.getElementById('sdate').value;
	ed		=	document.getElementById('edate').value;
	supplier_name		=	document.getElementById('bc').value;
	cb		=	document.getElementById('all').checked;
	
	
	window.open('stock_adj_report.php?sdate='+sd+'&edate='+ed+'&bc='+supplier_name+'&ckb='+cb,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px");
}

</script>
<div id="error" class="notice" style="display:none"></div>
<div id="reportsdiv">
<form name="frmreport" id="frmreport" style="width:920px;" class="form">
<fieldset>
<legend>
	Stock Adjustment Report</legend>
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
        	<td width="13%">
            	Start Date:            </td>
            <td width="87%">
            <input type="text" class="text" name="sdate" id="sdate" value="<?php echo date('d-m-Y',time())?>">            </td>
        </tr>
        <tr>
            <td>
                End Date:            </td>
            <td>
            <input type="text" class="text"  name="edate"id="edate" value="<?php echo date('d-m-Y',time())?>">            </td>
        </tr>
              <tr >
        	<td>
            	Barcode
			</td>
        	<td>
            	<input type="text" name="bc"  id="bc" />	</td>
        </tr>
         </tr>
              <tr  >
       <td>
       	</td>
       
       
        	<td >
            	<input type="checkbox" name="all" value="1" id="all" /> &nbsp; &nbsp; &nbsp; ALL Items
			</td>
          </tr>

       

        <tr>
        	<td colspan="2">
            	<div class="buttons">
                    <button type="button" class="positive" onclick="showreport();">
                        <img src="../images/tick.png" alt=""/> 
                        View Report                    </button>
                     <a href="javascript:void(0);" onclick="hidediv('reportsdiv');" class="negative">
                        <img src="../images/cross.png" alt=""/>
                        Cancel                    </a>                  </div>            </td>
        </tr>
  
    </table>
    </fieldset>
</form>
</div>
<div id="displayreport"></div>