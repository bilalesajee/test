<?php
include_once("includes/security/adminsecurity.php");
global $AdminDAO;

$bc	=	 $_GET['bc'];

$barcode_array		=	$AdminDAO->getrows('barcode','pkbarcodeid',"`barcode`='$bc'");
//print_r($barcode_array);
$productname 		=	$barcode_array[0]['productname'];
$productid	 		=	$barcode_array[0]['pkproductid'];
$pkbarcodeid 		=	$barcode_array[0]['pkbarcodeid'];

$pricequery		=	"SELECT	
						price
	    		     FROM
					$dbname_detail.pricechange
					WHERE
					fkbarcodeid	=	'$pkbarcodeid'
					LIMIT 0,1
					";
$priceresult	=	$AdminDAO->queryresult($pricequery);
$priceinrs		=	$priceresult[0]['price'];
if(!$priceinrs)
 {
	  $sqlstk=" SELECT 
			  MAX(priceinrs) as priceinrs 
			  FROM 
			  $dbname_detail.stock
			  WHERE
			  fkbarcodeid	='$pkbarcodeid'
	          ";
	  $stkpricearr	=	$AdminDAO->queryresult($sqlstk);
	  $priceinrs	=	$stkpricearr[0]['priceinrs'];
  }
echo $priceinrs;
?>