<?php

require_once("../includes/security/adminsecurity.php");

include_once("saledetail.php");

include_once("../includes/bc/barcode.php");

global $AdminDAO,$Component,$userSecurity;

$rights	 	=	$userSecurity->getRights(8);

//getting default currency

$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");

$defaultcurrency = $currency[0]['currencyname'];

$closingid	=	$_GET['id'];

if($closingid!='')

{

	$closingsession	=	$closingid;

}

genBarCode($closingsession,'closing.png');

$sql="SELECT * from $dbname_detail.closinginfo where countername='$countername' AND pkclosingid='$closingsession'";

//***********************sql for record set**************************



$closingarray	=	$AdminDAO->queryresult($sql);

$datetime		=	$closingarray[0]['closingdate'];

$closingdate	=	date("d-m-y h:i:s",$datetime);

$countername	=	$closingarray[0]['countername'];

$fkstoreid		=	$closingarray[0]['fkstoreid'];

$addressbookid	=	$closingarray[0]['fkaddressbookid'];

//collection information

$cashcollect			=	$closingarray[0]['cashcollect'];

$cccollect				=	$closingarray[0]['cccollect'];

$fccollect				=	$closingarray[0]['fccollect'];

$chequecollect			=	$closingarray[0]['chequecollect'];

$totalcollection		=	$cashcollect+$fccollect+$cccollect+$chequecollect;

$totalcashcollection	=	$cashcollect+$fccollect;

$sql="SELECT 

			storename

		from 

			store 

		where 

		pkstoreid='$fkstoreid'";

$storearray=	$AdminDAO->queryresult($sql);

//$storenameadd=	$storearray[0]['storeaddress'].', '.$storearray[0]['storephonenumber'];

$storenameadd	=	$storearray[0]['storename'];

 $sql="SELECT 

			CONCAT(firstname,' ',lastname) as cashiername 

		from 

			addressbook 

		where 

		pkaddressbookid='$addressbookid'";

$cashierarray=	$AdminDAO->queryresult($sql);

$cashiername=	$cashierarray[0]['cashiername'];





$sql="SELECT 

			`fksaleid` 

		from 

			$dbname_detail.closingsales

		where 

			`fkclosingid`='$closingid'";



			

$closingsalesarray	=	$AdminDAO->queryresult($sql);

$cashsale	=	0;

$ccsale		=	0;

$fcsale		=	0;

$chequesale	=	0;

$ccbytype	=	ccbytype($closingsession);

$fcbycurrency	=	fcbycurrency($closingsession);

$chequebybank	=	chequebybank($closingsession);

//retrieving the last closing info

$lastclosingidres	=	$AdminDAO->getrows("$dbname_detail.closinginfo","pkclosingid","countername='$countername' AND pkclosingid<'$closingsession' AND closingstatus='a' ORDER BY pkclosingid DESC limit 0,1");

$lastclosingid		=	$lastclosingidres[0][pkclosingid];

$cashplusfc		=	$closingarray[0]['cashsale']+$closingarray[0]['cashcollect']+$closingarray[0]['foreigncurrencysale']+$closingarray[0]['fccollect']+$closingarray[0]['openingbalance'];

?>

<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />

<div style="width:3.0in; margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif;" align="left">

<div style="width:2.6in;padding:0px;font-size:17px;" align="center"> <!--<img src="images/esajeelogo.jpg" width="150" height="50"><br />-->

  <span style="font-size:11px;font-family:'Comic Sans MS', cursive;"> <!--<b>Think globally shop locally</b>--> <br />

  <?php echo $storenameadd;?></span> </div>

<div style="width:2.6in; padding:2px; margin-top:5px;font-size:12px;font-weight:bold;" align="center"> Date: <?php echo $closingdate; ?> </div>

<div style="width:2.6in; padding:2px;margin-top:5px;font-size:12px;font-weight:bold;" align="center"> Counter: <?php echo $closingarray[0]['countername'];?> </div>

<div style="width:2.6in; padding:2px; font-size:12px; margin-top:5px;font-weight:bold;" align="center"> Cashier: <?php echo $cashiername; ?> </div>

