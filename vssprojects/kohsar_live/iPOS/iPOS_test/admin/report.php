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


?><title>Reports</title>
<link href="../includes/css/autocomplete.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/autocomplete/proautocomplete.js"></script>
<script language="javascript" type="text/javascript">
jQuery(function($)
{	


document.getElementById('sdate').focus();
$("#sdate").mask("9999-99-99");
$("#edate").mask("9999-99-99");
$("#sdate").datepicker({dateFormat: 'yy-mm-dd'});
$("#edate").datepicker({dateFormat: 'yy-mm-dd'});
});




function disablearr()
{
	if(document.getElementById('arrangement').disabled==false)
		document.getElementById('arrangement').disabled	=	true;
	else
		document.getElementById('arrangement').disabled	=	false;		
}
function showreport()
{
	var reptype			=	document.getElementById('reptype').value;
	var paymentmethod	=	document.getElementById('paymentmethod').value;
	sd	=	document.getElementById('sdate').value;
	ed	=	document.getElementById('edate').value;
	arr	=	document.frmreport.arrangement.value;
	ord	=	document.frmreport.sortorder.value;
	pro	=	document.getElementById('productname').value;
	sup_id=document.getElementById('supplier_name').value;
	ship=document.getElementById('ship').value;
	invoice=document.getElementById('invoice').value;
	if(document.getElementById('cashiers').value)
		csh	=	document.getElementById('cashiers').value;
	else
		csh	=	'';
	if(document.getElementById('counter').value)
		ctr	=	document.getElementById('counter').value;
	else
		ctr	=	'';
	if(document.getElementById('productcat').checked==true)
	{
		var cat	=	1;
	}
	if(reptype==1)
	{
		var url='showreport.php';
	}
	else if(reptype==2)
	{
		var url='paymentmethodreport.php';
	}
	else if(reptype==3)
	{
		var url='canceledsales.php';
	}
	else if(reptype==4)
	{
		var url='returneditems.php';
	}
	else if(reptype==5)
	{
		var url='discounteditems.php';
	}
	else if(reptype==6)
	{
		var url='damageditems.php';
	}
	else if(reptype==7)
	{
		var url='viewsupplierreport.php';
	}
	else if(reptype==8)
	{
		var url='viewcompreport.php';
	}
	else if(reptype==9)
	{
		var url='expireditems.php';
	
	}else if(reptype==10)
	{
		var url='newpurchasereport.php';
	}	
	else if(reptype==11)
	{
		var url='purchase_return.php';
	}
	window.open(url+'?sdate='+sd+'&edate='+ed+'&cashier='+csh+'&ship='+ship+'&invoice='+invoice+'&counter='+ctr+'&arrangement='+arr+'&sortorder='+ord+'&cat='+cat+'&pro='+pro+'&paymentmethod='+paymentmethod+'&sup_id='+sup_id,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px");
}
function reporttype(val)
{
		if(val==2)
		{
			//hide these rows proname procat arrangeby sortby
			document.getElementById('proname').style.display='none';
			document.getElementById('proname1').style.display='none';
			document.getElementById('proname2').style.display='none';
			document.getElementById('procat').style.display='none';	
			document.getElementById('arrangeby').style.display='none';	
			document.getElementById('sortby').style.display='none';	
			//show this row payment meth
			document.getElementById('paymentmeth').style.display='block';	
			document.getElementById('paymentmeth').style.display='table-row';
		}
		else if(val==3 || val==4)
		{
			//hide these rows proname procat arrangeby sortby
			document.getElementById('proname').style.display='none';
				document.getElementById('proname').style.display='none';
			document.getElementById('proname1').style.display='none';
			document.getElementById('procat').style.display='none';	
			document.getElementById('arrangeby').style.display='none';	
			document.getElementById('sortby').style.display='none';	
			document.getElementById('paymentmeth').style.display='none';
			// show rows
			document.getElementById('cashier').style.display='block';
			document.getElementById('cashier').style.display='table-row';
			document.getElementById('pos').style.display='block';
			document.getElementById('pos').style.display='table-row';
		}
		else if(val==5 || val==6)
		{
			//hide these rows proname procat arrangeby sortby
			document.getElementById('proname').style.display='none';
			document.getElementById('proname').style.display='none';
			document.getElementById('proname1').style.display='none';
			document.getElementById('pos').style.display='none';
			document.getElementById('procat').style.display='none';	
			document.getElementById('arrangeby').style.display='none';	
			document.getElementById('sortby').style.display='none';	
			document.getElementById('paymentmeth').style.display='none';
			//show
			document.getElementById('cashier').style.display='block';
			document.getElementById('cashier').style.display='table-row';
		}
		else if(val==7)
		{
			document.getElementById('cashier').style.display='none';
			document.getElementById('pos').style.display='none';
			document.getElementById('paymentmeth').style.display='none';
			document.getElementById('proname').style.display='block';	
			document.getElementById('proname').style.display='table-row';
			document.getElementById('proname').style.display='none';
			document.getElementById('proname1').style.display='none';
			document.getElementById('procat').style.display='none';	
				
			document.getElementById('arrangeby').style.display='none';	
			document.getElementById('arrangeby').style.display='none';
			document.getElementById('sortby').style.display='none';	
			document.getElementById('sortby').style.display='none';
			document.getElementById('supplier').style.display='block';
			document.getElementById('supplier').style.display='table-row';
		}
		else if(val==8)
		{
			document.getElementById('cashier').style.display='none';
			document.getElementById('pos').style.display='none';
			document.getElementById('paymentmeth').style.display='none';
			document.getElementById('proname').style.display='none';
			document.getElementById('proname').style.display='none';
			document.getElementById('proname1').style.display='none';
			document.getElementById('procat').style.display='none';	
				
			document.getElementById('arrangeby').style.display='none';	
			document.getElementById('arrangeby').style.display='none';
			document.getElementById('sortby').style.display='none';	
			document.getElementById('sortby').style.display='none';
			document.getElementById('supplier').style.display='none';
		}
		else if(val==9)
		{
			document.getElementById('cashier').style.display='none';
			document.getElementById('pos').style.display='none';
			document.getElementById('paymentmeth').style.display='none';
			document.getElementById('proname').style.display='none';
			document.getElementById('proname').style.display='none';
			document.getElementById('proname1').style.display='none';
			document.getElementById('procat').style.display='none';	
				
			document.getElementById('arrangeby').style.display='none';	
			document.getElementById('arrangeby').style.display='none';
			document.getElementById('sortby').style.display='none';	
			document.getElementById('sortby').style.display='none';
			document.getElementById('supplier').style.display='none';
		}		
		else if(val==10)
		{
			//hide
			document.getElementById('cashier').style.display='none';
			document.getElementById('pos').style.display='none';
			document.getElementById('paymentmeth').style.display='none';
			//show
			document.getElementById('proname').style.display='block';	
			document.getElementById('proname').style.display='table-row';
			document.getElementById('proname1').style.display='block';	
			document.getElementById('proname1').style.display='table-row';
			document.getElementById('proname2').style.display='block';	
			document.getElementById('proname2').style.display='table-row';
			document.getElementById('procat').style.display='block';	
			document.getElementById('procat').style.display='table-row';	
			document.getElementById('arrangeby').style.display='block';	
			document.getElementById('arrangeby').style.display='table-row';
			document.getElementById('sortby').style.display='block';	
			document.getElementById('sortby').style.display='table-row';
		}
		else 
		{
			//hide
			document.getElementById('cashier').style.display='none';
			document.getElementById('pos').style.display='none';
			document.getElementById('paymentmeth').style.display='none';
			//show
			document.getElementById('proname').style.display='block';	
			document.getElementById('proname').style.display='table-row';
			document.getElementById('proname1').style.display='block';	
			document.getElementById('proname1').style.display='table-row';
			document.getElementById('proname2').style.display='block';	
			document.getElementById('proname2').style.display='table-row';
			document.getElementById('procat').style.display='block';	
			document.getElementById('procat').style.display='table-row';	
			document.getElementById('arrangeby').style.display='block';	
			document.getElementById('arrangeby').style.display='table-row';
			document.getElementById('sortby').style.display='block';	
			document.getElementById('sortby').style.display='table-row';
		}
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="reportsdiv">
<form name="frmreport" id="frmreport" style="width:920px;" class="form">
<fieldset>
<legend>
	Reports
</legend>
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
          <td>Report Type </td>
          <td colspan="2">
		  <select name="reptype" id="reptype" title="Please Select Report type by default(General Sales Report)" onchange="reporttype(this.value)">
            <option value="1" title="This will show the Sales Reports">Sales Report</option>
            <option value="2" title="This will show reports by payment method sales">Payment Method</option>
            <option value="3" title="This shows canceled sales with printed bills">Canceled Prints</option>
            <option value="4" title="This option displays the returned items">Sale Returns</option>
            <option value="5" title="This option displays the discounted items">Discounts</option>
            <option value="6" title="This option displays the damaged items">Damages</option>
            <option value="7" title="This option displays the supplier Report">Supplier Report</option>
            <option value="8" title="This option displays the Comparison Report">Comparison Report</option>
            <option value="9" title="This option displays the Expiry Report">Expiry Report</option>
             <option value="10" title="This option displays the Purchase Report">Purchase Report</option>
              <option value="11" title="This option displays the Purchase Report">Purchase Return Report</option>
          </select>
		  </td>
        </tr>
        <tr>
        	<td width="13%">
            	Start Date:            </td>
            <td colspan="2">
            <input type="text" class="text" name="sdate" id="sdate" value="<?php echo date('Y-m-d',time())?>">            </td>
        </tr>
        <tr>
            <td>
                End Date:            </td>
            <td colspan="2">
            <input type="text" class="text"  name="edate"id="edate" value="<?php echo date('Y-m-d',time())?>">            </td>
        </tr>
		<tr id="proname">
        	<td >
            	Product Name</td>
        	<td colspan="2">
            	<input type="text" class="text" autocomplete="off" id="productname" name="productname" />            </td>
        </tr>
        <tr id="proname1">
          <td>Shipment</td>
          <td width="19%"><?php echo $shipments;?></td>
          <td width="68%">&nbsp;</td>
        </tr>
        <tr id="proname2">
          <td>Invoice</td>
          <td><?php echo $invc;?></td>
          <td>&nbsp;</td>
        </tr>
        <tr id="procat">
        	<td>
            	Product wise Sale</td>
        	<td colspan="2">
            	<input type="checkbox" id="productcat" name="productcat" onclick="disablearr();" />            </td>
        </tr>
        <tr id="arrangeby">
        	<td>
            	Arrage by</td>
        	<td colspan="2">
            	<select name="arrangement" id="arrangement">
                	<option value="itemdescription">Item Description</option>
                	<option value="barcode">Barcode</option>
                	<option value="quantity">Sold</option>
                	<option value="unitsremaining">Remaining</option>
                </select>            </td>
        </tr>
        <tr id="sortby">
        	<td>
            	Order by</td>
        	<td colspan="2">
            	<select name="sortorder">
                	<option value="DESC">Descending</option>
                	<option value="ASC">Ascending</option>
                </select>            </td>
        </tr>
		<tr id="paymentmeth" style="display:none">
        	<td>
            	Payment Method
			</td>
        	<td colspan="2">
            	<select name="paymentmethod" id="paymentmethod" title="Select a payment method to get its report">
                	<option value="cc">Credit Card(CC)</option>
                	<option value="fc">Foreign Currency</option>
					<option value="chq">Cheque</option>
					<option value="c">Cash</option>
                </select>            
			</td>
        </tr>
        <tr id="cashier" style="display:none">
        	<td>
            	User
			</td>
        	<td colspan="2">
            	<?php echo $cashiers;?>         
			</td>
        </tr>
        <tr id="pos" style="display:none">
        	<td>
            	Point of Sale
			</td>
        	<td colspan="2">
            	<?php echo $counters;?>           
			</td>
        </tr>
              <tr id="supplier" style="display:none">
        	<td>
            	Supplier Name
			</td>
        	<td colspan="2">
            	<select name="supplier_name" id="supplier_name" title="Select a supplier name to get its report">
                 <option value="0">Select Supplier</option> 
				
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
        	<td colspan="3">
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