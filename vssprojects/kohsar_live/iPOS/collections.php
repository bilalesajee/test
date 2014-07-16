<?php
session_start();
include_once("includes/security/adminsecurity.php");
include_once("surl.php");
//include_once("includes/classes/customerbalance.php");
global $AdminDAO,$Component;
//checking delete operation
$param		=	$_GET['param'];
if($param!='bill')
{
  $customerid	=	$_GET['id'];
  $customer = (int)$_GET['id'];
 // if($_SESSION['SERVER_ACC_ONLINE']==1){
  $cust_bal= file_get_contents($serverUrl_.$customer);
  $cust_total= file_get_contents($serverUrl_total.$customer);
  $cust_totale = json_decode($cust_total, true);
  $cust_total=$cust_totale['sale'];
  $cust_dis=$cust_totale['dicount'];
  $Total_Paid=$cust_totale['totalpaid'];
  //}
}
else
{
	$billid	=	$_GET['id'];//changed $dbname_main to $dbname_detail on line 24 by ahsan 22/02/2012
	$sql="SELECT 
				 fkaccountid,
				 cash,
				 cc,
				 fc,
				 cheque,
				 totalamount,
				 globaldiscount 
			FROM 
				$dbname_detail.sale 
			WHERE 
				pksaleid='$billid'";
	$billarr	=	$AdminDAO->queryresult($sql);
	$customerid	=	$billarr[0]['fkaccountid'];
	$cash		=	$billarr[0]['cash'];
	$cc			=	$billarr[0]['cc'];
	$fc			=	$billarr[0]['fc'];
	$cheque		=	$billarr[0]['cheque'];
	$totalamount	=	$billarr[0]['totalamount'];
	$globaldiscount	=	$billarr[0]['globaldiscount'];
	$gtotal		=	($cash+$cc+$fc+chequ)-$globaldiscount;
	$credit		=	$totalamount-$gtotal;	
	//$credit		=	number_format($billarr[0]['credit'],0);
}//changed $dbname_main to $dbname_detail on line 54, 55 by ahsan 22/02/2012
$query="SELECT 
			DISTINCT id as pkcustomerid,
			CONCAT(firstname,', ',lastname) as name,
			CONCAT(address1,', ',address2) as address,
			email,
			title as companyname,
			CONCAT(phone ,', ',mobile) as phone,
			nic,
			ROUND(SUM(cash)) as cash,
			ROUND(SUM(cc)) as cc,
			ROUND(SUM(fc)) as fc,
			ROUND(SUM(cheque)) as cheque,
			SUM(totalamount) as totalamount,
			SUM(globaldiscount) as discount
		FROM
			$dbname_detail.sale,
			$dbname_detail.account LEFT JOIN $dbname_detail.addressbook ON (pkaddressbookid=fkaddressbookid)
		WHERE
			fkaccountid='$customerid' AND
			id=fkaccountid
		";
