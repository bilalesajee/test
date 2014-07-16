<?php
include("includes/security/adminsecurity.php");
global $AdminDAO,$userSecurity;
 $id = $_GET['id'];
$style1 = "style='display:block'";
$style2 = "style='display:none'";
$style3 = "style='display:none'";
$style4 = "style='display:none'";

$qs	=	$_SESSION['qstring'];
	if($id =="-1")
{
	$sql			=	"SELECT max(pkcouponid) as pkcouponid  from $dbname_detail.coupon_management ";
   $result2			=	$AdminDAO->queryresult($sql);
   
   $couponid	=	$result2[0]['pkcouponid']+1;
 
}
else 
{
	$query = $AdminDAO->getrows("$dbname_detail.coupon_management",'*',"`pkcouponid`='$id'");
	//print_r($query);
	$couponid = $query[0]['pkcouponid'];
	 $paymentmethod		=	$query[0]['paymentmethod'];
   $creditcharges		=	$query[0]['charges'];

    $cctype		=	$query[0]['fkcctypeid'];
 $status		=	$query[0]['status'];

     $ccnumber		=	$query[0]['ccno'];
     $fcrate		=	$query[0]['rate'];
    $fccharges		=	$query[0]['fccharges'];
    $chequenumber		=	$query[0]['chequeno'];
    $currency				=	$query[0]['fkcurrencyid'];
    $chequedate				=	$query[0]['chequedate'];

    $reason				=	$query[0]['reason'];
if($paymentmethod =='c')
{

 $style1 = "style='display:block'";
 $amount		=	$query[0]['amount'];	

}
else if($paymentmethod =='cc')
{
	$style2 = "style='display:block'";
   $amount		=	$query[0]['amount'];
 $banks		=	$query[0]['fkbankid'];	
}
else if($paymentmethod =='fc')
{
	$style3 = "style='display:block'";
$amount		=	$query[0]['amount'];	
}
else if($paymentmethod =='ch')
{
$style4 = "style='display:block'";
$amount		=	$query[0]['amount'];	
$banks		=	$query[0]['fkbankid'];
}
}
/*******************************COMPONENTS*****************************************/
/*$banks_array	=	$AdminDAO->getrows('bank','*', ' 1');
$banks			=	$Component->makeComponent('d','banks',$banks_array,'pkbankid','bankname',1,$selected_banks,'','selectbox');*/
/*$cards_array	=	$AdminDAO->getrows('cctype','*', ' 1');
$ccs			=	$Component->makeComponent('d','card',$cards_array,'pkcctypeid','typename',1,$selected_cards);
$currency_array	=	$AdminDAO->getrows('currency','*', ' 1');
// Chnaged by Yasir -- 07-07-11. Previous was \"onChange=processcurrencyrate(); return false;\"
$d1			=	"<select name=\"currency\" id=\"currency\" style=\"width:80px;\" onChange=\"processcurrencyrate();\" return false;><option value=\"\">Select Currency</option>";
for($i=0;$i<sizeof($currency_array);$i++)
{
	$d2			.=	"<option value = \"".$currency_array[$i]['pkcurrencyid']."\">".$currency_array[$i]['currencyname']." ".$currency_array[$i]['currencysymbol']."</option>";
}
$currency		=	$d1.$d2."</select>";*/
//$currency		=	$Component->makeComponent('d','currency',$currency_array,'pkcurrencyid','currencysymbol',1,$selected_currencies,'onChange=processcurrencyrate(); return false;');
// $chequebanks	=	$Component->makeComponent('d','bank',$banks_array,'pkbankid','bankname',1,$selected_banks,'','selectbox');
?>
<script language="javascript">
function addform(id)
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
		
	
	
		/*else if(document.getElementById('bank').value == '')
		{
			alert('Please Select Bank');
			document.getElementById('bank').focus();
			return false;
		}*/
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
		
		
	
	
		/*else if(document.getElementById('bank').value == '')
		{
			alert('Please Select Bank');
			document.getElementById('bank').focus();
			return false;
		}*/
		else if(document.getElementById('chequedate').value == '')
		{
			alert('Please Enter Date');
			document.getElementById('chequedate').focus();
			return false;
		}
	
	
	
}
	
	//loading('System is Saving The Data....');
	options	=	{	
					url : 'insertcoupon_main.php',
					type: 'POST',
					success: function(id){	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=350,height=600,left=100,top=25';
		window.open('generate_coupon_bill.php?id='+id,'Invice',display); }
				}
	jQuery('#curform').ajaxSubmit(options);
	
	jQuery('#main-content').load('coupon_man.php');

