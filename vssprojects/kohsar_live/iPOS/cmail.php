<?php ob_start();
error_reporting(-1);

session_start();
$_SESSION = array(
    "storeid" => 3,
    "siteconfig" => 2,
    "countername" => -1,
    "siteconfig" => 2,
    "addressbookid" => 40,
    "name" => 'System Admin',
    "admin_section" => 'Admin logged in',
    "groupid" => 6,
    "groupname" => 'Administrator',);



//require_once "Mail.php";

global $closingid;

if($closingid=='')

{

	//print"i am in if";

	$closingid	=	$_REQUEST['id'];

	//print"<h2>Closing ID: $closingid</h2>";

	

}


$date	=	date('d-m-Y',time());

$from = "Kohsar SHOP System <kohsar@esajee.com>";

//$to = "hesajee@gmail.com,m_esajee@hotmail.com,esajeeco@gmail.com";

$to = "accounts@esajee.com";
//$to = "fahadbuttqau@gmail.com";

$subject = "Esajee Kohsar Counter Closing on $date Closing ID $closingid";

//hesajee@gmail.com,m_esajee@hotmail.com,

//$fh = fopen("http://localhost/esajeepos/closingmail.php?id=$closingid", 'r');

//echo $body = fread($fh); 

/*include("../esajeepos/includes/security/test.php");

include("../esajeepos/includes/security/adminsecurity3.php");*/

if(include("includes/security/adminsecurity.php"))

{

	include_once("saledetail.php");

}

else

{

	include("includes/security/adminsecurity.php?param=mailclosing");

}

global $AdminDAO;

$dbname_detail = "main_kohsar";

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

 $sql="SELECT * from $dbname_detail.closinginfo where pkclosingid='$closingsession'";

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

$lastclosingidres	=	$AdminDAO->getrows("$dbname_detail.closinginfo","pkclosingid,declaredamount","countername='$countername' AND pkclosingid < '$closingsession' AND closingstatus='a' ORDER BY pkclosingid DESC limit 0,1");

$lastclosingid		=	$lastclosingidres[0][pkclosingid];
$declareamt		=	$lastclosingidres[0][declaredamount];

$cashplusfc		=	$closingarray[0]['cashsale']+$closingarray[0]['cashcollect']+$closingarray[0]['foreigncurrencysale']+$closingarray[0]['fccollect']+$closingarray[0]['openingbalance'];

$totalsale		=	$closingarray[0]['totalsale'];

//displaying bills with discount processed during the closing session

$discounts	=	$AdminDAO->getrows("$dbname_detail.sale","pksaleid,round(totalamount,2) totalamount,round(globaldiscount,2) as discount,from_unixtime(updatetime,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND status=1 AND globaldiscount>0");

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

						       FROM $dbname_detail.sale

							  WHERE totalamount < cash+cc+fc+cheque+globaldiscount

								AND fkaccountid = '0'

								AND fkclosingid = '$closingsession'";

  

  $amountpaymentsarray	=	 $AdminDAO->queryresult($sql_amountpayments); 

  

  

  for($diff=0;$diff<sizeof($amountpaymentsarray);$diff++)

  {

	  $cashdiff	+=	($amountpaymentsarray[$diff]['cash']+$amountpaymentsarray[$diff]['cc']+$amountpaymentsarray[$diff]['fc']+$amountpaymentsarray[$diff]['cheque']) - ($amountpaymentsarray[$diff]['totalamount'] - $amountpaymentsarray[$diff]['globaldiscount']) ;

  }    

}

//



// cancel sales added by Yasir 12-09-11

$sql_cancelsales	=	"SELECT pksaleid, from_unixtime(datetime,'%H:%i:%s') as dtime,(SELECT SUM(quantity*saleprice) FROM $dbname_detail.saledetail WHERE fksaleid = pksaleid) as totalamount,

							(SELECT COUNT(pksaledetailid) FROM $dbname_detail.saledetail WHERE fksaleid = pksaleid) as itemnum			

						       FROM $dbname_detail.sale

							  WHERE status = '2'								

								AND fkclosingid = '$closingsession'";

  

