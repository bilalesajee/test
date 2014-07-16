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

$date = date('d-m-Y', strtotime('-1 day'));

$query = "SELECT su.billnumber,s.fksupplierinvoiceid, s.addtime adddate, s.fksupplierid ,SUM(s.quantity * s.priceinrs) as invoice_value from $dbname_detail.supplierinvoice su
	 left join $dbname_detail.stock s on su.pksupplierinvoiceid = s.fksupplierinvoiceid
	 where  FROM_UNIXTIME(s.addtime,'%d-%m-%Y') = '$date' and su.invoice_status = 1  group by s.fksupplierinvoiceid
	 ";

$reportresult = $AdminDAO->queryresult($query);
if (count($reportresult))
    echo json_encode($reportresult); else
    echo json_encode(array());

?>





