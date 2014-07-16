<?php

error_reporting(-1);

require_once "Mail.php";

global $closingid;

if($closingid=='')

{

	//print"i am in if";

	$closingid	=	$_REQUEST['id'];

	print"<h2>Closing ID: $closingid</h2>";

	

}

session_start();

$_SESSION['param']='mailclosing';

$date	=	date('d-m-Y',time());

$from = "Kohsar SHOP System <kohsar@esajee.com>";

//$to = "hesajee@gmail.com,m_esajee@hotmail.com,esajeeco@gmail.com,jafer@esajeesolutions.com,yasir@esajeesolutions.com";

$to = "uuaqarahmed@gmail.com";

$subject = "Esajee Kohsar Closing on $date Closing ID $closingid";

if(include("../iPOS/includes/security/adminsecurity2.php"))

{

	include_once("../iPOS/saledetail.php");

}

else

{

	include("../iPOS/includes/security/adminsecurity2.php?param=mailclosing");

}



global $AdminDAO;

$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");

$defaultcurrency = $currency[0]['currencyname'];



if($closingid!='')

{

	$closingsession	=	$closingid;

}

//echo $closingsession.'hello pakistan this is closingid'; exit;

//genBarCode($closingsession,'closing.png');

if($countername!='' && $_REQUEST['ids']=='' )

{

	$counterinfo	=	"countername='$countername' AND";

}

$sql="SELECT * from $dbname_main.closinginfo where $counterinfo pkclosingid='$closingsession'";

//***********************sql for record set**************************



$closingarray	=	$AdminDAO->queryresult($sql);



// added by Yasir 26-07-11

$total_payments	=	$closingarray[0]['cashsale']+$closingarray[0]['creditcardsale']+$closingarray[0]['chequesale']+$closingarray[0]['foreigncurrencysale'];

//



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

			storephonenumber,

			storeaddress 

		from 

			store 

		where 

		pkstoreid='$fkstoreid'";

$storearray=	$AdminDAO->queryresult($sql);

$storenameadd=	$storearray[0]['storeaddress'].', '.$storearray[0]['storephonenumber'];



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

			$dbname_main.closingsales

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

$lastclosingidres	=	$AdminDAO->getrows("$dbname_main.closinginfo","pkclosingid","countername='$countername' AND pkclosingid<'$closingsession' AND closingstatus='a' ORDER BY pkclosingid DESC limit 0,1");

$lastclosingid		=	$lastclosingidres[0][pkclosingid];

$cashplusfc		=	$closingarray[0]['cashsale']+$closingarray[0]['cashcollect']+$closingarray[0]['foreigncurrencysale']+$closingarray[0]['fccollect']+$closingarray[0]['openingbalance'];

$totalsale		=	$closingarray[0]['totalsale'];

//displaying bills with discount processed during the closing session

$discounts	=	$AdminDAO->getrows("$dbname_main.sale","pksaleid,round(totalamount,2) totalamount,round(globaldiscount,2) as discount,from_unixtime(updatetime,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND status=1 AND globaldiscount>0");

for($disk=0;$disk<sizeof($discounts);$disk++)

{

	$tdisk	+=	$discounts[$disk]['discount'];

}



// added by Yasir 26-07-11

$cashdiff	=	0;

if ($total_payments >  ( $closingarray[0]['totalsale'] - $tdisk ) )

{

  // to get the sales withe payment more than total amount added by Yasir 26-07-11

  

  $sql_amountpayments	=	"SELECT pksaleid, globaldiscount, cash, cc, fc, cheque, totalamount										

						       FROM $dbname_main.sale

							  WHERE totalamount < cash+cc+fc+cheque+globaldiscount

								AND fkcustomerid = '0'

								AND fkclosingid = '$closingsession'";

  

  $amountpaymentsarray	=	 $AdminDAO->queryresult($sql_amountpayments); 

  

  

  for($diff=0;$diff<sizeof($amountpaymentsarray);$diff++)

  {

	  $cashdiff	+=	($amountpaymentsarray[$diff]['cash']+$amountpaymentsarray[$diff]['cc']+$amountpaymentsarray[$diff]['fc']+$amountpaymentsarray[$diff]['cheque']) - ($amountpaymentsarray[$diff]['totalamount'] - $amountpaymentsarray[$diff]['globaldiscount']) ;

  }    

}

//



// cancel sales added by Yasir 12-09-11

$sql_cancelsales	=	"SELECT pksaleid, from_unixtime(datetime,'%H:%i:%s') as dtime,(SELECT SUM(quantity*saleprice) FROM $dbname_main.saledetail WHERE fksaleid = pksaleid) as totalamount,

							(SELECT COUNT(pksaledetailid) FROM $dbname_main.saledetail WHERE fksaleid = pksaleid) as itemnum			

						       FROM $dbname_main.sale

							  WHERE status = '2'								

								AND fkclosingid = '$closingsession'";

  

$cancelsalesarray	=	 $AdminDAO->queryresult($sql_cancelsales);

//

//print_r($cancelsalesarray);

?>





<?php

$myvar1	='';

if($closingarray[0]['payout'])

