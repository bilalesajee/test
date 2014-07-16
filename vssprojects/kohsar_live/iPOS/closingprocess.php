<?php
include("includes/security/adminsecurity.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 		=	$userSecurity->getRights(8);
$closingsession	=	$_SESSION['closingsession'];

// added by Yasir - 07-07-11
 if(!isset($closingsession) || $closingsession=='' || $closingsession==0){
  echo "No active closing available.";
  exit;
 }
 
 $breakmode				=	$_SESSION['breakmode'];
 if($breakmode==1)
 {
	echo "Your counter is in break mode. You are unable to continue closing.";
	  exit;
  }	 
// 
//include_once("dbgrid.php"); Commented by Yasir -- 18-07-11
/*
$lastclosing	=	$AdminDAO->getrows("closinginfo","*","countername='$countername' AND fkaddressbookid='$empid' AND pkclosingid<>'$closingsession' AND closingstatus='a' ORDER BY closingdate DESC");
//*********************** closing info **************************
$lastcosingbalance	=	$lastclosing[0]['lastcosingbalance'];

$closingid			=	$_REQUEST['id'];
*/
//checking incomplete and held sales
//Dated: 26/01/2010
$salesquery			=	"SELECT pksaleid FROM $dbname_detail.sale WHERE fkclosingid='$closingsession' AND status NOT IN (1,2)";
$incompletesales	=	$AdminDAO->queryresult($salesquery);
$salefields			=	array('status');
for($i=0;$i<sizeof($incompletesales);$i++)
{
	$pksaleid	=	$incompletesales[$i]['pksaleid'];
	$saledata	=	array(2);
	$AdminDAO->updaterow("$dbname_detail.sale",$salefields,$saledata,"pksaleid='$pksaleid'");
}
$cashierpassword	=	$_REQUEST['password'];
$cashierpassword=md5($cashierpassword);
if($cashierpassword=='')
{
	echo "Please enter your password";
	exit;
}
$rows	=	$AdminDAO->getrows("addressbook","1","pkaddressbookid='$empid' AND password='$cashierpassword'");
if(sizeof($rows)<1)
{
	echo "You are not authorized to perform closing on this counter";
	exit;
}
$declaredamount		=	$_REQUEST['declaredamount'];
if($declaredamount<=0)
{
	echo "Please enter declared amount.";
	exit;
}
$totalcheques		=	$_REQUEST['totalcheques'];
$chequeamount		=	$_REQUEST['chequeamount'];
$currencies			=	$_REQUEST['currencyid'];

for($c=0;$c<sizeof($currencies);$c++)
{
	$currencyid		=	$currencies[$c];
	$curamount		=	$_REQUEST[$currencyid];
	$fieldc			=	array('fkcurrencyid','fkclosingid','amount');
	$valuec			=	array($currencyid,$closingsession,$curamount);
	$AdminDAO->insertrow("$dbname_detail.closingcurrency",$fieldc,$valuec);
}


//$fkaddressbookid	=	$_SESSION['addressbookid'];
$closingdate		=	time();
//calculating opening balance
$query	=	"SELECT
				round(
					  (SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) as amount from $dbname_detail.payments cp,$dbname_detail.sale WHERE pksaleid=fksaleid AND status='1' AND cp.fkclosingid	=	'$closingsession'),2) as totalamount
			FROM 
				$dbname_detail.closinginfo  ci 
			WHERE 
				pkclosingid 	=	'$closingsession'
				";
$queryres			=	$AdminDAO->queryresult($query);
$totalcollections	=	0;
foreach($queryres as $totalbalance)
{
	$totalcollections	+=	$totalbalance[totalamount];
}
//total paid = total sales + previous opening balance (countername,fkaddressbookid,closingstatus,closingdate)
$openingbalance		=	$AdminDAO->getrows("$dbname_detail.closinginfo","openingbalance","pkclosingid = '$closingsession'");
$openingbalance		=	$openingbalance[0]['openingbalance'];

