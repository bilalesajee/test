<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;

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
	counter		=	document.getElementById('counter').value;
	paymentmethod		=	document.getElementById('paymentmethod').value;
	cctype		=	document.getElementById('cctype').value;
	banks		=	document.getElementById('banks').value;
	var summary		=	document.getElementById('summary').value;
	if(document.getElementById('summary').checked==true)
	{
		var summary	=	1;
	}
	
window.open('credit_card_report.php?sdate='+sdate+'&edate='+edate+'&counter='+counter+'&paymentmethod='+paymentmethod+'&cctype='+cctype+'&bank='+banks+'&summary='+summary,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=800px");
}
</script>
<title>Credit Card Reports</title>

<div id="error" class="notice" style="display:none"></div>
<div id="reportsdiv">
<form name="frmreport" id="frmreport" style="width:920px;" class="form">
<fieldset>
<legend>
	Credit Card Reports</legend>
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
		  <td>Counter:</td>
		  <td><select name="counter" id="counter" style="width:136px;">
		    <option value="">All</option>
		    <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
           
		    </select></td>
	    </tr>
     
          <tr>
        <td>Payment Method:</td>
        <td><select name="paymentmethod" id="paymentmethod" >
                	<option value="cc">Credit Card(CC)</option>
                	<option value="fc">Foreign Currency</option>
					<option value="ch">Cheque</option>
					<option value="c">Cash</option>
                </select>  &nbsp;</td>
      </tr>
        <tr>
        <td>Credit Card Type:</td>
        <td><select name="cctype" id="cctype" >
                	<option value="">Select Card Type</option>
                	<option value="2">MCB</option>
					<option value="1">Al-Falah</option>
                </select>  &nbsp;</td>
      </tr>
              <tr>
        <td>Select Bank:</td>
        <td>
<select id="banks" class="selectbox" size="1" name="banks">
<option value="">Select Bank</option>
<option value="1"> visa </option>
<option value="12"> master </option>
<option value="23"> BANK OF AMERICA </option>
<option value="34"> City Bank </option>
<option value="45"> mcb </option>
<option value="56"> HSBC </option>
<option value="67"> Bank Alfalah </option>
<option value="70"> Standard Charted </option>
<option value="71"> Bzarclays </option>
<option value="2"> UBK </option>
<option value="3"> UBL </option>
<option value="4"> RBS </option>
<option value="5"> Alied bank </option>
<option value="6"> citi bank </option>
<option value="7"> Dubai Lounge </option>
<option value="8"> habib bank </option>
<option value="9"> alfalha </option>
<option value="10"> askari Bank </option>
<option value="11"> pocketmate </option>
<option value="13"> commonwealth Bank </option>
<option value="14"> askari bank </option>
<option value="15"> gold bank </option>
<option value="16"> rbs bank </option>
<option value="17"> Halifax </option>
<option value="18"> hsbc </option>
<option value="19"> Askari Bank </option>
<option value="20"> habib </option>
<option value="21"> RBS </option>
<option value="22"> barclays </option>
<option value="24"> hbl </option>
<option value="25"> lioyds tsb </option>
<option value="26"> h b l </option>
<option value="27"> r b s </option>
<option value="28"> citi </option>
<option value="29"> askari commercial Bank </option>
<option value="30"> h b l </option>
<option value="31"> Paypal </option>
<option value="32"> abn amro </option>
<option value="33"> abn amro </option>
<option value="35"> wallet </option>
<option value="36"> barclays </option>
<option value="37"> h b l </option>
<option value="38"> barcalys </option>
<option value="39"> loly </option>
<option value="40"> DUBAI iSLAMIC bANK </option>
<option value="41"> barclays </option>
<option value="42"> askari </option>
<option value="43"> AbN amro </option>
<option value="44"> H B L </option>
<option value="46"> PLATINUM </option>
<option value="47"> soneri </option>
<option value="48"> R B S </option>
<option value="49"> abn </option>
<option value="50"> CITI </option>
<option value="51"> DUBI BANK </option>
<option value="52"> RBS </option>
<option value="53"> BARCLAYS </option>
<option value="54"> CHASE </option>
<option value="55"> ASKARI BANK </option>
<option value="57"> union bank </option>
<option value="58"> citi </option>
<option value="59"> RAK BANK </option>
<option value="60"> al habib </option>
<option value="61"> ABN AMRo </option>
<option value="62"> ABN AMRo </option>
<option value="63"> abl </option>
<option value="64"> BARCLAYS </option>
<option value="65"> Gold Check Card </option>
<option value="66"> bank islami </option>
<option value="68"> faysal bank </option>
<option value="69"> Pocketmate </option>
</select>
&nbsp;</td>
      </tr>
       <tr>
        <td>Summary:</td>
        <td><input type="checkbox" id="summary" name="summary" value=""  />   &nbsp;</td>
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