<table width="300" style="font-size:9px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <td>Closing IDD</td>

    <td align="right"><?php echo $closingsession;?></td>

  </tr>

  <tr>

    <td>Last Closing ID</td>

    <td align="right"><?php echo $lastclosingid;?></td>

  </tr>

  <tr>

    <td>No of Bills</td>

    <td align="right"><?php echo $closingarray[0]['totalbills'];?></td>

  </tr>

  <tr>

    <td>Total Items</td>

    <td align="right"><?php echo $closingarray[0]['totalitems'];?></td>

  </tr>

  <tr>

    <td>Opening Balance</td>

    <td align="right"><?php echo numbers($closingarray[0]['openingbalance']);?></td>

  </tr>

  <tr>

  	<th colspan="2">Cash</th>

  </tr>

  <tr>

    <td>Sales</td>

    <td align="right"><?php echo numbers($closingarray[0]['cashsale']);?></td>

  </tr>

  <tr>

    <td>Collections</td>

    <td align="right"><?php echo numbers($closingarray[0]['cashcollect']);?></td>

  </tr>

  <tr>

    <td>Sub Total</td>

    <td align="right"><?php echo numbers($closingarray[0]['cashsale']+$closingarray[0]['cashcollect']);?></td>

  </tr>

  <?php

  if($closingarray[0]['foreigncurrencysale'] || $closingarray[0]['fccollect'])

  {

	?>

  <tr>

  	<th colspan="2">Foreign Currency</th>

  </tr>

  <tr>

  	<td>Sales</td>

    <td align="right"><?php echo numbers($closingarray[0]['foreigncurrencysale']);?></td>

  </tr>

  <tr>

  	<td>Collections</td>

    <td align="right"><?php echo numbers($closingarray[0]['fccollect']);?></td>

  </tr>

  <?php

  }

  for($x=0;$x<sizeof($fcbycurrency);$x++)

  {

	  $currencyname		=	$fcbycurrency[$x]['currencyname'];

	  $currencysymbol	=	$fcbycurrency[$x]['currencysymbol'];

	  $currency			=	$currencyname." ".$currencysymbol;

	  $fcamount			=	$fcbycurrency[$x]['amount'];

  ?>

  <tr>

    <td><?php echo $currency;?></td>

    <td align="right"><?php echo numbers($fcamount);?></td>

  </tr>

  <?php

  }

  ?>

  <tr>

    <td>Sub Total</td>

    <td align="right"><?php echo numbers($closingarray[0]['foreigncurrencysale']+$closingarray[0]['fccollect']);?></td>

  </tr>

  <tr>

  	<th align="left">Cash + F.Currency + Opening Balance</th>

    <th align="right"><?php echo numbers($cashplusfc); ?></th>

  </tr>

  <?php

  if($closingarray[0]['payout'])

  {

  ?>

  <tr>

    <th align="left">Total Payouts</th>

    <th align="right"><?php echo numbers($closingarray[0]['payout']);?></th>

  </tr>

  <?php

  }

  if($closingarray[0]['globaldiscount'])

  {

  ?>

  <tr>

  	<th align="left">Total Discount</th>

    <th align="right"><?php echo numbers($closingarray[0]['globaldiscount']); ?></th>

  </tr>

  <?php

  }

  ?>

  <tr>

  	<th align="left">Balance</th>

    <th align="right"><?php echo numbers($cashplusfc-$closingarray[0]['payout']-$closingarray[0]['globaldiscount']); ?></th>

  </tr>

  <tr>

    <th align="left">Declared Amount</th>

    <th align="right"><?php echo numbers($closingarray[0]['declaredamount']);?></th>

  </tr>

  <tr>

    <th align="left">Cash Difference</th>

    <th align="right"><?php echo numbers($closingarray[0]['cashdiffirence']-$totalcashcollection);?></th>

  </tr>

  <tr>

  	<th colspan="2">Credit Card</th>

  </tr>

  <tr>

    <td><strong>Sales</strong></td>

    <td align="right"><strong><?php echo numbers($closingarray[0]['creditcardsale']);?></strong></td>

  </tr>

  <tr>

  <?php

  for($x=0;$x<sizeof($ccbytype);$x++)

  {

	  $ccname	=	$ccbytype[$x]['typename'];

	  $ccamount	=	$ccbytype[$x]['amount'];

  ?>

  <tr>

    <td><?php echo $ccname;?></td>

    <td align="right"><?php echo numbers($ccamount);?></td>

  </tr>

  <?php

  }

  ?>

  <tr>

  	<th colspan="2">Cheque</th>

  </tr>

  <tr>

    <td><strong>Sales</strong></td>

    <td align="right"><strong><?php echo numbers($closingarray[0]['chequesale']);?></strong></td>

  </tr>

  <?php

  for($x=0;$x<sizeof($chequebybank);$x++)

  {

	  $bankname	=	$chequebybank[$x]['bankname'];

	  $chamount	=	$chequebybank[$x]['amount'];

  ?>

  <tr>

    <td><?php echo $bankname;?></td>

    <td align="right"><?php echo numbers($chamount);?></td>

  </tr>

  <?php

  }

  ?>  

  <tr>

  	<th colspan="2">Credit</th>

  </tr>

  <tr>

    <td>Sale</td>

    <td align="right"><?php echo numbers($closingarray[0]['creditsale']);?></td>

  </tr>

