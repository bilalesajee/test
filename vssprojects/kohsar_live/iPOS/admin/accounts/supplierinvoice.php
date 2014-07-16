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

$option = $_REQUEST['q'];

$supplier		=  preg_replace('/^(0)+/', '', $_REQUEST['supp_code'] ); 
if ($option != '') {
    $cond = " and ( a.billnumber like '%{$option}%' or  a.pksupplierinvoiceid like '{$option}%' ) ";
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
 $query = "SELECT a.pksupplierinvoiceid as invoiceid,
  a.billnumber,
  FROM_UNIXTIME(a.datetime,'%d-%m-%Y') as datetime ,
  a.fksupplierid,
  a.invoice_status,
  a.refrance_id,
   (select round(sum(priceinrs * quantity),2) from $dbname_detail.stock  where fksupplierinvoiceid = a.pksupplierinvoiceid ) amount from $dbname_detail.supplierinvoice a 

where 1=1 $cond and a.fksupplierid = '$supplier' and a.datetime > 1357037506 order by a.datetime desc";//FROM_UNIXTIME(p.deadline,'%d-%m-%Y') = '$get_option'


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
echo json_encode($reportresult);
//echo "<pre>";
//print_r($array);
?>
