<?php

session_start();

$option = $_REQUEST['option'];

if ($option == '')
{
    $get_option = date("d-m-Y", time());
}
else
{
    $get_option = $option;
}

$dbname_detail = 'main_kohsar';

$dbh = new PDO('mysql:host=localhost;dbname='.$dbname_detail, 'myagent', 'lib@K51@rpm@sock');
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "SELECT  case fkaccountid when '0' then 'General' else 'Credit' end fkaccountid, sum(s.totalamount) - sum(s.globaldiscount) sale, sum(s.globaldiscount) as discount from $dbname_detail.sale s 
where FROM_UNIXTIME(s.datetime,'%d-%m-%Y') = '$get_option' and s.status = 1 group by case fkaccountid when '0' then 0 else 1 end 
having sale > 0 ";
$stmt = $dbh->prepare($query);

$ret = $stmt->execute();

$booking_voucher = $stmt->fetchAll(PDO::FETCH_ASSOC);


$query = " SELECT s.fkaccountid,sum(s.totalamount) sale from $dbname_detail.sale s 
where FROM_UNIXTIME(s.datetime,'%d-%m-%Y') = '$get_option' and s.status = 1 and s.fkaccountid != 0 group by fkaccountid having sale > 0 ";

$stmt = $dbh->prepare($query);

$ret = $stmt->execute();

$credit_customer_sale = $stmt->fetchAll(PDO::FETCH_ASSOC);


$query = " SELECT p.paymenttype, s.countername, p.paymentmethod, s.fkaccountid,sum(p.amount) payment, p.fkcctypeid  from $dbname_detail.sale s right join $dbname_detail.payments p on p.fksaleid = s.pksaleid
where FROM_UNIXTIME(p.paytime,'%d-%m-%Y') = '$get_option' group by p.paymenttype, s.fkaccountid , p.paymentmethod, p.fkcctypeid, s.countername
having payment > 0
order by  p.paymentmethod
 ";

$stmt = $dbh->prepare($query);

$ret = $stmt->execute();

$receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$query = " SELECT case when ac.AccountCode = '' then '210100005' else ac.AccountCode end AccountCode, ac.loccode location, ac.fksupplierid, ac.vehicleCode, ap.countername, ap.fkaccountid, sum(ap.amount) payment, description, paymentmethod, chequeno, chequedate  
from $dbname_detail.accountpayment ap left join $dbname_detail.account ac on ac.id = ap.fkaccountid
where FROM_UNIXTIME(ap.paymentdate,'%d-%m-%Y') = '$get_option'  
group by ap.fkaccountid, description, paymentmethod, chequeno, chequedate, ap.countername
 ";

$stmt = $dbh->prepare($query);

$ret = $stmt->execute();

$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$a['booking'] = $booking_voucher;
$a['credit_sale'] = $credit_customer_sale;
$a['receipts'] = $receipts;
$a['payments'] = $payments;
echo json_encode($a);

?>