{

	$myvar1	=	numbers($closingarray[0]['payout']);

}

$netsales	=	$closingarray[0]['totalsale']-$tdisk; 



$body	=	"



<link rel='stylesheet' type='text/css' href='http://210.2.171.12/esajeepos/includes/css/style.css' />

<div align='left'>

<div > <img src='http://210.2.171.12/esajeepos/images/esajeelogo.jpg' width='150' height='50'><br />

   <b>Think globally shop locally</b> <br />

  ". $storenameadd."</span> </div>

<div > Date: ". $closingdate ." </div>

<div > Counter: ". $closingarray[0]['countername']." </div>

<div > Closing #: ". $closingarray[0]['closingnumber']." (".$closingarray[0]['pkclosingid'].") </div>

<div > Cashier: ". $cashiername ." </div>

<table width='300' style='font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;margin-top:10px;' class='simple'>

  <tr>

  	<th align='left'>Total Sales</th>

    <th align='right'>". numbers($closingarray[0]['totalsale'])."</th>

  </tr>

  <tr>

  	<th align='left'>Total Discount</th>

    <th align='right'>". numbers($tdisk)."</th>

  </tr>

  <tr>

    <th align='left'>Net Sales</th>

    <th align='right'>". numbers($netsales)."</th>

  </tr>

  <tr>

    <th align='left'>Cash Sales</th>

    <th align='right'>". numbers($closingarray[0]['cashsale']-$cashdiff)."</th>

  </tr>

  <tr>

    <th align='left'>Collections</th>

    <th align='right'>". numbers($closingarray[0]['cashcollect'])."</th>

  </tr>

  <tr>

    <th align='left'>Balance</th>

    <th align='right'>". numbers($cashplusfc-$cashdiff-$closingarray[0]['payout'])."</th>

  </tr>

  <tr>

    <th align='left'>Total Payouts</th>

    <th align='right'>". $myvar1."</th>

  </tr>

  <tr>

    <th align='left'>Declared Amount</th>

    <th align='right'>". numbers($closingarray[0]['declaredamount'])."</th>

  </tr>

  <tr>

    <th align='left'>Cash Difference</th>

    <th align='right'>". numbers($closingarray[0]['cashdiffirence']+$cashdiff)."</th>

  </tr>

</table>

