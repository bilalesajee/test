<?php

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
global $AdminDAO, $Component;


$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$start_datee				=	strtotime($start_date.'00:00:00'); 
$end_datee				=	strtotime($end_date."23:59:59");
if ($start_date == '' and $end_date=='')
{
//$date=time();
//$cand="datetime='$date'";
$cand="";
}
else if ($end_date=='')
{
$cand="and FROM_UNIXTIME(a.datetime,'%d-%m-%Y') = '$start_date'";
}else{
$cand="and a.datetime between '$start_datee' and '$end_datee'";	
	}

/* $query = "SELECT a.pksupplierinvoiceid as invoiceid,
  a.billnumber,
  FROM_UNIXTIME(a.datetime,'%d-%m-%Y') as datetime ,
  a.fksupplierid,
  a.invoice_status,
  a.refrance_id,
   (select round(sum(priceinrs * quantity),2) from $dbname_detail.stock  where fksupplierinvoiceid = a.pksupplierinvoiceid ) amount from $dbname_detail.supplierinvoice a 

where 1=1 $cond and a.fksupplierid = '$supplier' and ((select round(sum(priceinrs * quantity),2) from $dbname_detail.stock  where fksupplierinvoiceid = a.pksupplierinvoiceid ) -a.paidamount) >= 0 and a.datetime > 1357037506 order by a.datetime desc";//FROM_UNIXTIME(p.deadline,'%d-%m-%Y') = '$get_option'
*/
 $query = "SELECT 0 location,a.pksupplierinvoiceid as invoiceid,
  a.billnumber,
  FROM_UNIXTIME(a.datetime,'%d-%m-%Y') as datetime ,
  a.fksupplierid,
   (select round(sum(priceinrs * quantity),2) from $dbname_detail.stock  where fksupplierinvoiceid = a.pksupplierinvoiceid ) amount from $dbname_detail.supplierinvoice a 

where  a.datetime > 1357037506  $cand order by a.datetime desc";//FROM_UNIXTIME(p.deadline,'%d-%m-%Y') = '$get_option'


$reportresult = $AdminDAO->queryresult($query);
//print_r($reportresult);
/*$trow = count($reportresult);

for ($p = 0; $p < $trow; $p++) {

	$billnumber = $reportresult[$p]['billnumber'];
        $datetime = $reportresult[$p]['datetime'];
        $fksupplierid = $reportresult[$p]['fksupplierid'];
        $invoice_status = $reportresult[$p]['invoice_status'];
        $refrance_id = $reportresult[$p]['refrance_id'];
		
       
        
	 
            
         
$array[] =  array('billnumber'=>$billnumber,'datetime'=>$datetime,'fksupplierid'=>$fksupplierid,'invoice_status'=>$invoice_status,'refrance_id'=>$refrance_id);
   
}*/
//$arr1=json_encode($reportresult);
$reportresult2 = array();
$reportresult2_json=file_get_contents("https://warehouse.esajee.com/admin/accounts/get_invoices.php?start_date=$start_date");
$reportresult2 = json_decode($reportresult2_json, true);

$data = array_merge($reportresult,$reportresult2);


echo json_encode($data);

//echo "<pre>";
//print_r($array);
?>