</table>

<?php

//collections display added by riz 08-01-2010



if($cashcollect>'0' || $cccollect>'0' || $fccollect>'0' || $chequecollect>'0')

{

?>

<table width="300" style="font-size:9px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="2">Collections</th>

  </tr>

 <?php

 if($cashcollect>0)

 {

 ?>

  <tr>

    <td align="left">Cash Collection</td>

    <td align="right"><?php echo numbers($cashcollect);?></td>

  </tr>

	 <?php

	 }

	 if($cccollect>0)

	 {

	

	 ?>

 

	<tr>

		<td align="left">Credit card Collection</td>

		<td align="right"><?php echo numbers($cccollect);?></td>

	</tr>

	 <?php

	 }

	 if($fccollect>0)

	 {

	

	 ?>

	<tr>

		<td align="left">Foreign Currency Collection</td>

		<td align="right"><?php echo numbers($fccollect);?></td>

	</tr>

	<?php

	 }

	 if($chequecollect>0)

	 {

	

	 ?>

	<tr>

		<td align="left">Cheque Collection</td>

		<td align="right"><?php echo numbers($chequecollect);?></td>

	</tr>

	

	<?php

	}

	?>

	<tr>

	  <td align="left">Total</td>

	  <td align="right"><?php echo  numbers($totalcollection);?></td>

  </tr>

</table>



<?php

}//end of if collection

//displaying bills with discount processed during the closing session

$discounts	=	$AdminDAO->getrows("$dbname_detail.sale","pksaleid,round(globaldiscount,2) as discount,from_unixtime(updatetime,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND status=1 AND globaldiscount>0");

if(sizeof($discounts)>0)

{

?>

<table width="300" style="font-size:9px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="3">Discounts</th>

  </tr>

  <tr>

    <td width="100">ID</td>

    <td width="100">Time</td>

    <td width="100">Amount</td>

  </tr>

  <?php

$totaldiscount	=	0;

for($i=0;$i<sizeof($discounts);$i++)

{

	$totaldiscount+=$discounts[$i][discount];

?>

  <tr>

    <td width="100"><?php echo $discounts[$i][pksaleid];?></td>

    <td width="100"><?php echo $discounts[$i][dtime];?></td>

    <td width="100" align="right"><?php echo $discounts[$i][discount];?></td>

  </tr>

  <?php

}

?>

  <tr>

    <td colspan="2" align="right">Total Discount</td>

    <td align="right"><?php echo numbers($totaldiscount);?></td>

  </tr>

</table>

<?php 

}//end discount section

// calculating closing bills paid via Credit Card

$ccpayments	=	$AdminDAO->getrows("$dbname_detail.ccpayment c,$dbname_detail.sale s, $dbname_detail.saledetail sd,cctype","pksaleid,(select round(sum(amount),2) from $dbname_detail.ccpayment cp where cp.fksaleid=s.pksaleid AND pkcctypeid=fkcctypeid) as amount,from_unixtime(s.updatetime,'%H:%i:%s') as dtime,round(sum(sd.saleprice*sd.quantity),2) as stotal, typename,ccno","c.fkclosingid='$closingsession' AND c.fksaleid=s.pksaleid AND sd.fksaleid=s.pksaleid AND fkcctypeid=pkcctypeid AND s.status=1 GROUP BY pksaleid,fkcctypeid");

if(sizeof($ccpayments)>0)

