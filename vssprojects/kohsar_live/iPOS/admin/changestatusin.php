<?php
include_once("../includes/security/adminsecurity.php");
include_once("../surl.php");
$id	=	$_REQUEST['ids'];
$idarr	= 	explode(",", $id);
$emp=$_SESSION['addressbookid'];
$invoice_status	=	$_REQUEST['invoice_status'];
/*echo "<pre>";
print_r($_GET);
echo "</pre>";*/
 $q = "select invoice_status from $dbname_detail.supplierinvoice where pksupplierinvoiceid = '$idarr[1]'";
$rres = mysql_query($q);
$r=mysql_fetch_array($rres);
$status			=	$r['invoice_status'];

if($status == 1 and $invoice_status=='close')
{
	
	echo $msq= 'Status Allready Closed';
	exit;
	
}else  if($status == 0 and $invoice_status=='open'){
	echo $msq= 'Status Allready Open';
	exit;
	}else  if($status == 1 and $invoice_status=='void'){
	echo $msq= 'You can not void Closed Invoice';
	exit;
	}

	
	
if($status == 1){
//////////////////////////////////////////////////////////24-03-2014////////////////////////////////////////////////////////////////////////////////////	      
$fkemployeeid=1887;
$st= '0';
$update= mysql_query("update  $dbname_detail.supplierinvoice set invoice_status = '$st' , invoice_reopen_date='".time()."' , reopen_by='$emp'  where  pksupplierinvoiceid = '$idarr[1]' ");
$invoiceid=$idarr[1];
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$checkdata=$invoice_link= file_get_contents($Url_admin."invoice_status&invoiceid={$invoiceid}&location=0");
	 if($checkdata > 0){
     $msq= 'Status Changed From Closed To Open and sent to accounts';
	      $sub = "Invoice # ($invoiceid ) Status Changed  by ( $addressbookid ) From Closed To Open and sent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Invoice Open Alert',1888,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
	 }else{
	 $msq= 'Status Changed From Closed To Open and not sent to accounts';
	      $sub = "Invoice # ($invoiceid ) Status Changed  by ( $addressbookid ) From Closed To Open and not sent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Invoice Open Alert',1888,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);	 
		 }
}else  if($status == 0){
	if($invoice_status=='close'){
	$st= '1';
	 //$update= mysql_query("update  $dbname_detail.supplierinvoice set invoice_status = '$st' , invoice_close_date='".time()."' where  pksupplierinvoiceid = '$idarr[1]' ");
	 $msq= 'Status Changed From Open To Closed';
	 $query_invoice			=	"SELECT SUM( s.quantity * s.priceinrs ) AS invoice_amount,sup.fksupplierid as supplierid,s.fksupplierinvoiceid as invoiceid,sup.billnumber,sup.datetime

              FROM $dbname_detail.stock s

			  left join $dbname_detail.supplierinvoice sup  on sup.pksupplierinvoiceid = s.fksupplierinvoiceid

              WHERE s.fksupplierinvoiceid = '$idarr[1]'";

             $result_invoice		=	$AdminDAO->queryresult($query_invoice);
   


             $invoice_amount			=	round($result_invoice[0]['invoice_amount'],2);
			  if($invoice_amount > 0){
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $fields_invoicen		=	array('invamount','invoice_status','invoice_close_date','closedby');
				$data_invoicen			=	array($invoice_amount,$st,time(),$emp);
				$AdminDAO->updaterow("$dbname_detail.supplierinvoice",$fields_invoicen,$data_invoicen,"pksupplierinvoiceid='$idarr[1]'");
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			   $supplierid			=	$result_invoice[0]['supplierid'];
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $fields_st		=	array('fksupplierid');
				$data_st		=	array($supplierid);
				$AdminDAO->updaterow("$dbname_detail.stock",$fields_st,$data_st,"fksupplierinvoiceid='$idarr[1]'");
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



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
		
		
		  $sub = "Invoice # ($invoiceid ),Supplier($companyname_) ,bill no ($billnumber) is closed and sent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Invoice Close Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		
		 }else{
		
	      $sub = "Invoice # ($invoiceid ),Supplier($companyname_) ,bill no ($billnumber) is closed and not sent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Invoice Close Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
			 } 

			  }else{
				$msq='Error Invoice Has No Stock';  
				  }

	}else if($invoice_status=='void'){
		$msq='Invoice Status Changed To Void'; 
		$Inv_ID=$idarr[1];
		if($Inv_ID > 0){
		$queryg = "INSERT INTO $dbname_detail.stock_woide ( pkstockid ,batch,quantity,unitsremaining ,expiry ,  purchaseprice ,costprice ,retailprice , priceinrs , shipmentcharges ,
  suggestedsaleprice ,fkshipmentgroupid ,fkshipmentid ,fkbarcodeid ,fkorderid ,fksupplierid , fkagentid , fkcountryid ,
  fkstoreid ,
  fksupplierinvoiceid ,
  fkemployeeid ,
  fkbrandid ,
  updatetime ,
  unitsreserved ,
  shipmentpercentage ,
  boxprice ,
  refstockid ,
  srcstoreid ,
  fkconsignmentdetailid ,
  fkpurchaseid ,
  fkqueryloggerid ,
  addtime ,
  fkproduct_id ,
  fksupplierinvid,shiftdate  ) SELECT  pkstockid ,batch,quantity,unitsremaining ,expiry ,  purchaseprice ,costprice ,retailprice , priceinrs , shipmentcharges ,
  suggestedsaleprice ,fkshipmentgroupid ,fkshipmentid ,fkbarcodeid ,fkorderid ,fksupplierid , fkagentid , fkcountryid ,
  fkstoreid ,
  fksupplierinvoiceid ,
  fkemployeeid ,
  fkbrandid ,
  updatetime ,
  unitsreserved ,
  shipmentpercentage ,
  boxprice ,
  refstockid ,
  srcstoreid ,
  fkconsignmentdetailid ,
  fkpurchaseid ,
  fkqueryloggerid ,
  addtime ,
  fkproduct_id ,
  fksupplierinvid,'".time()."'  FROM    $dbname_detail.stock where fksupplierinvoiceid='$Inv_ID'";
  $reportresultg = $AdminDAO->queryresult($queryg);
  $queryh1 = "delete from $dbname_detail.stock where fksupplierinvoiceid='$Inv_ID'";
  $AdminDAO->queryresult($queryh1);	
		
$queryh2 = "update $dbname_detail.supplierinvoice set invoice_status = 2 , voiddate='".time()."' ,voidby='$addressbookid'  where pksupplierinvoiceid='$Inv_ID' and invoice_status = 0";
  $AdminDAO->queryresult($queryh2);
           $sub = "Invoice # ($Inv_ID ) is Void by ($emp)";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Invoice Void Alert',1888,1887,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		}
	}


	}
	
	
		//echo "update  dbname_detail.supplierinvoice set invoice_status = '$st' where  pksupplierinvoiceid = '$idarr[1]' ";
		
	 
     echo $msq;

/*if(sizeof($_POST)>0)
{
	$id	=	$_REQUEST['ids'];
//	echo $res			=	$AdminDAO->getrows("$dbname_detail.supplierinvoice","invoice_status","pksupplierinvoiceid = '$id'");
	  // $status			=	$res[0]['invoice_status'];
	$idarr	= 	explode(",", $id);
	
	
	
	
	$var	=	sizeof($idarr)-1;
	for($i=1; $i<=$var; $i++)
	{
		if($i==1)
		continue;

		$status			=	$_POST['status'];
	
		$fields			=	array('invoice_status');
		$data			=	array($status);
		$AdminDAO->updaterow("$dbname_detail.supplierinvoice",$fields,$data, "pksupplierinvoiceid = '$idarr[$i]'");
	
	}	
}
else
{
	echo "Unknown Value";
	exit;		
}*/
?>