<?php
session_start();
include("includes/security/adminsecurity.php");
global $AdminDAO;
$saleid	=	$_SESSION['tempsaleid'];
$customername	=	trim(filter($_REQUEST['q'])," ");

$loc	=	trim(filter($_REQUEST['loc'])," ");
/****************************PRODUCT DATA*****************************/
$sql=" SELECT CONCAT( firstname,' ', lastname,' (',nic,')') as customername,  pkcustomerid,taxable
			FROM customer
			WHERE		ctype=2 and location='$loc'	and isdeleted <> 1	
			HAVING customername LIKE '%$customername%'";
if($customername!='')
{
	$customer_array	=	$AdminDAO->queryresult($sql);
	//$barcode_array		=	$AdminDAO->getrows('barcode,product','*',"`fkproductid`=`pkproductid` AND `productname` LIKE '%$productname%' group by barcode");
	for($a=0;$a<count($customer_array);$a++)
	{
		$customername	=	$customer_array[$a]['customername'];
		$id				=	$customer_array[$a]['pkcustomerid'];
		$taxable		=	$customer_array[$a]['taxable'];
		if($taxable==1)
		{
			//selecting total sale amount and then calculating taxes
			$squery			=	$AdminDAO->getrows("$dbname_detail.saledetail","sum(saleprice*quantity) as taxamount","fksaleid='$saleid'");
			$saleamount		=	$squery[0]['taxamount'];
			$taxpercentage	=	$AdminDAO->getrows("$dbname_detail.gst","amount","1 ORDER BY pkgstid DESC LIMIT 0,1");
			$salestax		=	$taxpercentage[0]['amount'];
			$totaltax		=	$saleamount*($salestax/100);
		}
		echo "$customername|$id|$totaltax\n";
	}
}
?>