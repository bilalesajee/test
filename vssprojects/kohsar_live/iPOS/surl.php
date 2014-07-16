<?php
ob_start();
error_reporting(7);
session_start();
$empid				=	$_SESSION['addressbookid'];
///////////////////////////////////////////////////////////////////////////////////
/////////////////////////////Accounts System Url///////////////////////////////////
$serverUrl_='http://210.2.171.14/accounts/customer_balance.php?type=c&customer=';
$serverUrl_send='http://210.2.171.14/accounts/create_receipt.php?customerid=';
$Url_admin='http://210.2.171.14/accounts/pos_common_entry.php?username='.$empid.'&type=';
$Url_admin_test='http://210.2.171.14/accounts_test/pos_common_entry.php?type=';
///////////////////////////////////////kohsar server Url///////////////////////
$serverUrl_total='http://192.168.10.110/admin/accounts/get_total.php?customerid=';
/////////////////////////////////////////////////////////////////////////////////
$customerUrl_="https://main.esajee.com/admin/addremotecustomer.php?get=";

?>