{

?>

<table width="300" style="font-size:9px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="6">Credit Card Payments</th>

  </tr>

  <tr>

    <td width="50">ID</td>

    <td width="50">Time</td>

    <td width="50">Last 4</td>

    <td width="50">Total</td>

    <td width="50">CC Type</td>

    <td width="50">CC Paid</td>

  </tr>

  <?php 

$sumccs	=	0;

for($i=0;$i<sizeof($ccpayments);$i++)

{

	$sumccs+=$ccpayments[$i][amount];

?>

  <tr>

    <td width="60"><?php echo $ccpayments[$i][pksaleid];?></td>

    <td width="60"><?php echo $ccpayments[$i][dtime];?></td>

    <td width="60" align="right"><?php echo $ccpayments[$i][ccno];?></td>

    <td width="60" align="right"><?php echo $ccpayments[$i][stotal];?></td>

    <td width="60"><?php echo $ccpayments[$i][typename];?></td>

    <td width="60" align="right"><?php echo $ccpayments[$i][amount];?></td>

  </tr>

  <?php 

}

?>

  <tr>

    <td colspan="5" align="right">Total</td>

    <td align="right"><?php echo numbers($sumccs);?></td>

  </tr>

</table>

<?php 

}//end ccpayments section

// calculating Foreign Currency

$fcurrency	=	$AdminDAO->getrows("$dbname_detail.fcpayment,currency","currencyname,currencysymbol,round(sum(amount),2) as fcamount, round(fcpayment.rate,2) as fcrate, charges, from_unixtime(paytime,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND pkcurrencyid=fkcurrencyid GROUP BY fkcurrencyid,fcrate");

if(sizeof($fcurrency)>0)

{

?>

<table width="300" style="font-size:9px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="5">Foreign Currency</th>

  </tr>

  <tr>

    <td width="60">Date & Time</td>

    <td width="60">Currency</td>

    <td width="60">Amount</td>

    <td width="60">Rate</td>

    <td width="60">Charges</td>

  </tr>

  <?php 

$fcsum	=	0;

for($i=0;$i<sizeof($fcurrency);$i++)

{

	$fcsum+=($fcurrency[$i]['fcamount']*$fcurrency[$i]['fcrate']);

	$fcharges+=$fcurrency[$i]['charges'];

?>

  <tr>

    <td><?php echo $fcurrency[$i][dtime];?></td>

    <td><?php echo $fcurrency[$i][currencyname]." ".$fcurrency[$i][currencysymbol];?></td>

    <td align="right"><?php echo $fcurrency[$i][fcamount];?></td>

    <td align="right"><?php echo $fcurrency[$i][fcrate];?></td>

    <td align="right"><?php echo $fcurrency[$i][charges];?></td>

  </tr>

  <?php

}

?>

  <tr>

    <td colspan="4" align="right">Total Charges</td>

    <td align="right"><?php echo numbers($fcharges);?></td>

  </tr>

  <tr>

    <td colspan="4" align="right">Total in <?php echo $defaultcurrency;?></td>

    <td align="right"><?php echo numbers($fcsum);?></td>

  </tr>

  <tr>

    <td colspan="4" align="right">Total</td>

    <td align="right"><?php echo numbers($fcsum+$fcharges);?></td>

  </tr>

</table>

<?php 

}//end fcpayments section

// calculating returns

$returns	=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.saledetail sd,$dbname_detail.stock st,barcode bc","pksaleid,round(sd.saleprice*sd.quantity,2) as returnamount, from_unixtime(timestamp,'%H:%i:%s') as dtime,barcode","fksaleid=pksaleid AND sd.quantity<0 AND sd.fkclosingid='$closingsession' AND sd.fkstockid=st.pkstockid AND st.fkbarcodeid=bc.pkbarcodeid");

if(sizeof($returns)>0)

{

?>

<table width="300" style="font-size:9px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Returns</th>

  </tr>

  <tr>

    <td width="75">ID</td>

    <td width="75">Time</td>

    <td width="75">Barcode</td>

    <td width="75">Amount</td>

  </tr>

  <?php 

$returnsum	=	0;

for($i=0;$i<sizeof($returns);$i++)

{

	$returnsum+=$returns[$i][returnamount];

?>

  <tr>

    <td><?php echo $returns[$i][pksaleid];?></td>

    <td><?php echo $returns[$i][dtime];?></td>

    <td><?php echo $returns[$i][barcode];?></td>

    <td align="right"><?php echo $returns[$i][returnamount];?></td>

  </tr>

  <?php

}

?>

  <tr>

    <td colspan="3" align="right">Total</td>

    <td align="right"><?php echo numbers($returnsum);?></td>

  </tr>

</table>

<?php 

}//end returns 

