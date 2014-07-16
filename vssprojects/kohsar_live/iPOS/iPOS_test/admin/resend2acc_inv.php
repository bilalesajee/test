<?php
include_once("../includes/security/adminsecurity.php");
include_once("../surl.php");
$id	=	$_REQUEST['ids'];
$idarr	= 	explode(",", $id);
$emp=$_SESSION['addressbookid'];

$q = "select invoice_status,accdatasent from $dbname_detail.supplierinvoice where pksupplierinvoiceid = '$idarr[1]'";
$rres = mysql_query($q);
$r=mysql_fetch_array($rres);
$status			=	$r['invoice_status'];
$checkacc_sent=	$r['accdatasent'];

if($status == 1 and $checkacc_sent==1)
{
	echo $msq= 'Allready Sent';
	exit;
}else  if($status == 1 and $checkacc_sent==0){

	         $msq= 'Invoice Resent to Accounts';
	         $query_invoice			=	"SELECT SUM( s.quantity * s.priceinrs ) AS invoice_amount,sup.fksupplierid as supplierid,s.fksupplierinvoiceid as invoiceid,sup.billnumber,sup.datetime

              FROM $dbname_detail.stock s

			  left join $dbname_detail.supplierinvoice sup  on sup.pksupplierinvoiceid = s.fksupplierinvoiceid

              WHERE s.fksupplierinvoiceid = '$idarr[1]'";

             $result_invoice		=	$AdminDAO->queryresult($query_invoice);
   


             $invoice_amount			=	round($result_invoice[0]['invoice_amount'],2);
			  if($invoice_amount > 0){
			    $supplierid			=	$result_invoice[0]['supplierid'];
			    $invoiceid			=	$result_invoice[0]['invoiceid'];
			    $billnumber			=	$result_invoice[0]['billnumber'];
				$invdate			=	date('d-m-Y', $result_invoice[0]['datetime']);
				
                 $addressbookid= 1888;
                 $fkemployeeid=1887;
 $billnumber=urlencode($billnumber);
$checkdata=$invoice_link= file_get_contents($Url_admin."invoice_voucher&invoiceid={$invoiceid}&supplierid={$supplierid}&invoice_amount={$invoice_amount}&billnumber={$billnumber}&invdate={$invdate}&location=0");

////////////////////////////////////Get supplier/////////////////////////////////////////////////////////////////////////////////////////////////////////	  
                $query_inv="SELECT companyname FROM main.supplier WHERE pksupplierid = '$supplierid'";
                $result_inv		=	$AdminDAO->queryresult($query_inv);
                $companyname_		=	$result_inv[0]['companyname'];
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			 	  

 if($checkdata>0){
		  mysql_query("update  $dbname_detail.supplierinvoice set accdatasent = 1  where  pksupplierinvoiceid = '$invoiceid' ");
		
		
		  $sub = "Invoice # ($invoiceid ),Supplier($companyname_) ,bill no ($billnumber) is rsent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Invoice Resend Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		
		 }else{
		
	      $sub = "Invoice # ($invoiceid ),Supplier($companyname_) ,bill no ($billnumber) is not rsent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Invoice Resend Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
			$msq='Error Invoice Has Not Sent to Accounts';  
			 } 

			  }else{
				$msq='Error Invoice Has No Stock';  
				  }

	}else{
		 $msq= 'Please Close Invoice';
		}
     echo $msq;
?>