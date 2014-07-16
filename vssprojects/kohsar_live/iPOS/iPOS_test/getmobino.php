<?php
include("includes/security/adminsecurity.php");
global $AdminDAO,$userSecurity;
$query="SELECT mobile from main.customer where pkcustomerid='".$_REQUEST['mcode']."' and  location=3 ";
$resultarr		=	$AdminDAO->queryresult($query);
echo $resultarr[0]['mobile'];
?>