<table width='300' class='simple' border='1'>

  <tr>

    <td>Closing ID</td>

    <td align='right'>". $closingsession."</td>

  </tr>

  <tr>

    <td>Last Closing ID</td>

    <td align='right'>". $lastclosingid."</td>

  </tr>

  <tr>

    <td>No of Bills</td>

    <td align='right'>". $closingarray[0]['totalbills']."</td>

  </tr>

  <tr>

    <td>Total Items</td>

    <td align='right'>". $closingarray[0]['totalitems']."</td>

  </tr>

  <tr>



    <td>Opening Balance</td>

    <td align='right'>". numbers($closingarray[0]['openingbalance'])."</td>

  </tr>

  <tr>

  	<td>Total Sale (Cash+FC+CC+Credit)</td>

    <td align='right'>". numbers($totalsale)."</td>

  </tr>

  <tr>

  	<th colspan='2'>Cash</th>

  </tr>

  <tr>

    <td>Sales</td>

    <td align='right'>". numbers($closingarray[0]['cashsale']-$cashdiff)."</td>

  </tr>

  <tr>

    <td>Collections</td>

    <td align='right'>". numbers($closingarray[0]['cashcollect'])."</td>

  </tr>

  <tr>

    <td>Sub Total</td>

    <td align='right'>". numbers($closingarray[0]['cashsale']-$cashdiff+$closingarray[0]['cashcollect'])."</td>

  </tr>"

  ;

  if($closingarray[0]['foreigncurrencysale'] || $closingarray[0]['fccollect'])

  {

	$body	.=	"  <tr>

  	<th colspan='2'>Foreign Currency</th>

  </tr>

  <tr>

  	<td>Sales</td>

    <td align='right'>".numbers($closingarray[0]['foreigncurrencysale'])."</td>

  </tr>

  <tr>

  	<td>Collections</td>

    <td align='right'>".numbers($closingarray[0]['fccollect'])."</td>

  </tr>";

  }

  



   

  for($x=0;$x<sizeof($fcbycurrency);$x++)

  {

	  $currencyname		=	$fcbycurrency[$x]['currencyname'];

	  $currencysymbol	=	$fcbycurrency[$x]['currencysymbol'];

	  $currency			=	$currencyname." ".$currencysymbol;

	  $fcamount			=	$fcbycurrency[$x]['amount'];

	  

	  $body	.="   <tr>

					<td>". $currency."</td>

					<td align='right'>". numbers($fcamount)."</td>

				  </tr>";



  }

  if($fcamount)

  {

	  $body	.="  

   <tr>

    <td>Sub Total</td>

    <td align='right'>". numbers($closingarray[0]['foreigncurrencysale']+$closingarray[0]['fccollect'])."</td>

  </tr>";



  }

	  $body	.="  

  <tr>

  	<th align='left'>Cash + F.Currency + Opening Balance</th>

    <th align='right'>". numbers($cashplusfc-$cashdiff)."</th>

  </tr>";



  if($closingarray[0]['payout'])

  {

	  $body	.=" 	  

  <tr>

    <th align='left'>Total Payouts</th>

    <th align='right'>". numbers($closingarray[0]['payout'])."</th>

  </tr>";

  }

 

	  $body	.="   

  <tr>

  	<th align='left'>Balance</th>

    <th align='right'>". numbers($cashplusfc-$cashdiff-$closingarray[0]['payout'])."</th>

  </tr>

  <tr>

    <th align='left'>Declared Amount</th>

    <th align='right'>". numbers($closingarray[0]['declaredamount'])."</th>

  </tr>

  <tr>

    <th align='left'>Cash Difference</th>

    <th align='right'>". numbers($closingarray[0]['cashdiffirence']+$cashdiff)."</th>

  </tr>

  <tr>

  	<th colspan='2'>C.C Sales</th>

  </tr>

  <tr>

    <td><strong>Sales</strong></td>

    <td align='right'><strong>". numbers($closingarray[0]['creditcardsale'])."</strong></td>

  </tr>

  <tr>";

 

  

  for($x=0;$x<sizeof($ccbytype);$x++)

  {

	  $ccname	=	$ccbytype[$x]['typename'];

	  $ccamount	=	$ccbytype[$x]['amount'];

	  $body	.="

			  <tr>

				<td>". $ccname."</td>

				<td align='right'>". numbers($ccamount)."</td>

			  </tr> ";	  

  }

	  $body	.="  

  <tr>

  	<th colspan='2'>Cheque</th>

  </tr>

  <tr>

    <td><strong>Sales</strong></td>

    <td align='right'><strong>". numbers($closingarray[0]['chequesale'])."</strong></td>

  </tr>";



  for($x=0;$x<sizeof($chequebybank);$x++)

  {

	  $bankname	=	$chequebybank[$x]['bankname'];

	  $chamount	=	$chequebybank[$x]['amount'];

	  $body	.="  	  

  <tr>

    <td>". $bankname."</td>

    <td align='right'>". numbers($chamount)."</td>

  </tr>";

  }

  

  // displaying credit bills

	$query	=	"SELECT

				pksaleid,

				CONCAT(firstname,', ',lastname,' ',nic) as name,

				from_unixtime(updatetime,'%H:%i:%s') as dtime,

				round(

			  (SELECT	sum(saleprice * quantity)-(SELECT SUM(globaldiscount) FROM $dbname_main.sale sg WHERE s1.fkcustomerid=pkcustomerid AND sg.fkclosingid='$closingsession' AND s.pksaleid = sg.pksaleid) as subtotal FROM $dbname_main.saledetail,$dbname_main.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid AND s1.fkclosingid='$closingsession' AND s.pksaleid = s1.pksaleid)

			  -

			  (SELECT (IF(sum(amount)IS NULL,0,sum(amount))) as am FROM $dbname_main.cashpayment ,$dbname_main.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid AND s1.fkclosingid='$closingsession' AND s.pksaleid = s1.pksaleid AND paymenttype<>'c')

			  -

			  (SELECT (IF (sum(amount)IS NULL,0,sum(amount))) as am FROM $dbname_main.ccpayment ,$dbname_main.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid AND s1.fkclosingid='$closingsession' AND s.pksaleid = s1.pksaleid AND paymenttype<>'c')

			  -

			  (SELECT (IF(sum(amount*rate)IS NULL,0,sum(amount*rate))) as am FROM $dbname_main.fcpayment ,$dbname_main.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid AND s1.fkclosingid='$closingsession' AND s.pksaleid = s1.pksaleid AND paymenttype<>'c')

			  -

			  (SELECT (IF (sum(amount)IS NULL,0,sum(amount))) as am FROM $dbname_main.chequepayment ,$dbname_main.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid AND s1.fkclosingid='$closingsession' AND s.pksaleid = s1.pksaleid AND paymenttype<>'c')

			  ,2) as totalcredit

				

		FROM 

				$dbname_main.sale s,$dbname_main.customer LEFT JOIN $dbname_main.addressbook ON fkaddressbookid=pkaddressbookid

		WHERE

				s.fkclosingid	=	'$closingsession' AND

				s.fkcustomerid	=	pkcustomerid

	

	";

	//echo $query;

	$creditresult	=	$AdminDAO->queryresult($query);

	

	// calculate credit sale -- added by Yasir - 25-07-11

	$totalcredit = 0;

	for($i=0;$i<sizeof($creditresult);$i++)

	{

		$totalcredit+=$creditresult[$i][totalcredit];		

	}	

	//

	  $body	.="   

  <tr>

  	<th colspan='2'>Credit Sales</th>

  </tr>

  <tr>

    <td>Sale</td>

    <td align='right'>". numbers($totalcredit)."</td>

  </tr>

</table>"; 



//collections display added by riz 08-01-2010



if($cashcollect>'0' || $cccollect>'0' || $fccollect>'0' || $chequecollect>'0')