//echo $query;
$customer_array		=	$AdminDAO->queryresult($query);
$customername		=	$customer_array[0]['name'];
//$total				=	$customer_array[0]['total'];
$total				=	$customer_array[0]['totalamount'];
$discount			=	$customer_array[0]['discount'];
$cash				=	$customer_array[0]['cash'];
$cc					=	$customer_array[0]['cc'];
$fc					=	$customer_array[0]['fc'];
$cheque				=	$customer_array[0]['cheque'];
//$totalpaid		=	floor($customer_array[0]['paid']);
$totalpaid			=	floor($cash+$cc+$fc+$cheque);
//$remainingprice	=	ceil($customer_array[0]['pending']);
$remainingprice		=	ceil($cust_total-$Total_Paid-$cust_dis);
/*if($credit>0)
{
	$remainingprice=$credit;
}*/
/*******************************COMPONENTS*****************************************/
$banks_array	=	$AdminDAO->getrows('bank','*', ' 1');
$banks			=	$Component->makeComponent('d','banks',$banks_array,'pkbankid','bankname',1,$selected_banks,'','selectbox');
$cards_array	=	$AdminDAO->getrows('cctype','*', ' 1');
$ccs			=	$Component->makeComponent('d','card',$cards_array,'pkcctypeid','typename',1,$selected_cards);
$currency_array	=	$AdminDAO->getrows('currency','*', ' 1');
// Chnaged by Yasir -- 07-07-11. Previous was \"onChange=processcurrencyrate(); return false;\"
$d1			=	"<select name=\"currency\" id=\"currency\" style=\"width:80px;\" onChange=\"processcurrencyrate();\" return false;><option value=\"\">Select Currency</option>";
for($i=0;$i<sizeof($currency_array);$i++)
{
	$d2			.=	"<option value = \"".$currency_array[$i]['pkcurrencyid']."\">".$currency_array[$i]['currencyname']." ".$currency_array[$i]['currencysymbol']."</option>";
}
$currency		=	$d1.$d2."</select>";
//$currency		=	$Component->makeComponent('d','currency',$currency_array,'pkcurrencyid','currencysymbol',1,$selected_currencies,'onChange=processcurrencyrate(); return false;');
$chequebanks	=	$Component->makeComponent('d','bank',$banks_array,'pkbankid','bankname',1,$selected_banks,'','selectbox');
?>
<script language="javascript" type="text/javascript">
function processamount()
{
 paymentmethod	=	document.getElementById('paymentmethod').value;
	if(paymentmethod == 'c')
{
	if(document.getElementById('amount1').value == '')
		{
			alert('Please Enter Amount ');
			document.getElementById('amount1').focus();
			return false;
		}
		
	
	
	
}
else 	if(paymentmethod == 'cc')
{
	if(document.getElementById('amount2').value == '')
		{
			alert('Please Enter CC Amount ');
			document.getElementById('amount2').focus();
			return false;
		}
		else if(document.getElementById('creditcharges').value == '')
		{
			alert('Please Enter Credit Charges');
			document.getElementById('creditcharges').focus();
			return false;
		}
		else if((document.getElementById('card1').checked ==false) && (document.getElementById('card').checked ==false))
		{
			
			alert('Please Select Machine Type');
			document.getElementById('card').focus();
			return false;
		}
		
	
	
		else if(document.getElementById('banks').value == '')
		{
			alert('Please Select Bank');
			document.getElementById('banks').focus();
			return false;
		}
		else if(document.getElementById('ccnumber').value == '')
		{
			alert('Please Enter CC Number');
			document.getElementById('ccnumber').focus();
			return false;
		}
	
	
	
}
else 	if(paymentmethod == 'fc')
{
	if(document.getElementById('amount3').value == '')
		{
			alert('Please Enter FC Amount ');
			document.getElementById('amount3').focus();
			return false;
		}
		else if(document.getElementById('currency').value == '')
		{
			alert('Please Select Currency');
			document.getElementById('currency').focus();
			return false;
		}
		
		
	
	
		else if(document.getElementById('fcrate').value == '')
		{
			alert('Please Enter Rate');
			document.getElementById('fcrate').focus();
			return false;
		}
		else if(document.getElementById('fccharges').value == '')
		{
			alert('Please Enter FC Charges');
			document.getElementById('fccharges').focus();
			return false;
		}
	
	
	
}
	
	else 	if(paymentmethod == 'ch')
{
	if(document.getElementById('amount4').value == '')
		{
			alert('Please Enter Cheque Amount ');
			document.getElementById('amount4').focus();
			return false;
		}
		else if(document.getElementById('chequenumber').value == '')
		{
			alert('Please Enter Cheque Number');
			document.getElementById('chequenumber').focus();
			return false;
		}
		
		
	
	
		else if(document.getElementById('bank').value == '')
		{
			alert('Please Select Bank');
			document.getElementById('bank').focus();
			return false;
		}
		else if(document.getElementById('chequedate').value == '')
		{
			alert('Please Enter Date');
			document.getElementById('chequedate').focus();
			return false;
		}
	
	
	
}
	
	if(confirm("Are you sure to proccess this Amount!"))
	{
		loading('Please wait while the payment is processed ...');
		options	=	{	
						url : 'collectionsaction.php?customerid=<?php echo $customerid;?>',
						type: 'POST',
						success: collectcresponse
					}
		jQuery('#paymentform').ajaxSubmit(options);
		jQuery('#main-content').load('customers.php');
	}
	else
	{
		return false;
	}
}
function collectcresponse(text)
{ 
	if(text==1)
	{
		notice('Please enter opening amount','',5000);
		$('#openingfrmdiv').load('openingfrm.php');
		document.getElementById('openingfrmdiv').style.display='block';
	}
	// added by Yasir -- 07-07-11
	else if(text != '')		
	{
		notice(text,0,5000);
	}
	//
	else if(text=='') // replaced text=='' by text!='' by Yasir 25-07-11
	{
		text="Payment has been proccessed.";
		printcollectionbill('<?php echo $customerid;?>');
		notice(text,'',5000);
	}
	/*if(text!='')
	{
		notice(text,'',5000);
		
	}
	else
	{
		//document.getElementById('fcamount').focus();
		//jQuery('#main-content').load('collections.php');
	}*/
}
function calcfcprice(val)
{
	var fcamountval,newfcamount,newfc;
	if(document.getElementById('newfc'))
	{
		if(document.getElementById('newfc').value!='')
		{
			fcamountval	=	<?php echo $remainingprice; ?>;
			newfcamount	=	fcamountval/val;
			newfc		=	Math.ceil(newfcamount);
			document.getElementById('amount3').value	=	newfc;
		}
		else
		{
			return false;
		}
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
			val	=	document.getElementById('amount'+divID).value;
		}
		divID = "1";		
		document.getElementById("payment_details1").style.display="block";
		document.getElementById('amount'+divID).value	=	val;
		
		
	}
	if(value=='cc')
	{
		if(divID!='')
		{
			document.getElementById("payment_details"+divID).style.display="none";
			val	=	document.getElementById('amount'+divID).value;
		}
		divID = "2";		
		document.getElementById("payment_details2").style.display="block";	
		document.getElementById('amount'+divID).value	=	val;
	}
	if(value=='fc')
	{
		if(divID!='')
		{
			document.getElementById("payment_details"+divID).style.display="none";
			val	=	document.getElementById('amount'+divID).value;
		}
		divID = "3";		
		document.getElementById("payment_details3").style.display="block";	
		document.getElementById('amount'+divID).value	=	val;
	}
	if(value=='ch')
	{
		if(divID!='')
		{
			document.getElementById("payment_details"+divID).style.display="none";
			val	=	document.getElementById('amount'+divID).value;
		}
		divID = "4";		
		document.getElementById("payment_details4").style.display="block";
		document.getElementById('amount'+divID).value	=	val;
	}
}
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
jQuery(function($)
{
	$("#chequedate").datepicker({dateFormat: 'yy-mm-dd'});
});
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
	document.getElementById('amount3').value=newrate;	
	document.getElementById('fcrate').focus();
}
</script>
<div id="loading" class="loading" style="display:none;"> </div>
<div id="barcode">
  <form id="paymentform">
    <div class="Table">
      <div class="Row">
        <div class="Column">
          <label>Pay by</label>
          <select name="paymentmethod" id="paymentmethod" onchange="paymentdetails(this.value);">
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
            <input type="text" name="amount1" class="text" id="amount1" value="" onkeydown="javascript:if(event.keyCode==13) {processamount(); return false;}" onkeypress="return isNumberKey(event)" onfocus="this.select()"/>
          </div>
          <div class="Row">
            <div class="Column">
              <label>&nbsp;</label>
              <span class="buttons">
              <button type="button" onclick="processamount();" > <img src="images/tick.png" alt=""/> Pay Now </button>
              <button type="button" name="button2" id="button2" onclick="loadsection('main-content','sale.php');" title="BACKSPACE"> <img src="images/cross.png" alt=""/> Cancel </button>
              </span>
            </div>
          </div>
        </div>
        <div id="payment_details2" style="display:none;">
          <!-- case 2 credit card -->
          <div class="Column">
            <label>Amount</label>
            <input type="text" name="amount2" class="text" id="amount2" value="" onkeydown="javascript:if(event.keyCode==13) {processamount(); return false;}" onkeypress="return isNumberKey(event)"  onfocus="this.select()"/>
          </div>
          <div class="Column">
            <label>Charges</label>
            <input type="text" name="creditcharges" class="text" id="creditcharges" value="0" onkeydown="javascript:if(event.keyCode==13) {processamount(); return false;}" onkeypress="return isNumberKey(event)"/>
          </div>
         <div class="Column">
            <label>Type</label>
            <div id="addcc"><input type="radio" name="card" id="card1" value="1">&nbsp;Al-Flah&nbsp;
