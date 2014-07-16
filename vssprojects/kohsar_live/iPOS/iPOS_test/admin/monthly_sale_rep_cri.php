<?php

include_once("../includes/security/adminsecurity.php");

include_once("dbgrid.php");

global $AdminDAO,$AdminDAO2,$Component;



?>



<script language="javascript" type="text/javascript">





function showreport()

{

	

		var month1		=	document.getElementById('month1').value;

	    var year1		=	document.getElementById('year1').value;



	    var m



	



	window.open('monthlyreport.php?month1='+month1+'&year1='+year1,"myWin","menubar,scrollbars,left=30px,top=40px,height=1000px,width=1000px");

}





</script>

<title>Monthly Reports</title>

<div id="iteminfo" style="display:none;"></div>

<div id="error" class="notice" style="display:none"></div>

<div id="reportsdiv">

  <form name="frmreport" id="frmreport" style="width:920px;" class="form">

    <fieldset>

      <legend>Monthly Reports </legend>

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

            <td width="17%">Select Month & Year:</td>

            <td width="48%"><span style="border:none; width:150px;">

              <select name="month1" class="accounts_combo" id="month1" style="width:67px" >

                <option value = "01">January</option>

                <option value = "02">February</option>

                <option value = "03">March</option>

                <option value = "04">April</option>

                <option value = "05" >May</option>

                <option value = "06" selected="selected">June</option>

                <option value = "07">July</option>

                <option value = "08">August</option>

                <option value = "09">September</option>

                <option value = "10">October</option>

                <option value = "11">November</option>

                <option value = "12">December</option>

              </select>

              &nbsp;&nbsp;&nbsp;&nbsp;

              <select name="year1" class="accounts_combo" id="year1" style="width:67px" >

              

                <option value = "2013" selected="selected">2013</option>
                <option value = "2014" >2014</option>

              </select>

              </span></td>

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

            <td colspan="3"><div class="buttons">

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