$totalcollections	=	$totalcollections	+ $openingbalance;
$query2				=	"SELECT SUM(amount) as closingamount FROM $dbname_detail.accountpayment WHERE fkclosingid 	=	'$closingsession'";
$query2res		=	$AdminDAO->queryresult($query2);
$totalpayouts	=	$query2res[0][closingamount];

//$openingbalance	=	$totalcollections - $totalpayouts;

//end opening balance

//calculating lastclosingbalance old

/*$sql="SELECT pkclosingid from $dbname_detail.closinginfo where countername='$countername' AND fkstoreid='$storeid' AND  closingstatus='a' ORDER BY closingdate DESC LIMIT 0,1";
$lastclosingres		=	$AdminDAO->queryresult($sql);
$closingid			=	$lastclosingres[0][pkclosingid];
$sqlclosingbal="
				SELECT round(
					  (SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) as amount from $dbname_detail.cashpayment cp,$dbname_detail.sale WHERE pksaleid=fksaleid AND status='1' AND  cp.fkclosingid='$closingid'  and cp.paymenttype<>'c')
					  +
					  (SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) as ccamount from $dbname_detail.ccpayment ccp,$dbname_detail.sale  WHERE pksaleid=fksaleid AND status='1' AND  ccp.fkclosingid='$closingid'  and ccp.paymenttype<>'c')
					  +
					  (SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) as chamount from $dbname_detail.chequepayment cqp,$dbname_detail.sale WHERE pksaleid=fksaleid AND status='1' AND cqp.fkclosingid='$closingid'  and cqp.paymenttype<>'c')
					  +
					  (SELECT IF(SUM(amount*rate) IS NULL,0,SUM(amount*rate)) as fcamount from $dbname_detail.fcpayment fcp,$dbname_detail.sale WHERE  pksaleid=fksaleid AND status='1' AND fcp.fkclosingid='$closingid'  and fcp.paymenttype<>'c'),2)-(ROUND(SUM(amount),2)) as totalamount from $dbname_detail.accountpayment where fkclosingid='$closingid'
";
$lastclosingbalres		=	$AdminDAO->queryresult($sqlclosingbal);
$lastclosingbalance		=	$lastclosingbalres[0][totalamount];*/

//end lastclosingbalance

// calculating last closing balance -- by Yasir 22-06-11
$sql="SELECT declaredamount from $dbname_detail.closinginfo where countername='$countername' AND fkstoreid='$storeid' AND  closingstatus='a' ORDER BY closingdate DESC LIMIT 0,1";
$lastclosingres_	=	$AdminDAO->queryresult($sql);
$lastclosingbalance		=	$lastclosingres_[0][declaredamount];
// end caluculating last balance