// calculating cheque bills

$cheques	=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.chequepayment cp,bank","pksaleid,chequeno,bankname,round(sum(amount),2) as chamount, from_unixtime(paytime,'%H:%i:%s') as dtime","cp.fksaleid=pksaleid AND cp.fkbankid=pkbankid AND cp.fkclosingid='$closingsession' GROUP by pksaleid");

if(sizeof($cheques)>0)

{

?>

<table width="300" style="font-size:9px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="5">Cheques</th>

  </tr>

  <tr>

    <td width="60">ID</td>

    <td width="60">Time</td>

    <td width="60">#</td>

    <td width="60">Bank</td>

    <td width="60">Amount</td>

  </tr>

  <?php 

$chksum	=	0;

for($i=0;$i<sizeof($cheques);$i++)

{

	$chksum+=$cheques[$i][chamount];

?>

  <tr>

    <td><?php echo $cheques[$i][pksaleid];?></td>

    <td><?php echo $cheques[$i][dtime];?></td>

    <td><?php echo $cheques[$i][chequeno];?></td>

    <td><?php echo $cheques[$i][bankname];?></td>

    <td align="right"><?php echo $cheques[$i][chamount];?></td>

  </tr>

  <?php

}

?>

  <tr>

    <td colspan="4" align="right">Total</td>

    <td align="right"><?php echo numbers($chksum);?></td>

  </tr>

</table>

<?php 

}//end cheques

// calculating Payouts

$payouts	=	$AdminDAO->getrows("$dbname_detail.accountpayment,$dbname_detail.accounthead","pkaccountpaymentid,accounttitle,round(amount,2) as payamount, from_unixtime(paymentdate,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND fkaccountheadid=pkaccountheadid");

if(sizeof($payouts)>0)

{

?>

<table width="300" style="font-size:9px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Payouts</th>

  </tr>

  <tr>

  	<td width="75">ID</td>

    <td width="75">Time</td>

    <td width="75">Account</td>

    <td width="75">Amount</td>

  </tr>

  <?php 

$paysum	=	0;

for($i=0;$i<sizeof($payouts);$i++)

{

	$paysum+=$payouts[$i][payamount];

?>

  <tr>

    <td><?php echo $payouts[$i][pkaccountpaymentid];?></td>

    <td><?php echo $payouts[$i][dtime];?></td>

    <td><?php echo $payouts[$i][accounttitle];?></td>

    <td align="right"><?php echo $payouts[$i][payamount];?></td>

  </tr>

  <?php

}

?>

  <tr>

    <td colspan="3" align="right">Total</td>

    <td align="right"><?php echo numbers($paysum);?></td>

  </tr>

</table>

<?php 

}//end payouts

// calculating price changes

$pchanges	=	$AdminDAO->getrows("$dbname_detail.sale,$dbname_detail.saledetail sd,$dbname_detail.stock st,barcode bc,discountreason","pksaledetailid,itemdescription,round(sd.saleprice,2) as sprice,round(sd.originalprice,2) as originalprice,from_unixtime(timestamp,'%H:%i:%s') as dtime,barcode,reasontitle","sd.fkclosingid='$closingsession' AND sd.originalprice<>sd.saleprice AND sd.fkstockid=st.pkstockid AND st.fkbarcodeid=bc.pkbarcodeid AND fkreasonid=pkreasonid GROUP by pksaledetailid");

if(sizeof($pchanges)>0)