$cancelsalesarray	=	 $AdminDAO->queryresult($sql_cancelsales);


$myvar1	='';

if($closingarray[0]['payout'])

{

	$myvar1	=	numbers($closingarray[0]['payout']);

}

$netsales	=	$closingarray[0]['totalsale']-$tdisk; 

 $query_advance = "SELECT SUM(amount) as advance_booking from  $dbname_detail.coupon_management 
	where  fkclosingid = '$closingsession'";
   $result_query_advance		=	$AdminDAO->queryresult($query_advance);


$advbk=$result_query_advance[0]['advance_booking'];

$query_used = "SELECT SUM(c.amount) as used_amount FROM $dbname_detail.sale s  left join $dbname_detail.coupon_management c on c.pkcouponid = s.fkcouponid
	
	where  c.status='2' and c.fkclosingid = '$closingsession' "; 
   $result_query_used		=	$AdminDAO->queryresult($query_used);




$body	=	"



<link rel='stylesheet' type='text/css' href='https://kohsar.esajee.com/includes/css/style.css' />

<div align='left'>

<div > <img src='https://kohsar.esajee.com/images/esajeelogo.jpg' width='150' height='50'><br />

   <b>Think globally shop locally</b> <br />

  ". $storenameadd."</span> </div>

<div > Date: ". $closingdate ." </div>

<div > Counter: ". $closingarray[0]['countername']." </div>

<div > Closing #: ". $closingarray[0]['closingnumber']." (".$closingarray[0]['pkclosingid'].") </div>

<div > Cashier: ". $cashiername ." </div>

<table width='300' style='font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;margin-top:10px;' class='simple'>

  <tr>

  	<th align='left'>Total Sales</th>

    <th align='right'>". numbers($closingarray[0]['totalsale']+$advbk)."</th>

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

    <th align='left'>Total Payouts</th>

    <th align='right'>". $myvar1."</th>

  </tr>

  <tr>

    <th align='left'>Declared Amount</th>

    <th align='right'>". numbers($closingarray[0]['declaredamount'])."</th>

  </tr>

  <tr>

    <th align='left'>Cash Difference</th>

    <th align='right'>". numbers($closingarray[0]['cashdiffirence']+$cashdiff-$advbk)."</th>

  </tr>
   <tr>

    <th align='left'>Advance Booking</th>

    <th align='right'>". numbers($advbk)."</th>

  </tr>
   <tr>

    <th align='left'>Coupon Used</th>

    <th align='right'>". numbers($result_query_used[0]['used_amount'])."</th>

  </tr> 


 <tr>

    <th align='left'>Cash at Counter (Balance)</th>

    <th align='right'>". round(($closingarray[0]['openingbalance']+$closingarray[0]['cashsale']-$myvar1),2)."</th>

  </tr>
<tr>

    <th align='left'>Cash Sales with Discount</th>

    <th align='right'>". round(($closingarray[0]['cashsale']+$tdisk),2)."</th>

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

    <td>Difference (last Closing Declare amount - opening)</td>

    <td align='right'>".(($declareamt)-$closingarray[0]['openingbalance'])."</td>

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

			  (SELECT sum(saleprice * quantity)-(SELECT SUM(globaldiscount) FROM $dbname_detail.sale sg WHERE s1.fkaccountid=pkcustomerid AND sg.fkclosingid='$closingsession' AND s.pksaleid = sg.pksaleid) as subtotal FROM $dbname_detail.saledetail,$dbname_detail.sale s1 WHERE s1.fkaccountid =  pkcustomerid AND pksaleid = fksaleid AND s1.fkclosingid='$closingsession' AND s.pksaleid = s1.pksaleid)

			  -

			  (SELECT (IF(sum(amount)IS NULL,0,sum(amount))) as am FROM $dbname_detail.payments ,$dbname_detail.sale s1 WHERE s1.fkaccountid =  pkcustomerid AND pksaleid = fksaleid AND s1.fkclosingid='$closingsession' AND s.pksaleid = s1.pksaleid AND paymenttype<>'c'),2) as totalcredit

		FROM 

				$dbname_detail.sale s,customer

		WHERE

				s.fkclosingid	=	'$closingsession' AND

				s.fkaccountid	=	pkcustomerid

	

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





