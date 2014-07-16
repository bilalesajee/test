<?php

include("includes/security/adminsecurity.php");

include_once("saledetail.php");

include_once("includes/bc/barcode.php");

global $AdminDAO;

$closingid	=	$_GET['id'];

$param		=	$_GET['param'];

if($param=='admin')

{

	$id			=	$_REQUEST['ids'];

	$id			=	trim($id,',');

	$ids		=	explode(',',$id);

	$arrcount	=	count($ids);

	$arrcount	=	$arrcount-1;

	$closingid			=	$ids[$arrcount];

}

if($closingid!='')

{

	$closingsession	=	$closingid;

}

genBarCode($closingsession,'closing.png');

if($countername!='' && $_REQUEST['ids']=='' )

{

	$counterinfo	=	"countername='$countername' AND";

}

	 $sql="SELECT * from $dbname_detail.closinginfo where $counterinfo pkclosingid='$closingsession'";//changed $dbname_main to $dbname_detail by ahsan 22/02/2012



//getting default currency

$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");

$defaultcurrency = $currency[0]['currencyname'];

//***********************sql for record set**************************

$closingarray	=	$AdminDAO->queryresult($sql);



// added by Yasir 26-07-11

$total_payments	=	$closingarray[0]['cashsale']+$closingarray[0]['creditcardsale']+$closingarray[0]['chequesale']+$closingarray[0]['foreigncurrencysale'];

//



$datetime		=	$closingarray[0]['closingdate'];

$closingdate	=	date("d-m-y h:i:s",$datetime);

$openingdate	=	date("d-m-y h:i:s",$closingarray[0]['openingdate']);

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

$storearray		=	$AdminDAO->queryresult($sql);

//$storenameadd	=	$storearray[0]['storeaddress'].', '.$storearray[0]['storephonenumber'];

$storenameadd	=	$storearray[0]['storename'];

 $sql="SELECT 

			CONCAT(firstname,' ',lastname) as cashiername 

		from 

			addressbook 

		where 

		pkaddressbookid='$addressbookid'";

$cashierarray=	$AdminDAO->queryresult($sql);

$cashiername=	$cashierarray[0]['cashiername'];



//changed $dbname_main to $dbname_detail on line 74 by ahsan 22/02/2012

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

//retrieving the last closing info //changed $dbname_main to $dbname_detail on line 88 by ahsan 22/02/2012

$lastclosingidres	=	$AdminDAO->getrows("$dbname_detail.closinginfo","pkclosingid,declaredamount","countername='$countername' AND pkclosingid<'$closingsession' AND closingstatus='a' ORDER BY pkclosingid DESC limit 0,1");

$lastclosingid		=	$lastclosingidres[0]['pkclosingid'];

$lastclosingbal		=	$lastclosingidres[0]['declaredamount'];

$cashplusfc		=	$closingarray[0]['cashsale']+$closingarray[0]['cashcollect']+$closingarray[0]['foreigncurrencysale']+$closingarray[0]['fccollect']+$closingarray[0]['openingbalance'];

$ptype	=	$_GET['ptype'];

//displaying bills with discount processed during the closing session //changed $dbname_main to $dbname_detail on line 94 by ahsan 22/02/2012

$discounts	=	$AdminDAO->getrows("$dbname_detail.sale","pksaleid,round(globaldiscount,2) as discount,round(totalamount,2) totalamount,from_unixtime(updatetime,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND status=1 AND globaldiscount>0");

for($disk=0;$disk<sizeof($discounts);$disk++)

{

	$tdisk	+=	$discounts[$disk]['discount'];

}

// added by Yasir 26-07-11

$cashdiff	=	0;

if ($total_payments >  ( $closingarray[0]['totalsale'] - $tdisk ) )