{

?>

<table width="300" style="font-size:9px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="6">Price Changes</th>

  </tr>

  <tr>

  	<td width="50">ID</td>

    <td width="50">Time</td>

    <td width="50">Item</td>

    <td width="50">Original</td>

    <td width="50">Changed</td>

    <td width="50">Reason</td>

  </tr>

  <?php

$original	=	0;

$pchange	=	0;

for($i=0;$i<sizeof($pchanges);$i++)

{

	$pchange+=$pchanges[$i][sprice];

	$original+=$pchanges[$i][originalprice];

?>

  <tr>

    <td><?php echo $pchanges[$i][pksaledetailid];?></td>

    <td><?php echo $pchanges[$i][dtime];?></td>

    <td><?php echo $pchanges[$i][barcode]."<br />".$pchanges[$i][itemdescription];?></td>

    <td align="right"><?php echo $pchanges[$i][originalprice];?></td>

    <td align="right"><?php echo $pchanges[$i][sprice];?></td>

    <td align="right"><?php echo $pchanges[$i][reasontitle];?></td>

  </tr>

  <?php

}

?>

  <tr>

    <td colspan="4" align="right">Total</td>

    <td align="right"><?php echo numbers($original);?></td>

    <td align="right"><?php echo numbers($pchange);?></td>

  </tr>

</table>

<?php 

}//end price changes

// displaying credit bills

$query	=	"SELECT

			pksaleid,

			CONCAT(firstname,', ',lastname,' ',nic) as name,

			from_unixtime(updatetime,'%H:%i:%s') as dtime,

			round(

		  (SELECT	sum(saleprice * quantity)-(SELECT SUM(globaldiscount) FROM $dbname_detail.sale sg WHERE s1.fkcustomerid=pkcustomerid AND sg.fkclosingid='$closingsession' AND s.pksaleid = sg.pksaleid) as subtotal FROM $dbname_detail.saledetail,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid AND s1.fkclosingid='$closingsession' AND s.pksaleid = s1.pksaleid)

		  -

		  (SELECT (IF(sum(amount)IS NULL,0,sum(amount))) as am FROM $dbname_detail.cashpayment ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid AND s1.fkclosingid='$closingsession' AND s.pksaleid = s1.pksaleid AND paymenttype<>'c')

		  -

		  (SELECT (IF (sum(amount)IS NULL,0,sum(amount))) as am FROM $dbname_detail.ccpayment ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid AND s1.fkclosingid='$closingsession' AND s.pksaleid = s1.pksaleid AND paymenttype<>'c')

		  -

		  (SELECT (IF(sum(amount*rate)IS NULL,0,sum(amount*rate))) as am FROM $dbname_detail.fcpayment ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid AND s1.fkclosingid='$closingsession' AND s.pksaleid = s1.pksaleid AND paymenttype<>'c')

		  -

		  (SELECT (IF (sum(amount)IS NULL,0,sum(amount))) as am FROM $dbname_detail.chequepayment ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid AND s1.fkclosingid='$closingsession' AND s.pksaleid = s1.pksaleid AND paymenttype<>'c')

		  ,2) as totalcredit

			

	FROM 

			$dbname_detail.sale s,$dbname_detail.customer LEFT JOIN $dbname_detail.addressbook ON fkaddressbookid=pkaddressbookid

	WHERE

			s.fkclosingid	=	'$closingsession' AND

			s.fkcustomerid	=	pkcustomerid



";

//echo $query;

$creditresult	=	$AdminDAO->queryresult($query);

if(sizeof($creditresult)>0)

{

?>

<table width="300" style="font-size:9px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Credit Sales</th>

  </tr>

  <tr>

    <td width="75">ID</td>

    <td width="75">Time</td>

    <td width="75">Customer</td>

    <td width="75">Amount</td>

  </tr>

  <?php

$totalcredit	=	0;

for($i=0;$i<sizeof($creditresult);$i++)

{

	$totalcredit+=$creditresult[$i][totalcredit];

?>

  <tr>

    <td><?php echo $creditresult[$i][pksaleid];?></td>

    <td><?php echo $creditresult[$i][dtime];?></td>

    <td><?php echo $creditresult[$i][name];?></td>

    <td align="right"><?php echo $creditresult[$i][totalcredit];?></td>

  </tr>

  <?php

}

?>

  <tr>

    <td colspan="3" align="right">Total</td>

    <td align="right"><?php echo numbers($totalcredit);?></td>

  </tr>

</table>

<?php

}//end credit result

?>

<div align="center">

  <?php 

echo date('Y-m-d h:i:s'); 

//empty closing session variable for new session

if($_GET['id']=='')

{

	$_SESSION['closingsession']='';	

}

?>

</div>

<div align="center"><img src="closing.png" /></div>

<script language="javascript">

	window.print();

	//window.close();

</script>