{

	  $body	.=" 	

<table width='300' class='simple'  border='1'>

  <tr>

    <th colspan='2'>Collections</th>

  </tr>";



	 if($cashcollect>0)

	 {

	  $body	.=" 		 

  <tr>

    <td align='left'>Cash Collection</td>

    <td align='right'>". numbers($cashcollect)."</td>

  </tr>";

	 }

	 if($cccollect>0)

	 {

	  $body	.=" 	

	<tr>

		<td align='left'>Credit card Collection</td>

		<td align='right'>". numbers($cccollect)."</td>

	</tr>";

	 }

	 if($fccollect>0)

	 {

	  $body	.=" 		

	<tr>

		<td align='left'>Foreign Currency Collection</td>

		<td align='right'>". numbers($fccollect)."</td>

	</tr>";

	 }

	 if($chequecollect>0)

	 {

	  $body	.=" 		

	<tr>

		<td align='left'>Cheque Collection</td>

		<td align='right'>". numbers($chequecollect)."</td>

	</tr>";

	}

	  $body	.="     

	<tr>

	  <td align='left'>Total</td>

	  <td align='right'>".  numbers($totalcollection)."</td>

  </tr>

</table>";



}//end of if collection

//displaying cancel sales during the closing session	

	// added by Yasir 12-09-11

	if(sizeof($cancelsalesarray)>0)

	{

	  $body	.="     		

<table width='300' class='simple'>

  <tr>

    <th colspan='5'>Canceled Sales</th>

  </tr>

  <tr>

    <td width='75'>ID</td>

    <td width='75'>Time</td>

    <td width='75'>Amount</td>

    <td width='75'>Items</td>

  </tr>";



	for($i=0;$i<sizeof($cancelsalesarray);$i++)

	{

	  $body	.="    	

  <tr>

    <td width='75'>". $cancelsalesarray[$i][pksaleid]."</td>

    <td width='75'>". $cancelsalesarray[$i][dtime]."</td>

    <td width='75' align='right'>". numbers($cancelsalesarray[$i][totalamount])."</td>    

    <td width='75'>". $cancelsalesarray[$i][itemnum]."</td>

  </tr> ";	



	}

	  $body	.="</table>";

}//end cancel sales



if(sizeof($discounts)>0)

{

	  $body	.="   	

<table width='300' class='simple'  border='1'>

  <tr>

    <th colspan='4'>Discounts</th>

  </tr>

  <tr>

    <td width='75'>ID</td>

    <td width='75'>Time</td>

    <td width='75'>Amount</td>

    <td width='75'>Discount</td>

  </tr>";



$totaldiscount	=	0;

for($i=0;$i<sizeof($discounts);$i++)

{

	$totaldiscount+=$discounts[$i][discount];

	$totalsaleamount+=$discounts[$i][totalamount];

	  $body	.="   	

  <tr>

    <td width='75'>". $discounts[$i][pksaleid]."</td>

    <td width='75'>". $discounts[$i][dtime]."</td>

    <td width='75' align='right'>". numbers($discounts[$i][totalamount])."</td>

    <td width='75' align='right'>". numbers($discounts[$i][discount])."</td>

  </tr>";



}

	  $body	.="   	

  <tr>

    <td colspan='2' align='right'>Total </td>

    <td align='right'>". numbers($totalsaleamount)."</td>

    <td align='right'>". numbers($totaldiscount)."</td>

  </tr>

</table>";

}//end discount section





$ccpayments	=	$AdminDAO->getrows("$dbname_main.sale s, $dbname_main.ccpayment cc LEFT JOIN bank ON (pkbankid=cc.fkbankid) LEFT JOIN cctype ON (cc.fkcctypeid=pkcctypeid)","pkccpaymentid, round( sum( amount ) , 2 ) amount, from_unixtime( s.updatetime, '%H:%i:%s' ) dtime, typename, ccno, cc.fksaleid, (SELECT totalamount FROM $dbname_main.sale WHERE cc.fksaleid=pksaleid) stotal, bankname bank","cc.fkclosingid='$closingsession' AND paymenttype<>'c' AND cc.amount<>0 AND cc.fksaleid=pksaleid AND s.status=1 GROUP BY cc.fksaleid,pkccpaymentid");



if(sizeof($ccpayments)>0)

{

	  $body	.="  

<table width='300' class='simple'  border='1'>

  <tr>

    <th colspan='7'><span id='description1'>Credit Card Transactions</span></th>

  </tr>

  <tr>

    <td width='40'>ID</td>

    <td width='40'>Time</td>

    <td width='40'>Last 4</td>

    <td width='40'>Sale Total</td>

    <td width='40'>CC Type</td>

    <td width='40'>Bank</td>

    <td width='40'>CC Paid</td>

  </tr>";	



$sumccs	=	0;

//print_r($ccpayments);

for($i=0;$i<sizeof($ccpayments);$i++)

{

	$sumccs+=$ccpayments[$i]['amount'];

	  $body	.="  	

  <tr>

    <td width='40'>". $ccpayments[$i]['fksaleid']."</td>

    <td width='40'>". $ccpayments[$i]['dtime']."</td>

    <td width='40' align='right'>". $ccpayments[$i]['ccno']."</td>

    <td width='40' align='right'>". numbers($ccpayments[$i]['stotal'])."</td>

    <td width='40'>". $ccpayments[$i]['typename']."</td>

    <td width='40'>". $ccpayments[$i]['bank']."</td>

    <td width='40' align='right'>". numbers($ccpayments[$i]['amount'])."</td>

  </tr>";

}

	  $body	.="  	

  <tr>

    <td colspan='6' align='right'>Total</td>

    <td align='right'>". numbers($sumccs)."</td>

  </tr>

</table>";



}//end ccpayments section

