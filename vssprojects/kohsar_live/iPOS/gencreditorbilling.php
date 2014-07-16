<?php
session_start();
include("includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(8);
$saleid	=	$_REQUEST['id'];
$customerid	=	$_GET['param'];

define(IMGPATH,'images/');
//***********************sql for record set**************************
if($saleid=='')
{
	print"<script>
	notice('Please select a bill.',0,5000);
	jQuery('#main-content').load('sale.php');
    </script>";
	exit;	
}//changed $dbname_main to $dbname_detail on line 26, 27, 28, 30, 33 by ahsan 22/02/2012
$query		=	"SELECT 
				pksaleid,
				round(globaldiscount,2) as discount,
				from_unixtime(updatetime,'%Y-%m-%d %h:%m:%s') as datetime, 
				countername, 
				CONCAT(firstname,' ', lastname) employeename,
				(SELECT round(SUM(saleprice*quantity),2) as Total FROM $dbname_detail.saledetail sdt,$dbname_detail.sale st2 WHERE st2.pksaleid = sdt.fksaleid AND st2.pksaleid = st.pksaleid) AS total, 							
				round((SELECT IF(sum(amount)IS NULL,0,sum(amount))FROM $dbname_detail.payments cp WHERE cp.fksaleid=st.pksaleid),2) as paid,
				(SELECT CONCAT(firstname ,' ', lastname)cn FROM $dbname_detail.sale st1, $dbname_detail.addressbook LEFT JOIN ($dbname_detail.account c) ON (pkaddressbookid= c.fkaddressbookid) WHERE st.pksaleid = st1.pksaleid AND id = fkaccountid) AS cn,
				
				round((SELECT (SUM(saleprice*quantity)) as Total FROM $dbname_detail.saledetail sdt,$dbname_detail.sale st2 WHERE st2.pksaleid = sdt.fksaleid AND st2.pksaleid = st.pksaleid)-((SELECT IF(sum(amount)IS NULL,0,sum(amount))FROM $dbname_detail.payments cp WHERE cp.fksaleid=st.pksaleid))-(globaldiscount),2) as credit
				
				FROM 
					$dbname_detail.sale st, 
					addressbook
				LEFT JOIN (employee) ON (pkaddressbookid = fkaddressbookid)
				WHERE st.fkstoreid ='$storeid'  
				AND fkuserid = employee.fkaddressbookid
				AND pksaleid='$saleid'
				HAVING credit>0
				";
/************* DUMMY SET ***************/
//$labels = array("ID","Bill #","Date","Counter","Cashier","Customer","Paid","Discount","Credit","Total");
/*******************************COMPONENTS*****************************************/
$banks_array	=	$AdminDAO->getrows('bank','*', ' 1');
$banks			=	$Component->makeComponent('d','banks',$banks_array,'pkbankid','bankname',1,$selected_banks,'','selectbox');
$cards_array	=	$AdminDAO->getrows('cctype','*', ' 1');
$ccs			=	$Component->makeComponent('d','card',$cards_array,'pkcctypeid','typename',1,$selected_cards);
$currency_array	=	$AdminDAO->getrows('currency','*', ' 1');
$d1			=	"<select name=\"currency\" id=\"currency\" style=\"width:80px;\" \" onChange=processcurrencyrate(); return false;\"><option value=\"\">Select Currency</option>";
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
	if(confirm("Are you sure to proccess this Amount!"))
	{
		loading('Please wait while the payment is processed ...');
		options	=	{	
						url : 'gencreditorbillingaction.php',
						type: 'POST',
						success: collectcresponse
					}
		jQuery('#creditobillingfrm').ajaxSubmit(options);
	}
	else
	{
		return false;
	}
}
function collectcresponse(text)
{
	if(text!='')
	{
		printcollectionbill(text);
	}
	jQuery('#main-content').load('customerbilling.php?id='+'<?php echo $customerid;?>');
}
function calcfcprice(val)
{
	/*var fcamountval,newfcamount,newfc;
	if(document.getElementById('newfc').value!='')
	{
		//fcamountval	=	<?php //echo $remainingprice; ?>;
		//newfcamount	=	fcamountval/val;
		newfc		=	Math.ceil(newfcamount);
		document.getElementById('fcamount').value	=	newfc;
	}
	else
	{
		return false;
	}*/
}
var divID=1;
function paymentdetails(value)
{
	//alert(value+' '+divID);
	//return false;
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
	// uncommented by Yasir - 06-07-11
	options	=	{	
					url : 'getrate.php',
					type: 'POST',
					success: fcrate
				}
	jQuery('#creditobillingfrm').ajaxSubmit(options); // paymentform replaced from creditobillingfrm by yasir 06-07-11  
	//
}
function fcrate(text)
{ 
	var val, rate,newrate;
	val		=	text.split(',');
	rate	=	val[0];
	newrate	=	val[1];
	//document.getElementById('error').style.display	=	'block';
	document.getElementById('fcrate').value=rate;
	document.getElementById('amountrecieved').value=newrate; // amountrecieved replaced from fcamount by Yasir -- 06-07-11	
	document.getElementById('fcrate').focus();
}
function validateamount(creditamountid,amount,id)
{
	var creditamount	=	document.getElementById(creditamountid).value;
	//alert(document.getElementById(amountrecieved[id]).value);
	if(amount>creditamount)
	{
		alert("Amount is greater than customer credit for this bill.");
		document.getElementById(id).value=creditamount;
		return false;
	}
}
</script>
<div id="barcode">
<form name="creditobillingfrm" id="creditobillingfrm">
<div id="barcode">
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
        </div>
        <div id="payment_details1" style="display:none;">
        </div>
       
        <div id="payment_details2" style="display:none;">
          <!-- case 2 credit card -->
         
          <div class="Column">
            <label>Charges</label>
            <input type="text" name="creditcharges" class="text" id="creditcharges" onkeydown="javascript:if(event.keyCode==13) { return false;}" onkeypress="return isNumberKey(event)"/>
          </div>
          <div class="Column">
            <label>Type</label>
            <div id="addcc"><?php echo $ccs; ?></div>
          </div>
          <div class="Column">
            <label>Bank</label>
            <div id="addbank"> <?php echo $banks; ?> </div>
          </div>
          <div class="Column">
            <label>CC #</label>
            <input type="text" name="ccnumber" class="text" id="ccnumber" onkeydown="javascript:if(event.keyCode==13) { return false;}"/>
          </div>
          <div class="Row">
            <!--<div class="Column">
              <label>&nbsp;</label>
              <button type="button" onclick="processamount();" title="CTRL+S"> <img src="images/tick.png" alt=""/> Pay Now </button>
              <button type="button" name="button2" id="button2" onclick="loadsection('main-content','sale.php');"> <img src="images/cross.png" alt=""/> Cancel </button>
            </div>-->
          </div>
        </div>
        <!-- End Payment Details 2 -->
        <div id="payment_details3" style="display:none;">
          <!-- case 3 foreign currency -->
          
          <div class="Column">
            <label>Currency</label>
            <div id="addnewfc"> <?php echo $currency; ?></div>
          </div>
          <div class="Column">
            <label>Rate</label>
            <input type="text" name="fcrate" class="text" id="fcrate" onkeydown="javascript:if(event.keyCode==13) { return false;}" onkeypress="return isNumberKey(event)" onblur="calcfcprice(this.value);"/>
          </div>
          <div class="Column">
            <label>Charges</label>
            <input type="text" name="fccharges" class="text" id="fccharges" onkeydown="javascript:if(event.keyCode==13) { return false;}" onkeypress="return isNumberKey(event)"/>
          </div>
         <!-- <div class="Row">
            <div class="Column">
              <label>&nbsp;</label>
              <button type="button" onclick="processamount();" title="CTRL+S"> <img src="images/tick.png" alt=""/> Pay Now </button>
              <button type="button" name="button2" id="button2" onclick="loadsection('main-content','sale.php');"> <img src="images/cross.png" alt=""/> Cancel </button>
            </div>
          </div>-->
        </div>
        <!-- End Payment Details 3 -->
        <div id="payment_details4" style="display:none;">
          <!-- case 4 cheque -->
         
          <div class="Column">
            <label>Cheque #</label>
            <input type="text" name="chequenumber" class="text" id="chequenumber" onkeydown="javascript:if(event.keyCode==13) { return false;}"/>
          </div>
          <div class="Column">
            <label>Bank</label>
            <div id="chkbank"> <?php echo $chequebanks;?>  </div>
          </div>
          <div class="Column">
            <label>Date</label>
            <input type="text" name="chequedate" class="text" id="chequedate" onkeydown="javascript:if(event.keyCode==13) { return false;}"/>
          </div>
          <!--<div class="Row">
            <div class="Column">
              <label>&nbsp;</label>
              <button type="button" onclick="processamount();" title="CTRL+S"> <img src="images/tick.png" alt=""/> Pay Now </button>
              <button type="button" name="button2" id="button2" onclick="loadsection('main-content','sale.php');"> <img src="images/cross.png" alt=""/> Cancel </button>
            </div>
          </div>-->
        </div>
        <!-- End Payment Details 4 -->
      </div>
    </div>
    <!-- . Table -->
