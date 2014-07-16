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
 $q = "select * from $dbname_detail.purchase_return where pkpurchasereturnid = '$idarr[1]'";
$rres = mysql_query($q);
$r=mysql_fetch_array($rres);
    $status			=	$r['status'];
	$supplierid			=	$r['fksupplierid'];
	$invdate			=	$r['addtime'];
	$inid			    =	$r['pkpurchasereturnid'];
	$billnumber			=	urlencode($r['remarks']);	

if($status == 1 and $return_status=='close')
{
	 
	echo $msq= 'Status Allready Closed';
	exit;
	
}else  if($status == 0 and $return_status=='open')
	{
	echo $msq= 'Status Allready Open';
	exit;
	}else if($status == 1 and $return_status=='open'){
		 echo  $msq= 'Status Changed From Closed To Open';
		 
	$update= mysql_query("update  $dbname_detail.purchase_return set status=0 , reopen_by='$emp' , reopen_time='".time()."' where  pkpurchasereturnid = '$idarr[1]' ");
	      $checkdata=$invoice_link= file_get_contents($Url_admin."return_status&pid={$idarr[1]}&location=0");
	 if($checkdata > 0){
	    
	      $sub = "Invoice # ($idarr[1]) Status Changed by ( $emp ) From Closed To Open and send to accounts ";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Purchase Return Open Alert',1888,$emp,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
	 }else{
          $sub = "Invoice # ($idarr[1]) Status Changed by ( $emp ) From Closed To Open and not send to accounts ";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Purchase Return Open Alert',1888,$emp,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		 
		 
		 }
exit;
			}

	
	

	 if($status == 0 and $return_status=='close')
	{
	
 $q22 = "select sum(value) as value from $dbname_detail.purchase_return_detail where fkpurchasereturnid = '$inid'";
$rres22 = mysql_query($q22);
$r22=mysql_fetch_array($rres22);
$invoice_amount	=	$r22['value'];
	$st= '1';
	 $update= mysql_query("update  $dbname_detail.purchase_return set status = '$st' , closedby='$emp' , closedtime='".time()."'  where  pkpurchasereturnid = '$idarr[1]' ");
	 $msq= 'Status Changed From Open To Closed';
$checkdata=$invoice_link= file_get_contents($Url_admin."invoice_return&fksupplierid={$supplierid}&fksupplierinvoiceid=&return={$invoice_amount}&billnumber={$billnumber}&adddate={$invdate}&pid={$idarr[1]}&location=0");
 
//////////////////////////////////////////////////////Stock Operation////////////////////////////////////////////////////////////////////////////////////////////	
$adjstk = mysql_query("select fkbarcodeid,quantity from $dbname_detail.purchase_return_detail where fkpurchasereturnid = '$inid'");
while($rowstk=mysql_fetch_array($adjstk)){
    $qret=$rowstk['quantity'];
	$fkid=$rowstk['fkbarcodeid'];
	$upq=mysql_query("UPDATE $dbname_detail.stock SET unitsremaining=(unitsremaining-$qret) where fkbarcodeid='$fkid' ORDER BY pkstockid DESC LIMIT 1");
	}	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	}
	
                 $addressbookid= 1888;
                 $fkemployeeid=1887;
	if($checkdata>0){
		   mysql_query("update  $dbname_detail.purchase_return set accdatasent=1 where  pkpurchasereturnid = '$idarr[1]' ");
		
		
		  $sub = "Invoice Amount  ($invoice_amount),Supplier Id ($supplierid) ,bill no ($billnumber) is returned and sent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Purchase return without invoices Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		
		 }else{
			 
	      $sub = "Invoice Amount  ($invoice_amount),Supplier Id ($supplierid) ,bill no ($billnumber) is returned and not sent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Purchase return without invoices Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
			 } 

	
		//echo "update  dbname_detail.supplierinvoice set invoice_status = '$st' where  pksupplierinvoiceid = '$idarr[1]' ";
		
	 
     echo $msq;


?>