<?php
session_start();
include_once("includes/security/adminsecurity.php");
include_once("includes/classes/pricing.php");
global $AdminDAO,$Component;
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];
if($_SESSION['discountsmode']==1)
{
	include_once("discount.inc.php");
}

$id				=	$_SESSION['tempsaleid'];
$customerid		=	$_SESSION['customerid'];
$tpmode			=	$_SESSION['tpmode'];
$customerid			=	$_SESSION['customerid'];
$creditcustomername	=	$_SESSION['creditcustomername'];
if($tpmode==2 && $customerid!='')
{
	$_SESSION['tpmode']=0;
	$_SESSION['customerid']=0;
	$_SESSION['creditcustomername']='';
	
	?>
	<script language="javascript">
   		
		<?php
			if($creditcustomername=='')
			{
		?>
			printaleinvice('<?php echo $_SESSION['tempsaleid'];?>');
		<?php
			}
			elseif($creditcustomername!='')
			{
			?>
				printcustomerinvoice('<?php echo $_SESSION['tempsaleid'];?>','<?php echo $customerid;?>');
			<?php
		}
		?>
		loadsection('main-content','sale.php?salecompleted=1');	
	</script>
	<?php
	exit;
}
//checking delete operation
$delid	=	$_GET['id'];
$action	=	$_GET['action'];
$gdiscount	=	getdiscount($id);
//echo $gdiscount."is the g discount .. $id";
if($delid && $action=='del')
{
	$delid_arr	=	explode("_",$delid);
	$paytype	=	$delid_arr[0];
	$newid		=	$delid_arr[1];
	if($paytype	==	'c')
	{
		$paymenttype='cash';
	}
	elseif ($paytype	==	'cc')
	{
		$paymenttype='cc';
	}
	elseif ($paytype	==	'fc')
	{
		$paymenttype='fc';
	}
	elseif ($paytype	==	'ch')
	{
		$paymenttype='cheque';		
	}
	$sqldelsel="select amount from $dbname_detail.payments where pkpaymentid='$newid'";
	$delamarr	=	$AdminDAO->queryresult($sqldelsel);
	$delamount	=	$delamarr[0]['amount'];
	$sqlupdateamount="Update $dbname_detail.sale set $paymenttype=($paymenttype-($delamount)),updatetime='$time' where pksaleid='$id'";
	$AdminDAO->queryresult($sqlupdateamount);
	
	$AdminDAO->deleterows("$dbname_detail.payments"," pkpaymentid='$newid'","1");
	//bringing back the returns to zero
	$adfield	=	array('adjustment');
	$advalue	=	array(0);
	$AdminDAO->updaterow("$dbname_detail.sale",$adfield,$advalue,"pksaleid='$id'");
	$delpayments		=	getprice($id);
	$getotalpaid		=	getpaidamount($id);
	$getotalpaid		=	explode("_",$getotalpaid);
	$remainingamountrs	=	$getotalpaid[0];
	$totalpricepaid		=	$getotalpaid[1];
	$pricetendered		=	$getotalpaid[2];
	$pricereturns		=	$getotalpaid[3];
	$pricewithdiscount	=	$getotalpaid[4];
	if($pricetendered>$totalpricepaid)
	$payableamount		=	($pricetendered+$pricereturns)-($totalpricepaid);
	//calculating remaining price
	//echo ".....................".$remainingamountrs."is the remaining amount in rs and the payable is $payableamount";
	if($remainingamountrs>$payableamount)
	{
		for($k=0;$k<sizeof($delpayments);$k++)
		{
			if($delpayments[$k]['amount']!=$delpayments[$k]['tendered'])
			{

				$deletefields	=	array('amount');
				$deletedata		=	array($delpayments[$k]['tendered']);
				$fieldid		=	$delpayments[$k]['id'];				
				$AdminDAO->updaterow("$dbname_detail.payments",$deletefields,$deletedata, " fksaleid='$id' AND pkpaymentid = '$fieldid'");				
			}// if amount comparison
		}// for 
	} // if price and payable
	else if($totalpricepaid<$pricewithdiscount)
	{
		$newpricetoadd		=	$pricewithdiscount-$totalpricepaid;
		$delpayments2		=	getprice($id);
		for($x=0;$x<sizeof($delpayments2);$x++)
		{
			if($delpayments2[$x]['amount']!=$delpayments2[$x]['tendered'])
			{
				$deletefields	=	array('amount');
				$fieldvariable	=	$delpayments2[$x]['amount']+$newpricetoadd;
				$deletedata		=	array($fieldvariable);
				$fieldid		=	$delpayments2[$x]['id'];
				$AdminDAO->updaterow("$dbname_detail.payments",$deletefields,$deletedata, " fksaleid='$id' AND pkpaymentid = '$fieldid'");
			}// if amount comparison
		}// for 
	}
} // delete
$banks_array	=	$AdminDAO->getrows('bank','*', ' 1');
$banks			=	$Component->makeComponent('d','banks',$banks_array,'pkbankid','bankname',1,$selected_banks,'','selectbox');
$cards_array	=	$AdminDAO->getrows('cctype','*', ' 1');
$ccs			=	$Component->makeComponent('d','card',$cards_array,'pkcctypeid','typename',1,$selected_cards);
$currency_array	=	$AdminDAO->getrows('currency','*', ' 1');

