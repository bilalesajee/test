<?php ob_start();
session_start();
$_SESSION = array(
    "storeid" => 3,
    "siteconfig" => 2,
    "countername" => -1,
    "siteconfig" => 2,
    "addressbookid" => 40,
    "name" => 'System Admin',
    "admin_section" => 'Admin logged in',
    "groupid" => 6,
    "groupname" => 'Administrator',);
include("../../includes/security/adminsecurity.php");
include_once("../../surl.php");
global $AdminDAO, $Component;

$query_supplieri2 = "select * from $dbname_detail.supplierinvoice where invoice_status=1 and accdatasent=0 and pksupplierinvoiceid > 22921";
$reportresult = $AdminDAO->queryresult($query_supplieri2);

	for($i=0;$i<count($reportresult);$i++)
		{
		
		 $pksupplierinvoiceid=$reportresult[$i]["pksupplierinvoiceid"];
	     //echo "<br>";
		 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		     $query_invoice			=	"SELECT SUM( s.quantity * s.priceinrs ) AS invoice_amount,sup.fksupplierid as supplierid,s.fksupplierinvoiceid as invoiceid,sup.billnumber,sup.datetime

              FROM $dbname_detail.stock s

			  left join $dbname_detail.supplierinvoice sup  on sup.pksupplierinvoiceid = s.fksupplierinvoiceid

              WHERE s.fksupplierinvoiceid = '$pksupplierinvoiceid'";

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
echo $checkdata=$invoice_link= file_get_contents($Url_admin."invoice_voucher&invoiceid={$invoiceid}&supplierid={$supplierid}&invoice_amount={$invoice_amount}&billnumber={$billnumber}&invdate={$invdate}&location=0");

////////////////////////////////////Get supplier/////////////////////////////////////////////////////////////////////////////////////////////////////////	  
                $query_inv="SELECT companyname FROM main.supplier WHERE pksupplierid = '$supplierid'";
                $result_inv		=	$AdminDAO->queryresult($query_inv);
                $companyname_		=	$result_inv[0]['companyname'];
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			 	  

 if($checkdata>0){
		  mysql_query("update  $dbname_detail.supplierinvoice set accdatasent = 1  where  pksupplierinvoiceid = '$invoiceid'");
		
		
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
     $q = "select * from $dbname_detail.purchase_return where status=1 and accdatasent=0";
     $rres = mysql_query($q);
     $r=mysql_fetch_array($rres); 
	 $supplierid			=	$r['fksupplierid'];
	 $invdate			    =	$r['addtime'];
	 $inid			        =	$r['pkpurchasereturnid'];
	 $billnumber			=	urlencode($r['remarks']);	
	
     $q22 = "select sum(value) as value from $dbname_detail.purchase_return_detail where fkpurchasereturnid = '$inid'";
     $rres22 = mysql_query($q22);
     $r22=mysql_fetch_array($rres22);
     $invoice_amount	=	$r22['value'];

	 
$checkdata=$invoice_link= file_get_contents($Url_admin."invoice_return&fksupplierid={$supplierid}&fksupplierinvoiceid=&return={$invoice_amount}&billnumber={$billnumber}&adddate={$invdate}&location=0");
 
	
                 $addressbookid= 1888;
                 $fkemployeeid=1887;
	     if($checkdata>0){
	
		   mysql_query("update  $dbname_detail.purchase_return set accdatasent=1 where  pkpurchasereturnid = '$inid' ");
		  $sub = "Invoice Amount  ($invoice_amount),Supplier Id ($supplierid) ,bill no ($billnumber) is  resent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Purchase return Resent Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		
		 }else{
			 
	      $sub = "Invoice Amount  ($invoice_amount),Supplier Id ($supplierid) ,bill no ($billnumber) is  not resent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Purchase return Resent Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
			 } 

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

?>