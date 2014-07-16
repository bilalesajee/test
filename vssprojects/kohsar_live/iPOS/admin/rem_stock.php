<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$AdminDAO2,$Component;

?>

<script language="javascript" type="text/javascript">
function loaditeminfo(val)
{

	$('#iteminfo').load('loaditem.php?bc='+val);
}
$().ready(function() 
{
	document.getElementById('barcode').focus();
	function findValueCallback(event, data, formatted) 
	{
		document.getElementById('barcode').value=data[1];
		document.getElementById('itemdescription').value=data[0];
		document.getElementById('barcodeid').value=data[2];
		document.getElementById('btn2').focus();			
		$("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
	}
	function formatItem(row) 
	{
		return row[0] + " (<strong>id: " + row[0] + "</strong>)";
	}
	function formatResult(row) 
	{
		return row[0].replace(/(<.+?>)/gi,'');
	}
	$(":text, textarea").result(findValueCallback).next().click(function() 
	{
		$(this).prev().search();
	});
	$("#clear").click(function() 
	{
		$(":input").unautocomplete();
	});
	$("#itemdescription").autocomplete("itemautocomplete.php") ;
});

//////////////////////////////////////////////////////////////////////////////

function showreport()
{
	if(document.getElementById('barcode').value == '')
		{
			alert('Please Enter Barcode ');
			document.getElementById('barcode').focus();
			return false;
		}
	loc	=	document.getElementById('loc').value;
	barcode	=	document.getElementById('barcode').value;

	

	window.open('accounts/get_stock.php?loc='+loc+'&barcode='+barcode,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px");
}


</script>
<title>Remaining Stock Report</title>
<div id="iteminfo" style="display:none;"></div>
<div id="error" class="notice" style="display:none"></div>
<div id="reportsdiv">
  <form name="frmreport" id="frmreport" style="width:920px;" class="form">
    <fieldset>
      <legend>Stock Reports </legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="showreport();"> <img src="../images/tick.png" alt=""/> View Report </button>
        <!--<a href="javascript:void(0);" onclick="hidediv('reportsdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span>--> </span></div>
      <table width="100%">
      <tr>
        <td width="16%"><table width="100%">
          <!-- <tr>
          <td>Report Type </td>
          <td width="89%">
		  <select name="reptype" id="reptype" title="Please Select Report type by default(General Sales Report)" onchange="reporttype(this.value)">
            <option value="1" title="This will show the Sales Reports">Sales Report</option>
            <option value="2" title="This will show reports by payment method sales">Payment Method</option>
            <option value="3" title="This shows canceled sales with printed bills">Canceled Prints</option>
            <option value="4" title="This option displays the returned items">Returns</option>
            <option value="5" title="This option displays the discounted items">Discounts</option>
            <option value="6" title="This option displays the damaged items">Damages</option>
            <option value="7" title="This option displays the supplier Report">Supplier Report</option>
            <option value="8" title="This option displays the Comparison Report">Comparison Report</option>
            <option value="9" title="This option displays the Expiry Report">Expiry Report</option>
          </select>
		  </td>
        </tr>-->
         <!-- <tr>
            <td width="14%"> Start Date: </td>
            <td width="16%"><input type="text" class="text" name="sdate" id="sdate" value="<?php //echo date('Y-m-d',time())?>" /></td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="set_year(11)" ><b>Last Day</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(12)" ><b>Last Week</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(13)" ><b>Last Month</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(6)" ><b>Six Month</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(1)" ><b>One Year</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(2)" ><b>Two Year</b></a></td>
          </tr>
          <tr>
            <td> End Date: </td>
            <td><input type="text" class="text"  name="edate"id="edate" value="<?php //echo date('Y-m-d',time())?>" /></td>
          </tr>-->
         
         
         
         
          <tr >
            <td width="10%">Select Location : </td>
            <td colspan="3"><select id="loc" name="loc">
             <option>All</option>
              <option value="1">DHA</option>
              <option value="2">Gulberg</option>
              <option value="3">Kohsar</option>
              <option value="4">Warehouse</option>
              <option value="5">Pharmacy</option>
           
            </select></td>
            <td width="35%">&nbsp;</td>
          </tr>
          <!-- <tr >
          <td> Location wise</td>
          <td><input type="checkbox" id="productcat2" name="productcat2" onclick="disablearr(2);" /></td>
       
           <td id="proname2" style="display:none;">
          Select Location :&nbsp;&nbsp;  <select id="loc" name="loc">
          <option value="1">Kohsar</option>
          <option value="2">DHA</option>
          <option value="3">Gulberg</option>
          </select>
          </td>
        </tr>-->
           <tr>
        	<td width="10%">Barcode:</td>
            <td width="19%"><input type="text" class="text" id="barcode" name="barcode"  onkeydown="javascript:if(event.keyCode==13) {loaditeminfo(this.value); return false;}" onfocus="this.select();" /></td>
            <td width="10%">Item Name:</td>
            <td width="26%"><input name="itemdescription" type="text" class="text" id="itemdescription" onfocus="this.select();" size="37" autocomplete="off" /></td>
           </tr>
		<tr>
        	<td width="10%">&nbsp;</td>
        	<td colspan="3">&nbsp;</td>
        </tr>
          <tr>
            <td colspan="5"><div class="buttons">
              <button type="button" class="positive"  id="btn2" onclick="showreport();"> <img src="../images/tick.png" alt=""/> View Report </button>
              <!-- <a href="javascript:void(0);" onclick="hidediv('reportsdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> -->
              </div></td>
          </tr>
        </table></td>
      </tr>
      </table>
    </fieldset><input type="hidden" name="barcodeid" id="barcodeid" value="" />
  </form>
</div>
<div id="displayreport"></div>