$ccpayments	=	$AdminDAO->getrows("$dbname_detail.sale s, $dbname_detail.payments cc LEFT JOIN bank ON (pkbankid=cc.fkbankid) LEFT JOIN cctype ON (cc.fkcctypeid=pkcctypeid)","pkpaymentid, round( sum( amount ) , 2 ) amount, from_unixtime( s.updatetime, '%H:%i:%s' ) dtime, typename, ccno, cc.fksaleid, (SELECT totalamount FROM $dbname_detail.sale WHERE cc.fksaleid=pksaleid) stotal, bankname bank","cc.fkclosingid='$closingsession' AND paymenttype<>'c' AND cc.amount<>0 AND cc.fksaleid=pksaleid AND s.status=1 AND paymentmethod='cc' GROUP BY cc.fksaleid,pkpaymentid");



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



$fcurrency	=	$AdminDAO->getrows("$dbname_detail.payments,currency","currencyname,currencysymbol,round(sum(fcamount),2) as fcamount, round(payments.rate,2) as fcrate, charges, from_unixtime(paytime,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND pkcurrencyid=fkcurrencyid AND paymenttype <> 'c' AND amount <> 0 AND paymentmethod='fc' GROUP BY fkcurrencyid,fcrate");

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

$returns	=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.saledetail sd,$dbname_detail.stock st,barcode bc","pksaleid,round(sd.saleprice*sd.quantity,2) as returnamount, from_unixtime(timestamp,'%H:%i:%s') as dtime,barcode,shortdescription,itemdescription","fksaleid=pksaleid AND sd.quantity<0 AND sd.fkclosingid='$closingsession' AND sd.fkstockid=st.pkstockid AND st.fkbarcodeid=bc.pkbarcodeid");

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

	  $body	.="  <tr>

    <td colspan='4' align='right'>Total</td>

    <td align='right'>". numbers($returnsum)."</td>

  </tr>

</table>";



}//end returns 

// calculating cheque bills

// added  AND cp.paymenttype <> 'c' AND cp.amount <> 0 by Yasir 25-07-11

$cheques	=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.payments cp,bank","pksaleid,chequeno,bankname,round(sum(amount),2) as chamount, from_unixtime(paytime,'%H:%i:%s') as dtime","cp.fksaleid=pksaleid AND cp.fkbankid=pkbankid AND cp.paymenttype <> 'c' AND cp.amount <> 0 AND cp.fkclosingid='$closingsession' AND paymentmethod='ch' GROUP by chequeno");

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

$payouts	=	$AdminDAO->getrows("$dbname_detail.accountpayment,$dbname_detail.account","pkaccountpaymentid,title as accounttitle,round(amount,2) as payamount, from_unixtime(paymentdate,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND fkaccountid=id");

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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 $gop=0;
 
$sql_mobilesales	=	"SELECT fksaleid,from_unixtime(sd.timestamp,'%H:%i:%s') as dtime,sd.quantity,saleprice,itemdescription FROM $dbname_detail.saledetail sd left join $dbname_detail.stock on (pkstockid=fkstockid) left join main.barcode on  (fkbarcodeid=pkbarcodeid) 

							  WHERE fkclosingid = '$closingsession' and fkbarcodeid in (70115,85692,85691,12014,12037,12044,3902,3904,3905,3903,3906,3907,3910,3909,3915,3917,3918, 
3916,12425,3911,3913,3914,12762,12782,85275,56445,56446,11269,13223,13224,13225,13226,13328,13365,11324,11325,11326,13864,14141,14146,11404,11426,48946,56398,56517,56518,56519,56520,56521,85271,85693,85690,85272,85273,85274,85694,85687,85685,85688,70725,85684)";

