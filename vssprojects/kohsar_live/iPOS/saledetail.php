<?php 
require_once("includes/security/adminsecurity2.php");

// returns different credit cards and their respective totals for specific saleid
function ccbytype($closingsession)
{
	global $AdminDAO,$dbname_detail;
	$ccdata		=	$AdminDAO->getrows("$dbname_detail.payments LEFT JOIN cctype ON fkcctypeid=pkcctypeid "," SUM(amount) as amount, typename,pkcctypeid "," fkclosingid='$closingsession' AND paymentmethod='cc' GROUP by pkcctypeid ");
	return $ccdata;
}
// returns different foreign currency and their respective totals for specific saleid
function fcbycurrency($closingsession)
{
	global $AdminDAO,$dbname_detail;
	$fcdata		=	$AdminDAO->getrows("$dbname_detail.payments,currency","SUM(fcamount) as amount, currencyname,currencysymbol,pkcurrencyid","fkclosingid='$closingsession' AND fkcurrencyid=pkcurrencyid AND paymentmethod='fc' GROUP by pkcurrencyid");
	return $fcdata;
}
// returns different credit cards and their respective totals for specific saleid
function chequebybank($closingsession)
{
	global $AdminDAO,$dbname_detail;
	$chdata		=	$AdminDAO->getrows("$dbname_detail.payments LEFT JOIN bank ON fkbankid=pkbankid","SUM(amount) as amount, bankname,pkbankid","fkclosingid='$closingsession' AND paymentmethod='ch' GROUP by pkbankid");
	return $chdata;
}
/*********************************************************************/
//function written by Rizwan Abbas (Used On the codeitem php for payment details.)

// returns different credit cards and their respective totals for specific saleid
function ccpaymentbytype($saleid)
{
	global $AdminDAO,$dbname_detail;
	$ccdata		=	$AdminDAO->getrows("$dbname_detail.payments LEFT JOIN cctype ON fkcctypeid=pkcctypeid "," amount , typename,pkcctypeid,ccno "," fksaleid='$saleid' AND paymentmethod='cc'");
	return $ccdata;
}
// returns different foreign currency and their respective totals for specific saleid
function fcpaymentbycurrency($saleid)
{
	global $AdminDAO,$dbname_detail;
	$fcdata		=	$AdminDAO->getrows("$dbname_detail.payments LEFT JOIN currency ON fkcurrencyid=pkcurrencyid","fcamount amount, currencyname,currencysymbol,pkcurrencyid","fksaleid='$saleid' AND paymentmethod='fc' AND paymenttype <> 'c' "); //  AND paymenttype <> 'c'  added by Yasir -- 06-07-11
	return $fcdata;
}

// returns different credit cards and their respective totals for specific saleid
function chequepaymentbybank($saleid)
{
	global $AdminDAO,$dbname_detail;
	$chdata		=	$AdminDAO->getrows("$dbname_detail.payments LEFT JOIN bank ON fkbankid=pkbankid","amount, bankname,pkbankid,chequeno","fksaleid='$saleid' AND paymentmethod='ch'");
	return $chdata;
}

?>