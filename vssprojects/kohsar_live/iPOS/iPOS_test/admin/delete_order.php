<?php
/*//error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;


	

		
		 $pkpurchasereturndetailid = $_REQUEST['pkpurchasereturndetailid'];
		 $delete= mysql_query("delete from $dbname_detail.purchase_return_detail where pkpurchasereturndetailid='$pkpurchasereturndetailid' ");
		
		 // $accounttitle = $_REQUEST['accounttitle']; 
		 
	 // $reasons = $_REQUEST['reasons']; 
	// echo  $pkaccountpaymentid = $_REQUEST['pkaccountpaymentid']; 
	

echo 'yes';*/
   
?>
<?php
//error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;



	

		
	
	
		/*if($_POST['tradeprice_'.$i]	== 0 || $_POST['tradeprice_'.$i] == '')
		{
			continue;
		}*/
		
		 $stockid = $_POST['pkpurchaseorderdetailid'];
		/*echo  $_POST['newstock_'.$i]; echo "-----";
		echo  $_POST['tradeprice_'.$i];echo "-----";
		echo  $_POST['retailprice_'.$i];echo "-----";
		 exit;*/
		
  //$delete= mysql_query("delete from $dbname_detail.purchase_order_detail where pkpurchaseorderdetailid='$stockid' ");
	$AdminDAO->deleterows("$dbname_detail.purchase_order_detail","`pkpurchaseorderdetailid`='$stockid'",1);	

echo 'yes';
		
	
/*if($val != '' and is_integer($val))
{
	$rep = " retailprice='{$val}' ";
}

$q = " update stock set qty = $qty {$rep} {$trad} where pkstockid = $stockid ";*/


	
	

   
			
	

?>