$mobilesalesarray	=	 $AdminDAO->queryresult($sql_mobilesales); 
	$gop=sizeof($mobilesalesarray);
	



	if($gop>0)

	{

	

 $body	.=" <table width='411' style='font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;' class='simple'>

  <tr>

    <th colspan='6'>Mobile Card Transactions</th>

  </tr>

  <tr>
     
     <td width='70'>ID</td>
     <td width='75'>Time</td>
     <td width='100'>Item</td>
     <td width='75'>Quantity</td>
     <td width='75'>Rate</td>
     <td width='75'>Amount</td>
    

  </tr>";


	$paysum	=	0;

	for($i=0;$i<sizeof($mobilesalesarray);$i++)

	{

		$paysum1+=round($mobilesalesarray[$i][quantity]*$mobilesalesarray[$i][saleprice],2);


 $body	.="   <tr>
    <td width='70'>".$mobilesalesarray[$i][fksaleid]."</td>
    <td>".$mobilesalesarray[$i][dtime]."</td>
    <td >".$mobilesalesarray[$i][itemdescription]."</td>
    <td>". $mobilesalesarray[$i][quantity]."</td>
    <td>".$mobilesalesarray[$i][saleprice]."</td>
    <td>". round($mobilesalesarray[$i][quantity]*$mobilesalesarray[$i][saleprice],2)."</td>
 </tr>";
}	
  $body	.=" <tr>

    <td colspan='5' align='right'>Total</td>

    <td align='right'>".numbers($paysum1)."</td>

  </tr>

</table>";

	}//end Mobile Card Transactions

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// calculating price changes

$pchanges	=	$AdminDAO->getrows("$dbname_detail.sale,$dbname_detail.saledetail sd,$dbname_detail.stock st,barcode bc,discountreason","pksaledetailid,itemdescription,round(sd.saleprice,2) as sprice,round(sd.originalprice,2) as originalprice,from_unixtime(timestamp,'%H:%i:%s') as dtime,barcode,reasontitle","sd.fkclosingid='$closingsession' AND sd.originalprice<>sd.saleprice AND sd.fkstockid=st.pkstockid AND st.fkbarcodeid=bc.pkbarcodeid AND fkreasonid=pkreasonid GROUP by pksaledetailid");

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

							c.pkpaymentid,from_unixtime(paytime,'%H:%i:%s') as dtime,SUM(c.amount) as amount,CONCAT(t.firstname,' ',t.lastname) as name,t.companyname cname

						FROM 

							$dbname_detail.payments c, $dbname_detail.sale s, customer t

						WHERE 

							c.fksaleid = pksaleid

						AND s.status =1

						AND s.fkaccountid = t.pkcustomerid

						AND c.paymenttype = 'c'

						AND c.amount <> 0

						AND c.fkclosingid ='$closingsession'

						AND c.paymentmethod='c'

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

							c.pkpaymentid,from_unixtime(paytime,'%H:%i:%s') as dtime,SUM(c.amount) as amount,CONCAT(firstname,' ',lastname) as name,t.companyname cname

						FROM 

							$dbname_detail.payments c, $dbname_detail.sale s, customer t

						WHERE 

						c.fksaleid = pksaleid

						AND s.status =1

						AND s.fkaccountid = t.pkcustomerid

						AND c.paymenttype = 'c'

						AND c.amount <> 0

						AND c.fkclosingid ='$closingsession'

						AND c.paymentmethod='cc'

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

							c.pkpaymentid,from_unixtime(paytime,'%H:%i:%s') as dtime,SUM(c.amount) as amount,CONCAT(firstname,' ',lastname) as name,companyname cname

						FROM 

							$dbname_detail.payments c, $dbname_detail.sale s, customer

						WHERE 

							c.fksaleid = pksaleid

						AND s.status =1

						AND s.fkaccountid = pkcustomerid

					
						AND c.paymenttype = 'c'

						AND c.amount <> 0

						AND c.fkclosingid ='$closingsession'

						AND c.paymentmethod='fc'

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

							c.pkpaymentid,from_unixtime(paytime,'%H:%i:%s') as dtime,SUM(c.amount) as amount,CONCAT(firstname,' ',lastname) as name,companyname cname

						FROM 

							$dbname_detail.payments c, $dbname_detail.sale s, customer

						WHERE 

							c.fksaleid = pksaleid

						AND s.status =1

						AND s.fkaccountid = pkcustomerid

						AND c.paymenttype = 'c'

						AND c.amount <> 0

						AND c.fkclosingid ='$closingsession'

						AND c.paymentmethod='ch'

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

  </tr>";

 $body	.="</table>";



}
 $query = "SELECT
		c.pkcouponid  as pkcouponid,c.amount,case c.paymentmethod when 'c' then 'Cash' when 'cc' then 'Credit Card' when 'fc' then 'Foreign Currency' else 'Cheque' end paymentmethod0,c.reason
      FROM
	 $dbname_detail.coupon_management c 
	where  c.fkclosingid = '$closingsession' ";
   $result_query		=	$AdminDAO->queryresult($query);