{

  // to get the sales withe payment more than total amount added by Yasir 26-07-11

//changed $dbname_main to $dbname_detail on line 106 by ahsan 22/02/2012  

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

// cancel sales added by Yasir 12-09-11 //changed $dbname_main to $dbname_detail on line 117, 118, 119 by ahsan 22/02/2012
//Print changes by fahad 6-6-2012
$sql_cancelsales	=	"SELECT pksaleid, from_unixtime(datetime,'%H:%i:%s') as dtime,(SELECT SUM(quantity*saleprice) FROM $dbname_detail.saledetail WHERE fksaleid = pksaleid) as totalamount,

							(SELECT COUNT(pksaledetailid) FROM $dbname_detail.saledetail WHERE fksaleid = pksaleid) as itemnum,
							
							(SELECT COUNT(pkbillid) FROM $dbname_detail.bill WHERE fksaleid = pksaleid) as printnum  			

						       FROM $dbname_detail.sale

							  WHERE status = '2'								

								AND fkclosingid = '$closingsession'";

$cancelsalesarray	=	 $AdminDAO->queryresult($sql_cancelsales); 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

<link rel="stylesheet" type="text/css" href="includes/css/style.css" />

<?php

if($ptype==1)

{

	?>

<div style="width:3.0in; margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif;" align="left">

<div style="width:2.6in;padding:0px;font-size:17px;" align="center"> <!--<img src="images/esajeelogo.jpg" width="150" height="50"><br />-->

  <span style="font-size:11px;font-family:'Comic Sans MS', cursive;"> <!--<b>Think globally shop locally</b>--> <br />

  <?php echo $storenameadd;?></span> </div>

<div style="width:2.6in; padding:2px; margin-top:5px;font-size:12px;font-weight:bold;" align="center"> Opening Date: <?php echo $openingdate; ?> </div>

<div style="width:2.6in; padding:2px; margin-top:5px;font-size:12px;font-weight:bold;" align="center"> Closing Date: <?php echo $closingdate; ?> </div>

<div style="width:2.6in; padding:2px;margin-top:5px;font-size:12px;font-weight:bold;" align="center"> Counter: <?php echo $closingarray[0]['countername'];?> </div>

<div style="width:2.6in; padding:2px;margin-top:5px;font-size:12px;font-weight:bold;" align="center"> Closing #: <?php echo $closingarray[0]['closingnumber']." (".$closingarray[0]['pkclosingid'].")";?> </div>

<div style="width:2.6in; padding:2px; font-size:12px; margin-top:5px;font-weight:bold;" align="center"> Cashier: <?php echo $cashiername; ?> </div>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;margin-top:10px;" class="simple">

  <tr>

  	<th align="left">Total Sales</th>

    <th align="right"><?php echo numbers($closingarray[0]['totalsale']);?></th>

  </tr>

  <tr>

  	<th align="left">Total Discount</th>

    <th align="right"><?php echo numbers($tdisk);?></th>

  </tr>

  <tr>

    <th align="left">Net Sales</th>

    <th align="right"><?php $netsales	=	$closingarray[0]['totalsale']-$tdisk; echo numbers($netsales);?></th>

  </tr>

  <tr>

    <th align="left">Cash Sales</th>

    <th align="right"><?php echo numbers($closingarray[0]['cashsale']-$cashdiff); // -$cashdiff added by Yasir 26-07-11  ?></th>

  </tr>

  <tr>

    <th align="left">Collections</th>

    <th align="right"><?php echo numbers($closingarray[0]['cashcollect']);?></th>

  </tr>

  <?php /*?><tr>

    <th align="left">Balance</th>

    <th align="right"><?php echo numbers($cashplusfc-$cashdiff-$closingarray[0]['payout']); // -$cashdiff added by Yasir 26-07-11 ?></th>

  </tr>
<?php */?>
  <tr>

    <th align="left">Total Payouts</th>

    <?php

	  if($closingarray[0]['payout'])

	  {

	  ?>

    <th align="right"><?php echo numbers($closingarray[0]['payout']);?></th>

    <?php

	  }

	  else

	  {

	  ?>

    <th>&nbsp;</th>

    <?php

		}

	  ?>

  </tr>

  <tr>

    <th align="left">Declared Amount</th>

    <th align="right"><?php echo numbers($closingarray[0]['declaredamount']);?></th>

  </tr>

  <tr>

    <th align="left">Cash Difference</th>

    <th align="right"><?php /* // commented by Yasir 23-06-11 echo numbers($closingarray[0]['cashdiffirence']-$totalcashcollection);*/ echo numbers($closingarray[0]['cashdiffirence']+$cashdiff); // +$cashdiff added by Yasir 26-07-11 

		?></th>

  </tr>
  <tr>

    <th align='left'>Cash at Counter (Balance)</th>

    <th align='right'><?php echo round(($closingarray[0]['openingbalance']+$closingarray[0]['cashsale']-$closingarray[0]['payout']),2);?></th>

  </tr>
<tr>

    <th align='left'>Cash Sales with Discount</th>

    <th align='right'><?php echo round(($closingarray[0]['cashsale']+$tdisk),2); ?></th>

  </tr>


</table>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;margin-top:10px;" class="simple">

  <tr>

    <td>Closing ID</td>

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

      <td>Last Closing Declared</td>

      <td align="right"><?php echo numbers($lastclosingbal);?></td>

  </tr>

  <tr>

    <td>Opening Balance</td>

    <td align="right"><?php echo numbers($closingarray[0]['openingbalance']);?></td>

  </tr>
<tr>

      <td>Difference</td>

      <td align="right"><?php echo ($lastclosingbal)-($closingarray[0]['openingbalance']);?></td>

  </tr>

  <tr>

    <th colspan="2">Cash</th>

  </tr>

  <tr>

    <td>Sales</td>

    <td align="right"><?php echo numbers($closingarray[0]['cashsale']-$cashdiff); // -$cashdiff added by Yasir 26-07-11 ?></td>

  </tr>

  <tr>

    <td>Collections</td>

    <td align="right"><?php echo numbers($closingarray[0]['cashcollect']);?></td>

  </tr>

  <tr>

    <td>Sub Total</td>

    <td align="right"><?php echo numbers($closingarray[0]['cashsale']-$cashdiff+$closingarray[0]['cashcollect']); // -$cashdiff added by Yasir 26-07-11?></td>

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

	  if($fcamount)

	  {

	  ?>

  <tr>

    <td>Sub Total</td>

    <td align="right"><?php echo numbers($closingarray[0]['foreigncurrencysale']+$closingarray[0]['fccollect']);?></td>

  </tr>

  <?php

	  }

	  ?>

  <tr>

    <th align="left">Cash + F.Currency + Opening Balance</th>

    <th align="right"><?php echo numbers($cashplusfc-$cashdiff); // -$cashdiff added by Yasir 26-07-11 ?></th>

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

	  ?>

  <tr>

    <th align="left">Balance</th>

    <th align="right"><?php echo numbers($cashplusfc-$cashdiff-$closingarray[0]['payout']); // -$cashdiff added by Yasir 26-07-11 ?></th>

  </tr>

  <tr>

    <th align="left">Declared Amount</th>

    <th align="right"><?php echo numbers($closingarray[0]['declaredamount']);?></th>

  </tr>

  <tr>

    <th align="left">Cash Difference</th>

    <th align="right"><?php /*// commented by Yasir 23-06-11 echo numbers($closingarray[0]['cashdiffirence']-$totalcashcollection);*/ echo numbers($closingarray[0]['cashdiffirence']+$cashdiff); // +$cashdiff added by Yasir 26-07-11 

		?></th>

  </tr>

  <tr>

    <th colspan="2">C.C Sales</th>

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

  // displaying credit bills //changed $dbname_main to $dbname_detail on line 344, 346, 348 by ahsan 22/02/2012

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

	$creditresult	=	$AdminDAO->queryresult($query);

	// calculate credit sale -- added by Yasir - 18-07-11

	$totalcredit = 0;

	for($i=0;$i<sizeof($creditresult);$i++)

	{

		$totalcredit+=$creditresult[$i][totalcredit];		

	}	

	//  	  

	  ?>

  <tr>

    <th colspan="2">Credit Sales</th>

  </tr>

  <tr>

    <td>Sale</td>

    <td align="right"><?php echo numbers($totalcredit);?></td> <?php // replaced $closingarray[0]['creditsale'] by $totalcredit by Yasir -- 18-07-11 ?>

  </tr>

</table>

<?php

	//collections display added by riz 08-01-2010

	

	if($cashcollect>'0' || $cccollect>'0' || $fccollect>'0' || $chequecollect>'0')

	{

	?>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

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

	

	//displaying cancel sales during the closing session	

	// added by Yasir 12-09-11

	if(sizeof($cancelsalesarray)>0)

	{

	?>

<table width="505" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="5">Canceled Sales</th>

  </tr>

  <tr>

    <td width="70">ID</td>

    <td width="71">Time</td>

    <td width="72">Amount</td>    

    <td width="71">Items</td>
    <td width="71">Print Status</td>

  </tr>

  <?php

	for($i=0;$i<sizeof($cancelsalesarray);$i++)

	{

	?>

  <tr>

    <td width="70"><?php echo $cancelsalesarray[$i][pksaleid];?></td>

    <td width="71"><?php echo $cancelsalesarray[$i][dtime];?></td>

    <td width="72" align="right"><?php echo numbers($cancelsalesarray[$i][totalamount]);?></td>    

    <td width="71"><?php echo $cancelsalesarray[$i][itemnum];?></td>
     <?php if($cancelsalesarray[$i][printnum]==0){?>
     <td width="71">No Print Taken</td>
	 <?php }else{?>
     <td width="122" style="background-color:#666; color:#FFF;">Total Print Taken = <?php echo $cancelsalesarray[$i][printnum];?></td>
      <?php }?>
  </tr>

  <?php

	}

	?>

 </table>

<?php 

	}//end cancel sales

	

	//displaying bills with discount processed during the closing session	

	if(sizeof($discounts)>0)

	{

	?>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Discounts</th>

  </tr>

  <tr>

    <td width="75">ID</td>

    <td width="75">Time</td>

    <td width="75">Amount</td>

    <td width="75">Discount</td>

  </tr>

  <?php

	$totaldiscount	=	0;

	for($i=0;$i<sizeof($discounts);$i++)

	{

		$totaldiscount+=$discounts[$i][discount];

		$totalsaleamount+=$discounts[$i][totalamount];

	?>

  <tr>

    <td width="75"><?php echo $discounts[$i][pksaleid];?></td>

    <td width="75"><?php echo $discounts[$i][dtime];?></td>

    <td width="75" align="right"><?php echo numbers($discounts[$i][totalamount]);?></td>

    <td width="75" align="right"><?php echo numbers($discounts[$i][discount]);?></td>

  </tr>

  <?php

	}

	?>

  <tr>

    <td colspan="2" align="right">Total</td>

    <td align="right"><?php echo numbers($totalsaleamount);?></td>

    <td align="right"><?php echo numbers($totaldiscount);?></td>

  </tr>

</table>

<?php 

	}//end discount section

	// calculating closing bills paid via Credit Card

	// Query changed by Yasir 03-10-11 //changed $dbname_main to $dbname_detail on line 502 by ahsan 22/02/2012

	$ccpayments	=	$AdminDAO->getrows("$dbname_detail.sale s, $dbname_detail.payments cc LEFT JOIN bank ON (pkbankid=cc.fkbankid) LEFT JOIN cctype ON (cc.fkcctypeid=pkcctypeid)","pkpaymentid, round( sum( amount ) , 2 ) amount, from_unixtime( s.updatetime, '%H:%i:%s' ) dtime, typename, ccno, cc.fksaleid, (SELECT totalamount FROM $dbname_detail.sale WHERE cc.fksaleid=pksaleid) stotal, bankname bank","cc.fkclosingid='$closingsession' AND paymenttype<>'c' AND cc.amount<>0 AND cc.fksaleid=pksaleid AND s.status=1 AND paymentmethod='cc' GROUP BY cc.fksaleid,pkpaymentid");

	if(sizeof($ccpayments)>0)

	{

	?>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="7"><span id="description1">Credit Card Transactions</span></th>

  </tr>

  <tr>

    <td width="40">ID</td>

    <td width="40">Time</td>

    <td width="40">Last 4</td>

    <td width="40">Sale Total</td>

    <td width="40">CC Type</td>

    <td width="40">Bank</td>

    <td width="40">CC Paid</td>

  </tr>

  <?php 

	$sumccs	=	0;

	//print_r($ccpayments);

	for($i=0;$i<sizeof($ccpayments);$i++)

	{

		$sumccs+=$ccpayments[$i]['amount'];

		

		// added bank by Yasir 26-07-11

		// added fksaleid by Yasir 18-10-11

	?>

  <tr>

    <td width="40"><?php echo $ccpayments[$i]['fksaleid'];?></td>

    <td width="40"><?php echo $ccpayments[$i]['dtime'];?></td>

    <td width="40" align="right"><?php echo $ccpayments[$i]['ccno'];?></td>

    <td width="40" align="right"><?php echo numbers($ccpayments[$i]['stotal']);?></td>

    <td width="40"><?php echo $ccpayments[$i]['typename'];?></td>

    <td width="40"><?php echo $ccpayments[$i]['bank'];?></td>

    <td width="40" align="right"><?php echo numbers($ccpayments[$i]['amount']);?></td>

  </tr>

  <?php 

	}

	?>

  <tr>

    <td colspan="6" align="right">Total</td>

    <td align="right"><?php echo numbers($sumccs);?></td>

  </tr>

</table>

<?php 

	}//end ccpayments section

	// calculating Foreign Currency

	// added  AND paymenttype <> 'c' AND amount <> 0 by Yasir 25-07-11 //changed $dbname_main to $dbname_detail on line 550 by ahsan 22/02/2012

	$fcurrency	=	$AdminDAO->getrows("$dbname_detail.payments,currency","currencyname,currencysymbol,round(sum(fcamount),2) as fcamount, round(payments.rate,2) as fcrate, charges, from_unixtime(paytime,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND pkcurrencyid=fkcurrencyid AND paymenttype <> 'c' AND amount <> 0 AND paymentmethod='fc' GROUP BY fkcurrencyid,fcrate");

	if(sizeof($fcurrency)>0)

	{

	?>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

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

    <td align="right"><?php echo numbers($fcurrency[$i][fcamount]);?></td>

    <td align="right"><?php echo numbers($fcurrency[$i][fcrate]);?></td>

    <td align="right"><?php echo numbers($fcurrency[$i][charges]);?></td>

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

	// calculating returns //changed $dbname_main to $dbname_detail on line 598 by ahsan 22/02/2012

	$returns	=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.saledetail sd,$dbname_detail.stock st,barcode bc","pksaleid,round(sd.saleprice*sd.quantity,2) as returnamount, from_unixtime(timestamp,'%H:%i:%s') as dtime,barcode,shortdescription,itemdescription","fksaleid=pksaleid AND sd.quantity<0 AND sd.fkclosingid='$closingsession' AND sd.fkstockid=st.pkstockid AND st.fkbarcodeid=bc.pkbarcodeid");

	if(sizeof($returns)>0)

	{

	?>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="5">Returns</th>

  </tr>

  <tr>

    <td width="30">ID</td>

    <td width="60">Time</td>

    <td width="60">Barcode</td>

    <td width="90">Item</td>

    <td width="60">Amount</td>

  </tr>

  <?php 

	$returnsum	=	0;

	for($i=0;$i<sizeof($returns);$i++)

	{

		$returnsum+=$returns[$i][returnamount];

		$item	=	$returns[$i]['shortdescription'];

		if($item=='')

		{

			$item	=	$returns[$i]['itemdescription'];

		}

	?>

  <tr>

    <td><?php echo $returns[$i][pksaleid];?></td>

    <td><?php echo $returns[$i][dtime];?></td>

    <td><?php echo $returns[$i][barcode];?></td>

    <td><?php echo $item;?></td>

    <td align="right"><?php echo numbers($returns[$i][returnamount]);?></td>

  </tr>

  <?php

	}

	?>

  <tr>

    <td colspan="4" align="right">Total</td>

    <td align="right"><?php echo numbers($returnsum);?></td>

  </tr>

</table>

<?php 

	}//end returns 

	// calculating cheque bills

	// added  AND cp.paymenttype <> 'c' AND cp.amount <> 0 by Yasir 25-07-11 //changed $dbname_main to $dbname_detail on line 643 by ahsan 22/02/2012

	$cheques	=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.payments cp,bank","pksaleid,chequeno,bankname,round(sum(amount),2) as chamount, from_unixtime(paytime,'%H:%i:%s') as dtime","cp.fksaleid=pksaleid AND cp.fkbankid=pkbankid AND cp.paymenttype <> 'c' AND cp.amount <> 0 AND cp.fkclosingid='$closingsession' AND paymentmethod='ch' GROUP by chequeno");

	if(sizeof($cheques)>0)

	{

	?>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Cheques</th>

  </tr>

  <tr>

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

    <td><?php echo $cheques[$i][dtime];?></td>

    <td><?php echo $cheques[$i][chequeno];?></td>

    <td><?php echo $cheques[$i][bankname];?></td>

    <td align="right"><?php echo numbers($cheques[$i][chamount]);?></td>

  </tr>

  <?php

	}

	?>

  <tr>

    <td colspan="3" align="right">Total</td>

    <td align="right"><?php echo numbers($chksum);?></td>

  </tr>

</table>

<?php 

	}//end cheques

	// calculating Payouts //changed $dbname_main to $dbname_detail on line 680 by ahsan 22/02/2012

	 $payouts	=	$AdminDAO->getrows("$dbname_detail.accountpayment,$dbname_detail.account","pkaccountpaymentid,title as accounttitle,round(amount,2) as payamount, from_unixtime(paymentdate,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND fkaccountid=id");

	if(sizeof($payouts)>0)

	{

	?>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

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

    <td align="right"><?php echo numbers($payouts[$i][payamount]);?></td>

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

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 $gop=0;
 if($_SESSION['addressbookid']==1888){
$sql_mobilesales	=	"SELECT fksaleid,from_unixtime(sd.timestamp,'%H:%i:%s') as dtime,sd.quantity,saleprice,itemdescription FROM $dbname_detail.saledetail sd left join $dbname_detail.stock on (pkstockid=fkstockid) left join main.barcode on  (fkbarcodeid=pkbarcodeid) 

							  WHERE fkclosingid = '$closingsession' and fkbarcodeid in (70115,85692,85691,12014,12037,12044,3902,3904,3905,3903,3906,3907,3910,3909,3915,3917,3918, 
3916,12425,3911,3913,3914,12762,12782,85275,56445,56446,11269,13223,13224,13225,13226,13328,13365,11324,11325,11326,13864,14141,14146,11404,11426,48946,56398,56517,56518,56519,56520,56521,85271,85693,85690,85272,85273,85274,85694,85687,85685,85688,70725,85684)";

$mobilesalesarray	=	 $AdminDAO->queryresult($sql_mobilesales); 
	$gop=sizeof($mobilesalesarray);
	
	}


	if($gop>0)

	{

	?>

<table width="411" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="6">Mobile Card Transactions</th>

  </tr>

  <tr>
     
     <td width="70">ID</td>
     <td width="75">Time</td>
     <td width="100">Item</td>
     <td width="75">Quantity</td>
     <td width="75">Rate</td>
     <td width="75">Amount</td>
    

  </tr>

  <?php 

	$paysum	=	0;

	for($i=0;$i<sizeof($mobilesalesarray);$i++)

	{

		$paysum1+=round($mobilesalesarray[$i][quantity]*$mobilesalesarray[$i][saleprice],2);

	?>

  <tr>
    <td width="70"><?php echo $mobilesalesarray[$i][fksaleid];?></td>
    <td><?php echo $mobilesalesarray[$i][dtime];?></td>
    <td ><?php echo $mobilesalesarray[$i][itemdescription];?></td>
    <td><?php echo $mobilesalesarray[$i][quantity];?></td>
    <td><?php echo $mobilesalesarray[$i][saleprice];?></td>
    <td><?php echo round($mobilesalesarray[$i][quantity]*$mobilesalesarray[$i][saleprice],2);?></td>
 </tr>
<?php }	?>
 <tr>

    <td colspan="5" align="right">Total</td>

    <td align="right"><?php echo numbers($paysum1);?></td>

  </tr>

</table>

<?php 

	}//end payouts

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// calculating price changes //changed $dbname_main to $dbname_detail on line 717 by ahsan 22/02/2012

	//$pchanges	=	$AdminDAO->getrows("$dbname_detail.sale,$dbname_detail.saledetail sd,$dbname_detail.stock st,barcode bc,discountreason","pksaledetailid,itemdescription,round(sd.saleprice,2) as sprice,round(sd.originalprice,2) as originalprice,from_unixtime(timestamp,'%H:%i:%s') as dtime,barcode,reasontitle","sd.fkclosingid='$closingsession' AND sd.originalprice<>sd.saleprice AND sd.fkstockid=st.pkstockid AND st.fkbarcodeid=bc.pkbarcodeid AND fkreasonid=pkreasonid GROUP by pksaledetailid");

	if(sizeof($pchanges)>0)

	{

	?>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="5">Price Changes</th>

  </tr>

  <tr>

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

		$pchange+=$pchanges[$i]['sprice'];

		$original+=$pchanges[$i]['originalprice'];

		$item	=	$pchanges[$i]['shortdescription'];

		if($item=='')

		{

			$item	=	$pchanges[$i]['itemdescription'];

		}

	?>

  <tr>

    <td><?php echo $pchanges[$i][dtime];?></td>

    <td><?php echo $pchanges[$i][barcode]."<br />".$item;?></td>

    <td align="right"><?php echo numbers($pchanges[$i][originalprice]);?></td>

    <td align="right"><?php echo numbers($pchanges[$i][sprice]);?></td>

    <td align="right"><?php echo $pchanges[$i][reasontitle];?></td>

  </tr>

  <?php

	}

	?>

  <tr>

    <td colspan="2" align="right">Total</td>

    <td align="right"><?php echo numbers($original);?></td>

    <td align="right"><?php echo numbers($pchange);?></td>

    <td>&nbsp;</td>

  </tr>

</table>

<?php 

	}//end price changes

		

	if(sizeof($creditresult)>0)

	{

	?>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

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

    <td align="right"><?php echo numbers($creditresult[$i][totalcredit]);?></td>

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

	// displaying cash collections

	// AND c.amount <> 0 added by Yasir 25-07-11 //changed $dbname_main to $dbname_detail on line 805 by ahsan 22/02/2012

	$cashcollectquery	=	"SELECT 

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

						AND c.paymentmethod='c'

						GROUP BY pkcustomerid

	

	";

	//echo $cashcollectquery;

	$collectionresult	=	$AdminDAO->queryresult($cashcollectquery);

	if(sizeof($collectionresult)>0)

	{

	?>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Cash Collections</th>

  </tr>

  <tr>

    <td width="75">ID</td>

    <td width="75">Time</td>

    <td width="75">Customer</td>

    <td width="75">Amount</td>

  </tr>

  <?php

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

	?>

  <tr>

    <td><?php echo $collectionresult[$i][pkpaymentid];?></td>

    <td><?php echo $collectionresult[$i][dtime];?></td>

    <td><?php echo $cname;?></td>

    <td align="right"><?php echo numbers($collectionresult[$i][amount]);?></td>

  </tr>

  <?php

	}//end cash collections

	?>

  <tr>

    <td colspan="3" align="right">Total</td>

    <td align="right"><?php echo numbers($totalcashcollect);?></td>

  </tr>

</table>

<?php

	}

	// displaying credit card collections

	// AND c.amount <> 0 added by Yasir 25-07-11

	//changed $dbname_main to $dbname_detail on line 869 by ahsan 22/02/2012

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

	?>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Credit Card Collections</th>

  </tr>

  <tr>

    <td width="75">ID</td>

    <td width="75">Time</td>

    <td width="75">Customer</td>

    <td width="75">Amount</td>

  </tr>

  <?php

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

	?>

  <tr>

    <td><?php echo $collectionresult[$i][pkpaymentid];?></td>

    <td><?php echo $collectionresult[$i][dtime];?></td>

    <td><?php echo $cname;?></td>

    <td align="right"><?php echo numbers($collectionresult[$i][amount]);?></td>

  </tr>

  <?php

	}//end credit card collections

	?>

  <tr>

    <td colspan="3" align="right">Total</td>

    <td align="right"><?php echo numbers($totalcccollect);?></td>

  </tr>

</table>

<?php

	}

	// displaying foreign currency collections

	// AND c.amount <> 0 added by Yasir 25-07-11

	$fccollectquery	=	"SELECT 

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

						AND c.paymentmethod='fc'

						GROUP BY pkcustomerid

	";

	$collectionresult	=	$AdminDAO->queryresult($fccollectquery);

	if(sizeof($collectionresult)>0)

	{

	?>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Foreign Currency Collections</th>

  </tr>

  <tr>

    <td width="75">ID</td>

    <td width="75">Time</td>

    <td width="75">Customer</td>

    <td width="75">Amount</td>

  </tr>

  <?php

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

	?>

  <tr>

    <td><?php echo $collectionresult[$i][pkpaymentid];?></td>

    <td><?php echo $collectionresult[$i][dtime];?></td>

    <td><?php echo $cname;?></td>

    <td align="right"><?php echo numbers($collectionresult[$i][amount]);?></td>

  </tr>

  <?php

	}//end foreign currency collections

	?>

  <tr>

    <td colspan="3" align="right">Total</td>

    <td align="right"><?php echo numbers($totalfccollect);?></td>

  </tr>

</table>

<?php

	}

	// displaying cheque collections

	// AND c.amount <> 0 added by Yasir 25-07-11 //changed $dbname_main to $dbname_detail on line 992 by ahsan 22/02/2012

	$chequecollectquery	=	"SELECT 

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

						AND c.paymentmethod='ch'

						GROUP BY pkcustomerid

	

	";

	$collectionresult	=	$AdminDAO->queryresult($chequecollectquery);

	if(sizeof($collectionresult)>0)

	{

	?>

<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Cheque Collections</th>

  </tr>

  <tr>

    <td width="75">ID</td>

    <td width="75">Time</td>

    <td width="75">account</td>

    <td width="75">Amount</td>

  </tr>

  <?php

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

	?>

  <tr>

    <td><?php echo $collectionresult[$i][pkpaymentid];?></td>

    <td><?php echo $collectionresult[$i][dtime];?></td>

    <td><?php echo $cname;?></td>

    <td align="right"><?php echo numbers($collectionresult[$i][amount]);?></td>

  </tr>

  <?php

	}//end foreign currency collections

	?>

  <tr>

    <td colspan="3" align="right">Total</td>

    <td align="right"><?php echo numbers($totalchequecollect);?></td>

  </tr>

