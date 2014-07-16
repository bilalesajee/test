<?php
include_once("../includes/security/adminsecurity.php");
include_once("../surl.php");
$id	=	$_REQUEST['ids'];
$idarr	= 	explode(",", $id);
$emp=$_SESSION['addressbookid'];
if($idarr[1]==''){
	echo $msq= 'Plz select any row';
	exit;
}
 
 $q = "select * from $dbname_detail.purchase_return where pkpurchasereturnid = '$idarr[1]'";
 $rres = mysql_query($q);
 $r=mysql_fetch_array($rres);
 $status			=	$r['status'];
 $chkacc			=	$r['accdatasent'];

 if($status == 1 and $chkacc==1){
	
	echo $msq= 'Already Sent';
	exit;
}else  if($status == 1  and $chkacc==0){

	$supplierid			=	$r['fksupplierid'];
	$invdate			=	$r['addtime'];
	$inid			    =	$r['pkpurchasereturnid'];
	$billnumber			=	urlencode($r['remarks']);	
	
 $q22 = "select sum(value) as value from $dbname_detail.purchase_return_detail where fkpurchasereturnid = '$inid'";
 $rres22 = mysql_query($q22);
 $r22=mysql_fetch_array($rres22);
 $invoice_amount	=	$r22['value'];
 $msq= 'Invoice Resent';
 $checkdata=$invoice_link= file_get_contents($Url_admin."invoice_return&fksupplierid={$supplierid}&fksupplierinvoiceid=&return={$invoice_amount}&billnumber={$billnumber}&adddate={$invdate}&location=0");
 
                 $addressbookid= 1888;
                 $fkemployeeid=1887;
	if($checkdata>0){
		   mysql_query("update  $dbname_detail.purchase_return set accdatasent=1 where  pkpurchasereturnid = '$idarr[1]' ");
		
		
		  $sub = "Invoice Amount  ($invoice_amount),Supplier Id ($supplierid) ,bill no ($billnumber) is rsent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Purchase return without invoices Resent Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		
		 }else{
			 
	      $sub = "Invoice Amount  ($invoice_amount),Supplier Id ($supplierid) ,bill no ($billnumber) is  not rsent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Purchase return without invoices Resent Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		$msq= 'Error not sent ';
		
			 } 
	

	}else{
		
		$msq= 'Please Mark As Close First';
		}
	
     echo $msq;
?>