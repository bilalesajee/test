<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$brandname	=	trim(filter($_REQUEST['q'])," ");
/****************************PRODUCT DATA*****************************/
$sql=" SELECT 	pkbrandid ,	
				brandname 	
				
			FROM 
				brand 
			WHERE
				brandname LIKE '%$brandname%' LIMIT 0,50
			";
if($brandname!='')
{
	$brand_array	=	$AdminDAO->queryresult($sql);
	//$barcode_array		=	$AdminDAO->getrows('barcode,product','*',"`fkproductid`=`pkproductid` AND `productname` LIKE '%$productname%' group by barcode");
	for($a=0;$a<count($brand_array);$a++)
	{
		$pkbrandid		=	$brand_array[$a]['pkbrandid'];
		$brandname	=	$brand_array[$a]['brandname'];
		echo "$brandname|$pkbrandid\n";
	}
}
?>
