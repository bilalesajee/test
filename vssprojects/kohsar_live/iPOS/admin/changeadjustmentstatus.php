<?php
include_once("../includes/security/adminsecurity.php");
include_once("../surl.php");
 $id	=	$_REQUEST['ids'];

$idarr	= 	explode(",", $id);
$invoice_status	=	$_REQUEST['invoice_status'];
 $q = "select invoice_status from $dbname_detail.supplierinvoice where pksupplierinvoiceid = '$idarr[1]'";
$rres = mysql_query($q);
$r=mysql_fetch_array($rres);
$status			=	$r['invoice_status'];


	
 if($status == 1)
	{
	$st= '3';
	 //$update= mysql_query("update  $dbname_detail.supplierinvoice set invoice_status = '$st' , invoice_close_date='".time()."' where  pksupplierinvoiceid = '$idarr[1]' ");
	 
	 
	        $query_1		=	"SELECT * FROM $dbname_detail.adjustment  WHERE  fksupplierinvoiceid = '$idarr[1]' and issclose=1";
            $result_1		=	$AdminDAO->queryresult($query_1);
	        $adjentery	=	$result_1[0]['pkadjustmentid'];
			$invdate			=	date('d-m-Y', $result_1[0]['adjustmenttime']);
	        if($adjentery!=''){
			$query_2		=	"SELECT invamount FROM $dbname_detail.supplierinvoice  WHERE  pksupplierinvoiceid = '$idarr[1]'";
            $result_2		=	$AdminDAO->queryresult($query_2);
	        $adjentery2	=	$result_2[0]['invamount'];
				
				
	 $query_invoice			=	"SELECT SUM( s.quantity * s.priceinrs ) AS invoice_amount,sup.fksupplierid as supplierid,s.fksupplierinvoiceid as invoiceid,sup.billnumber,sup.datetime

              FROM $dbname_detail.stock s

			  left join $dbname_detail.supplierinvoice sup  on sup.pksupplierinvoiceid = s.fksupplierinvoiceid

              WHERE s.fksupplierinvoiceid = '$idarr[1]'";

             $result_invoice		=	$AdminDAO->queryresult($query_invoice);
			 
             $invoice_amount			=	round($result_invoice[0]['invoice_amount'],2);
			   $supplierid			=	$result_invoice[0]['supplierid'];
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                     $invoiceid			=	$result_invoice[0]['invoiceid'];

			  
				 $addressbookid= 1888;
                 $fkemployeeid=1887;

      if($adjentery2>$invoice_amount){
		  $invoice_amount=$adjentery2-$invoice_amount;
   $checkdata=$invoice_link= file_get_contents($Url_admin."stock_voucher&operation=minus&invoiceid={$invoiceid}&supplierid={$supplierid}&amount={$invoice_amount}&date={$invdate}&location=0");
		 if($checkdata>0){
		 mysql_query("update  $dbname_detail.adjustment set accdatasent=1  where  fksupplierinvoiceid = '$invoiceid' ");

		  $sub = "Invoice # ($invoiceid ),Supplier Id ($supplierid) ,Amount Adjusted ($invoice_amount) is adjusted and sent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Invoice Status Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		
		 }else{
			 
	  $sub = "Invoice # ($invoiceid ),Supplier Id ($supplierid) ,Amount Adjusted ($invoice_amount) is adjusted and not sent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Invoice Status Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
			 } 
		 
		 
		  }else if($adjentery2<$invoice_amount){
			  $invoice_amount=$invoice_amount-$adjentery2;
   $checkdata=$invoice_link=file_get_contents($Url_admin."stock_voucher&operation=plus&invoiceid={$invoiceid}&supplierid={$supplierid}&amount={$invoice_amount}&date={$invdate}&location=0");
		 if($checkdata>0){
		  mysql_query("update  $dbname_detail.adjustment set accdatasent=1  where  fksupplierinvoiceid = '$invoiceid' ");
		
	$sub = "Invoice # ($invoiceid ),Supplier Id ($supplierid) ,Amount Adjusted ($invoice_amount) is adjusted and sent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Invoice Status Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		
		 }else{
			 
	    $sub = "Invoice # ($invoiceid ),Supplier Id ($supplierid) ,Amount Adjusted ($invoice_amount) is adjusted and not sent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Invoice Status Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
			 } 
		 
		  }
		  
		  $msq= 'Status Changed From  Closed To Adjusted';
		  }else{
			  
			  $msq= 'Nothing Changed';
			  } 

	}
     echo $msq;

?>