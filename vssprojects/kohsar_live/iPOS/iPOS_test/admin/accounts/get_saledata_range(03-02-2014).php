<?php

session_start();
$option = $_REQUEST['option'];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$counter = $_REQUEST['counter'];

$start_datee = strtotime($start_date . '00:00:00');
$end_datee = strtotime($end_date . "23:59:59");

if ($start_date == '' and $end_date == '')
{
    $date = time();
    $date = date('d-m-Y', (strtotime('-1 day', $date)));
    $cand = "and (openingdate='$date')";
}
else if ($end_date == '')
{
    $cand = "and (FROM_UNIXTIME(openingdate,'%d-%m-%Y') = '$start_date'  )";
}
else
{
    $cand = "and (openingdate between '$start_datee' and '$end_datee') ";
}
if ($counter != '')
{
    $and2 = "and countername='$counter'";
}
$dbname_detail = 'main_kohsar';
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
try
{
    $dbh = new PDO('mysql:host=localhost;dbname=' . $dbname_detail, 'posapp', 'posapp');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e)
{
    mail('fahadbuttqau@gmail.com', 'Failed connecting to Kohsar - Accounts - Daily Sale File', serialize($e));

    exit;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$query = " ( SELECT s.countername, s.datetime,  0 as return1,(sum(sd.quantity*sd.saleprice)) as sale,s.pksaleid,s.fkaccountid from $dbname_detail.sale s left join $dbname_detail.saledetail sd on s.pksaleid=sd.fksaleid
where  s.status = 1 and sd.quantity > 0  and s.fkaccountid <> 0 and  s.datetime  between  $start_datee and $end_datee group by s.countername, s.pksaleid,s.fkaccountid, s.datetime )";

$query .= "UNION";

$query .= " ( SELECT s.countername, s.datetime datetime,  1 as return1, (sum(sd.quantity*sd.saleprice)*-1) as sale,s.pksaleid,s.fkaccountid from $dbname_detail.sale s left join $dbname_detail.saledetail sd  on s.pksaleid=sd.fksaleid
where  s.status = 1 and sd.quantity < 0 and   s.fkaccountid <> 0   and s.datetime  between $start_datee and $end_datee group by s.countername, s.pksaleid,s.fkaccountid, s.datetime ) ";

$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$credit_customer_data = $stmt->fetchAll(PDO::FETCH_ASSOC); //SALEID

//$a['creditsale'] = $credit_customer_data;

echo json_encode($credit_customer_data);
?>