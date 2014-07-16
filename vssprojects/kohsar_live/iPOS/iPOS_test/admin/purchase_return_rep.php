<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$AdminDAO2,$Component;
//$counter_array	=	$AdminDAO->getrows('counter','countername');

$shipmentsarray		= 	$AdminDAO->getrows("shipment","*", "shipmentdeleted<>1 order by pkshipmentid desc");
$shipmentsel		=	"<select name=\"ship\" id=\"ship\" style=\"width:150px;\" ><option value=\"\">Select Shipment</option>";
for($i=0;$i<sizeof($shipmentsarray);$i++)
{
	$shipmentname	=	$shipmentsarray[$i]['shipmentname'];
	$shipmentid		=	$shipmentsarray[$i]['pkshipmentid'];
	$select			=	"";
	if($shipmentid==$selected_shipments)
	{
		$select = "selected=\"selected\"";
	}
	$shipmentsel2	.=	"<option value=\"$shipmentid\" $select>$shipmentname</option>";
}
$shipments			=	$shipmentsel.$shipmentsel2."</select>";
//selecting cashiers
$cashiersarray		= 	$AdminDAO->getrows("employee,addressbook","CONCAT(firstname,' ',lastname) name,pkaddressbookid", "fkaddressbookid=pkaddressbookid AND fkgroupid in (1,3,4,9,11)");
$supplier_rec		=	$AdminDAO->getrows("supplier","pksupplierid,companyname");
/*echo "<pre>";
print_r($supplier_rec);
echo "</pre>";*/
$cashiersel		=	"<select name=\"cashiers\" id=\"cashiers\" style=\"width:120px;\" ><option value=\"\">Any</option>";
for($i=0;$i<sizeof($cashiersarray);$i++)
{
	$cashiername	=	$cashiersarray[$i]['name'];
	$cashierid		=	$cashiersarray[$i]['pkaddressbookid'];
	$cashiersel2	.=	"<option value=\"$cashierid\" >$cashiername</option>";
}
$cashiers			=	$cashiersel.$cashiersel2."</select>";
//selecting posales
$counterarray		= 	$AdminDAO->getrows("$dbname_detail.counter","countername", " fkstoreid='$storeid'");
$countersel			=	"<select name=\"counter\" id=\"counter\" style=\"width:120px;\" ><option value=\"\">Any</option>";
for($i=0;$i<sizeof($counterarray);$i++)
{
	$counterid	=	$counterarray[$i]['countername'];
	$countersel2	.=	"<option value=\"$counterid\" >$counterid</option>";
}
$counters			=	$countersel.$countersel2."</select>";


$invoice_list	=	$AdminDAO->getrows("$dbname_detail.supplierinvoice","pksupplierinvoiceid,concat(pksupplierinvoiceid,' (',billnumber,')') billnumber","fksupplierid!='' order by pksupplierinvoiceid desc");
$d1			=	"<select name=\"invoice\" id=\"invoice\" style=\"width:150px;\"><option value = \"\" >Select Invoice</option>";
for($i=0;$i<sizeof($invoice_list);$i++)
{
	$d2			.=	"<option value = \"".$invoice_list[$i]['pksupplierinvoiceid']."\">".$invoice_list[$i]['billnumber']."</option>";
}
//////////////////////////////////For Damages///////////////////////////////////////////////////////////////
$invc		=	$d1.$d2."</select>";


?><title>Purchase Return Without Invoice Report</title>

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
	supplier_name		=	document.getElementById('supplier_name').value;
	
	
	window.open('purchase_return_invoice_report.php?sdate='+sd+'&edate='+ed+'&supplier_name='+supplier_name,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px");
}

</script>
<div id="error" class="notice" style="display:none"></div>
<div id="reportsdiv">
<form name="frmreport" id="frmreport" style="width:920px;" class="form">
<fieldset>
<legend>
	Purchase Return Without Invoice Report</legend>
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
              <tr id="supplier" >
        	<td>
            	Supplier Name
			</td>
        	<td>
            	<select name="supplier_name" id="supplier_name" title="Select a supplier name to get its report">
                 <option value="">Select Supplier</option> 
				
                <?php
				foreach($supplier_rec as $supplier)
				{
					
					?>
					
                	<option value="<?php echo $supplier[0];?>"><?php echo $supplier[1];?></option>
                 <?php
				}
				?>
                	
                </select>            
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