</table>

<?php

	}

}

else

{

	?>

<div style="width:8.2in; margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif;" align="left">

<div style="width:3.5in;padding:0px;font-size:17px;float:left;"> <!--<img src="images/esajeelogo.jpg" width="150" height="50"><br />-->

  <span style="font-size:11px;font-family:'Comic Sans MS', cursive;"> <!--<b>Think globally shop locally</b>--> <br />

  <?php echo $storenameadd;?></span> </div>

<div style="width:4.8in; padding:2px; margin-top:2px;font-size:12px;font-weight:bold;" align="center"> Opening Date: <?php echo $openingdate; ?> </div>

<div style="width:4.8in; padding:2px; margin-top:2px;font-size:12px;font-weight:bold;" align="center"> Closing Date: <?php echo $closingdate; ?> </div>

<div style="width:4.8in; padding:2px;margin-top:2px;font-size:12px;font-weight:bold;" align="center"> Counter: <?php echo $closingarray[0]['countername'];?> </div>

<div style="width:4.8in; padding:2px;margin-top:2px;font-size:12px;font-weight:bold;" align="center"> Closing #: <?php echo $closingarray[0]['closingnumber']." (".$closingarray[0]['pkclosingid'].")";?> </div>

<div style="width:4.8in; padding:2px; font-size:12px; margin-top:2px;font-weight:bold;" align="center"> Cashier: <?php echo $cashiername; ?> </div>

</div><br />

<div id="wrapper" align="center" style="width:820px;">

	<div id="lwrapper" style="float:left;width:409px;">

		<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;margin-top:10px;" class="simple">

	<tr>

  		<th align="left">Total Sales</th>

	    <th align="right"><?php echo numbers($closingarray[0]['totalsale']);?></th>

	</tr>

    <tr>

  		<th align="left">Total Discount</th>

    	<th align="right"><?php echo numbers($tdisk);?></th>

  	</tr>

    <tr>

  		<th align="left">Net Sales</th>

    	<th align="right"><?php $netsales	=	$closingarray[0]['totalsale']-$tdisk; echo numbers($netsales);?></th>

  	</tr>

    <tr>

      <th align="left">Cash Sales</th>

      <th align="right"><?php echo numbers($closingarray[0]['cashsale']-$cashdiff); // -$cashdiff added by Yasir 26-07-11  ?></th>

    </tr>

    <tr>

      <th align="left">Collections</th>

      <th align="right"><?php echo numbers($closingarray[0]['cashcollect']);?></th>

    </tr>

    <?php /*?><tr>

      <th align="left">Balance</th>

      <th align="right"><?php echo numbers($cashplusfc-$cashdiff-$closingarray[0]['payout']); // -$cashdiff added by Yasir 26-07-11 ?></th>

    </tr>
<?php */?>
    <tr>

      <th align="left">Total Payouts</th>

      <?php

	  if($closingarray[0]['payout'])

	  {

	  ?>

      <th align="right"><?php echo numbers($closingarray[0]['payout']);?></th>

      <?php

	  }

	  else

	  {

	  ?>

      <th>&nbsp;</th>

      <?php

		}

	  ?>

    </tr>

    <tr>

      <th align="left">Declared Amount</th>

      <th align="right"><?php echo numbers($closingarray[0]['declaredamount']);?></th>

    </tr>

    <tr>

      <th align="left">Cash Difference</th>

      <th align="right"><?php /* // commented by Yasir 23-06-11 echo numbers($closingarray[0]['cashdiffirence']-$totalcashcollection);*/ echo numbers($closingarray[0]['cashdiffirence']+$cashdiff); // +$cashdiff added by Yasir 26-07-11 

		?></th>

    </tr>
 <tr>

    <th align='left'>Cash at Counter (Balance)</th>

    <th align='right'><?php echo round(($closingarray[0]['openingbalance']+$closingarray[0]['cashsale']-$closingarray[0]['payout']),2);?></th>

  </tr>
<tr>

    <th align='left'>Cash Sales with Discount</th>

    <th align='right'><?php echo round(($closingarray[0]['cashsale']+$tdisk),2); ?></th>

  </tr>

  </table>

  		<table  width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;margin-top:10px;" class="simple">

    <tr>

      <td>Closing ID</td>

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

      <td>Last Closing Declared</td>

      <td align="right"><?php echo numbers($lastclosingbal);?></td>

    </tr>

    <tr>

      <td>Opening Balance</td>

      <td align="right"><?php echo numbers($closingarray[0]['openingbalance']);?></td>

    </tr>
<tr>

      <td>Difference</td>

      <td align="right"><?php echo ($lastclosingbal)-($closingarray[0]['openingbalance']);?></td>

  </tr>

  </table>

		<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

        <tr>

          <th colspan="2">Cash</th>

        </tr>

        <tr>

          <td>Sales</td>

          <td align="right"><?php echo numbers($closingarray[0]['cashsale']-$cashdiff); // -$cashdiff added by Yasir 26-07-11 ?></td>

        </tr>

        <tr>

          <td>Collections</td>

          <td align="right"><?php echo numbers($closingarray[0]['cashcollect']);?></td>

        </tr>

        <tr>

          <td>Sub Total</td>

          <td align="right"><?php echo numbers($closingarray[0]['cashsale']-$cashdiff+$closingarray[0]['cashcollect']); // -$cashdiff added by Yasir 26-07-11?></td>

        </tr>

        <tr>

          <th align="left">Cash + F.Currency + Opening Balance</th>

          <th align="right"><?php echo numbers($cashplusfc-$cashdiff); // -$cashdiff added by Yasir 26-07-11 ?></th>

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

	  ?>

        <tr>

          <th align="left">Balance</th>

          <th align="right"><?php echo numbers($cashplusfc-$cashdiff-$closingarray[0]['payout']); // -$cashdiff added by Yasir 26-07-11 ?></th>

        </tr>

        <tr>

          <th align="left">Declared Amount</th>

          <th align="right"><?php echo numbers($closingarray[0]['declaredamount']);?></th>

        </tr>

        <tr>

          <th align="left">Cash Difference</th>

          <th align="right"><?php /*// commented by Yasir 23-06-11 echo numbers($closingarray[0]['cashdiffirence']-$totalcashcollection);*/ echo numbers($closingarray[0]['cashdiffirence']+$cashdiff); // +$cashdiff added by Yasir 26-07-11 

		?></th>

        </tr>

      </table>

		<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

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

	  if($fcamount)

	  {

	  ?>

        <tr>

          <td>Sub Total</td>

          <td align="right"><?php echo numbers($closingarray[0]['foreigncurrencysale']+$closingarray[0]['fccollect']);?></td>

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

          <th colspan="2">C.C Sales</th>

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

	  

	  // credit sale calculation //changed $dbname_main to $dbname_detail on line 1276, 1278, 1281 by ahsan 22/02/2012

	  $query	=	"SELECT

				pksaleid,

				CONCAT(firstname,', ',lastname,' ',nic) as name,

				from_unixtime(updatetime,'%H:%i:%s') as dtime,

				round(

			  (SELECT	sum(saleprice * quantity)-(SELECT SUM(globaldiscount) FROM $dbname_detail.sale sg WHERE s1.fkaccountid=pkcustomerid AND sg.fkclosingid='$closingsession' AND s.pksaleid = sg.pksaleid) as subtotal FROM $dbname_detail.saledetail,$dbname_detail.sale s1 WHERE s1.fkaccountid =  pkcustomerid AND pksaleid = fksaleid AND s1.fkclosingid='$closingsession' AND s.pksaleid = s1.pksaleid)

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

	

	$totalcredit	=	0;

	for($i=0;$i<sizeof($creditresult);$i++)

	{

	 	$totalcredit+=$creditresult[$i][totalcredit];

	}

	  

	  

	  ?>

        <tr>

          <th colspan="2">Credit Sales</th>

        </tr>

        <tr>

          <td>Sale</td>

          <td align="right"><?php echo numbers($totalcredit);?></td> <?php // $closingarray[0]['creditsale'] replaced by $totalcredit by Yasir -- 18-07-11 ?>

        </tr>

      </table>

      	<?php

		//collections display added by riz 08-01-2010

		if($cashcollect>'0' || $cccollect>'0' || $fccollect>'0' || $chequecollect>'0')

		{

		?>

		<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

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

		?>

        <?php

		//displaying bills with discount processed during the closing session //changed $dbname_main to $dbname_detail on line 1366 by ahsan 22/02/2012

		$discounts	=	$AdminDAO->getrows("$dbname_detail.sale","pksaleid,round(globaldiscount,2) as discount,round(totalamount,2) totalamount,from_unixtime(updatetime,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND status=1 AND globaldiscount>0");

		if(sizeof($discounts)>0)

		{

		?>

		<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Discounts</th>

  </tr>

  <tr>

    <td width="25%">ID</td>

    <td width="25%">Time</td>

    <td width="25%">Amount</td>

    <td width="25%">Amount</td>

  </tr>

  <?php

	$totaldiscount	=	0;

	$totalsaleamount	=	0;

	for($i=0;$i<sizeof($discounts);$i++)

	{

		$totaldiscount+=$discounts[$i][discount];

		$totalsaleamount+=$discounts[$i][totalamount];

	?>

  <tr>

    <td width="25%"><?php echo $discounts[$i][pksaleid];?></td>

    <td width="25%"><?php echo $discounts[$i][dtime];?></td>

     <td width="25%" align="right"><?php echo numbers($discounts[$i][totalamount]);?></td>

    <td width="25%" align="right"><?php echo numbers($discounts[$i][discount]);?></td>

  </tr>

  <?php

	}

	?>

  <tr>

    <td colspan="2" align="right">Total </td>

    <td align="right"><?php echo numbers($totalsaleamount);?></td>

    <td align="right"><?php echo numbers($totaldiscount);?></td>

  </tr>

</table>

		<?php 

		}//end discount section

		?>

        <?php

	// calculating closing bills paid via Credit Card

	// added  AND c.paymenttype <> 'c' AND c.amount <> 0 by Yasir 25-07-11

	// added , (select bankname from $dbname_main.ccpayment cp LEFT JOIN bank ON (pkbankid=cp.fkbankid) where cp.fksaleid=s.pksaleid) as bank by Yasir 26-07-11

	//changed $dbname_main to $dbname_detail on line 1411 by ahsan 22/02/2012

	$ccpayments	=	$AdminDAO->getrows("$dbname_detail.sale s, $dbname_detail.payments cc LEFT JOIN bank ON (pkbankid=cc.fkbankid) LEFT JOIN cctype ON (cc.fkcctypeid=pkcctypeid)","pkpaymentid, round( sum( amount ) , 2 ) amount, from_unixtime( s.updatetime, '%H:%i:%s' ) dtime, typename, ccno, cc.fksaleid, (SELECT totalamount FROM $dbname_detail.sale WHERE cc.fksaleid=pksaleid) stotal, bankname bank","cc.fkclosingid='$closingsession' AND paymenttype<>'c' AND cc.amount<>0 AND cc.fksaleid=pksaleid AND s.status=1 AND cc.paymentmethod = 'cc' GROUP BY cc.fksaleid,pkpaymentid");

	if(sizeof($ccpayments)>0)

	{

	?>

<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="7"><span id="description1">Credit Card Transactions</span></th>

  </tr>

  <tr>

  	<td width="14%">ID</td>

    <td width="14%">Time</td>

    <td width="14%">Last 4</td>

    <td width="14%">Sale Total</td>

    <td width="14%">CC Type</td>

    <td width="14%">Bank</td>

    <td width="14%">CC Paid</td>

  </tr>

  <?php 

	$sumccs	=	0;

	//print_r($ccpayments);

	for($i=0;$i<sizeof($ccpayments);$i++)

	{

		$sumccs+=$ccpayments[$i]['amount'];

		

		// added bank by Yasir 26-07-11

		// added fksaleid by Yasir 18-10-11

	?>

  <tr>

    <td width="14%"><?php echo $ccpayments[$i]['fksaleid'];?></td>

    <td width="14%"><?php echo $ccpayments[$i]['dtime'];?></td>

    <td width="14%" align="right"><?php echo $ccpayments[$i]['ccno'];?></td>

    <td width="14%" align="right"><?php echo numbers($ccpayments[$i]['stotal']);?></td>

    <td width="14%"><?php echo $ccpayments[$i]['typename'];?></td>

    <td width="14%"><?php echo $ccpayments[$i]['bank'];?></td>

    <td width="14%" align="right"><?php echo numbers($ccpayments[$i]['amount']);?></td>

  </tr>

  <?php 

	}

	?>

  <tr>

    <td colspan="6" align="right">Total</td>

    <td align="right"><?php echo numbers($sumccs);?></td>

  </tr>

</table>

<?php 

	}//end ccpayments section

	?>

	    <?php

	// calculating Foreign Currency

	// added  AND paymenttype <> 'c' AND amount <> 0 by Yasir 25-07-11 //changed $dbname_main to $dbname_detail on line 1461 by ahsan 22/02/2012

	$fcurrency	=	$AdminDAO->getrows("$dbname_detail.payments,currency","currencyname,currencysymbol,round(sum(amount),2) as fcamount, round(payments.rate,2) as fcrate, charges, from_unixtime(paytime,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND paymenttype <> 'c' AND amount <> 0 AND pkcurrencyid=fkcurrencyid GROUP BY fkcurrencyid,fcrate");

	if(sizeof($fcurrency)>0)

	{

	?>

<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="5">Foreign Currency</th>

  </tr>

  <tr>

    <td width="20%">Date & Time</td>

    <td width="20%">Currency</td>

    <td width="20%">Amount</td>

    <td width="20%">Rate</td>

    <td width="20%">Charges</td>

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

    <td align="right"><?php echo numbers($fcurrency[$i][fcamount]);?></td>

    <td align="right"><?php echo numbers($fcurrency[$i][fcrate]);?></td>

    <td align="right"><?php echo numbers($fcurrency[$i][charges]);?></td>

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

	?>

	</div>

	<div id="rwrapper" style="float:left;width:409px;margin-top:10px;">

    	<?php

	// calculating returns //changed $dbname_main to $dbname_detail on line 1513 by ahsan 22/02/2012

	$returns	=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.saledetail sd,$dbname_detail.stock st,barcode bc","pksaleid,round(sd.saleprice*sd.quantity,2) as returnamount, from_unixtime(timestamp,'%H:%i:%s') as dtime,barcode,shortdescription,itemdescription","fksaleid=pksaleid AND sd.quantity<0 AND sd.fkclosingid='$closingsession' AND sd.fkstockid=st.pkstockid AND st.fkbarcodeid=bc.pkbarcodeid");

	if(sizeof($returns)>0)

	{

	?>

<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="5">Returns</th>

  </tr>

  <tr>

    <td width="10%">ID</td>

    <td width="20%">Time</td>

    <td width="30%">Barcode</td>

    <td width="20%">Item</td>

    <td width="20%">Amount</td>

  </tr>

  <?php 

	$returnsum	=	0;

	for($i=0;$i<sizeof($returns);$i++)

	{

		$returnsum+=$returns[$i][returnamount];

		$item	=	$returns[$i]['shortdescription'];

		if($item=='')

		{

			$item	=	$returns[$i]['itemdescription'];

		}

	?>

  <tr>

    <td><?php echo $returns[$i][pksaleid];?></td>

    <td><?php echo $returns[$i][dtime];?></td>

    <td><?php echo $returns[$i][barcode];?></td>

    <td><?php echo $item;?></td>

    <td align="right"><?php echo numbers($returns[$i][returnamount]);?></td>

  </tr>

  <?php

	}

	?>

  <tr>

    <td colspan="4" align="right">Total</td>

    <td align="right"><?php echo numbers($returnsum);?></td>

  </tr>

</table>

<?php 

	}//end returns

	?>

	    <?php 

	// calculating cheque bills

	// added  AND cp.paymenttype <> 'c' AND cp.amount <> 0 by Yasir 25-07-11 //changed $dbname_main to $dbname_detail on line 1560 by ahsan 22/02/2012

	$cheques	=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.payments cp,bank","pksaleid,chequeno,bankname,round(sum(amount),2) as chamount, from_unixtime(paytime,'%H:%i:%s') as dtime","cp.fksaleid=pksaleid AND cp.paymenttype <> 'c' AND cp.amount <> 0 AND cp.fkbankid=pkbankid AND cp.fkclosingid='$closingsession' AND cp.paymentmethod='ch' GROUP by chequeno");

	if(sizeof($cheques)>0)

	{

	?>

<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Cheques</th>

  </tr>

  <tr>

    <td width="25%">Time</td>

    <td width="25%">#</td>

    <td width="25%">Bank</td>

    <td width="25%">Amount</td>

  </tr>

  <?php 

	$chksum	=	0;

	for($i=0;$i<sizeof($cheques);$i++)

	{

		$chksum+=$cheques[$i][chamount];

	?>

  <tr>

    <td><?php echo $cheques[$i][dtime];?></td>

    <td><?php echo $cheques[$i][chequeno];?></td>

    <td><?php echo $cheques[$i][bankname];?></td>

    <td align="right"><?php echo numbers($cheques[$i][chamount]);?></td>

  </tr>

  <?php

	}

	?>

  <tr>

    <td colspan="3" align="right">Total</td>

    <td align="right"><?php echo numbers($chksum);?></td>

  </tr>

</table>

<?php 

	}//end cheques

	?>

	    <?php

	// calculating Payouts //changed $dbname_main to $dbname_detail on line 1599 by ahsan 22/02/2012

	 $payouts	=	$AdminDAO->getrows("$dbname_detail.accountpayment,$dbname_detail.account","pkaccountpaymentid, title as accounttitle,round(amount,2) as payamount, from_unixtime(paymentdate,'%H:%i:%s') as dtime","fkclosingid='$closingsession' AND fkaccountid=id");

	if(sizeof($payouts)>0)

	{

	?>

<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Payouts</th>

  </tr>

  <tr>

    <td width="25%">ID</td>

    <td width="25%">Time</td>

    <td width="25%">Account</td>

    <td width="25%">Amount</td>

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

    <td align="right"><?php echo numbers($payouts[$i][payamount]);?></td>

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

	?>

	    <?php

	// calculating price changes //changed $dbname_main to $dbname_detail on line 1638 by ahsan 22/02/2012

	//$pchanges	=	$AdminDAO->getrows("$dbname_detail.sale,$dbname_detail.saledetail sd,$dbname_detail.stock st,barcode bc,discountreason","pksaledetailid,itemdescription,round(sd.saleprice,2) as sprice,round(sd.originalprice,2) as originalprice,from_unixtime(timestamp,'%H:%i:%s') as dtime,barcode,reasontitle","sd.fkclosingid='$closingsession' AND sd.originalprice<>sd.saleprice AND sd.fkstockid=st.pkstockid AND st.fkbarcodeid=bc.pkbarcodeid AND fkreasonid=pkreasonid GROUP by pksaledetailid");

	if(sizeof($pchanges)>0)

	{

	?>

<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="5">Price Changes</th>

  </tr>

  <tr>

    <td width="20%">Time</td>

    <td width="20%">Item</td>

    <td width="20%">Original</td>

    <td width="20%">Changed</td>

    <td width="20%">Reason</td>

  </tr>

  <?php

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

	?>

  <tr>

    <td><?php echo $pchanges[$i][dtime];?></td>

    <td><?php echo $pchanges[$i][barcode]."<br />".$item;?></td>

    <td align="right"><?php echo numbers($pchanges[$i][originalprice]);?></td>

    <td align="right"><?php echo numbers($pchanges[$i][sprice]);?></td>

    <td align="right"><?php echo $pchanges[$i][reasontitle];?></td>

  </tr>

  <?php

	}

	?>

  <tr>

    <td colspan="2" align="right">Total</td>

    <td align="right"><?php echo numbers($original);?></td>

    <td align="right"><?php echo numbers($pchange);?></td>

    <td>&nbsp;</td>

  </tr>

</table>

<?php 

	}//end price changes

	?>

	    <?php

	// displaying credit bills	

	if(sizeof($creditresult)>0)

	{

	?>

<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Credit Sales</th>

  </tr>

  <tr>

    <td width="25%">ID</td>

    <td width="25%">Time</td>

    <td width="25%">Customer</td>

    <td width="25%">Amount</td>

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

    <td align="right"><?php echo numbers($creditresult[$i][totalcredit]);?></td>

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

	    <?php

	// displaying cash collections //changed $dbname_main to $dbname_detail on line 1729 by ahsan 22/02/2012

	$cashcollectquery	=	"SELECT 

							c.pkpaymentid,from_unixtime(paytime,'%H:%i:%s') as dtime,SUM(c.amount) as amount,CONCAT(firstname,' ',lastname) as name,t.companyname cname

						FROM 

							$dbname_detail.payments c, $dbname_detail.sale s, customer t

						WHERE 

							c.fksaleid = pksaleid

						AND s.status =1

						AND s.fkaccountid = t.pkcustomerid

						AND c.paymenttype = 'c'

						AND c.fkclosingid ='$closingsession'

						AND c.paymentmethod='c'

						GROUP BY pkcustomerid

	

	";

	//echo $cashcollectquery;

	$collectionresult	=	$AdminDAO->queryresult($cashcollectquery);

	if(sizeof($collectionresult)>0)

	{

	?>

<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Cash Collections</th>

  </tr>

  <tr>

    <td width="25%">ID</td>

    <td width="25%">Time</td>

    <td width="25%">Customer</td>

    <td width="25%">Amount</td>

  </tr>

  <?php

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

	?>

  <tr>

    <td><?php echo $collectionresult[$i][pkpaymentid];?></td>

    <td><?php echo $collectionresult[$i][dtime];?></td>

    <td><?php echo $cname;?></td>

    <td align="right"><?php echo numbers($collectionresult[$i][amount]);?></td>

  </tr>

  <?php

	}//end cash collections

	?>

  <tr>

    <td colspan="3" align="right">Total</td>

    <td align="right"><?php echo numbers($totalcashcollect);?></td>

  </tr>

</table>

<?php

	}

	// displaying credit card collections

	?>

    	<?php //changed $dbname_main to $dbname_detail on line 1792 by ahsan 22/02/2012

	$cccollectquery	=	"SELECT 

							c.pkpaymentid,from_unixtime(paytime,'%H:%i:%s') as dtime,SUM(c.amount) as amount,CONCAT(firstname,' ',lastname) as name,t.companyname cname

						FROM 

							$dbname_detail.payments c, $dbname_detail.sale s, customer t

						WHERE 

							c.fksaleid = pksaleid

						AND s.status =1

						AND s.fkaccountid = t.pkcustomerid

			

						AND c.paymenttype = 'c'

						AND c.fkclosingid ='$closingsession'

						AND c.paymentmethod='cc'

						GROUP BY pkcustomerid

	

	";

	$collectionresult	=	$AdminDAO->queryresult($cccollectquery);

	if(sizeof($collectionresult)>0)

	{

	?>

<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Credit Card Collections</th>

  </tr>

  <tr>

    <td width="25%">ID</td>

    <td width="25%">Time</td>

    <td width="25%">Customer</td>

    <td width="25%">Amount</td>

  </tr>

  <?php

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

	?>

  <tr>

    <td><?php echo $collectionresult[$i][pkpaymentid];?></td>

    <td><?php echo $collectionresult[$i][dtime];?></td>

    <td><?php echo $cname;?></td>

    <td align="right"><?php echo numbers($collectionresult[$i][amount]);?></td>

  </tr>

  <?php

	}//end credit card collections

	?>

  <tr>

    <td colspan="3" align="right">Total</td>

    <td align="right"><?php echo numbers($totalcccollect);?></td>

  </tr>

</table>

<?php

	}

	?>

	    <?php

	// displaying foreign currency collections //changed $dbname_main to $dbname_detail on line 1854 by ahsan 22/02/2012

	$fccollectquery	=	"SELECT 

							c.pkpaymentid,from_unixtime(paytime,'%H:%i:%s') as dtime,SUM(c.amount) as amount,CONCAT(firstname,' ',lastname) as name,t.companyname cname

						FROM 

							$dbname_detail.payments c, $dbname_detail.sale s, customer t

						WHERE 

							c.fksaleid = pksaleid

						AND s.status =1

						AND s.fkaccountid = t.pkcustomerid

			

						AND c.paymenttype = 'c'

						AND c.fkclosingid ='$closingsession'

						AND c.paymentmethod='fc'

						GROUP BY pkcustomerid

	";

	$collectionresult	=	$AdminDAO->queryresult($fccollectquery);

	if(sizeof($collectionresult)>0)

	{

	?>

<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Foreign Currency Collections</th>

  </tr>

  <tr>

    <td width="25%">ID</td>

    <td width="25%">Time</td>

    <td width="25%">Customer</td>

    <td width="25%">Amount</td>

  </tr>

  <?php

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

	?>

  <tr>

    <td><?php echo $collectionresult[$i][pkpaymentid];?></td>

    <td><?php echo $collectionresult[$i][dtime];?></td>

    <td><?php echo $cname;?></td>

    <td align="right"><?php echo numbers($collectionresult[$i][amount]);?></td>

  </tr>

  <?php

	}//end foreign currency collections

	?>

  <tr>

    <td colspan="3" align="right">Total</td>

    <td align="right"><?php echo numbers($totalfccollect);?></td>

  </tr>

</table>

<?php

	}

	?>

	    <?php

	// displaying cheque collections //changed $dbname_main to $dbname_detail on line 1915 by ahsan 22/02/2012

	$chequecollectquery	=	"SELECT 

							c.pkpaymentid,from_unixtime(paytime,'%H:%i:%s') as dtime,SUM(c.amount) as amount,CONCAT(firstname,' ',lastname) as name,t.companyname cname

						FROM 

							$dbname_detail.payments c, $dbname_detail.sale s, customer t

						WHERE 

							c.fksaleid = pksaleid

						AND s.status =1

						AND s.fkaccountid = t.pkcustomerid

						AND c.paymenttype = 'c'

						AND c.fkclosingid ='$closingsession'

						AND c.paymentmethod='ch'

						GROUP BY pkcustomerid

	

	";

	$collectionresult	=	$AdminDAO->queryresult($chequecollectquery);

	if(sizeof($collectionresult)>0)

	{

	?>

<table width="100%" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="4">Cheque Collections</th>

  </tr>

  <tr>

    <td width="25%">ID</td>

    <td width="25%">Time</td>

    <td width="25%">account</td>

    <td width="25%">Amount</td>

  </tr>

  <?php

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

	?>

  <tr>

    <td><?php echo $collectionresult[$i][pkpaymentid];?></td>

    <td><?php echo $collectionresult[$i][dtime];?></td>

    <td><?php echo $cname;?></td>

    <td align="right"><?php echo numbers($collectionresult[$i][amount]);?></td>

  </tr>

  <?php

	}//end foreign currency collections

	?>

  <tr>

    <td colspan="3" align="right">Total</td>

    <td align="right"><?php echo numbers($totalchequecollect);?></td>

  </tr>

</table>

<?php

	}

	

	//displaying cancel sales during the closing session	

	// added by Yasir 12-09-11

	if(sizeof($cancelsalesarray)>0)

	{

	?>

<table width="505" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="5">Canceled Sales</th>

  </tr>

  <tr>

    <td width="70">ID</td>

    <td width="71">Time</td>

    <td width="72">Amount</td>    

    <td width="71">Items</td>
    <td width="71">Print Status</td>

  </tr>

  <?php

	for($i=0;$i<sizeof($cancelsalesarray);$i++)

	{

	?>

  <tr>

    <td width="70"><?php echo $cancelsalesarray[$i][pksaleid];?></td>

    <td width="71"><?php echo $cancelsalesarray[$i][dtime];?></td>

    <td width="72" align="right"><?php echo numbers($cancelsalesarray[$i][totalamount]);?></td>    

    <td width="71"><?php echo $cancelsalesarray[$i][itemnum];?></td>
     <?php if($cancelsalesarray[$i][printnum]==0){?>
     <td width="71">No Print Taken</td>
	 <?php }else{?>
     <td width="122" style="background-color:#666; color:#FFF;">Total Print Taken = <?php echo $cancelsalesarray[$i][printnum];?></td>
      <?php }?>
  </tr>

  <?php

	}

	?>

 </table>

<?php 

	}//end cancel sales

	
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 $gop=0;
 if($_SESSION['addressbookid']==1888){
$sql_mobilesales	=	"SELECT fksaleid,from_unixtime(sd.timestamp,'%H:%i:%s') as dtime,sd.quantity,saleprice,itemdescription FROM $dbname_detail.saledetail sd left join $dbname_detail.stock on (pkstockid=fkstockid) left join main.barcode on  (fkbarcodeid=pkbarcodeid) 

							  WHERE fkclosingid = '$closingsession' and fkbarcodeid in (70115,85692,85691,12014,12037,12044,3902,3904,3905,3903,3906,3907,3910,3909,3915,3917,3918, 
3916,12425,3911,3913,3914,12762,12782,85275,56445,56446,11269,13223,13224,13225,13226,13328,13365,11324,11325,11326,13864,14141,14146,11404,11426,48946,56398,56517,56518,56519,56520,56521,85271,85693,85690,85272,85273,85274,85694,85687,85685,85688,70725,85684)";

$mobilesalesarray	=	 $AdminDAO->queryresult($sql_mobilesales); 
	$gop=sizeof($mobilesalesarray);
	
	}


	if($gop>0)

	{

	?>

<table width="411" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;" class="simple">

  <tr>

    <th colspan="6">Mobile Card Transactions</th>

  </tr>

  <tr>
     
     <td width="70">ID</td>
     <td width="75">Time</td>
     <td width="100">Item</td>
     <td width="75">Quantity</td>
     <td width="75">Rate</td>
     <td width="75">Amount</td>
    

  </tr>

  <?php 

	$paysum	=	0;

	for($i=0;$i<sizeof($mobilesalesarray);$i++)

	{

		$paysum1+=round($mobilesalesarray[$i][quantity]*$mobilesalesarray[$i][saleprice],2);

	?>

  <tr>
    <td width="70"><?php echo $mobilesalesarray[$i][fksaleid];?></td>
    <td><?php echo $mobilesalesarray[$i][dtime];?></td>
    <td ><?php echo $mobilesalesarray[$i][itemdescription];?></td>
    <td><?php echo $mobilesalesarray[$i][quantity];?></td>
    <td><?php echo $mobilesalesarray[$i][saleprice];?></td>
    <td><?php echo round($mobilesalesarray[$i][quantity]*$mobilesalesarray[$i][saleprice],2);?></td>
 </tr>
<?php }	?>
 <tr>

    <td colspan="5" align="right">Total</td>

    <td align="right"><?php echo numbers($paysum1);?></td>

  </tr>

</table>

<?php 

	}//end payouts

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	?>
<?php

 /*$query 	= 	"SELECT 
		c.pkcouponid id,c.pkcouponid  as coupon_id,IF(FROM_UNIXTIME(c.updatetime,'%d-%m-%Y')= '01-01-1970','',FROM_UNIXTIME(c.updatetime,'%d-%m-%Y'))as updatetime,
        c.amount, CONCAT(b.firstname,' ', b.lastname) username_name,
		case c.status when '1' then 'Active' when '2' then 'Used' else 'In Active' end status0
      FROM
	  $dbname_detail.sale s
	left join $dbname_main.coupon_man c on c.pkcouponid = s.fkcouponid
	left join $dbname_main.addressbook b on b.pkaddressbookid = c.fkaddressbookid
	where 1=1 and s.fkclosingid = '$closingsession'
	
	 ";*/
	 
	  $query = "SELECT
		c.pkcouponid  as pkcouponid,c.amount,case c.paymentmethod when 'c' then 'Cash' when 'cc' then 'Credit Card' when 'fc' then 'Foreign Currency' else 'Cheque' end paymentmethod0,c.reason
      FROM
	$dbname_detail.sale s
	left join $dbname_detail.coupon_management c on c.pkcouponid = s.fkcouponid
	where 1=1 and c.fkclosingid = '$closingsession' order by pkcouponid desc ";
   $result_query		=	$AdminDAO->queryresult($query);
//$closingsession
if(sizeof($result_query)>0)

{
 
?>
<table width="318" class="simple" style="font-size:9px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; border-color:#FFF;">
  <tr>
    <th colspan="4">Advance Booking</th>
  </tr>
  <tr>
    <td width="70">CouponID</td>
    <td width="82">Payment Method</td>
    <td width="57">Reason</td>
   <td width="71">Amount</td>
   
  </tr>
  <?php

$total_amount	=	0;

for($i=0;$i<sizeof($result_query);$i++)

{

	  $total_amount+=	$result_query[$i]['amount'];
	

?>
  <tr>
    <td><?php echo $result_query[$i]['pkcouponid'];?></td>
    <td><?php echo $result_query[$i]['paymentmethod0'];?></td>
    <td><?php echo $result_query[$i]['reason'];?></td>
 
    <td align="right"><?php echo $result_query[$i]['amount'];?></td>
  </tr>
  <?php

}

?>
  <tr>
    <td colspan="3" align="right">Total</td>
    <td align="right"><?php echo numbers($total_amount);?></td>
  </tr>
</table>
<?php  } ?>
&nbsp;&nbsp;
<?php

	 
	  $query = "SELECT 
		c.pkcouponid as coupon_id,
        c.amount
      FROM
	
	$dbname_detail.sale s
	  left join $dbname_detail.coupon_management c on c.pkcouponid = s.fkcouponid
	
	where 1=1 and c.status='2' and c.fkclosingid = '$closingsession' "; 
   $result_query		=	$AdminDAO->queryresult($query);
//$closingsession
if(sizeof($result_query)>0)

{
 
?>
<table width="300" class="simple" style="font-size:9px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; border-color:#FFF;">
  <tr>
    <th colspan="2">Coupon Used </th>
  </tr>
  <tr>
    <td width="176">CouponID</td>
    <td width="112">Amount</td>
   
  </tr>
  <?php

$total_amount	=	0;

for($i=0;$i<sizeof($result_query);$i++)

{

	  $total_amount+=	$result_query[$i]['amount'];
	

?>
  <tr>
    <td><?php echo $result_query[$i]['coupon_id'];?></td>
    <td align="right"><?php echo $result_query[$i]['amount'];?></td>
  </tr>
  <?php

}

?>
  <tr>
    <td align="right">Total</td>
    <td align="right"><?php echo numbers($total_amount);?></td>
  </tr>
</table>
<?php  } ?>
	</div>

</div>

	<?php

}

?>
</p>

<div align="center" style="clear:both;margin-top:3px;">

  <?php 

echo date('Y-m-d h:i:s'); 

//empty closing session variable for new session

if($_GET['closingid']!='')

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