/*	 if(id !=-1)
	{
	 		
			
			var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=350,height=600,left=100,top=25';
		window.open('generate_coupon_bill.php?id='+id,'Invice',display); 
		
	}*/
}
function response(text)
{
	if(text=='')
	{
		adminnotice(' data has been saved.',0,5000);
		jQuery('#maindiv').load('coupon_man.php?'+'<?php echo $qs?>');		
	}
	
}
function hideform()
{
	
	document.getElementById('curdiv').style.display='none';
}




function calcfcprice(val)
{
	
	var fcamountval,newfcamount,newfc;
	if(document.getElementById('newfc'))
	{
		if(document.getElementById('newfc').value!='')
		{
			//fcamountval	=	<?php//echo $remainingprice; ?>;
			fcamountval = document.getElementById('amount_rem2').value
			
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
function processcurrencyrate()
{ 
	options	=	{	
					url : 'getrate.php',
					type: 'POST',
					success: fcrate
				}
	jQuery('#curform').ajaxSubmit(options);
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
jQuery(function($)
{
	$("#chequedate").datepicker({dateFormat: 'yy-mm-dd'});
});
</script>


<div id="curdiv">
 <div id="barcode2">
<form name="curform" id="curform" onSubmit="addform(); return false;" style="width:920px;" >
<!--<form id="paymentform">-->

    <div class="Table">
      <div class="Row">
      <div class="Column">
       <label>CouponID</label>
         <input name="couponid" id="couponid" type="text" disabled="disabled" size="5" value="<?php echo $couponid; ?>" >
         
        
        </div>
     <div class="Column">
       
          <label>Pay by</label>
          <?php if($id=='-1'){
          ?>
          <select name="paymentmethod" id="paymentmethod" onchange="paymentdetails(this.value);">
            
            
          <option value="c" <?php if($paymentmethod == 'c') echo "selected=selected"; ?>>Cash</option>
        	<!--<option value="cc" <?php// if($paymentmethod == 'cc') echo "selected=selected"; ?> >Credit Card</option>
           
            <option value="fc" <?php// if($paymentmethod == 'fc') echo "selected=selected"; ?> >Foreign Currency</option>
            <option value="ch" <?php// if($paymentmethod == 'ch') echo "selected=selected"; ?> >Cheque</option>-->
       
          </select>
          <?php }else{ ?>
          <input name="paymentmethod" type="hidden" value="<?php echo $paymentmethod ?>" />
            <select name="paymentmethod" id="paymentmethod" disabled="disabled" onchange="paymentdetails(this.value);">
            
             
          <option value="c" <?php if($paymentmethod == 'c') echo "selected=selected"; ?>>Cash</option>
        	<!--<option value="cc" <?php// if($paymentmethod == 'cc') echo "selected=selected"; ?> >Credit Card</option>
           
            <option value="fc" <?php// if($paymentmethod == 'fc') echo "selected=selected"; ?> >Foreign Currency</option>
            <option value="ch" <?php// if($paymentmethod == 'ch') echo "selected=selected"; ?> >Cheque</option>-->
          </select>
          <?php  } ?>
        </div>
       
         <div class="Column">
            <!-- case 1 cash -->
            <label>Reason</label>
            <textarea type="text" name="reason" class="text" id="reason" value="<?php echo $reason; ?>"></textarea>
            <!--<input type="text" name="reason" class="text" id="reason" value="<?php //echo $reason; ?>" />-->
          </div>
        <div id="payment_details1" <?php echo $style1?>>
          <div class="Column">
            <!-- case 1 cash -->
            <label>Amount</label>
           
            <input type="text" name="amount1" class="text" id="amount1" value="<?php echo $amount; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" onkeypress="return isNumberKey(event)" onfocus="this.select()"/>
          </div>
         
          <div class="Row">
           <?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
			<div class="buttons"  style="padding-left:743px;">
         
            <button type="button"  class="positive"  onclick="addform('<?php echo $id;?>');" style="font-size:12px;">
                 <img src="images/disk.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}
				else{echo 'Pay Now';}?>
            </button>
             
            <button type="button" name="button2" id="button2" onclick="hidediv('curdiv');" title="Cancel" style="font-size:12px;">
            <img src="images/cross.png" alt=""/> 
           Cancel
        </button>
          </div>		
		  <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012
	   		 buttons('insertcoupon_main.php','curform','maindiv','coupon_man.php',$place=0,$formtype)
		 ?>   	
          
          </div>
        </div>
        <div id="payment_details2" <?php echo $style2?>>
          <!-- case 2 credit card -->
          <div class="Column">
            <label>Amount</label>
            <input type="text" name="amount2" class="text" id="amount2" value="<?php echo $amount; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" onkeypress="return isNumberKey(event)"  onfocus="this.select()"/>
          </div>
          <div class="Column">
            <label>Charges</label>
            <input type="text" name="creditcharges" class="text" id="creditcharges" value="<?php echo $creditcharges; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" onkeypress="return isNumberKey(event)"/>
          </div>
         <div class="Column">
            <label>Type</label>
            
            <div id="addcc"><input type="radio" name="card" id="card1" value="1" <?php if ($cctype == '1'){?> checked="checked" <?php } ?>>&nbsp;Al-Flah&nbsp;
<input type="radio" name="card" id="card" value="2" <?php if ($cctype == '2'){?> checked="checked" <?php } ?>>&nbsp;MCB<?php //echo $ccs; ?><!--<a href="javascript:void(0)" onclick="addnewtext('addcc','newcard','Add New');" style="font-size:10px"> New</a>--></div>
          </div>
          <div class="Column">
            <label>Bank</label>
          <div id="addbank"> <select name="bank2"   id="bank2" style="width:150px" >
               <option value="">Select Bank</option>
        <?php $q=" select * from main.bank";
        $result = mysql_query($q);
       while($rows = mysql_fetch_array($result)){
		$bb=$rows['bankname'];  
		$pkbankid=$rows['pkbankid'];  ?>
        
        <option <?php if($pkbankid == $banks) echo "selected=selected"; ?>value="<?php echo $pkbankid;?>"><?php echo $bb;?></option>
        <?php  } ?>
        </select> </div>
          </div>
          <div class="Column">
            <label>CC #</label>
            <input type="text" name="ccnumber" class="text" id="ccnumber" value="<?php echo $ccnumber ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"/>
          </div>
         <div class="Row">
           <?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
			<div class="buttons">
         
            <button type="button"  class="positive"  onclick="addform('<?php echo $id;?>');" style="font-size:12px;">
                 <img src="images/disk.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}
				else{echo 'Pay Now';}?>
            </button>
             
            <button type="button" name="button2" id="button2" onclick="hidediv('curdiv');" title="Cancel" style="font-size:12px;">
            <img src="images/cross.png" alt=""/> 
           Cancel
        </button>
          </div>		
		  <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012
	   		 buttons('insertcoupon_main.php','curform','maindiv','coupon_man.php',$place=0,$formtype)
		 ?>   	
          
          </div>
        </div>
        <!-- End Payment Details 2 -->
        <div id="payment_details3" <?php echo $style3?>>
          <!-- case 3 foreign currency -->
          <div class="Column">
            <label>Amount</label>
            <input type="text" name="amount3" class="text" id="amount3" value="<?php echo $amount?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" onkeypress="return isNumberKey(event)"  onfocus="this.select()"/>
          </div>
          <div class="Column">
            <label>Currency</label>
            <div id="addnewfc"> <select name="currency" value="<?php echo $currency;?>"  id="currency" style="width:150px" >
               <option selected="<?php echo $currency;?>">Select Currency</option>
        <?php $q=" select * from main.currency";
        $result = mysql_query($q);
       while($rows = mysql_fetch_array($result)){
		$bc=$rows['currencyname'];  
		$pkcurrencyid=$rows['pkcurrencyid'];  ?>
        
        <option <?php if($pkcurrencyid == $currency) echo "selected=selected"; ?>value="<?php echo $pkcurrencyid;?>"><?php echo $bc;?></option>
        <?php  } ?>
        </select> <!--<a href="javascript:void(0)" onclick="addnewtext('addnewfc','newfc','Add New');" style="font-size:10px"> New</a>--> </div>
          </div>
          <div class="Column">
            <label>Rate</label>
            <input type="text" name="fcrate" class="text" id="fcrate" value="<?php echo $fcrate?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" onkeypress="return isNumberKey(event)" onblur="calcfcprice(this.value);"/>
          </div>
          <div class="Column">
            <label>Charges</label>
            <input type="text" name="fccharges" class="text" id="fccharges" value="<?php echo $fccharges?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" onkeypress="return isNumberKey(event)"/>
          </div>
          <div class="Row">
           <?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
			<div class="buttons">
         
            <button type="button"  class="positive"  onclick="addform('<?php echo $id;?>');" style="font-size:12px;">
                 <img src="images/disk.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}
				else{echo 'Pay Now';}?>
            </button>
             
            <button type="button" name="button2" id="button2" onclick="hidediv('curdiv');" title="Cancel" style="font-size:12px;">
            <img src="images/cross.png" alt=""/> 
           Cancel
        </button>
          </div>		
		  <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012
	   		 buttons('insertcoupon_main.php','curform','maindiv','coupon_man.php',$place=0,$formtype)
		 ?>   	
          
          </div>
        </div>
        <!-- End Payment Details 3 -->
        <div id="payment_details4" <?php echo $style4?>>
          <!-- case 4 cheque -->
          <div class="Column">
            <label>Amount</label>
            <input type="text" name="amount4" class="text" id="amount4" value="<?php echo $amount?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" onkeypress="return isNumberKey(event)"  onfocus="this.select()"/>
          </div>
          <div class="Column">
            <label>Cheque #</label>
            <input type="text" name="chequenumber" class="text" id="chequenumber" value="<?php echo $chequenumber; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"/>
          </div>
          <div class="Column">
            <label>Bank</label>
            <div id="chkbank"> <select name="bank"   id="bank" style="width:150px" >
               <option value="">Select Bank</option>
        <?php $q=" select * from main.bank";
        $result = mysql_query($q);
       while($rows = mysql_fetch_array($result)){
		$bb=$rows['bankname'];  
		$pkbankid=$rows['pkbankid'];  ?>
        
        <option <?php if($pkbankid == $banks) echo "selected=selected"; ?>value="<?php echo $pkbankid;?>"><?php echo $bb;?></option>
        <?php  } ?>
        </select><!-- <a href="javascript:void(0)" onclick="addnewtext('chkbank','newbank','Add New');" style="font-size:10px"> New</a> --></div>
          </div>
          <div class="Column">
            <label>Date</label>
            <input type="text" name="chequedate" class="text" id="chequedate" value="<?php echo $chequedate; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"/>
          </div>
          <div class="Row">
           <?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
			<div class="buttons">
         
            <button type="button"  class="positive"  onclick="addform('<?php echo $id;?>');" style="font-size:12px;">
                 <img src="images/disk.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}
				else{echo 'Pay Now';}?>
            </button>
             
            <button type="button" name="button2" id="button2" onclick="hidediv('curdiv');" title="Cancel" style="font-size:12px;">
            <img src="images/cross.png" alt=""/> 
           Cancel
        </button>
          </div>		
		  <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012
	   		 buttons('insertcoupon_main.php','curform','maindiv','coupon_man.php',$place=0,$formtype)
		 ?>   	
          
          </div>
        </div>
        <!-- End Payment Details 4 -->
      </div>
    </div>
    <!-- . Table -->
    <input type="hidden" name="remainingprice" value="<?php echo $remainingprice; ?>" />
    <input type="hidden" name="billid" value="<?php echo $billid; ?>" />
    
    
     <input name="cust_bal" id="cust_bal" type="hidden" value="" />
      <input name="amount_rem2" id="amount_rem2" type="hidden" value="" />
       <input name="cust_discount" id="cust_discount" type="hidden" value="" />
 
  <input type="hidden" name="id" value = <?php echo $id?> />	
</form>
</div>
</div>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
<script language="javascript">
//document.curfrm.brand.focus();
loading('Loading Form...');
</script>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
<script language="javascript">

loading('Loading Form...');
</script>
<script language="javascript">
	focusfield('amount');
</script>
<?php }//end edit?>