// calculating Foreign Currency

// added  AND paymenttype <> 'c' AND amount <> 0 by Yasir 25-07-11



$fcurrency	=	$AdminDAO->getrows("$dbname_main.fcpayment,currency","currencyname,currencysymbol,round(sum(amount),2) as fcamount, round(fcpayment.rate,2) as fcrate, charges, from_unixtime(paytime,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND paymenttype <> 'c' AND amount <> 0 AND pkcurrencyid=fkcurrencyid GROUP BY fkcurrencyid,fcrate");

if(sizeof($fcurrency)>0)

{

	  $body	.="  		

<table width='300' class='simple'  border='1'>

  <tr>

    <th colspan='5'>Foreign Currency</th>

  </tr>

  <tr>

    <td width='60'>Date & Time</td>

    <td width='60'>Currency</td>

    <td width='60'>Amount</td>

    <td width='60'>Rate</td>

    <td width='60'>Charges</td>

  </tr>";



$fcsum	=	0;

for($i=0;$i<sizeof($fcurrency);$i++)

{

	$fcsum+=($fcurrency[$i]['fcamount']*$fcurrency[$i]['fcrate']);

	$fcharges+=$fcurrency[$i]['charges'];

	  $body	.="  

  <tr>

    <td>". $fcurrency[$i][dtime]."</td>

    <td>". $fcurrency[$i][currencyname]." ".$fcurrency[$i][currencysymbol]."</td>

    <td align='right'>". numbers($fcurrency[$i][fcamount])."</td>

    <td align='right'>". numbers($fcurrency[$i][fcrate])."</td>

    <td align='right'>". numbers($fcurrency[$i][charges])."</td>

  </tr>";



}

	  $body	.="  

  <tr>

    <td colspan='4' align='right'>Total Charges</td>

    <td align='right'>". numbers($fcharges)."</td>

  </tr>

  <tr>

    <td colspan='4' align='right'>Total in ". $defaultcurrency."</td>

    <td align='right'>". numbers($fcsum)."</td>

  </tr>

  <tr>

    <td colspan='4' align='right'>Total</td>

    <td align='right'>". numbers($fcsum+$fcharges)."</td>

  </tr>

</table>";



}//end fcpayments section

// calculating returns

$returns	=	$AdminDAO->getrows("$dbname_main.sale s,$dbname_main.saledetail sd,$dbname_main.stock st,barcode bc","pksaleid,round(sd.saleprice*sd.quantity,2) as returnamount, from_unixtime(timestamp,'%H:%i:%s') as dtime,barcode,shortdescription,itemdescription","fksaleid=pksaleid AND sd.quantity<0 AND sd.fkclosingid='$closingsession' AND sd.fkstockid=st.pkstockid AND st.fkbarcodeid=bc.pkbarcodeid");

if(sizeof($returns)>0)

{

	  $body	.="  	

<table width='300' class='simple'  border='1'>

  <tr>

    <th colspan='5'>Returns</th>

  </tr>

  <tr>

    <td width='30'>ID</td>

    <td width='60'>Time</td>

    <td width='60'>Barcode</td>

    <td width='90'>Item</td>

    <td width='60'>Amount</td>

  </tr>";



$returnsum	=	0;

for($i=0;$i<sizeof($returns);$i++)

{

	$returnsum+=$returns[$i][returnamount];

	$item	=	$returns[$i]['shortdescription'];

	if($item=='')

	{

		$item	=	$returns[$i]['itemdescription'];

	}

	  $body	.=" 	

  <tr>

    <td>". $returns[$i][pksaleid]."</td>

    <td>". $returns[$i][dtime]."</td>

    <td>". $returns[$i][barcode]."</td>

    <td>". $item."</td>

    <td align='right'>". numbers($returns[$i][returnamount])."</td>

  </tr>";



}

	  $body	.=" 

  <tr>

    <td colspan='4' align='right'>Total</td>

    <td align='right'>". numbers($returnsum)."</td>

  </tr>

</table>";



}//end returns 

// calculating cheque bills

// added  AND cp.paymenttype <> 'c' AND cp.amount <> 0 by Yasir 25-07-11

$cheques	=	$AdminDAO->getrows("$dbname_main.sale s,$dbname_main.chequepayment cp,bank","pksaleid,chequeno,bankname,round(sum(amount),2) as chamount, from_unixtime(paytime,'%H:%i:%s') as dtime","cp.fksaleid=pksaleid AND cp.fkbankid=pkbankid AND cp.paymenttype <> 'c' AND cp.amount <> 0 AND cp.fkclosingid='$closingsession' GROUP by chequeno");

