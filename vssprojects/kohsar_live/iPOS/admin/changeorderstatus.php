<?php
include_once("../includes/security/adminsecurity.php");
include_once("../surl.php");
$id	=	$_REQUEST['ids'];
$idarr	= 	explode(",", $id);
$return_status	=	$_REQUEST['return_status'];
/*echo "<pre>";
print_r($_GET);
echo "</pre>";*/
 $q = "select status from $dbname_detail.purchase_order where pkpurchaseorderid = '$idarr[1]'";
$rres = mysql_query($q);
$r=mysql_fetch_array($rres);
$status			=	$r['status'];

if($status == 1 and $return_status=='close')
{
	
	echo $msq= 'Status Allready Closed';
	exit;
	
}
	
	
else  if($status == 0 and $invoice_status=='open')
	{
	echo $msq= 'Status Allready Open';
	exit;
	}
	
	

	 if($status == 0)
	{
	$st= '1';
	 $update= mysql_query("update  $dbname_detail.purchase_order set status = '$st' where  pkpurchaseorderid = '$idarr[1]' ");
	 $msq= 'Status Changed From Open To Closed';
	 


	}
	
	
		//echo "update  dbname_detail.supplierinvoice set invoice_status = '$st' where  pksupplierinvoiceid = '$idarr[1]' ";
		
	 
     echo $msq;


?>