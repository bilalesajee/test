<?php
include("../includes/security/adminsecurity.php");
include_once("../export/exportdata.php");
global $AdminDAO;
error_reporting(1);
$paymentmethod 		= 	$_GET['paymentmethod'];
$sdate				=	strtotime($_GET['sdate'].'00:00:00'); 
$edate				=	strtotime($_GET['edate']."23:59:59");
$cashsale	=	0;
$ccsale		=	0;
$fcsale		=	0;
$chequesale	=	0;
if($sdate && $edate)
{
	$date = " paytime >= $sdate AND paytime <= $edate";
}
else if($sdate && !$edate)
{
	$date = " paytime >= $sdate";
}
else if(!$sdate && $edate )
{
	$date = " paytime <= $edate";
}
switch ($paymentmethod) {
    case "cc":
        $str= "Credit Card ";
        break;
    case "fc":
        $str= "Foreign Currency ";
        break;
    case "chq":
        $str= "Cheque ";
        break;
	case "c":
        $str= "Cash ";
        break;

}
?>

<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />

<h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Detailed  Report of <i><u><?php echo $str;?></u></i> Sales <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;from  <?php echo $_GET['sdate'];?> to  <?php echo $_GET['edate'];?></h3>
<br>
<table style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple" >

<?php 
if($paymentmethod=='cc')
{

 $ccbytype		=	$AdminDAO->getrows("$dbname_detail.payments LEFT JOIN cctype ON fkcctypeid=pkcctypeid "," SUM(amount) as amount, typename,pkcctypeid,fkclosingid,paytime,nameoncc,ccno "," $date AND paymentmethod='cc' GROUP by pkcctypeid ");
?>

  <tr>
    <th>
		<strong>
			Closing ID	
		</strong>	
	</th>
    <th>
		<strong>
			CC Type	
		</strong>	
	</th>
    <th align="right">
		<strong>
			Name On CC		
		</strong>	
	</th>
	<th width="82"  align="right">
		<strong>
			CC No		
		</strong>	
	</th>
    <th align="right">
		<strong>
			Transaction time		
		</strong>	
	</th>
    <th  align="right">
		<strong>
			Amount		
		</strong>	
	</th>
  </tr>
  <tr>
  <?php
  for($x=0;$x<sizeof($ccbytype);$x++)
  {
	 $fkclosingid=	$ccbytype[$x]['fkclosingid'];
	 $ccname	=	$ccbytype[$x]['typename'];
	 $ccamount	=	$ccbytype[$x]['amount'];
	 $paytime	=	$ccbytype[$x]['paytime'];
	 $nameoncc	=	$ccbytype[$x]['nameoncc'];
	 $ccno		=	$ccbytype[$x]['ccno'];
  ?>
  <tr id="datarow">
    <td><?php echo $fkclosingid;?></td>
    <td><?php echo $ccname;?></td>
	<td><?php echo $nameoncc;?></td>
	<td><?php echo $ccno;?></td>
	<td><?php echo date('Y-m-d H:i:s',$paytime);?></td>
	
    <td align="right"><?php echo number_format($ccamount,2);?></td>
  </tr>
   <?php
  	$totalamount=$totalamount+$ccamount;
  }
  ?>
  <tr>
  	<td colspan="5" align="right"><strong>Total Amount</strong></td>
	<td  align="right"><?php echo  number_format($totalamount,2);?></td>
  </tr>
  <?php
}//if
if($paymentmethod=='chq')
{
$chequebybank		=	$AdminDAO->getrows("$dbname_detail.payments LEFT JOIN bank ON fkbankid=pkbankid","amount, fkclosingid,bankname,pkbankid,chequeno,paytime,chequedate","  $date AND paymentmethod='ch'");
  ?>
  <tr>
    <th>
	<strong>
		Closing ID
	</strong>	
	</th>
    <th align="right">
		<strong>
			Bank Name	
		</strong>	
	</th>
	 <th  align="right">
		<strong>
			Cheque No	
		</strong>	
	</th>
    <th align="right">
		<strong>
			Date		
		</strong>	
	</th>
    <th  align="right">
		<strong>
			Transaction time		
		</strong>	
	</th>
	<th align="right">
		<strong>
			Amount		
		</strong>	
	</th>
  </tr>
  <tr>
  <?php
  for($x=0;$x<sizeof($chequebybank);$x++)
  {
	 $fkclosingid	=	$chequebybank[$x]['fkclosingid'];
	 $chequedate	=	$chequebybank[$x]['chequedate'];
	 $bankname		=	$chequebybank[$x]['bankname'];
	 $chequeno		=	$chequebybank[$x]['chequeno'];
	 $paytime		=	$chequebybank[$x]['paytime'];
	 $amount		=	$chequebybank[$x]['amount'];
  ?>
  <tr>
    <td><?php echo $fkclosingid;?></td>
	<td><?php echo $bankname;?></td>
	<td><?php echo $chequeno;?></td>
	<td><?php echo $chequedate;?></td>
	<td><?php echo date('Y-m-d H:i:s',$paytime);?></td>
	
    <td align="right"><?php echo number_format($amount,2);?></td>
  </tr>
  <?php
  	$totalamount=$totalamount+$amount;
  }
  ?>
  <tr>
  	<td colspan="5" align="right"><strong>Total Amount</strong></td>
	<td  align="right"><?php echo  number_format($totalamount,2);?></td>
  </tr>
  <?php
 }//if