$coup=sizeof($result_query);
if($coup>0){
$body	.="<table width='300' class='simple'  border='1'>
  <tr>
    <th colspan='4'>Advance Booking</th>
  </tr>
  <tr>
    <td width='70'>CouponID</td>
    <td width='82'>Payment Method</td>
    <td width='57'>Reason</td>
   <td width='71'>Amount</td>
   
  </tr>";
   

$total_amount	=	0;

for($i=0;$i<$coup;$i++)

{

	  $total_amount+=	$result_query[$i]['amount'];
	


 $body	.=" <tr>
    <td>".$result_query[$i]['pkcouponid']."</td>
    <td> ".$result_query[$i]['paymentmethod0']."</td>
    <td>".$result_query[$i]['reason']."</td>
 
    <td align='right'>".$result_query[$i]['amount']."</td>
  </tr>";
 

}


 $body	.=" <tr>
    <td colspan='3' align='right'>Total</td>
    <td align='right'>". numbers($total_amount)."</td>
  </tr>";
$body	.="</table>";
}

$query = "SELECT 
		c.pkcouponid as coupon_id,
        c.amount
      FROM
	
	$dbname_detail.sale s
	  left join $dbname_detail.coupon_management c on c.pkcouponid = s.fkcouponid
	
	where  c.status='2' and c.fkclosingid = '$closingsession' "; 
   $result_query		=	$AdminDAO->queryresult($query);
$total_amount	=	0;
$cp=sizeof($result_query);
if($cp){
$body	.="<table width='300' class='simple'  border='1'>
  <tr>
    <th colspan='2'>Coupon Used </th>
  </tr>
  <tr>
    <td width='176'>CouponID</td>
    <td width='112'>Amount</td>
   
  </tr>";


for($i=0;$i<$cp;$i++)

{

	  $total_amount+=	$result_query[$i]['amount'];
	


 $body	.=" <tr>
    <td>".$result_query[$i]['coupon_id']."</td>
    <td align='right'>".$result_query[$i]['amount']."</td>
  </tr>";
  

}


  $body	.="<tr>
    <td align='right'>Total</td>
    <td align='right'>".numbers($total_amount)."</td>
  </tr>";
$body	.="</table>";

}
$body	.='</div>';
	$body	.=" <div align='center'>".date('Y-m-d h:i:s')."</div>";

//empty closing session variable for new session


$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

//$to = "fahadbuttqau@gmail.com";
//$subject = "Test mail";
//$message = "Hello! This is a simple email message.";

$headers .= "From:" . $from;
$msent=mail($to,$subject,$body,$headers);
if($msent){
echo 1;
}else{
echo 0;	
	}
$body = addslashes($body);	
$time_to_send=date('Y-m-d h:i:s');
$created=date('Y-m-d');
$query= mysql_query("INSERT INTO $dbname_detail.closing_email (subject, body, `to`, `from`, time_to_send, login_id,created_at)
VALUES ('$subject','".$body."','$to','$from','$time_to_send','$addressbookid','$time_to_send')");
	
?> 