$d1			=	"<select name=\"currency\" id=\"currency\" style=\"width:80px;\" onChange=\"processcurrencyrate(); return false;\"><option value=\"\">Select Currency</option>";
for($i=0;$i<sizeof($currency_array);$i++)
{
	$d2			.=	"<option value = \"".$currency_array[$i]['pkcurrencyid']."\">".$currency_array[$i]['currencyname']." ".$currency_array[$i]['currencysymbol']."</option>";
}
$currency		=	$d1.$d2."</select>";
//$currency		=	$Component->makeComponent('d','currency',$currency_array,'pkcurrencyid','currencysymbol',1,$selected_currencies,'onChange=processcurrencyrate(); return false;');
$chequebanks	=	$Component->makeComponent('d','bank',$banks_array,'pkbankid','bankname',1,$selected_banks,'','selectbox');
//sale sum minus discount = total
//temp id to be deleted after tempid is available
$totalamount		=	getpaidamount($id);
$totalamount		=	explode("_",$totalamount);
$remainingprice		=	$totalamount[0];
// checking discount
$remainingprice		=	$remainingprice	- $gdiscount;
//$remainingprice		=	ceil($remainingprice);
$totalpaid			=	$totalamount[1];
$totalpaid			=	floor($totalpaid);
$tenderedamountrs	=	$totalamount[2];
$returnedamount		=	$totalamount[3];
$adjustmentamount	=	getadjustment($id);
$discountedprice	=	$totalamount[4];
//getting customer data
$sql=" SELECT CONCAT( firstname,' ', lastname,' (',nic,')') as customername, pkcustomerid
			FROM  customer, $dbname_detail.sale
			WHERE  pksaleid='$id' AND fkaccountid=pkcustomerid
	";
$customer_array	=	$AdminDAO->queryresult($sql);
$customername	=	$customer_array[0]['customername'];
$customerid		=	$customer_array[0]['pkcustomerid'];
// calculating Returns
if($tenderedamountrs>$totalpaid)
$payable	=	($tenderedamountrs)-($totalpaid+$adjustmentamount);
$payable	=	floor($payable);
//if($payable	< 0)
	//$returnedamount	=	$payable;
	//checking to see if the customer really paid something or is this just a return for a previous bill
	//checking amount paid previously
	if($discountedprice<0 && $discountedprice==$totalpaid)
	{
		$payable		=	0;
	}
	else if($remainingprice<0)
	{
		$payable		=	-($remainingprice);
	}