if($paymentmethod=='c')
{

 $cashpayment		=	$AdminDAO->getrows("$dbname_detail.payments  "," SUM(amount) as amount,tendered,returned, 	paytime,fkclosingid"," $date AND paymentmethod='c' GROUP by fksaleid ");
?>

  <tr>
    <th  >
	<strong>
		Closing ID	</strong>	</th>
    <th  >
	<strong>
		Tendered</strong>	</th>
    <th align="right">
		<strong>
			Returned		
		</strong>	
	</th>
	
    <th align="right">
		<strong>
			Transaction time		
		</strong>	
	</th>
    <th  align="right">
		<strong>
			Amount		
		</strong>	
	</th>
  </tr>
  <tr>
  <?php
  for($x=0;$x<sizeof($cashpayment);$x++)
  {
	 $fkclosingid=	$cashpayment[$x]['fkclosingid'];
	 $tendered	=	$cashpayment[$x]['tendered'];
	 $returned	=	$cashpayment[$x]['returned'];
	 $paytime	=	$cashpayment[$x]['paytime'];
	
	 $amount		=	$cashpayment[$x]['amount'];
  ?>
  <tr>
    <td><?php echo $fkclosingid;?></td>
    <td><?php echo number_format($tendered,2);?></td>
	<td><?php echo number_format($returned,2);?></td>
	
	<td><?php echo date('Y-m-d H:i:s',$paytime);?></td>
	
    <td align="right"><?php echo number_format($amount,2);?></td>
  </tr>
  <?php
  	$totalamount=$totalamount+$amount;
  }
  ?>
  <tr>
  	<td colspan="4" align="right"><strong>Total Amount</strong></td>
	<td  align="right"><?php echo  number_format($totalamount,2);?></td>
  </tr>
  <?php
}//if
if($paymentmethod=='fc')
{
 ?>
 	 <tr>
    <th  >
	<strong>
		Closing ID	</strong>	</th>
    <th  >
	<strong>
		Currency Name</strong>	</th>
    <th align="right">
		<strong>
			Symbol		
		</strong>	
	</th>
	
    <th align="right">
		<strong>
			Rate		
		</strong>	
	</th>
	 <th align="right">
		<strong>
			Transaction Time		
		</strong>	
	</th>
    <th  align="right">
		<strong>
			Amount		
		</strong>	
	</th>
  </tr>
 <?php
 $fcbycurrency		=	$AdminDAO->getrows("$dbname_detail.payments LEFT JOIN currency ON fkcurrencyid=pkcurrencyid","fcamount, currencyname,currencysymbol,pkcurrencyid,$dbname_detail.payments.rate,fkclosingid,paytime"," $date AND paymentmethod='fc'");
  for($x=0;$x<sizeof($fcbycurrency);$x++)
  {
	  $currencyname		=	$fcbycurrency[$x]['currencyname'];
	  $currencysymbol	=	$fcbycurrency[$x]['currencysymbol'];
	  $rate				=	$fcbycurrency[$x]['rate'];
	  $fkclosingid		=	$fcbycurrency[$x]['fkclosingid'];
	  $currency			=	$currencyname." ".$currencysymbol;
	  $fcamount			=	$fcbycurrency[$x]['fcamount'];
	   $paytime			=	$fcbycurrency[$x]['paytime'];
  ?>
  <tr>
    <td><?php echo $fkclosingid;?></td>
	 <td><?php echo $currencyname;?></td>
	  <td><?php echo $currencysymbol;?></td>
	   <td><?php echo $rate;?></td>
	   <td><?php echo date('Y-m-d H:i:s',$paytime);?></td>
    <td align="right"><?php echo number_format($fcamount,2);?></td>
  </tr>
  <?php
  	$totalamount=$totalamount+$fcamount;
  }
  ?>
  <tr>
  	<td colspan="5" align="right"><strong>Total Amount</strong></td>
	<td align="right"><?php echo  number_format($totalamount,2);?></td>
  </tr>
  <?php

 }//if
  ?>
</table>
</form>
<?php echo $exporactions;?>