<span class="buttons">
              <label>&nbsp;</label>
              <button type="button" onclick="processamount();" > <img src="images/tick.png" alt=""/> Pay Now </button>
              <button type="button" name="button2" id="button2" onclick="loadsection('main-content','sale.php');" title="BACKSPACE"> <img src="images/cross.png" alt=""/> Cancel </button>
</span>
<table class="pos" width="100%">
	<tr>
        <th width="7%">
            Bill#
        </th>
        <th width="7%">
            Date
        </th>
        <th width="11%">
            Counter
        </th>
        <th width="11%">
            Cashier
        </th>
       <?php /*?> <th>
            Customer
        </th><?php */?>
        <th width="7%">
            Paid
        </th>
        <th width="12%">
            Discount
        </th>
        <th width="8%">
            Total
        </th>
        <th width="9%">
            Credit
        </th>
        <th width="11%">Amount Recieved </th>
        <th width="17%">Tax Amount</th>
      </tr> 
   <?php
   	$billsarr	=	$AdminDAO->queryresult($query);
   for($a=0;$a<count($billsarr);$a++)
   {
   //	$fields = array("pksaleid","pksaleid","datetime","countername","employeename","cn","paid","discount","credit","total");
		$pksaleid		=		$billsarr[$a]['pksaleid'];
		$datetime		=		$billsarr[$a]['datetime'];
		$countername	=		$billsarr[$a]['countername'];
		$employeename	=		$billsarr[$a]['employeename'];
		//$cn				=		$billsarr[$a]['cn'];
		$paid			=		$billsarr[$a]['paid'];
		$discount		=		$billsarr[$a]['discount'];
		$credit			=		$billsarr[$a]['credit'];
		$total			=		$billsarr[$a]['total'];
   ?>
   	<tr>	 
        <td>
          <?php echo $pksaleid;?>
        </td>
        <td>
            <?php echo $datetime;?>
        </td>
        <td>
           <?php echo $countername;?>
        </td>
       <?php /*?> <td>
            <?php echo $cn;?>
        </td><?php */?>
        <td>
            <?php echo $employeename;?>
        </td>
        <td>
            <?php echo $paid;?>
        </td>
        <td>
            <?php echo $discount;?>
        </td>
        <td>
            <?php echo $total;?>
        </td>
        <!-- removed [] from saleid, amountrecieved, incometax by Yasir -- 06-07-11--> 
        <td>
           <?php echo $credit;?>
        <!-- Removed tab index from amount and tax by Yasir -- 07-07-11 -->
        <input type="hidden" id="creditamntid" name="creditamntid" value="<?php echo $credit;?>" />
        </td>
        <td><input type="hidden" name="saleid" value="<?php echo $pksaleid;?>" id="saleid" />
        <input type="text" id="amountrecieved" name="amountrecieved" value="0" size="10" maxlength="20" onfocus="this.select()" onkeypress="return isNumberKey(event)"/></td>
        <td><input type="text" id="incometax" name="incometax" value="0" size="10" maxlength="20" onfocus="this.select()" onkeypress="return isNumberKey(event)"/></td>
      </tr>
   <?php
   }
   ?>
  </table>
<span class="buttons">
              <label>&nbsp;</label>
              <button type="button" onclick="processamount();" > <img src="images/tick.png" alt=""/> Pay Now </button>
              <button type="button" name="button2" id="button2" onclick="loadsection('main-content','sale.php');" title="BACKSPACE"> <img src="images/cross.png" alt=""/> Cancel </button>
</span>
            <input type="hidden" name="customerid" value="<?php echo $customerid;?>" id="saleid" />
           
           <!--Added By Yasir - 06-07-11--> 
            <input type="hidden" name="remainingprice" value="<?php echo $credit;?>" id="remainingprice" />
            <!---->
  </form>   
<?php 
	//grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>
<?php
if($_SESSION['closingsession']=='')
{//changed $dbname_main to $dbname_detail on line 398 by ahsan 22/02/2012
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
<!--Added by Yasir - 08-07-11-->
<script language="javascript" type="text/javascript">
	document.creditobillingfrm.paymentmethod.focus();	
</script>