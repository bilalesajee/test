<?php
$customer=$_REQUEST['customer'];
$typ=$_REQUEST['type'];
if($typ=='c'){
echo file_get_contents(' http://210.2.171.14/accounts/customer_balance.php?type=c&customer='.$customer);	
	}else{
//echo file_get_contents('http://210.2.171.42:200/accounts/create_receipt.php?customerid='.$customer);		
		}



?>