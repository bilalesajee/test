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

$option = $_REQUEST['option'];
$id = $_REQUEST['id'];
if($option!=''){
$date = date('d-m-Y', strtotime($option));
	}else{
$date = date('d-m-Y', strtotime('-1 day'));
	}
 $query = "( SELECT 0 as return1, su.billnumber,s.fksupplierinvoiceid, FROM_UNIXTIME(su.datetime,'%d-%m-%Y') adddate, s.fksupplierid ,SUM(s.quantity * s.priceinrs) as invoice_value from $dbname_detail.supplierinvoice su
	 left join $dbname_detail.stock s on su.pksupplierinvoiceid = s.fksupplierinvoiceid
	 where  FROM_UNIXTIME(su.datetime,'%d-%m-%Y') = '$date' and su.invoice_status = 1 and  su.accdatasent = 0  group by s.fksupplierinvoiceid)
	 
	 UNION
	 
	 (SELECT 1 as return1, su.billnumber,s.fksupplierinvoiceid, FROM_UNIXTIME(su.datetime,'%d-%m-%Y') adddate, s.fksupplierid ,SUM(s.quantity * s.priceinrs) as invoice_value  from $dbname_detail.supplierinvoice su
	 left join $dbname_detail.stock s on su.pksupplierinvoiceid = s.fksupplierinvoiceid
	  left join $dbname_detail.returns r on r.fkstockid = s.pkstockid
	 where  FROM_UNIXTIME(su.datetime,'%d-%m-%Y') = '$date' and su.invoice_status = 1 and  su.accdatasent = 0 and r.returnstatus = 'c'  group by s.fksupplierinvoiceid)";

  

$reportresult = $AdminDAO->queryresult($query);
if (count($reportresult))
    echo json_encode($reportresult); else
    echo json_encode(array());

?>