<input type="radio" name="card" id="card" value="2">&nbsp;MCB<?php //echo $ccs; ?><!--<a href="javascript:void(0)" onclick="addnewtext('addcc','newcard','Add New');" style="font-size:10px"> New</a>--></div>
          </div>
          <div class="Column">
            <label>Bank</label>
            <div id="addbank"> <?php echo $banks; ?> <!--<a href="javascript:void(0)" onclick="addnewtext('addbank','newbank','Add New');" style="font-size:10px"> New</a>--> </div>
          </div>
          <div class="Column">
            <label>CC #</label>
            <input type="text" name="ccnumber" class="text" id="ccnumber" onkeydown="javascript:if(event.keyCode==13) {processamount(); return false;}"/>
          </div>
          <div class="Row">
            <div class="Column">
              <label>&nbsp;</label>
              <span class="buttons">
              <button type="button" onclick="processamount();" title="CTRL+S"> <img src="images/tick.png" alt=""/> Pay Now </button>
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
            <input type="text" name="amount3" class="text" id="amount3" value="" onkeydown="javascript:if(event.keyCode==13) {processamount(); return false;}" onkeypress="return isNumberKey(event)"  onfocus="this.select()"/>
          </div>
          <div class="Column">
            <label>Currency</label>
            <div id="addnewfc"> <?php echo $currency; ?> <!--<a href="javascript:void(0)" onclick="addnewtext('addnewfc','newfc','Add New');" style="font-size:10px"> New</a>--> </div>
          </div>
          <div class="Column">
            <label>Rate</label>
            <input type="text" name="fcrate" class="text" id="fcrate" onkeydown="javascript:if(event.keyCode==13) {processamount(); return false;}" onkeypress="return isNumberKey(event)" onblur="calcfcprice(this.value);"/>
          </div>
          <div class="Column">
            <label>Charges</label>
            <input type="text" name="fccharges" class="text" id="fccharges" value="0" onkeydown="javascript:if(event.keyCode==13) {processamount(); return false;}" onkeypress="return isNumberKey(event)"/>
          </div>
          <div class="Row">
            <div class="Column">
              <label>&nbsp;</label>
              <span class="buttons">
              <button type="button" onclick="processamount();" title="CTRL+S"> <img src="images/tick.png" alt=""/> Pay Now </button>
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
            <input type="text" name="amount4" class="text" id="amount4" value="" onkeydown="javascript:if(event.keyCode==13) {processamount(); return false;}" onkeypress="return isNumberKey(event)"  onfocus="this.select()"/>
          </div>
          <div class="Column">
            <label>Cheque #</label>
            <input type="text" name="chequenumber" class="text" id="chequenumber" onkeydown="javascript:if(event.keyCode==13) {processamount(); return false;}"/>
          </div>
          <div class="Column">
            <label>Bank</label>
            <div id="chkbank"> <?php echo $chequebanks;?><!-- <a href="javascript:void(0)" onclick="addnewtext('chkbank','newbank','Add New');" style="font-size:10px"> New</a> --></div>
          </div>
          <div class="Column">
            <label>Date</label>
            <input type="text" name="chequedate" class="text" id="chequedate" onkeydown="javascript:if(event.keyCode==13) {processamount(); return false;}"/>
          </div>
          <div class="Row">
            <div class="Column">
              <label>&nbsp;</label>
              <span class="buttons">
              <button type="button" onclick="processamount();" title="CTRL+S"> <img src="images/tick.png" alt=""/> Pay Now </button>
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
    <input type="hidden" name="billid" value="<?php echo $billid; ?>" />
  </form>
