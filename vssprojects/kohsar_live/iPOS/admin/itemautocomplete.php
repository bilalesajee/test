<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$productname	=	trim(filter($_REQUEST['q'])," ");
/****************************PRODUCT DATA*****************************/
$sql=" 		SELECT 	
				itemdescription, 
				barcode,
				pkbarcodeid
			FROM 
				barcode 
			WHERE
				itemdescription LIKE '%$productname%' 
			";
if($productname!='')
{
	$barcode_array	=	$AdminDAO->queryresult($sql);
	for($a=0;$a<count($barcode_array);$a++)
	{
		$itemdescription	=	$barcode_array[$a]['itemdescription'];
		$pkbarcodeid		=	$barcode_array[$a]['pkbarcodeid'];
		$barcode			=	$barcode_array[$a]['barcode'];
		echo "$itemdescription|$barcode|$pkbarcodeid\n";
	}
}
?>