?>
<script language="javascript" type="text/javascript">
$(document).ready(function() {
function findValueCallback(event, data, formatted)
		{
			document.getElementById('getcustomer').value=data[0];
			document.getElementById('customerid').value=data[1];
			if(data[2]!='')
			{
				document.getElementById('taxableamount').style.display	=	'block';
				document.getElementById('taxableamount').style.display = 'table-row';
				document.getElementById('taxamount').value=data[2];
			}
			else
			{
				document.getElementById('taxableamount').style.display	=	'none';
				document.getElementById('taxamount').value='';
			}
			if(data[1]==56)
			{
				//alert('this is a gen credit customer');
				document.getElementById('gencreditac').style.display = 'block';
				document.getElementById('gencreditac').style.display = 'table-row';
				document.getElementById('gencreditor').focus();
			}
			else
			{
				document.getElementById('gencreditac').style.display = 'none';
			}
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
			document.getElementById('getcustomer').focus();
			
			jQuery("#getcustomer").autocomplete({data:1}, {url:"getcustomers.php", extraParams: {loc:function(){return $('#loc').val();}} });
			jQuery(":text, textarea").result(findValueCallback).next().click(function() 
			{
				$(this).prev().search();
			});
			jQuery("#clear").click(function() 
			{
				jQuery(":input").unautocomplete();
			});
						   });
function savecustomer(id)
{
	if(id==1)
	{
		if(document.getElementById('newfname').value == '')
		{
			alert('Please add First Name to continue ');
			document.getElementById('newfname').focus();
			return false;
		}
		else if(document.getElementById('newphone').value == '')
		{
			alert('Please add Phone Number to continue ');
			document.getElementById('newphone').focus();
			return false;
		}
		else if(document.getElementById('nicno').value == '')
		{
			alert('Please add NIC Number to continue ');
			document.getElementById('nicno').focus();
			return false;
		}
		else
		{
			loading('Please wait while the payment is processed ...');
			options	=	{	
					url : 'savetransaction.php?new='+id,
					type: 'POST',
					success: customerresponse
				}
		//alert('now i am saving new customer form');
		jQuery('#newcustomerfrm').ajaxSubmit(options);
		}
	}
	else
	{
		if(document.getElementById('getcustomer').value == '')
		{
			alert('Please select a customer or click the Add New button to add a new customer');
			return false;
		}
		else
		{
			loading('Please wait while the payment is processed ...');
			options	=	{	
					url : 'savetransaction.php?new='+id,
					type: 'POST',
					success: customerresponse
				}
			jQuery('#customerfrm').ajaxSubmit(options);
		}
	}
}
function customerresponse(text)
{
	if(text=='')
	{
		//printaleinvice('<?php //echo $_SESSION['tempsaleid'];?>');
		<?php
			if($creditcustomername=='')
			{
		?>
			printaleinvice('<?php echo $_SESSION['tempsaleid'];?>');
		<?php
			}
			elseif($creditcustomername!='')
			{
			?>
				var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=800,height=600,left=100,top=25';
		//alert('from empty');
			window.open('../store/admin/generatecreditorreport.php?tempsaleid='+tempsaleid+'&reporttype=3&taxpercentage=16&customerid=<?php echo $customerid;?>&customercopy=y&serialno=&adjustmentmode=0','Invice',display); 

			<?php
		}
		?>
		loadsection('main-content','sale.php?salecompleted=1');
	}
	else
	{
		notice(text,'',5000);
	}
}
function newcustomer()
{
	document.getElementById('customers').style.display = 'none';
	document.getElementById('newcustomer').style.display = 'block';
	document.getElementById('newfname').focus();
}
function delitem(pid)
{
	var delconfirm = confirm("Are you sure, you want to delete this transaction?");
		if(delconfirm)
		{
			jQuery('#main-content').load('payment.php?id='+pid+'&action=del');
		}
		else
		{
			return false;
		}
}
var submitcheck = 0;
function processcash()
{
	if(submitcheck!=1)
	{
		var cash=document.getElementById('cashamount').value;
		if(cash==0 || cash=='')
		{
			return false;
		}
		loading('Please wait while the payment is processed ...');
		options	=	{	
						url : 'insertcash.php',
						type: 'POST',
						success: cashresponse
					}
		jQuery('#paymentform').ajaxSubmit(options);
		submitcheck= 1;
	}
}
function cashresponse(text)
{
	
	if(text!='')
	{
		notice(text,'',5000);
		submitcheck = 0;
	}
	else
	{
		document.getElementById('cashamount').focus();
		jQuery('#main-content').load('payment.php');
	}
}
function processcreditcard()
{
	if(submitcheck!=1)
	{
		var cash=document.getElementById('creditamount').value;
		var charges=document.getElementById('creditcharges').value;
		var ccn=document.getElementById('ccnumber').value; 
		var cardd=document.getElementById('card').value; 
		var cbnk=document.getElementById('banks').value; 
	
		
		if(document.getElementById('creditamount').value == '')
		{
			alert('Please Enter Amount ');
			document.getElementById('creditamount').focus();
			return false;
		}
		else if(document.getElementById('creditcharges').value == '')
		{
			alert('Please Enter Credit Charges');
			document.getElementById('creditcharges').focus();
			return false;
		}
	else if(document.getElementById('ccnumber').value == '')
		{
			alert('Please Enter CC Number');
			document.getElementById('ccnumber').focus();
			return false;
		}
	
		else if(document.getElementById('banks').value == '')
		{
			alert('Please Select Bank');
			document.getElementById('banks').focus();
			return false;
		}
		else if((document.getElementById('card1').checked ==false) && (document.getElementById('card').checked ==false))
		{
			
			alert('Please Select Machine Type');
			document.getElementById('card').focus();
			return false;
		}
		if(cash==0 || cash=='')
		{
			alert('Please Enter Cash');
			return false;
		}
		loading('Please wait while the payment is processed ...');
		options	=	{	
						url : 'insertcard.php',
						type: 'POST',
						success: ccresponse
					}
		jQuery('#paymentform').ajaxSubmit(options);
		submitcheck	=	1;
	}
}
function ccresponse(text)
{
	if(text!='')
	{
		notice(text,'',5000);
		submitcheck = 0;
	}
	else
	{
		document.getElementById('cashamount').focus();
		jQuery('#main-content').load('payment.php');
	}
}
function processfc()
{
	if(submitcheck!=1)
	{
		var cash=document.getElementById('fcamount').value;
		if(cash==0 || cash=='')
		{
			return false;
		}
		loading('Please wait while the payment is processed ...');
		options	=	{	
						url : 'insertfc.php',
						type: 'POST',
						success: fcresponse
					}
		jQuery('#paymentform').ajaxSubmit(options);
		submitcheck	=	1;
	}
}
function fcresponse(text)
{
	if(text!='')
	{
		notice(text,'',5000);
		submitcheck = 0;
	}
	else
	{
		document.getElementById('fcamount').focus();
		jQuery('#main-content').load('payment.php');
	}
}
function processcheque()
{
	if(submitcheck!=1)
	{
		var cash=document.getElementById('chequeamount').value;
		if(cash==0 || cash=='')
		{
			return false;
		}
		loading('Please wait while the payment is processed ...');
		options	=	{	
						url : 'insertcheque.php',
						type: 'POST',
						success: chequeresponse
					}
		jQuery('#paymentform').ajaxSubmit(options);
		submitcheck	=	1;
	}
}
function chequeresponse(text)
{
	if(text!='')
	{
		notice(text,'',5000);
		submitcheck = 0;
	}
	else
	{
		document.getElementById('chequeamount').focus();
		jQuery('#main-content').load('payment.php');
	}
}
var divID=1;
function paymentdetails(value)
{
	if(value=='c')
	{
		if(divID!='')
		{
			document.getElementById("payment_details"+divID).style.display="none";
		}
		divID = "1";		
		document.getElementById("payment_details1").style.display="block";
	}
	if(value=='cc')
	{
		if(divID!='')
		{
			document.getElementById("payment_details"+divID).style.display="none";
		}
		divID = "2";		
		document.getElementById("payment_details2").style.display="block";		
	}
	if(value=='fc')
	{
		if(divID!='')
		{
			document.getElementById("payment_details"+divID).style.display="none";
		}
		divID = "3";		
		document.getElementById("payment_details3").style.display="block";		
	}
	if(value=='ch')
	{
		if(divID!='')
		{
			document.getElementById("payment_details"+divID).style.display="none";
		}
		divID = "4";		
		document.getElementById("payment_details4").style.display="block";		
	}
}
function processcurrencyrate()
{ 
	options	=	{	
					url : 'getrate.php',
					type: 'POST',
					success: fcrate
				}
	jQuery('#paymentform').ajaxSubmit(options);
}
function fcrate(text)
{
	var val, rate,newrate;
	val		=	text.split(',');
	rate	=	val[0];
	newrate	=	val[1];
	document.getElementById('error').style.display	=	'block';
	document.getElementById('fcrate').value=rate;
	document.getElementById('fcamount').value=newrate;	
	document.getElementById('fcrate').focus();
}
function paycomplete()
{
	if('<?php echo $remainingprice ?>' != 0)
	{ 
	  /*if ('<?php echo $remainingprice ?>' < 0) { // added by Yasir 22-06-11
	   alert("Amount should not be less than 0, please click on Pay Now to clear it.");
	   false;
	  } else {*/ // Commented by Yasir 18-08-11 on request from kohsaar
		
		if(confirm("Should I transfer the remaining <?php echo "$remainingprice" ?> amount to Credit Account?"))
		{
			document.getElementById('customers').style.display = 'block';
			document.getElementById('getcustomer').focus();
			return false;
		}
		else
		{
			return false;
		}
	 // }
	}
	else if('<?php echo $remainingprice ?>' <= 0)
	{
		if('<?php echo $adjustmentamount ?>'==0)
		{
			processreturn();
		}		
		$.post("paycomplete.php", { remainngprice: "<?php echo $remainingprice;?>"},
		  function(data)
		  {
			//printaleinvice('<?php //echo $_SESSION['tempsaleid'];?>');
				<?php
			if($creditcustomername=='')
			{
		?>
			printaleinvice('<?php echo $_SESSION['tempsaleid'];?>');
		<?php
			}
			elseif($creditcustomername!='')
			{
			?>
				var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=800,height=600,left=100,top=25';
		//alert('from empty');
			window.open('../store/admin/generatecreditorreport.php?tempsaleid='+tempsaleid+'&reporttype=3&taxpercentage=16&customerid=<?php echo $customerid;?>&customercopy=y&serialno=&adjustmentmode=0','Invice',display); 

			<?php
		}
		?>
			
			loadsection('main-content','sale.php?salecompleted=1');
		  });
		return false;
	}
	else
	{
		return false;
	}
}
function givediscount()
{
	document.getElementById('disc').style.display = 'block';
	document.getElementById('discount').focus();
	
	if('<?php echo $gdiscount; ?>'>0)
	{
		document.getElementById('discount').value	= '<?php echo $gdiscount;?>';
	}
	else
	{
		//document.getElementById('discount').value	= '<?php echo $remainingprice; ?>';
		// above commented and added by Yasir 25-07-11
		document.getElementById('discount').value	= '0';
	}
	
}
function processdiscount(confirms)
{
	options	=	{	
					url : 'givediscount.php?conf='+confirms,
					type: 'POST',
					success: processdisc
				}
	jQuery('#discountform').ajaxSubmit(options);
}
function processdisc(text)
{
	if(text!='')
	{
		if(text=='Greater than five percent discount.')
		{
 			//notice(text,'',5000);
			jQuery('#confirmdiscount').show();
  			loadsection('confirmdiscount','confirmdiscount.php');
		}
		else
		{
			notice(text,'',5000);
			loadsection('main-content','payment.php');
		}
	}
}

////////////////////////////coupon//////////////////////////////////////////
function givecoupon_amount()
{
	document.getElementById('disc1').style.display = 'block';
	document.getElementById('discount').focus();
	
	if('<?php echo $gdiscount; ?>'>0)
	{
		document.getElementById('discount').value	= '<?php echo $gdiscount;?>';
	}
	else
	{
		//document.getElementById('discount').value	= '<?php echo $remainingprice; ?>';
		// above commented and added by Yasir 25-07-11
		document.getElementById('discount').value	= '0';
	}
	
	
}
/////////////////////////////////////////////////////////////////////////////

//////////////////////////////processcoupon/////////////////////////////////////
function processcoupon(confirms)
{
	options	=	{	
					url : 'givecoupon.php?conf='+confirms,
					type: 'POST',
					success: processdisc
				}
	jQuery('#discountform1').ajaxSubmit(options);
}
function processdisc(text)
{
	if(text!='')
	{
		if(text=='Greater than five percent discount.')
		{
 			//notice(text,'',5000);
			jQuery('#confirmdiscount').show();
  			loadsection('confirmdiscount','confirmdiscount.php');
		}
		else
		{
			notice(text,'',5000);
			loadsection('main-content','payment.php');
		}
	}
}

//////////////////////////////////////////////////////////////////////////////
function pcomplete(text)
{
	if(text!='')
	{
		notice(text,'',5000);
	}
	loadsection('main-content','sale.php?salecompleted=1');
}
jQuery(function($)
{
	$("#chequedate").datepicker({dateFormat: 'yy-mm-dd'});
});
function addnewtext(div,ctrlname,curr)
{
	if(curr=='Add New')
	{
		//curr="<a herf='void(0)' onclick='addnewtext(div,newcctype,curr)";	
	}
	var control="<input type='text' name='"+ctrlname+"' id='"+ctrlname+"' class='text'>";
	jQuery('#'+div).html(control);
	//return false;
}
function processreturn()
{
	if('<?php echo $payable; ?>'>0)
	{
		document.getElementById('returnamount').value	= '<?php echo $payable;?>';
	}
	options	=	{	
					url : 'givereturn.php',
					type: 'POST',
					success: processret
				}
	jQuery('#returnform').ajaxSubmit(options);
}
function processret(text)
{
	/*if(text!='')
	{
		notice(text,'',5000);
		printaleinvice('<?php// echo $_SESSION['tempsaleid'];?>');
		loadsection('main-content','sale.php?salecompleted=1');
	}*/
}
function calcfcprice(val)
{
	var fcamountval,newfcamount,newfc;
	if(document.getElementById('newfc').value!='')
	{
		fcamountval	=	<?php echo $remainingprice; ?>;
		newfcamount	=	fcamountval/val;
		newfc		=	Math.ceil(newfcamount);
		document.getElementById('fcamount').value	=	newfc;
	}
	else
	{
		return false;
	}
}
</script>
<div id="loading" class="loading" style="display:none;"> </div>
<div id="error"> </div>
<div id="barcode">
  <form id="paymentform">
    <div class="Table">
      <div class="Row">
        <div class="Column">
          <label>Pay by</label>
          <select name="paymentmethod" onchange="paymentdetails(this.value);">
            <option value="c" selected="selected">Cash</option>
            <option value="cc">Credit Card</option>
            <option value="fc">Foreign Currency</option>
            <option value="ch">Cheque</option>
          </select>
        </div>
        <div id="payment_details1" style="display:block;">
          <div class="Column">
            <!-- case 1 cash -->
            <label>Amount</label>
            <input type="text" name="cashamount" class="text" id="cashamount" value="<?php echo $remainingprice; ?>" onkeydown="javascript:if(event.keyCode==13) {processcash(); return false;}" onkeypress="return isNumberKey(event)" onfocus="this.select()"/>
          </div>
          <div class="Row">
            <div class="Column">
              <label>&nbsp;</label>
              <span class="buttons">
              <button type="button" onclick="processcash();"> <img src="images/tick.png" alt=""/> Pay Now </button>
              <button type="button" name="button2" id="button2" onclick="loadsection('main-content','sale.php');" title="BACKSPACE"> <img src="images/cross.png" alt=""/> Cancel </button>
              </span>
            </div>
          </div>
        </div>
        <div id="payment_details2" style="display:none;">
          <!-- case 2 credit card -->
          <div class="Column">
            <label>Amount</label>
            <input type="text" name="creditamount" class="text" id="creditamount" value="<?php echo $remainingprice; ?>" onkeydown="javascript:if(event.keyCode==13) {processcreditcard(); return false;}" onkeypress="return isNumberKey(event)"  onfocus="this.select()"/>
          </div>
          <div class="Column">
            <label>Charges</label>
            <input type="text" name="creditcharges" class="text" id="creditcharges" onkeydown="javascript:if(event.keyCode==13) {processcreditcard(); return false;}" onkeypress="return isNumberKey(event)"/>
          </div>
          <div class="Column">
            <label>Type</label>
            <div id="addcc"><input type="radio" name="card" id="card1" value="1">&nbsp;Al-Flah&nbsp;
<input type="radio" name="card" value="2" id="card">&nbsp;MCB<?php //echo $ccs; ?><!--<a href="javascript:void(0)" onclick="addnewtext('addcc','newcard','Add New');" style="font-size:10px"> New</a>--></div>
          </div>
          <div class="Column">
            <label>Bank</label>
            <div id="addbank"> <?php echo $banks; ?> <!--<a href="javascript:void(0)" onclick="addnewtext('addbank','newbank','Add New');" style="font-size:10px"> New</a> --></div>
          </div>
          <div class="Column">
            <label>CC #</label>
            <input type="text" name="ccnumber" class="text" id="ccnumber" onkeydown="javascript:if(event.keyCode==13) {processcreditcard(); return false;}" maxlength="4"/>
          </div>
          <div class="Row">
            <div class="Column">
              <label>&nbsp;</label>
              <span class="buttons">
              <button type="button" onclick="processcreditcard();" title="CTRL+S"> <img src="images/tick.png" alt=""/> Pay Now </button>
              <button type="button" name="button2" id="button2" onclick="loadsection('main-content','sale.php');"> <img src="images/cross.png" alt=""/> Cancel </button>
              </span>
            </div>
          </div>
        </div>
        <!-- End Payment Details 2 -->
        <div id="payment_details3" style="display:none;">
          <!-- case 3 foreign currency -->
          <div class="Column">
            <label>Amount</label>
            <input type="text" name="fcamount" class="text" id="fcamount" value="<?php echo $remainingprice; ?>" onkeydown="javascript:if(event.keyCode==13) {processfc(); return false;}" onkeypress="return isNumberKey(event)"  onfocus="this.select()"/>
          </div>
          <div class="Column">
            <label>Currency</label>
            <div id="addnewfc"> <?php echo $currency; ?> <!--<a href="javascript:void(0)" onclick="addnewtext('addnewfc','newfc','Add New');" style="font-size:10px"> New</a>--> </div>
          </div>
          <div class="Column">
            <label>Rate</label>
            <input type="text" name="fcrate" class="text" id="fcrate" onkeydown="javascript:if(event.keyCode==13) {processfc(); return false;}" onkeypress="return isNumberKey(event)" onblur="calcfcprice(this.value);"/>
          </div>
          <div class="Column">
            <label>Charges</label>
            <input type="text" name="fccharges" class="text" id="fccharges" onkeydown="javascript:if(event.keyCode==13) {processfc(); return false;}" onkeypress="return isNumberKey(event)"/>
          </div>
          <div class="Row">
            <div class="Column">
              <label>&nbsp;</label>
              <span class="buttons">
              <button type="button" onclick="processfc();" title="CTRL+S"> <img src="images/tick.png" alt=""/> Pay Now </button>
              <button type="button" name="button2" id="button2" onclick="loadsection('main-content','sale.php');"> <img src="images/cross.png" alt=""/> Cancel </button>
              </span>
            </div>
          </div>
        </div>
        <!-- End Payment Details 3 -->
        <div id="payment_details4" style="display:none;">
          <!-- case 4 cheque -->
          <div class="Column">
            <label>Amount</label>
            <input type="text" name="chequeamount" class="text" id="chequeamount" value="<?php echo $remainingprice; ?>" onkeydown="javascript:if(event.keyCode==13) {processcheque(); return false;}" onkeypress="return isNumberKey(event)"  onfocus="this.select()"/>
          </div>
          <div class="Column">
            <label>Cheque #</label>
            <input type="text" name="chequenumber" class="text" id="chequenumber" onkeydown="javascript:if(event.keyCode==13) {processcheque(); return false;}"/>
          </div>
          <div class="Column">
            <label>Bank</label>
            <div id="chkbank"> <?php echo $chequebanks;?> <!--<a href="javascript:void(0)" onclick="addnewtext('chkbank','newbank','Add New');" style="font-size:10px"> New</a>--> </div>
          </div>
          <div class="Column">
            <label>Date</label>
            <input type="text" name="chequedate" class="text" id="chequedate" onkeydown="javascript:if(event.keyCode==13) {processcheque(); return false;}"/>
          </div>
          <div class="Row">
            <div class="Column">
              <label>&nbsp;</label>
              <span class="buttons">
              <button type="button" onclick="processcheque();" title="CTRL+S"> <img src="images/tick.png" alt=""/> Pay Now </button>
              <button type="button" name="button2" id="button2" onclick="loadsection('main-content','sale.php');"> <img src="images/cross.png" alt=""/> Cancel </button>
              </span>
            </div>
          </div>
        </div>
        <!-- End Payment Details 4 -->
      </div>
    </div>
    <!-- . Table -->
    <input type="hidden" name="remainingprice" value="<?php echo $remainingprice; ?>" />
  </form>
</div>
<div id="leftpanel">
<div id="confirmdiscount" class="cancelsalebox" style="position:absolute;margin-top:75px;"></div>
<div id="discount_details"></div>
  <table class="pos">
    <tr>
      <th>Sr No.</th>
      <th>Time</th>
      <th>Payment Mode</th>
      <th>Amount</th>
      <th>Rate</th>
      <th>Payment Charges</th>
      <th>Amount <?php echo $defaultcurrency;?></th>
      <th><!-- --></th>
    </tr>
    <?php
/*echo "<pre>";
print_r($payments);
echo "</pre>";*/
$payments	=	getprice($id);
for($j=0;$j<sizeof($payments);$j++)
{
	$pid			=	$payments[$j]['id'];
	$ptype			=	$payments[$j]['type'];
	$amount			=	$payments[$j]['amount'];
	$currency		=	$payments[$j]['currency'];
	$rate			=	$payments[$j]['rate'];
	$paytime		=	$payments[$j]['paytime'];
	$pcharges		=	$payments[$j]['charges'];
	$returned		+=	$payments[$j]['returned'];	
	$paytime		=	date("F j, Y g:i a",$paytime);
	$tenderedamount	+=	($payments[$j]['tendered']);
	$returned		+=	($payments[$j]['returned']);
	$amountinrs		=	$amount;	
?>
    <tr>
      <td><?php echo $j+1; ?></td>
      <td><?php echo $paytime; ?></td>
      <td><?php 
		if($ptype=='c') {echo "Cash";} elseif($ptype=='cc') {echo "Credit Card";} elseif($ptype=='fc') {echo "Foreign Currency";} elseif($ptype=='ch') {echo "Cheque";}
		?></td>
      <td id="aright"><?php if ($ptype == 'fc') {echo numbers($amount/$rate);} else {echo numbers($amount);} echo " ".$currency;?></td>
      <td id="aright"><?php echo $rate;?></td>
      <td id="aright"><?php echo numbers($pcharges)?></td>
      <td id="aright"><?php echo numbers($amountinrs); ?></td>
      <td align="center"><a href="javascript: void(0)" onclick="delitem('<?php echo $ptype."_".$pid;?>')"><img src="images/hr.gif" border="0" /></a></td>
    </tr>
    <?php
}
?>
  </table>
</div>
<div id="rightpanel">
  <table class="price">
    <tr>
      <th>Total</th>
      <td id="aright"><?php echo numbers($discountedprice);?></td>
    </tr>
    <tr>
      <th>Paid</th>
      <td id="aright"><?php echo numbers($totalpaid);?></td>
    </tr>
    <tr>
      <th>Payable</th>
      <td id="aright"><?php echo numbers($remainingprice);?></td>
    </tr>
    <tr>
      <th>Return</th>
      <td id="aright"><?php echo numbers($payable);?></td>
    </tr>
    <tr>
      <th>Discount</th>
      <td id="aright"><?php echo numbers($gdiscount);?></td>
    </tr>
    <?php 
	// customer info section -- appears while the bill is adjusted for a credit sale
	if($customername)
	{
		
	?>
    <tr>
      <th>Customer</th>
      <td style="font-size:14px;" id="aright"><?php echo $customername;?></td>
    </tr>
    <?php 
	}
	?>
  </table>
  <span class="buttons">
  <button type="button" id="completetransactionbutton" onclick="paycomplete();" title="F3"> <img src="images/tick.png" alt=""/> Complete Transaction </button>
  <button type="button" onclick="givediscount();" title="F4"> <img src="images/tick.png" alt=""/> Discount </button>
   <button type="button" onclick="givecoupon_amount();" > <img src="images/tick.png" alt=""/> Coupon </button>
  </span>
  <div id="disc" style="display:none; clear:both; float:left;">
    <form id="discountform">
      <table class="price">
        <tr>
          <th>Discount Amount</th>
          <th><input type="text" name="discount" class="text" id="discount" onkeydown="javascript:if(event.keyCode==13) {processdiscount(); return false;}"  onfocus="this.select()"/>
          </th>
        </tr>
        <tr>
        <tr>
          <td colspan="2">
          <input type="hidden" name="total" value="<?php echo $discountedprice; ?>" /> <?php /*?><!--Added by Yasir -- 04-07-11--><?php */?>
          <span class="buttons">
          <button type="button" name="addnewdiscount" id="addnewdiscount" onclick="processdiscount();" title="SHIFT+S" style="font-size:12px;"> <img src="images/tick.png" alt=""/> Process Discount </button></span></td>
        </tr>
        </tr>
        
      </table>
    </form>
  </div>
   <div id="disc1" style="display:none; clear:both; float:left;">
    <form id="discountform1">
      <table class="price">
        <tr>
          <th>Coupon ID</th>
          <th><input type="text" name="discount" class="text" id="discount" onkeydown="javascript:if(event.keyCode==13) {processcoupon(); return false;}"  onfocus="this.select()"/>
          </th>
        </tr>
        <tr>
        <tr>
          <td colspan="2">
          <input type="hidden" name="total" value="<?php echo $discountedprice; ?>" /> <?php /*?><!--Added by Yasir -- 04-07-11--><?php */?>
          <span class="buttons">
          <button type="button" name="addnewdiscount" id="addnewdiscount" onclick="processcoupon();" title="SHIFT+S" style="font-size:12px;"> <img src="images/tick.png" alt=""/> Process Coupon </button></span></td>
        </tr>
        
      </table>
    </form>
  </div>
  <div id="returnfrms">
    <form id="returnform">
		<input type="hidden" name="returnamount" class="text" id="returnamount" />
    </form>
  </div>
  <div id="error"></div>
  <div id="customers" style="display:none; clear:both; float:left;">
    <form id="customerfrm">
      <table class="price">
       <tr>
          <th>Select Location</th>
          <td><select name="loc" id="loc" style="width:135px;" ><option value="">All</option>
  <?php $listlocs	=	$AdminDAO->getrows("store","*");
// pkgroupid 	groupname
for($i=0;$i<sizeof($listlocs);$i++)
{
	$locid	=	$listlocs[$i]['pkstoreid'];
	$groupname		=	$listlocs[$i]['storename'];
	$select		=	"";
	if($lid==''){
		$lid=3;
		}
	if($locid == $lid)
	{
		$select = "selected=\"selected\"";
	} ?>
	<option value="<?php echo $locid?>"  <?php echo $select ?>><?php echo $groupname ?></option>
<?php } ?>
</select>
        </td>
        </tr>
        <tr>
          <th>Select Customer</th>
          <td><input type="text" name="getcustomer" id="getcustomer" class="text" value="<?php echo $customername;?>">
            <input type="hidden" name="customerid" id="customerid" value="<?php echo $customerid;?>" autocomplete="off" /><input type="hidden" name="remamount" id="remamount" value="<?php echo $remainingprice;?>" />
        </td>
        </tr>
        <tr id="taxableamount" style="display:none;">
        	<th>Tax Amount</th>
            <td><input type="text" id="taxamount" name="taxamount" value="" class="text" /></td>
        </tr>
        <tr id="gencreditac" style="display:none;">
            <th>Customer Name</th>
            <td><input type="text" name="gencreditor" id="gencreditor" value="" class="text" maxlength="30" />
    	</tr>
        <tr>
          <td colspan="2"><span class="buttons"><button type="button" name="savecust" id="savecust" onclick="savecustomer(0);" title="ALT+S"  style="font-size:12px;"> <img src="images/disk.png" alt=""/> Save </button></span></td>
         <!-- <td><span class="buttons"><button type="button" name="addnew" id="addnew" onclick="newcustomer();" title="SHIFT+C"  style="font-size:12px;"> <img src="images/add.png" alt=""/> Add New </button></span></td>-->
        </tr>
      </table>
    </form>
  </div>
  <div id="newcustomer" style="display:none; clear:both; float:left;">
    <form id="newcustomerfrm">
      <table class="price">
        <tr>
          <th>First Name</th>
          <td><input type="text" name="newfname" id="newfname" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}"></td>
        </tr>
        <tr>
          <th>Last Name</th>
          <td><input type="text" name="newlname" id="newlname" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}"></td>
        </tr>
        <tr>
          <th>Phone Number</th>
          <td><input type="text" name="newphone" id="newphone" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}"></td>
        </tr>
        <tr>
          <th>NIC #</th>
          <td><input type="text" name="nicno" id="nicno" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}"></td>
        </tr>
        <tr>
          <td colspan="2"><span class="buttons"><button type="button" name="savenewcust" id="savenewcust" onclick="savecustomer(1);" title="SHIFT+N"  style="font-size:12px;"> <img src="images/disk.png" alt=""/> Save </button></span></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript">
	document.getElementById('cashamount').focus();
	// code added to complete the transaction when amount to be paid reaches zero
	// dated: 29-01-2010
	if(document.getElementById('cashamount').value==0)
	{
		paycomplete();
	}
</script>