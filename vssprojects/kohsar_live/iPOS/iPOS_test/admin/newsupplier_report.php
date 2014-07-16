<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
$suppliers		=	$AdminDAO->getrows("supplier","pksupplierid,companyname","supplierdeleted=0 order by companyname");

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
	supplier		=	document.getElementById('supplier').value;
	
	 ncz     =   document.getElementById('ob').checked;
	if(ncz==true){
	ncz=1;	
		}else{
		ncz=0;	
			}
window.open('newsupplier_report_detail.php?sdate='+sdate+'&edate='+edate+'&supplier='+supplier+'&ob='+ncz,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px");
}
</script>
<title>Supplier Reports</title>

<div id="error" class="notice" style="display:none"></div>
<div id="reportsdiv">
<form name="frmreport" id="frmreport" style="width:920px;" class="form">
<fieldset>
<legend>
	Supplier Report</legend>
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
		  <td>Select Supplier</td>
		  <td><select name="supplier" id="supplier" style="width:180px;" ><option value="">Select Supplier</option>
<?php for($i=0;$i<sizeof($suppliers);$i++)
{ 
	$suppliername	=	$suppliers[$i]['companyname'];
	$supplierid		=	$suppliers[$i]['pksupplierid'];
	?>
	<option value="<?php echo $supplierid ?>" ><?php echo $suppliername ?></option>
<?php } ?>
</select>

</td>
	    </tr><tr >

            <td>&nbsp;</td>

            <td width="48%" >&nbsp;<input type="checkbox"  value="1" name="ob" id="ob"  checked="checked"/>&nbsp;&nbsp;&nbsp;With Opening Balance</td>

          

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