<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$customername		=	trim(filter($_REQUEST['q'])," ");
$id				=	$_REQUEST['id'];
/****************************PRODUCT DATA*****************************/

 $sql=" SELECT firstname,lastname FROM `customer` 
				inner join addressbook 
				on pkaddressbookid=fkaddressbookid
			WHERE
				firstname LIKE '%$customername%' 
			";			
			
			
			
if($customername!='')
{
	$barcode_array	=	$AdminDAO->queryresult($sql);
	//$barcode_array		=	$AdminDAO->getrows('barcode,product','*',"`fkproductid`=`pkproductid` AND `productname` LIKE '%$productname%' group by barcode");
	for($a=0;$a<count($barcode_array);$a++)
	{
		/*
		$barcode		=	$barcode_array[$a]['bc'];
		$productname	=	$barcode_array[$a]['itemdescription'];
		$id				=	$barcode_array[$a]['pkbarcodeid'];
		$fkproductid	=	$barcode_array[$a]['fkproductid'];
		*/
		$cname		=	$barcode_array[$a]['firstname'];
		$cname		.=	$barcode_array[$a]['lastname'];
		//echo "$productname|$barcode|$id|$fkproductid\n";
		echo "$cname\n";
	}
}
?>
