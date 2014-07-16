<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$searchfield	=	trim(filter($_REQUEST['q'])," ");
$id				=	$_GET['id'];
$stype			=	$_GET['stype'];
$barcodesearch	=	0;
switch($stype)
{
	case 1://product search
	$table		=	'product';
	$field		=	'productname';
	$fieldid	=	'pkproductid';
	break;
	case 2://brand search
	$table		=	'brand';
	$field		=	'brandname';
	$fieldid	=	'pkbrandid';
	break;
	case 3://supplier search
	$table		=	'supplier';
	$field		=	'companyname';
	$fieldid	=	'pksupplierid';
	break;
	case 4://country search
	$table		=	'countries';
	$field		=	'code3';
	$fieldid	=	'pkcountryid';
	break;
	case 5://shipment search
	$table		=	'shipment';
	$field		=	'shipmentname';
	$fieldid	=	'pkshipmentid';
	break;
	case 6://Item name search
	$table		=	'barcode';
	$field		=	'itemdescription';
	$fieldid	=	'barcode';
	break;
	case 7://Barcode search
	$barcodesearch	=	1;
	//$table		=	'barcode';
	$field		=	'barcode';
	//$fieldid	=	'barcode';
	break;
}
/****************************PRODUCT DATA*****************************/
if($barcodesearch==1)
{
	echo "$searchfield|$searchfield|7\n";
}
else
{
	$sql=" SELECT $fieldid,$field
			FROM $table
				WHERE
					$field LIKE '%$searchfield%'
				";			
	if($searchfield!='')
	{
		$result_array	=	$AdminDAO->queryresult($sql);
		for($i=0;$i<count($result_array);$i++)
		{
			$fname		=	html_entity_decode($result_array[$i][$field]);
			$fid		=	$result_array[$i][$fieldid];
			echo "$fname|$fid|$stype\n";
		}
	}
}
// new format
/*if($searchfield!='')
{
	$result_array	=	$AdminDAO->queryresult($sql);
	for($i=0;$i<sizeof($result_array);$i++)
	{
		$arr[]	=	array("id"=>$result_array[$i][$fieldid],"name"=>$result_array[$i][$field]);
	}
	echo json_encode($arr);
}*/
?>