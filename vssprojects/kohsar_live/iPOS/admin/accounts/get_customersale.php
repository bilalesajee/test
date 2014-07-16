<?php
session_start();
$customer_code = (int)$_REQUEST['customer_code'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

if($customer_code > 0)
{
	$customerCond = " and s.fkaccountid = {$customer_code} ";
}
//$start_date				=	strtotime($start_date.'00:00:00'); 
//$end_date				=	strtotime($end_date."23:59:59");
$dbname_detail = 'main_kohsar';
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
try
{
	$dbh = new PDO('mysql:host=localhost;dbname='.$dbname_detail, 'posapp', 'posapp');
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
	mail('fahadbuttqau@gmail.com','Failed connecting to Kohsar - Accounts - Customer Balance File',serialize($e));
	
	exit;
}


$query = "SELECT c.ctype, s.fkaccountid, from_unixtime(datetime,'%d-%m-%Y') date, (sum(sd.quantity*sd.saleprice) - s.globaldiscount) as sale,s.pksaleid saleid from $dbname_detail.sale s inner join $dbname_detail.saledetail sd on s.pksaleid=sd.fksaleid
inner join main.customer c on c.pkcustomerid = s.fkaccountid
where datetime between  UNIX_TIMESTAMP('$start_date 00:00:00') and  UNIX_TIMESTAMP('$end_date 23:59:59') and c.ctype!=1  and s.status = 1 and sd.quantity > 0  $customerCond and s.fkaccountid > 0 group by s.pksaleid order by s.pksaleid ";


/*echo $query = "SELECT c.ctype, s.fkaccountid, from_unixtime(datetime,'%d-%m-%Y') date, (sum(sd.quantity*sd.saleprice) - s.globaldiscount) as sale,s.pksaleid saleid from $dbname_detail.sale s inner join $dbname_detail.saledetail sd on s.pksaleid=sd.fksaleid
inner join main.customer c on c.pkcustomerid = s.fkaccountid
where datetime between  '$start_date' and  '$end_date' and c.ctype!=1  and s.status = 1 and sd.quantity > 0  $customerCond and s.fkaccountid > 0 group by s.pksaleid order by s.pksaleid ";*/

$stmt = $dbh->prepare($query);

$ret = $stmt->execute();

$credit_customer_sale = $stmt->fetchAll(PDO::FETCH_ASSOC);//SALEID


 $query = "SELECT c.ctype, s.fkaccountid, from_unixtime(datetime,'%d-%m-%Y') date, (sum(sd.quantity*sd.saleprice)*-1) as returnsale, s.pksaleid saleid from $dbname_detail.sale s inner join $dbname_detail.saledetail sd on s.pksaleid=sd.fksaleid 
inner join main.customer c on c.pkcustomerid = s.fkaccountid
where datetime between UNIX_TIMESTAMP('$start_date 00:00:00') and  UNIX_TIMESTAMP('$end_date 23:59:59') and c.ctype!=1 and s.status = 1 and sd.quantity < 0  $customerCond and s.fkaccountid > 0 group by s.pksaleid order by s.pksaleid ";


/* $query = "SELECT c.ctype, s.fkaccountid, from_unixtime(datetime,'%d-%m-%Y') date, (sum(sd.quantity*sd.saleprice)*-1) as returnsale, s.pksaleid saleid from $dbname_detail.sale s inner join $dbname_detail.saledetail sd on s.pksaleid=sd.fksaleid 
inner join main.customer c on c.pkcustomerid = s.fkaccountid
where datetime between '$start_date'  and ' $end_date' and c.ctype!=1 and s.status = 1 and sd.quantity < 0  $customerCond and s.fkaccountid > 0 group by s.pksaleid order by s.pksaleid ";*/


$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$returnsale = $stmt->fetchAll(PDO::FETCH_ASSOC);

$a['credit_sale'] = $credit_customer_sale;
$a['credit_sale_return'] = $returnsale;
echo json_encode($a);
//file_get_contents('http://kohsar.esajee.com/admin/accounts/counter_wise.php');

?>

