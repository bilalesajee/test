<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$productname	=	trim(filter($_REQUEST['q'])," ");
$id				=	$_REQUEST['id'];
/****************************PRODUCT DATA*****************************/
$sql=" SELECT itemdescription , 
				barcode as bc,
				pkbarcodeid,
				fkproductid
			FROM 
				barcode 
			WHERE
				itemdescription LIKE '%$productname%' 
			";
if($productname!='')
{
	$barcode_array	=	$AdminDAO->queryresult($sql);
	//$barcode_array		=	$AdminDAO->getrows('barcode,product','*',"`fkproductid`=`pkproductid` AND `productname` LIKE '%$productname%' group by barcode");
	for($a=0;$a<count($barcode_array);$a++)
	{
		$barcode		=	$barcode_array[$a]['bc'];
		$productname	=	$barcode_array[$a]['itemdescription'];
		$id				=	$barcode_array[$a]['pkbarcodeid'];
		$fkproductid	=	$barcode_array[$a]['fkproductid'];
		echo "$productname|typebarcode|$barcode|$id|$fkproductid\n";
	}
}
?>