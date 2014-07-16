<?php
include_once("../security/adminsecurity.php");
global $AdminDAO,$Component;
/***********************************************SUPPLIER***********************************/
$countryid			=	$_GET['id'];
$suppliers_array	=	$AdminDAO->getrows('supplieroragent','pksupplierid,suppliername',"fkcountryid='$countryid'");
$selected_suppliers	=	array(1,3);
$suppliers			=	$Component->makeComponent('d','suppliers[]',$suppliers_array,'pksupplierid','suppliername',10,$selected_suppliers);
print"$suppliers";
?>