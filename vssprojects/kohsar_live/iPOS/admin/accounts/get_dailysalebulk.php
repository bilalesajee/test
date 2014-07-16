<?php
session_start();
$option = $_REQUEST['option'];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($option == '')
{
$get_option = date("d-m-Y", time());
}
else
{
$get_option = $option;
}

$start_date = (int) $_REQUEST['start_date'];
$end_date = (int) $_REQUEST['end_date'];

$dbname_detail = 'main_kohsar';
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
try
{
$dbh = new PDO('mysql:host=localhost;dbname='.$dbname_detail, 'posapp', 'posapp');
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
mail('fahadbuttqau@gmail.com','Failed connecting to Kohsar - Accounts - Daily Sale File',serialize($e));

exit;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
$sql="SELECT pkclosingid from $dbname_detail.closinginfo where from_unixtime(openingdate,'%d-%m-%Y')='$get_option' and closingstatus='a'";

$stmt = $dbh->prepare($sql);

$ret = $stmt->execute();
$count = $stmt->rowCount();
if($count > 0){
$closing_Id = $stmt->fetchAll(PDO::FETCH_ASSOC);
$values = array_map('array_pop', $closing_Id);
$Closingidz = implode(',', $values);

 $query = "SELECT case s.fkaccountid when '0' then 'General' else 'Credit' end fkaccountid, sum(s.totalamount) - sum(s.globaldiscount) sale, sum(s.globaldiscount) as discount,(SELECT c.amount FROM $dbname_detail.coupon_management c where  c.pkcouponid=s.fkcouponid ) as used_advance_booking,(SELECT SUM(c.amount) FROM $dbname_detail.coupon_management c where  c.fkclosingid in ({$Closingidz}) and c.status = 1   ) as advance_booking,

(SELECT (sum(sd.quantity*sd.saleprice)*-1) as returnsale from $dbname_detail.sale ss ,$dbname_detail.saledetail sd
where ss.fkclosingid in ({$Closingidz}) and ss.status = 1 and sd.quantity < 0 and ss.pksaleid=sd.fksaleid and ss.fkaccountid=s.fkaccountid)

as returnsale , cashdiffirence as cashdiff

from $dbname_detail.sale s,$dbname_detail.closinginfo cf
where s.fkclosingid in ({$Closingidz}) and s.status = 1 and s.fkclosingid=cf.pkclosingid group by case s.fkaccountid when '0' then 0 else 1 end
having sale > 0 ";
$stmt = $dbh->prepare($query);

$ret = $stmt->execute();

$booking_voucher = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
$query = "SELECT s.fkaccountid,(sum(sd.quantity*sd.saleprice)) as sale,s.pksaleid from $dbname_detail.sale s ,$dbname_detail.saledetail sd
where s.fkclosingid in ({$Closingidz}) and s.status = 1 and sd.quantity > 0 and s.pksaleid=sd.fksaleid and s.fkaccountid <> 0 group by s.fkaccountid ";

$stmt = $dbh->prepare($query);

$ret = $stmt->execute();

$credit_customer_sale = $stmt->fetchAll(PDO::FETCH_ASSOC);//SALEID
*/
/*
$query = " SELECT 1 n, p.paymenttype, s.countername, p.paymentmethod, s.fkaccountid, sum( p.amount ) payment, p.fkcctypeid , 0 datasent , 0 charges
FROM $dbname_detail.sale s
RIGHT JOIN $dbname_detail.payments p ON p.fksaleid = s.pksaleid
WHERE p.fkclosingid in ({$Closingidz})
GROUP BY fkaccountid, paymentmethod
UNION ALL
SELECT 2 n, 0 paymenttype, 0 countername, c.paymentmethod, c.fkaccountid, sum( c.amount ) payment, 0 fkcctypeid , c.datasent , c.charges
FROM $dbname_detail.collection4acc c
WHERE FROM_UNIXTIME( c.datetime, '%d-%m-%Y' ) = '$get_option'
AND c.amount >0
GROUP BY fkaccountid, paymentmethod
UNION
SELECT 3 as n,c.paymenttype ,0 countername,c.paymentmethod,0 fkaccountid,c.amount as payment,c.fkcctypeid,0 datasent,c.charges FROM
	 $dbname_detail.coupon_management c 
	where c.fkclosingid in ({$Closingidz}) and c.status = 1 

ORDER BY paymentmethod";

$stmt = $dbh->prepare($query);

$ret = $stmt->execute();

$receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);
*/

$query = " SELECT from_unixtime(paymentdate, '%d-%m-%Y') paydate, case when ac.AccountCode = '' then '210100005' else ac.AccountCode end AccountCode, ac.loccode location, ac.fksupplierid, ac.vehicleCode, ap.countername, ap.fkaccountid, (ap.amount) payment, description, paymentmethod, chequeno, chequedate , ap.pkaccountpaymentid
from $dbname_detail.accountpayment ap left join $dbname_detail.account ac on ac.id = ap.fkaccountid
where paymentdate between $start_date and $end_date
order by paymentdate
";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);//ACCOUNTPAYMENTID


/*
$query = "SELECT s.fkaccountid,(sum(sd.quantity*sd.saleprice)*-1) as returnsale from $dbname_detail.sale s ,$dbname_detail.saledetail sd
where s.fkclosingid in ({$Closingidz}) and s.status = 1 and sd.quantity < 0 and s.pksaleid=sd.fksaleid and s.fkaccountid <> 0 group by s.fkaccountid";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$returnsale = $stmt->fetchAll(PDO::FETCH_ASSOC);
*/

/*$query = " SELECT c.amount, c.paymentmethod, c.fkaccountid , c.ccno ,c.fkbankid,c.charges,c.chequeno from $dbname_detail.collection4acc c
where FROM_UNIXTIME(c.datetime,'%d-%m-%Y') = '$get_option' and c.datasent=1 GROUP BY fkaccountid, paymentmethod order by c.paymentmethod ";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$collections = $stmt->fetchAll(PDO::FETCH_ASSOC);

}*/

//$a['booking'] = $booking_voucher;
//$a['credit_sale'] = $credit_customer_sale;
//$a['receipts'] = $receipts;
$a['payments'] = $payments;
//$a['credit_sale_return'] = $returnsale;
echo json_encode($a);
//file_get_contents('http://kohsar.esajee.com/admin/accounts/counter_wise.php');

?>