//This closing Particulars
// Added " AND fkdiscountid=0" in total sale to only consider relevant sales
 $sqlclosingbalpart="
				SELECT 
					  round((SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) as amount from $dbname_detail.payments cp,$dbname_detail.sale WHERE pksaleid=fksaleid AND status='1' AND paymentmethod = 'c' AND  cp.fkclosingid='$closingsession' and cp.paymenttype<>'c'),2) as cashsale
					  
					  ,round((SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) as ccamount from $dbname_detail.payments ccp,$dbname_detail.sale WHERE pksaleid=fksaleid AND status='1'  AND paymentmethod = 'cc' AND   ccp.fkclosingid='$closingsession'  and ccp.paymenttype<>'c'),2) as ccsale
					 
					  ,round((SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) as chamount from $dbname_detail.payments cqp,$dbname_detail.sale WHERE pksaleid=fksaleid AND status='1'  AND paymentmethod = 'ch' AND cqp.fkclosingid='$closingsession'  and cqp.paymenttype<>'c'),2) as chequesale
					 
					 ,round((SELECT IF(SUM(amount*rate) IS NULL,0,SUM(amount*rate)) as fcamount from $dbname_detail.payments fcp,$dbname_detail.sale WHERE pksaleid=fksaleid AND status='1'  AND paymentmethod = 'fc' AND  fcp.fkclosingid='$closingsession'  and fcp.paymenttype<>'c'),2) as fcsale
					 ,round((SELECT IF(SUM(globaldiscount) IS NULL,0,SUM(globaldiscount)) as globaldiscount from $dbname_detail.sale WHERE  fkclosingid='$closingsession' AND status = '1'),2) as globaldiscount
					 ,round((SELECT IF(SUM(saleprice*quantity) IS NULL,0,SUM(saleprice*quantity)) as totalsale from $dbname_detail.sale s,$dbname_detail.saledetail sd WHERE   s.fkclosingid='$closingsession' AND sd.fksaleid=s.pksaleid AND status = '1' AND fkdiscountid=0),2) as totalsale
					 ,round((SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) as salediscount from $dbname_detail.salediscount WHERE  fkclosingid='$closingsession'),2) as salediscount
					 ,(SELECT COUNT(pksaledetailid) as totalitems from $dbname_detail.sale s,$dbname_detail.saledetail sd WHERE   s.fkclosingid='$closingsession' AND sd.fksaleid=s.pksaleid AND status = '1' ) as totalitems
					 ,(SELECT COUNT(*) from $dbname_detail.sale WHERE fkclosingid='$closingsession' AND status='1') as totalbills
					 from $dbname_detail.closinginfo where pkclosingid='$closingsession'

";

$resclosingparticulars	=	$AdminDAO->queryresult($sqlclosingbalpart);
$totalbills				=	$resclosingparticulars[0]['totalbills'];
$cashsale				=	$resclosingparticulars[0]['cashsale'];
$ccsale					=	$resclosingparticulars[0]['ccsale'];
$fcsale					=	$resclosingparticulars[0]['fcsale'];
$chequesale				=	$resclosingparticulars[0]['chequesale'];
$globaldiscount			=	$resclosingparticulars[0]['globaldiscount'];
$totalsale				=	$resclosingparticulars[0]['totalsale'];
$salediscount			=	$resclosingparticulars[0]['salediscount'];
// adjusting total sale with discounts
$totalsale				=	$totalsale	-	$salediscount;
//get total items from the saledetails after balancing the returns and adjustments
$total_items_query	=	"SELECT sum(quantity) as quantity FROM $dbname_detail.sale,$dbname_detail.saledetail sd WHERE pksaleid = fksaleid AND status = '1' AND sd.fkclosingid='$closingsession' GROUP BY fkstockid,saleprice";
$total_items_array	=	$AdminDAO->queryresult($total_items_query);
$totalitems = 0;
for($s=0;$s<sizeof($total_items_array);$s++)
{
	if($total_items_array[$s]['quantity']!=0)
	{
		$totalitems++;
	}//if
}//for
//$totalitems				=	$resclosingparticulars[0][totalitems];
$adjustquery	=	"SELECT	SUM(adjustment) as adjustment FROM $dbname_detail.sale, $dbname_detail.closinginfo WHERE pkclosingid = fkclosingid AND pkclosingid='$closingsession' AND status='1' ";
$adjustresult	=	$AdminDAO->queryresult($adjustquery);
$adjustment		=	$adjustresult[0]['adjustment'];

//$creditsale		=	($totalsale+$adjustment)-($cashsale+$ccsale+$fcsale+$chequesale+$globaldiscount);
$creditsale		=	($totalsale)-($cashsale+$ccsale+$fcsale+$chequesale+$globaldiscount);
//calculating foreign currency charges added to net cash
$chargesquery	=	"SELECT	SUM(charges) as charges FROM $dbname_detail.payments,$dbname_detail.sale,$dbname_detail.closinginfo WHERE pkclosingid = sale.fkclosingid AND pkclosingid='$closingsession' AND fksaleid = pksaleid AND status = 1  AND paymentmethod = 'fc'  and $dbname_detail.payments.paymenttype<>'c'";

