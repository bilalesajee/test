<?php
session_start();
include("includes/security/adminsecurity.php");
global $AdminDAO;
$fromDate	=	strtotime($_POST['fromDate']);
$toDate		=	strtotime($_POST['toDate']);
$itemname	=	trim(filter($_REQUEST['mysearchString'])," ");
/****************************PRODUCT DATA*****************************/
 $sql="SELECT 
			bc.pkbarcodeid,
			bc.barcode,
			bc.itemdescription,
			sd.quantity,
			sd.saleprice,
			sd.timestamp,
			sd.fksaleid 
		FROM 
			$dbname_detail.saledetail sd,
			$dbname_detail.stock s,
			barcode bc ,
			$dbname_detail.sale sl
		WHERE 
			sl.status='1' AND
			sl.pksaleid=sd.fksaleid and 
			s.fkbarcodeid=bc.pkbarcodeid AND
			s.pkstockid=sd.fkstockid AND
			
			bc.itemdescription LIKE '%$itemname%' LIMIT 0,100";
			
/*$sql=" SELECT CONCAT( firstname,' ', lastname,' (',nic,')') as customername, pkcustomerid
			FROM $dbname_detail.customer, $dbname_main.addressbook
			WHERE fkaddressbookid = pkaddressbookid
			HAVING customername LIKE '%$customername%'
	";*/
			
if($itemname!='')
{
	$item_array	=	$AdminDAO->queryresult($sql);
	//$barcode_array		=	$AdminDAO->getrows('barcode,product','*',"`fkproductid`=`pkproductid` AND `productname` LIKE '%$productname%' group by barcode");
	for($a=0;$a<count($item_array);$a++)
	{
		
		$pkbarcodeid	=	$item_array[$a]['pkbarcodeid'];
		$barcode		=	$item_array[$a]['barcode'];
		$itemdescription=	$item_array[$a]['itemdescription'];
		$quantity		=	$item_array[$a]['quantity'];
		$saleprice		=	$item_array[$a]['saleprice'];
		$timestamp		= date("d-M-Y",$item_array[$a]['timestamp']);
		$fksaleid		=	$item_array[$a]['fksaleid'];
		
		echo '<li onClick="fill(\''.$fksaleid.':'.$itemdescription.'\');"><font color=blue>'.$barcode.'</font> '.$itemdescription.' <br><font color=red>'.$quantity.' x '.$saleprice.'='.$quantity*$saleprice.'</font> <font color=green>'.$timestamp.' </font></li>';
		//echo "$itemdescription|$fksaleid\n";
	}
}
?>