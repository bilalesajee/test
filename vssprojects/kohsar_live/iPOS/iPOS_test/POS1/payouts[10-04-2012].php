<?php
include("includes/security/adminsecurity.php");
include_once("includes/classes/bill.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity,$Bill;
$Bill		=	new Bill($AdminDAO);
$rights	 	=	$userSecurity->getRights(8);
$banks_array	=	$AdminDAO->getrows('bank','*', ' 1');
$chequebanks	=	$Component->makeComponent('d','bank',$banks_array,'pkbankid','bankname',1,$selected_banks,'','selectbox');
//print_r($chequebanks);
?>
<script language="javascript" type="text/javascript">
$(document).ready(function() 
{
function findValueCallback(event, data, formatted)
		{
			document.getElementById('account').value=data[0];
			document.getElementById('accountid').value=data[1];			
//			document.getElementById('limit').value=data[2];			
			jQuery("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
		}
		function formatItem(row) 
		{
			return row[0] + " (<strong>id: " + row[0] + "</strong>)";
		}
		function formatResult(row) 
		{
			return row[0].replace(/(<.+?>)/gi, '');
		}
			jQuery("#account").autocomplete("getaccounts.php");
			jQuery(":text, textarea").result(findValueCallback).next().click(function() 
			{
				$(this).prev().search();
			});
			jQuery("#clear").click(function() 
			{
				jQuery(":input").unautocomplete();
			});
});
jQuery(function($)
{
	$("#chequedate").datepicker({dateFormat: 'yy-mm-dd'});
});
function confpayout()
{
	
	var pamount		=	document.getElementById('amount').value;
	var paccount	=	document.getElementById('account').value;
	var pdesc		=	document.getElementById('description').value;	
	var paymentmethod=document.getElementById('paymentmethod').value;	
	
	if(pamount=='')
	{
		alert("Please enter some amount to pay.");
		document.getElementById('amount').focus();
		//added by Yasir -- 05-09-11
		document.paymentform.button.disabled = "";
		//
		return false;
	}
	if(paccount=='')
	{
		alert("Please Enter account to pay.");
		document.getElementById('account').focus();
		//added by Yasir -- 05-09-11
		document.paymentform.button.disabled = "";
		//
		return false;
	}
	if(paymentmethod=='ch')
	{
		var chequenumber=document.getElementById('chequenumber').value;	
		var bank		=document.getElementById('bank').value;	
		if(chequenumber=='')
		{
			alert("Please Enter cheque number.");
			document.getElementById('chequenumber').focus();
			//added by Yasir -- 05-09-11
			document.paymentform.button.disabled = "";
			//
			return false;
		}
		if(bank=='')
		{
			alert("Please select Bank of the cheque.");
			document.getElementById('bank').focus();
			//added by Yasir -- 05-09-11
			document.paymentform.button.disabled = "";
			//
			return false;
		}
	}
	if(pdesc=='')
	{
		alert('Please enter Payout Description');
		document.getElementById('description').focus();
		//added by Yasir -- 05-09-11
		document.paymentform.button.disabled = "";
		//
		return false;
	}
		
	jQuery('#confpayout').show();
	loadsection('confpayout','confirmpayout.php');	
}
function processpayout()
{	
	//added by Yasir -- 05-09-11
	document.paymentform.button.disabled = "disabled";
	//
	
	var pamount			=	document.getElementById('amount').value;
	var paccount		=	document.getElementById('account').value;
	//var pdesc			=	document.getElementById('description').value;	
	//var paymentmethod	=	document.getElementById('paymentmethod').value;	
	
	if(confirm("Are you sure to pay "+pamount+" To "+paccount))
	{
		options	=	{	
						url : 'processpayout.php',
						type: 'POST',
						success: processpay
					}
		jQuery('#paymentform').ajaxSubmit(options);
	} else {
		//added by Yasir -- 05-09-11
		document.paymentform.button.disabled = "";
		//
	}
	return false;
}
function processpay(text)
{
	if(text==1)
	{
		notice('Please enter opening amount','',5000);
		$('#openingfrmdiv').load('openingfrm.php');
		document.getElementById('openingfrmdiv').style.display='block';
	}
	// added by yasir -- 07-07-11
	else if(text.indexOf('limit')>0)
	{
	 		var err="Account limit exceeded. Please enter less amount.";
			notice(err,'',5000);
			/*document.getElementById('amount').focus();
			return false;*/
			var pid	=	text.split('_');
			var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=350,height=600,left=100,top=25';
		window.open('generatepayoutbill.php?text='+pid[0],'Invice',display); 
			//printpayoutbill(text);
			selecttab('Payouts_tab','payouts.php');
	}
	else if(text=='break')
	{
	 		var err="Your counter is in break mode. You are unable to procceed cash payout.";
			notice(err,'',5000);
			//added by Yasir -- 05-09-11
			document.paymentform.button.disabled = "";
			//
			return false;	
	}
	//
	else if(text!='')
	{
			notice("Payout added successfully.",'',5000);
			var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=350,height=600,left=100,top=25';
		window.open('generatepayoutbill.php?text='+text,'Invice',display); 
			//printpayoutbill(text);
			selecttab('Payouts_tab','payouts.php');
	}
	else
	{
		
			var err="This account head does not exists. Please Add New account first.";
			notice(err,'',5000);		
			//added by Yasir -- 05-09-11
			document.paymentform.button.disabled = "";
			//	
			return false;
	}
}
function newaccount()
{
	document.getElementById('newaccount').style.display = 'block';
	document.getElementById('actitle').focus();
}
function savenewaccount()
{
	options	=	{	
					url : 'newaccount.php',
					type: 'POST',
					success: accountresponse
				}
	jQuery('#newaccountfrm').ajaxSubmit(options);
}
function accountresponse(text)
{
	if(text!='')
	{
		notice(text,'',5000);
		document.getElementById('newaccount').style.display = 'none';
	}
}
function hide(text)
{
	document.getElementById(text).style.display	=	'none';
}

var divID=1;
function paymentdetails(value)
{
	if(value=='c')
	{
		
		divID = "4";		
		document.getElementById("payment_details4").style.display="none";
	}
	
	if(value=='ch')
	{
		if(divID!='')
		{
			//document.getElementById("payment_details"+divID).style.display="none";
		}
		divID = "4";		
		document.getElementById("payment_details4").style.display="block";		
	}
}
</script>
<div id="loading" class="loading" style="display:none;"></div>
<div id="error"></div>
<div id="barcode" >
<form id="paymentform" name="paymentform">
    <div class="Table" >
        <div class="Row">
            <div class="Column">
          <label>Pay by</label>
          <select name="paymentmethod" onchange="paymentdetails(this.value);" id="paymentmethod">
            <option value="c" selected="selected">Cash</option>
           <!-- <option value="cc">Credit Card</option>
            <option value="fc">Foreign Currency</option>-->
            <option value="ch">Cheque</option>
          </select>
        </div>
			<div class="Column"><label>Amount</label>
              <input type="text" class="text" name="amount" id="amount" onkeydown="javascript:if(event.keyCode==13) {confpayout(); return false;}" >
            </div>
            <div class="Column"><label>Account</label>
            <input type="text" class="text" name="account" id="account" autocomplete="off" ><input type="hidden" name="accountid" id="accountid" value="<?php echo $accountid;?>" />
            </div>
		
		 <!-- case 2cheque -->
		<div id="payment_details4" style="display:none;">
          <!-- case 4 cheque -->
                    <div class="Column">
            <label>Cheque #</label>
            <input type="text" name="chequenumber" class="text" id="chequenumber" onkeydown="javascript:if(event.keyCode==13) {confpayout(); return false;}"/>
          </div>
          <div class="Column">
            <label>Bank</label>
            <div id="chkbank"> <?php echo $chequebanks;?> <!--<a href="javascript:void(0)" onclick="addnewtext('chkbank','newbank','Add New');" style="font-size:10px"> New</a>--> </div>
          </div>
          <div class="Column">
            <label>Date</label>
            <input type="text" name="chequedate" class="text" id="chequedate" onkeydown="javascript:if(event.keyCode==13) {confpayout(); return false;}"/>
          </div>
         </div> 
		    <div class="Column"><label>Description</label>
              <input type="text" class="text" name="description" id="description" onkeydown="javascript:if(event.keyCode==13) {confpayout(); return false;}" >
            </div>
       <div class="Column">
       <span class="buttons"><label>&nbsp;</label>
                <button type="button" name="button" id="button" onclick="javascript:confpayout();" >
                        <img src="images/tick.png" alt=""/> 
                       Pay Now
                </button>
                <!--Title added by Yasir - 12-07-11 -->
      </span>
      </div>
      <div id="Column">
      <span class="buttons"><label>&nbsp;</label>
                <button type="button" title="Add Payee Account" onclick="newaccount();" >
                        <img src="images/tick.png" alt=""/> 
                       Add New
                </button>
       </span>
        	</div>
       <!-- <div class="Row">
            
        </div>-->
    </div><!--  Table -->
</form>
</div>
<div id="mainpanel">
<div id="leftpanel">
<div id="confpayout" class="cancelsalebox" style="position:absolute;margin-top:75px;"></div>
<div id="newaccount" style="display:none;margin-top:45px;">
<form id="newaccountfrm">
<table class="price">
<tr>
    <th>Account Title</th>
    <td><input type="text" name="actitle" id="actitle" class="text" onkeydown="javascript:if(event.keyCode==13) {savenewaccount(); return false;}"></td>
</tr>
<tr>
    <th>Limit</th>
    <td><input type="text" name="limit" id="limit" class="text" onkeydown="javascript:if(event.keyCode==13) {savenewaccount(); return false;}" onkeypress="return isNumberKey(event)"></td>
</tr>
<tr>
    <td colspan="2">
    <span class="buttons" style="font-size:12px;">
     	<button type="button" name="savenewac" id="savenewac" onclick="savenewaccount();" >
            <img src="images/disk.png" alt=""/> 
           Save
        </button>
        <button type="button" name="cancel" id="cancel" onclick="hide('newaccount');" >
            <img src="images/cross.png" alt=""/> 
           Cancel
        </button>
    </span>
    </td>
</tr>
</table>
</form>
</div>
</div>
</div>
<script language="javascript" type="text/javascript">
	document.getElementById('amount').focus();
	jQuery('#paydetailsdiv').load('paydetails.php');
</script>
<div id="clear" style="clear:both"></div>
<div id="paydetailsdiv"></div>
<?php
if($_SESSION['closingsession']=='')
{
	$closingquery	=	"SELECT pkclosingid from $dbname_detail.closinginfo where closingstatus ='i' AND countername='$countername' AND fkaddressbookid='$empid' AND fkstoreid = '$storeid' ";
	$closingarray	=	$AdminDAO->queryresult($closingquery);
	$closingsession	=	$closingarray[0][pkclosingid];
	if($closingsession=='')
	{
		?>
        <script language="javascript" type="text/javascript">
		$('#openingfrmdiv').load('openingfrm.php');
		document.getElementById('openingfrmdiv').style.display	=	'block';
		</script>
        <?php
	}
}
?>