<?php ob_start();
error_reporting(-1); 
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
global $AdminDAO;
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$location = '0';
$start_datee = strtotime($start_date . '00:00:00');
$end_datee = strtotime($end_date . "23:59:59");

 $query_supplier = "SELECT  pksaleid billnumber,s.fkaccountid customer, from_unixtime(s.datetime, '%d-%m-%Y') date, (sum(sd.quantity*sd.saleprice)) - s.globaldiscount as Amount  from  $dbname_detail.sale s ,$dbname_detail.saledetail sd where   s.status=1 and sd.quantity < 0 and s.pksaleid=sd.fksaleid and s.fkaccountid > 0 and s.datetime between $start_datee and $end_datee  group by  pksaleid,s.fkaccountid ";
$reportresult_supplier = $AdminDAO->queryresult($query_supplier);	

$acc_array=file_get_contents("http://210.2.171.14/accounts_test/return_bill_no_list.php?startDate=$start_date&endDate=$end_date&location=0");
$acc_array=json_decode($acc_array,true);
$nbill=0;
$index = array();
foreach ($acc_array as $item) {
    $index[$item['billnumber']] = true;
}
echo "<br>";
foreach ($reportresult_supplier as $item) {
    if (!isset($index[$item['billnumber']])) {
        echo 'BILL DOES Not EXSISTS ON Accounts '.$item['billnumber'];
		$query_supplier = "SELECT  s.countername,s.fkclosingid,companyname,ctype,pksaleid billnumber,s.fkaccountid customer, from_unixtime(s.datetime, '%d-%m-%Y') date, (sum(sd.quantity*sd.saleprice)) - s.globaldiscount as Amount  from  $dbname_detail.sale s ,$dbname_detail.saledetail sd,customer where   s.status=1 and sd.quantity > 0 and s.pksaleid=sd.fksaleid and s.fkaccountid > 0 and pksaleid='".$item['billnumber']."' and pkcustomerid=s.fkaccountid and ctype=2 order by  s.fkaccountid";
$tp = $AdminDAO->queryresult($query_supplier);
    echo "<br>";  
   echo $tp[0]['countername'].'--------'.$tp[0]['fkclosingid'].'--------'.$tp[0]['billnumber'].'--------'.$tp[0]['customer'].'--------'.$tp[0]['date'].'--------'.$tp[0]['Amount'].'--------'.$tp[0]['companyname'].'--------'.$tp[0]['ctype'];
    echo "<br>";
	$nbill++;
	}
}
$nbill=0;
echo "<br>";
$index = array();
foreach ($reportresult_supplier as $item) {
    $index[$item['billnumber']] = true;
}

foreach ($acc_array as $item) {
    if (!isset($index[$item['billnumber']])) {
        echo 'BILL DOES EXSISTS ON POS '.$item['billnumber'];
		echo "<br>";
    $nbill++;
	}
}

?>