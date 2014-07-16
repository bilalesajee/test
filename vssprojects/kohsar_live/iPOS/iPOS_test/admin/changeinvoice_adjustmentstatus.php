<?php
include_once("../includes/security/adminsecurity.php");
include_once("../surl.php");
$id	=	$_REQUEST['ids'];
$idarr	= 	explode(",", $id);
$return_status	=	$_REQUEST['return_status'];
$emp=$_SESSION['addressbookid'];

if($idarr[1]=='')
{
	
	echo $msq= 'Plz select any row';
	exit;
	
}



/*echo "<pre>";
print_r($_GET);
echo "</pre>";*/
 $q = "select * from $dbname_detail.adjustment where pkadjustmentid = '$idarr[1]'";
$rres = mysql_query($q);
$r=mysql_fetch_array($rres);
$status			=	$r['status'];

if($status == 1 and $return_status=='close')
{
	
	echo $msq= 'Status Allready Closed';
	exit;
	
}else  if($status == 0 and $invoice_status=='open')
	{
	echo $msq= 'Status Allready Open';
	exit;
	}
	
	

	 if($status == 0)
	{
	$supplierid			=	$r['fksupplierid'];
	$invdate			=	$r['addtime'];
	$inid			    =	$r['pkadjustmentid'];
	$billnumber			=	urlencode($r['remarks']);	
	
 $q22 = "select sum(value) as value from $dbname_detail.adjustment_detail where fkadjustmentid = '$inid'";
$rres22 = mysql_query($q22);
$r22=mysql_fetch_array($rres22);
$invoice_amount	=	$r22['value'];
	$st= '1';
	 $update= mysql_query("update  $dbname_detail.adjustment set status = '$st' , closedby='$emp' , closedtime='".time()."'  where  pkadjustmentid = '$idarr[1]' ");
	 $msq= 'Status Changed From Open To Closed';
$checkdata=$invoice_link= file_get_contents($Url_admin."invoice_return&fksupplierid={$supplierid}&fksupplierinvoiceid=&return={$invoice_amount}&billnumber={$billnumber}&adddate={$invdate}&location=0");
 
	}
	
	
		//echo "update  dbname_detail.supplierinvoice set invoice_status = '$st' where  pksupplierinvoiceid = '$idarr[1]' ";
		
	 
     echo $msq;


?>