if(sizeof($cheques)>0)

{

	  $body	.=" 	

<table width='300' class='simple'  border='1'>

  <tr>

    <th colspan='4'>Cheques</th>

  </tr>

  <tr>

    <td width='60'>Time</td>

    <td width='60'>#</td>

    <td width='60'>Bank</td>

    <td width='60'>Amount</td>

  </tr>";



$chksum	=	0;

for($i=0;$i<sizeof($cheques);$i++)

{

	$chksum+=$cheques[$i][chamount];

	  $body	.=" 		

  <tr>

    <td>". $cheques[$i][dtime]."</td>

    <td>". $cheques[$i][chequeno]."</td>

    <td>". $cheques[$i][bankname]."</td>

    <td align='right'>". numbers($cheques[$i][chamount])."</td>

  </tr>";



}

	  $body	.=" 	

  <tr>

    <td colspan='3' align='right'>Total</td>

    <td align='right'>". numbers($chksum)."</td>

  </tr>

</table>";



}//end cheques

// calculating Payouts

$payouts	=	$AdminDAO->getrows("$dbname_main.accountpayment,$dbname_main.accounthead","pkaccountpaymentid,accounttitle,round(amount,2) as payamount, from_unixtime(paymentdate,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND fkaccountheadid=pkaccountheadid");

if(sizeof($payouts)>0)

{

	  $body	.=" 		

<table width='300' class='simple'  border='1'>

  <tr>

    <th colspan='4'>Payouts</th>

  </tr>

  <tr>

  	<td width='75'>ID</td>

    <td width='75'>Time</td>

    <td width='75'>Account</td>

    <td width='75'>Amount</td>

  </tr>";



$paysum	=	0;

for($i=0;$i<sizeof($payouts);$i++)

{

	$paysum+=$payouts[$i][payamount];

	  $body	.=" 		

  <tr>

    <td>". $payouts[$i][pkaccountpaymentid]."</td>

    <td>". $payouts[$i][dtime]."</td>

    <td>". $payouts[$i][accounttitle]."</td>

    <td align='right'>". numbers($payouts[$i][payamount])."</td>

  </tr>";

}

	  $body	.=" 

  <tr>

    <td colspan='3' align='right'>Total</td>

    <td align='right'>". numbers($paysum)."</td>

  </tr>

</table>";



}//end payouts

// calculating price changes

$pchanges	=	$AdminDAO->getrows("$dbname_main.sale,$dbname_main.saledetail sd,$dbname_main.stock st,barcode bc,discountreason","pksaledetailid,itemdescription,round(sd.saleprice,2) as sprice,round(sd.originalprice,2) as originalprice,from_unixtime(timestamp,'%H:%i:%s') as dtime,barcode,reasontitle","sd.fkclosingid='$closingsession' AND sd.originalprice<>sd.saleprice AND sd.fkstockid=st.pkstockid AND st.fkbarcodeid=bc.pkbarcodeid AND fkreasonid=pkreasonid GROUP by pksaledetailid");

if(sizeof($pchanges)>0)

{

	  $body	.=" 	

<table width='300' class='simple'  border='1'>

  <tr>

    <th colspan='5'>Price Changes</th>

  </tr>

  <tr>

  	<td width='50'>Time</td>

    <td width='50'>Item</td>

    <td width='50'>Original</td>

    <td width='50'>Changed</td>

    <td width='50'>Reason</td>

  </tr>";



$original	=	0;

$pchange	=	0;

for($i=0;$i<sizeof($pchanges);$i++)

{

	$pchange+=$pchanges[$i]['sprice'];

	$original+=$pchanges[$i]['originalprice'];

	$item	=	$pchanges[$i]['shortdescription'];

	if($item=='')

	{

		$item	=	$pchanges[$i]['itemdescription'];

	}

	  $body	.=" 	

  <tr>

    <td>". $pchanges[$i][dtime]."</td>

    <td>". $pchanges[$i][barcode]."<br />".$item."</td>

    <td align='right'>". numbers($pchanges[$i][originalprice])."</td>

    <td align='right'>". numbers($pchanges[$i][sprice])."</td>

    <td align='right'>". $pchanges[$i][reasontitle]."</td>

  </tr>";

}

	  $body	.="   

  <tr>

    <td colspan='2' align='right'>Total</td>

    <td align='right'>". numbers($original)."</td>

    <td align='right'>". numbers($pchange)."</td>

    <td>&nbsp;</td>

  </tr>

</table>";

}//end price changes



if(sizeof($creditresult)>0)

{

	  $body	.="  	

<table width='300' class='simple'  border='1'>

  <tr>

    <th colspan='4'>Credit Sales</th>

  </tr>

  <tr>

    <td width='75'>ID</td>

    <td width='75'>Time</td>

    <td width='75'>Customer</td>

    <td width='75'>Amount</td>

  </tr> ";



$totalcredit	=	0;

for($i=0;$i<sizeof($creditresult);$i++)

{

	$totalcredit+=$creditresult[$i][totalcredit];

	  $body	.=" 	

  <tr>

    <td>". $creditresult[$i][pksaleid]."</td>

    <td>". $creditresult[$i][dtime]."</td>

    <td>". $creditresult[$i][name]."</td>

    <td align='right'>". numbers($creditresult[$i][totalcredit])."</td>

  </tr> ";

}

	  $body	.=" 	

  <tr>

    <td colspan='3' align='right'>Total</td>

    <td align='right'>". numbers($totalcredit)."</td>

  </tr>

</table>";



}//end credit result

