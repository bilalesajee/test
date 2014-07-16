<?php
session_start();
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;

$id			=	$_REQUEST['id'];

$fields		=	array('defaultcurrency');
$values		=	array('1');
$values_old	=	array('0');
$AdminDAO->updaterow("currency",$fields,$values_old,"pkcurrencyid!='$id'");
$AdminDAO->updaterow("currency",$fields,$values,"pkcurrencyid='$id'");
?>
<script language="javascript">
	jQuery('#maindiv').load('managecurrencies.php');
</script>