$chargesresult	=	$AdminDAO->queryresult($chargesquery);
$charges		=	$chargesresult[0]['charges'];

$collectionqry="
				SELECT 
					  round((SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) as amount from $dbname_detail.payments cp,$dbname_detail.sale WHERE pksaleid=fksaleid AND status='1'  AND paymentmethod = 'c' AND  cp.fkclosingid='$closingsession' and cp.paymenttype='c'),2) as cashcollect
					  
					  ,round((SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) as ccamount from $dbname_detail.payments ccp,$dbname_detail.sale WHERE pksaleid=fksaleid AND status='1'  AND paymentmethod = 'cc' AND   ccp.fkclosingid='$closingsession'  and ccp.paymenttype='c'),2) as cccollect
					 
					  ,round((SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) as chamount from $dbname_detail.payments cqp,$dbname_detail.sale WHERE pksaleid=fksaleid AND status='1'  AND paymentmethod = 'ch' AND cqp.fkclosingid='$closingsession'  and cqp.paymenttype='c'),2) as chequecollect
					 
					 ,round((SELECT IF(SUM(amount*rate) IS NULL,0,SUM(amount*rate)) as fcamount from $dbname_detail.payments fcp,$dbname_detail.sale WHERE pksaleid=fksaleid AND status='1'  AND paymentmethod = 'fc' AND  fcp.fkclosingid='$closingsession'  and fcp.paymenttype='c'),2) as fccollect
					
									 from $dbname_detail.closinginfo where pkclosingid='$closingsession'

";
$rescollections		=	$AdminDAO->queryresult($collectionqry);
$cashcollect			=	$rescollections[0]['cashcollect'];
$cccollect				=	$rescollections[0]['cccollect'];
$fccollect				=	$rescollections[0]['fccollect'];
$chequecollect			=	$rescollections[0]['chequecollect'];

//$netcash		=	($cashsale + $fcsale + $openingbalance+$charges) - $adjustment - $totalpayouts;
// ** Discount is not needed here because the amount collected is CASH and hence nothing needs to be subtracted to calculate Net Cash */
$netcash		=	($cashsale + $fcsale + $openingbalance+$charges) - ($totalpayouts);

// added by Yasir 23-06-11 -- add collections in net cash
$netcash	=	$netcash + $cashcollect + $fccollect;
//

$cashdifference	=	$declaredamount - $netcash;
$closingstatus	=	'a';


$payouts	=	$AdminDAO->getrows("$dbname_detail.accountpayment","round(SUM(amount),2) as payamount","fkclosingid='$closingsession'");

$payout		=	$payouts[0]['payamount'];

$fields			=	array('lastclosingbalance','closingdate','cashsale','creditsale','creditcardsale','chequesale','foreigncurrencysale','netcash','payout','declaredamount','cashdiffirence','totalbills','totalsale','totalitems','closingstatus','totalcheques','declaredchequeamount','cashcollect','cccollect','fccollect','chequecollect');
$values			=	array($lastclosingbalance,$closingdate,$cashsale,$creditsale,$ccsale,$chequesale,$fcsale,$netcash,$payout,$declaredamount,$cashdifference,$totalbills,$totalsale,$totalitems,$closingstatus,$totalcheques,$chequeamount,$cashcollect,$cccollect,$fccollect,$chequecollect);
$AdminDAO->updaterow("$dbname_detail.closinginfo",$fields,$values,"pkclosingid='$closingsession'");

// accounts posting
if ($cashdifference > 0){
	$AdminDAO->posttransaction($cashacc,$closingsession,$cashdifference,$diffincashacc,$closingsession,$cashdifference,"Extra in Closing"); 
}

if ($cashdifference < 0){
	$AdminDAO->posttransaction($diffincashacc,$closingsession,$cashdifference,$cashacc,$closingsession,$cashdifference,"Short in Closing"); 
}
//
$key	=	md5("kstr");
file_get_contents("http://smk.esajee.com/sendmail.php?id=$closingsession&pm=$key");
?>