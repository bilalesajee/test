<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$suppliername	=	trim(filter($_GET['q'])," ");
$id				=	$_GET['id'];
$ccid			=	$_GET['cid'];
/****************************PRODUCT DATA*****************************/
$sql=" SELECT pksupplierid,companyname
		FROM supplier
			WHERE
				companyname LIKE '%$suppliername%'
			";			
if($suppliername!='')
{
	$supplier_array	=	$AdminDAO->queryresult($sql);
	for($a=0;$a<count($supplier_array);$a++)
	{
		$sname		=	html_entity_decode($supplier_array[$a]['companyname']);
		$sid		=	$supplier_array[$a]['pksupplierid'];
		echo "$sname|typesupplier|$sid|$ccid\n";
	}
}
?>