// displaying cash collections

  



// AND c.amount <> 0 added by Yasir 25-07-11

$cashcollectquery	=	"SELECT 

						c.pkcashpaymentid,from_unixtime(paytime,'%H:%i:%s') as dtime,SUM(c.amount) as amount,CONCAT(a.firstname,' ',a.lastname) as name,t.companyname cname

					FROM 

						$dbname_main.cashpayment c, $dbname_main.sale s, $dbname_main.customer t, $dbname_main.addressbook a

					WHERE 

						c.fksaleid = pksaleid

					AND s.status =1

					AND s.fkcustomerid = t.pkcustomerid

					AND t.fkaddressbookid = a.pkaddressbookid

					AND c.paymenttype = 'c'

					AND c.amount <> 0

					AND c.fkclosingid ='$closingsession'

					GROUP BY pkcustomerid



";

//echo $cashcollectquery;

$collectionresult	=	$AdminDAO->queryresult($cashcollectquery);

if(sizeof($collectionresult)>0)

{

	  $body	.=" 		

<table width='300' class='simple'  border='1'>

  <tr>

    <th colspan='4'>Cash Collections</th>

  </tr>

  <tr>

    <td width='75'>ID</td>

    <td width='75'>Time</td>

    <td width='75'>Customer</td>

    <td width='75'>Amount</td>

  </tr>";



$totalcashcollect	=	0;

for($i=0;$i<sizeof($collectionresult);$i++)

{

	$totalcashcollect+=$collectionresult[$i]['amount'];

	if($creditresult[$i]['cname']!='')

	{

		$cname	=	$collectionresult[$i]['cname'];

	}

	else

	{

		$cname	=	$collectionresult[$i]['name'];

	}

	  $body	.=" 		

  <tr>

    <td>". $collectionresult[$i][pkcashpaymentid]."</td>

    <td>". $collectionresult[$i][dtime]."</td>

    <td>". $cname."</td>

    <td align='right'>". numbers($collectionresult[$i][amount])."</td>

  </tr>";



}//end cash collections

	  $body	.=" 		

  <tr>

    <td colspan='3' align='right'>Total</td>

    <td align='right'>". numbers($totalcashcollect)."</td>

  </tr>

</table>";



}

// displaying credit card collections

// AND c.amount <> 0 added by Yasir 25-07-11

$cccollectquery	=	"SELECT 

						c.pkccpaymentid,from_unixtime(paytime,'%H:%i:%s') as dtime,SUM(c.amount) as amount,CONCAT(a.firstname,' ',a.lastname) as name,t.companyname cname

					FROM 

						$dbname_main.ccpayment c, $dbname_main.sale s, $dbname_main.customer t, $dbname_main.addressbook a

					WHERE 

						c.fksaleid = pksaleid

					AND s.status =1

					AND s.fkcustomerid = t.pkcustomerid

					AND t.fkaddressbookid = a.pkaddressbookid

					AND c.paymenttype = 'c'

					AND c.amount <> 0

					AND c.fkclosingid ='$closingsession'

					GROUP BY pkcustomerid



";

$collectionresult	=	$AdminDAO->queryresult($cccollectquery);

if(sizeof($collectionresult)>0)

{

	  $body	.=" 		

<table width='300' class='simple'  border='1'>

  <tr>

    <th colspan='4'>Credit Card Collections</th>

  </tr>

  <tr>

    <td width='75'>ID</td>

    <td width='75'>Time</td>

    <td width='75'>Customer</td>

    <td width='75'>Amount</td>

  </tr>";



$totalcccollect	=	0;

for($i=0;$i<sizeof($collectionresult);$i++)

{

	$totalcccollect+=$collectionresult[$i]['amount'];

	if($creditresult[$i]['cname']!='')

	{

		$cname	=	$collectionresult[$i]['cname'];

	}

	else

	{

		$cname	=	$collectionresult[$i]['name'];

	}

	  $body	.=" 	

  <tr>

    <td>". $collectionresult[$i][pkccpaymentid]."</td>

    <td>". $collectionresult[$i][dtime]."</td>

    <td>". $cname."</td>

    <td align='right'>". numbers($collectionresult[$i][amount])."</td>

  </tr>";

}//end credit card collections

	  $body	.=" 	

  <tr>

    <td colspan='3' align='right'>Total</td>

    <td align='right'><?php echo numbers($totalcccollect);?></td>

  </tr>

</table>";

}

// displaying foreign currency collections

// AND c.amount <> 0 added by Yasir 25-07-11

