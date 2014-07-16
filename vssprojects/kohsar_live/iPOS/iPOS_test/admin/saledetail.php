<?php 
require_once("../includes/security/adminsecurity.php");

// returns different credit cards and their respective totals for specific saleid
function ccbytype($closingsession)
{
	global $AdminDAO,$dbname_detail;
	$ccdata		=	$AdminDAO->getrows("$dbname_detail.ccpayment LEFT JOIN cctype ON fkcctypeid=pkcctypeid "," SUM(amount) as amount, typename,pkcctypeid "," fkclosingid='$closingsession' GROUP by pkcctypeid ");
	return $ccdata;
}
// returns different foreign currency and their respective totals for specific saleid
function fcbycurrency($closingsession)
{
	global $AdminDAO,$dbname_detail;
	$fcdata		=	$AdminDAO->getrows("$dbname_detail.fcpayment,currency","SUM(amount) as amount, currencyname,currencysymbol,pkcurrencyid","fkclosingid='$closingsession' AND fkcurrencyid=pkcurrencyid GROUP by pkcurrencyid");
	return $fcdata;
}
// returns different credit cards and their respective totals for specific saleid
function chequebybank($closingsession)
{
	global $AdminDAO,$dbname_detail;
	$chdata		=	$AdminDAO->getrows("$dbname_detail.chequepayment LEFT JOIN bank ON fkbankid=pkbankid","SUM(amount) as amount, bankname,pkbankid","fkclosingid='$closingsession' GROUP by pkbankid");
	return $chdata;
}
/*********************************************************************/
//function written by Rizwan Abbas (Used On the codeitem php for payment details.)

// returns different credit cards and their respective totals for specific saleid
function ccpaymentbytype($saleid)
{
	global $AdminDAO,$dbname_detail;
	$ccdata		=	$AdminDAO->getrows("$dbname_detail.ccpayment LEFT JOIN cctype ON fkcctypeid=pkcctypeid "," amount , typename,pkcctypeid,ccno "," fksaleid='$saleid' ");
	return $ccdata;
}
// returns different foreign currency and their respective totals for specific saleid
function fcpaymentbycurrency($saleid)
{
	global $AdminDAO,$dbname_detail;
	$fcdata		=	$AdminDAO->getrows("$dbname_detail.fcpayment LEFT JOIN currency ON fkcurrencyid=pkcurrencyid","amount, currencyname,currencysymbol,pkcurrencyid","fksaleid='$saleid' ");
	return $fcdata;
}

// returns different credit cards and their respective totals for specific saleid
function chequepaymentbybank($saleid)
{
	global $AdminDAO,$dbname_detail;
	$chdata		=	$AdminDAO->getrows("$dbname_detail.chequepayment LEFT JOIN bank ON fkbankid=pkbankid","amount, bankname,pkbankid,chequeno","fksaleid='$saleid'");
	return $chdata;
}

?>