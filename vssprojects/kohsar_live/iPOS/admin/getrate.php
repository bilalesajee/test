<?php 

include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['cid'];
//id is now the countryid
$curid	=	$AdminDAO->getrows("currency","*"," fkcountryid = '$id'");
$rate	=	$curid[0]['rate'];
$symbol	=	$curid[0]['currencysymbol'];
echo $rate."_".$symbol;
?>