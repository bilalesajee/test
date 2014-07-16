<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO;
$productname	=	trim(filter($_REQUEST['q'])," ");
/****************************PRODUCT DATA*****************************/
$sql=" SELECT 	productname , 
				pkproductid
				
			FROM 
				product 
			WHERE
				productname LIKE '%$productname%' LIMIT 0,50
			";
if($productname!='')
{
	$barcode_array	=	$AdminDAO->queryresult($sql);
	//$barcode_array		=	$AdminDAO->getrows('barcode,product','*',"`fkproductid`=`pkproductid` AND `productname` LIKE '%$productname%' group by barcode");
	for($a=0;$a<count($barcode_array);$a++)
	{
		$pkproductid		=	$barcode_array[$a]['pkproductid'];
		$productname	=	$barcode_array[$a]['productname'];
		echo "$productname|$pkproductid\n";
	}
}
?>