$fccollectquery	=	"SELECT 

						c.pkfcpaymentid,from_unixtime(paytime,'%H:%i:%s') as dtime,SUM(c.amount*c.rate) as amount,CONCAT(a.firstname,' ',a.lastname) as name,t.companyname cname

					FROM 

						$dbname_main.fcpayment c, $dbname_main.sale s, $dbname_main.customer t, $dbname_main.addressbook a

					WHERE 

						c.fksaleid = pksaleid

					AND s.status =1

					AND s.fkcustomerid = t.pkcustomerid

					AND t.fkaddressbookid = a.pkaddressbookid

					AND c.paymenttype = 'c'

					AND c.amount <> 0

					AND c.fkclosingid ='$closingsession'

					GROUP BY pkcustomerid

";

$collectionresult	=	$AdminDAO->queryresult($fccollectquery);

if(sizeof($collectionresult)>0)

{

	  $body	.=" 	

<table width='300' class='simple'  border='1'>

  <tr>

    <th colspan='4'>Foreign Currency Collections</th>

  </tr>

  <tr>

    <td width='75'>ID</td>

    <td width='75'>Time</td>

    <td width='75'>Customer</td>

    <td width='75'>Amount</td>

  </tr>";



$totalfccollect	=	0;

for($i=0;$i<sizeof($collectionresult);$i++)

{

	$totalfccollect+=$collectionresult[$i]['amount'];

	if($creditresult[$i]['cname']!='')

	{

		$cname	=	$collectionresult[$i]['cname'];

	}

	else

	{

		$cname	=	$collectionresult[$i]['name'];

	}

	  $body	.=" 	

  <tr>

    <td>". $collectionresult[$i][pkfcpaymentid]."</td>

    <td>". $collectionresult[$i][dtime]."</td>

    <td>". $cname."</td>

    <td align='right'>". numbers($collectionresult[$i][amount])."</td>

  </tr>";



}//end foreign currency collections

	  $body	.=" 	

  <tr>

    <td colspan='3' align='right'>Total</td>

    <td align='right'>". numbers($totalfccollect)."</td>

  </tr>

</table>";



}

// displaying foreign currency collections

$chequecollectquery	=	"SELECT 

						c.pkchequepaymentid,from_unixtime(paytime,'%H:%i:%s') as dtime,SUM(c.amount) as amount,CONCAT(a.firstname,' ',a.lastname) as name,t.companyname cname

					FROM 

						$dbname_main.chequepayment c, $dbname_main.sale s, $dbname_main.customer t, $dbname_main.addressbook a

					WHERE 

						c.fksaleid = pksaleid

					AND s.status =1

					AND s.fkcustomerid = t.pkcustomerid

					AND t.fkaddressbookid = a.pkaddressbookid

					AND c.paymenttype = 'c'

					AND c.fkclosingid ='$closingsession'

					GROUP BY pkcustomerid



";

$collectionresult	=	$AdminDAO->queryresult($chequecollectquery);

if(sizeof($collectionresult)>0)

{

	  $body	.=" 	

<table width='300'  class='simple'  border='1'>

  <tr>

    <th colspan='4'>Cheque Collections</th>

  </tr>

  <tr>

    <td width='75'>ID</td>

    <td width='75'>Time</td>

    <td width='75'>Customer</td>

    <td width='75'>Amount</td>

  </tr>";	



$totalfccollect	=	0;

for($i=0;$i<sizeof($collectionresult);$i++)

{

	$totalchequecollect+=$collectionresult[$i]['amount'];

	if($creditresult[$i]['cname']!='')

	{

		$cname	=	$collectionresult[$i]['cname'];

	}

	else

	{

		$cname	=	$collectionresult[$i]['name'];

	}

	  $body	.=" 

  <tr>

    <td>". $collectionresult[$i][pkchequepaymentid]."</td>

    <td>". $collectionresult[$i][dtime]."</td>

    <td>". $cname."</td>

    <td align='right'>". numbers($collectionresult[$i][amount])."</td>

  </tr>";



}//end foreign currency collections

	  $body	.=" 

  <tr>

    <td colspan='3' align='right'>Total</td>

    <td align='right'>".numbers($totalchequecollect)."</td>

  </tr>

</table>";



}

	  $body	.=" <div align='center'>".date('Y-m-d h:i:s')."</div>";

//empty closing session variable for new session

if($_GET['closingid']!='')

{

	$_SESSION['closingsession']='';	

}



 // $body =	'jafer its for you';

 //$body =file_get_contents("http://203.223.163.218/esajeepos/closingmail.php?id=$closingid");



$host = "mail.esajeesolutions.com";

$username = "dha@esajee.com";

$password = "esajee1901";

$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

//,'Content-type'=>'text/html\r\n'

$headers = array ('From' => $from,

  'To' => $to,

  'Subject' => $subject,'Content-type'=>'text/html; charset=iso-8859-1\r\n');

$smtp = Mail::factory('smtp',

  array ('host' => $host,

    'auth' => true,

    'username' => $username,

    'password' => $password));



$mail = $smtp->send($to, $headers, $body);



if (PEAR::isError($mail)) {

  echo("<p>" . $mail->getMessage() . "</p>");

 } else {

  echo("<p>Message successfully sent!</p>");

 }

?>

<script language="javascript">

	window.close();

</script>