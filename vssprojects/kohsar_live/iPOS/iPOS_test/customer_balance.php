<?php
session_start();
$_SESSION['SERVER_ACC_ONLINEb']=1;
include_once("surl.php");
$customer = (int)$_GET['customer'];
/*if($_SESSION['SERVER_ACC_ONLINEb']==1){
  $cc= file_get_contents($serverUrl_.$customer);

  $cust_total= file_get_contents($serverUrl_total.$customer);
  $cust_totale = json_decode($cust_total, true);

 echo $cust_total=$cust_totale['sale'] - $cust_totale['dicount'] -  $cust_totale['totalpaid'];


}
//var_dump($cc);
	if($_SESSION['SERVER_ACC_ONLINEb']!=1){*/
		echo "Balance Not Available";
		
		
//	}
	
		
		
?>