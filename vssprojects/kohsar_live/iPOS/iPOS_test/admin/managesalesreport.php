<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component;
//$counter_array	=	$AdminDAO->getrows('counter','countername');
?>
<script language="javascript" type="text/javascript">
 jQuery(function($)
 {
	 $("#sdate").mask('99-99-9999');
	 $("#edate").mask('99-99-9999');
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
	sd		=	document.getElementById('sdate').value;
	ed		=	document.getElementById('edate').value;
	sday	=	document.getElementById('startdayofweek').value;
	eday	=	document.getElementById('enddayofweek').value;
	shr		=	document.getElementById('starthour').value;
	ehr		=	document.getElementById('endhour').value;
	arr		=	document.frmreport.arrangement.value;
	ord		=	document.frmreport.sortorder.value;
	pro		=	document.getElementById('productname').value;
	bar		=	document.getElementById('barcode').value;
	if(document.getElementById('productcat').checked==true)
	{
		var cat	=	1;
	}
	window.open('salesreport.php?sdate='+sd+'&edate='+ed+'&arrangement='+arr+'&sortorder='+ord+'&cat='+cat+'&pro='+pro+'&startday='+sday+'&endday='+eday+'&starthour='+shr+'&endhour='+ehr+'&bar='+bar,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px");
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
        	<td width="14%">
            	From Date: 
            </td>
            <td width="86%">
            <input type="text" class="text" name="sdate" id="sdate" value="<?php echo date('d-m-Y',time())?>">
            </td>
        </tr>
        <tr>
            <td>
                To Date: 
            </td>
            <td>
            <input type="text" class="text"  name="edate"id="edate" value="<?php echo date('d-m-Y',time())?>">
            </td>
        </tr>
		<tr>
		  <td>Sales Start Day:</td>
		  <td>
              <select name="startdayofweek" id="startdayofweek" style="width:136px;">
              	<option value="">Any Day</option>
                <option value="1">Sunday</option>
                <option value="2">Monday</option>
                <option value="3">Tuesday</option>
                <option value="4">Wednesday</option>
                <option value="5">Thursday</option>
                <option value="6">Friday</option>
                <option value="7">Saturday</option>
              </select>
          </td>
	    </tr>
		<tr>
		  <td>Sales End Day:</td>
		  <td><select name="enddayofweek" id="enddayofweek" style="width:136px;">
		    <option value="">Any Day</option>
		    <option value="1">Sunday</option>
		    <option value="2">Monday</option>
		    <option value="3">Tuesday</option>
		    <option value="4">Wednesday</option>
		    <option value="5">Thursday</option>
		    <option value="6">Friday</option>
		    <option value="7">Saturday</option>
		    </select></td>
	    </tr>
		<tr>
		  <td>Sales Start Hour:</td>
		  <td><select name="starthour" id="starthour" style="width:136px;">
		    <option value="">Any Hour</option>
		    <option value="08">8 AM</option>
            <option value="09">9 AM</option>
            <option value="10">10 AM</option>
            <option value="11">11 AM</option>
            <option value="12">12 PM</option>
            <option value="13">1 PM</option>
            <option value="14">2 PM</option>
            <option value="15">3 PM</option>
		    <option value="16">4 PM</option>
            <option value="17">5 PM</option>
            <option value="18">6 PM</option>
            <option value="19">7 PM</option>
            <option value="20">8 PM</option>
            <option value="21">9 PM</option>
            <option value="22">10 PM</option>
            <option value="23">11 PM</option>
		    <option value="00">12 AM</option>
            <option value="01">1 AM</option>
            <option value="02">2 AM</option>
            <option value="03">3 AM</option>
            <option value="04">4 AM</option>
            <option value="05">5 AM</option>
            <option value="06">6 AM</option>
            <option value="07">7 AM</option>
		    </select></td>
	    </tr>
        <tr>
		  <td>Sales End Hour:</td>
		  <td><select name="endhour" id="endhour" style="width:136px;">
		    <option value="">Any Hour</option>
		    <option value="08">8 AM</option>
            <option value="09">9 AM</option>
            <option value="10">10 AM</option>
            <option value="11">11 AM</option>
            <option value="12">12 PM</option>
            <option value="13">1 PM</option>
            <option value="14">2 PM</option>
            <option value="15">3 PM</option>
		    <option value="16">4 PM</option>
            <option value="17">5 PM</option>
            <option value="18">6 PM</option>
            <option value="19">7 PM</option>
            <option value="20">8 PM</option>
            <option value="21">9 PM</option>
            <option value="22">10 PM</option>
            <option value="23">11 PM</option>
		    <option value="00">12 AM</option>
            <option value="01">1 AM</option>
            <option value="02">2 AM</option>
            <option value="03">3 AM</option>
            <option value="04">4 AM</option>
            <option value="05">5 AM</option>
            <option value="06">6 AM</option>
            <option value="07">7 AM</option>
		    </select></td>
	    </tr>
		<tr>
        	<td>
            	Product Name:</td>
        	<td>
            	<input type="text" class="text" autocomplete="off" id="productname" name="productname" />
            </td>
        </tr>
        <tr>
          <td>Barcode:</td>
          <td><input type="text" class="text" id="barcode" name="barcode"  />&nbsp;</td>
        </tr>
        <tr>
        	<td>
            	Product wise Sale:</td>
        	<td>
            	<input type="checkbox" id="productcat" name="productcat" onclick="disablearr();" />
            </td>
        </tr>
        <tr>
        	<td>
            	Arrange by:</td>
        	<td>
            	<select name="arrangement" id="arrangement" style="width:136px;">
                    <option value="barcode">Barcode</option>
                    <option value="itemdescription">Item Description</option>
                	<option value="quantity">Units Sold</option>
                	<option value="unitsremaining">Units Remaining</option>
                    <option value="amount">Sale Amount</option>
                    <option value="originalprice">Trade Amount</option>
                   <!-- <option value="profit">Profit</option>-->
                </select>
            </td>
        </tr>
        <tr>
        	<td>
            	Order by:</td>
        	<td>
            	<select name="sortorder" style="width:136px;">
                	<option value="DESC">Descending</option>
                	<option value="ASC">Ascending</option>
                </select>
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