</div>
<div id="rightpanel">
  <table class="price">
    <tr>
      <th>Total</th>
      <td id="aright"><?php echo numbers($cust_total);?></td>
    </tr>
    <tr>
    	<th>
        	Discount
        </th>
        <td id="aright"><?php echo numbers($cust_dis);?></td>
    </tr>
  
    
    <tr>
      <th>Paid</th>
      <td id="aright"><?php 
//if($_SESSION['SERVER_ACC_ONLINE']!=1){
//		echo "Balance Not Available";
//		}else{
       echo $Total_Paid;
	  
//		}
		

	  ?></td>
    </tr>
     <?php /*?> <tr>
      <th>Advance Paid</th>
      <td id="aright3"><?php echo numbers($totaladvancepaid);?></td>
    </tr><?php */?>
    <tr>
      <th>Remaining Balance</th>
      <td id="aright">
	 <?php 
//	 if($_SESSION['SERVER_ACC_ONLINE']==1){
	  		$rem=$cust_total-$Total_Paid-$cust_dis;
			echo numbers($rem);
	// }else{
	//	 echo "Remaining Balance Not Available";
	//	 }
		?>
      </td>
    </tr>
  </table>
   <div id="error"></div>
 </div>
<script language="javascript" type="text/javascript">
	document.getElementById('amount1').focus();
</script>
<?php
if(!isset($_SESSION['closingsession']) || $_SESSION['closingsession']=='' || $_SESSION['closingsession']==0)
{//changed $dbname_main to $dbname_detail on line 415 by ahsan 22/02/2012
	$closingquery	=	"SELECT pkclosingid from $dbname_detail.closinginfo where closingstatus ='i' AND countername='$countername' AND fkaddressbookid='$empid' AND fkstoreid = '$storeid' ";
	$closingarray	=	$AdminDAO->queryresult($closingquery);
	$closingsession	=	$closingarray[0][pkclosingid];
	if($closingsession=='' || $closingsession==0)
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