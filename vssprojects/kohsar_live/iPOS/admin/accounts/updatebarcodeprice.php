<?php session_start();
date_default_timezone_set('Asia/Karachi');
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$dbname_detail = 'main_kohsar';
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
try
{
$dbh = new PDO('mysql:host=localhost;dbname='.$dbname_detail, 'dailyupdate', 'Ye031@barcodeprice');
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
mail('fahadbuttqau@gmail.com','Failed connecting to Kohsar - Accounts - Daily Sale File',serialize($e));

exit;
}
$sql="TRUNCATE  $dbname_detail.barcodeprice";
$stmt = $dbh->prepare($sql);
$ret = $stmt->execute();

$sql="insert into $dbname_detail.barcodeprice (stockid)
select max(pkstockid) from $dbname_detail.stock where updatetime > addtime or addtime = updatetime group by fkbarcodeid";
$stmt = $dbh->prepare($sql);
$ret = $stmt->execute();

$sql="update $dbname_detail.barcodeprice b left join $dbname_detail.stock a on b.stockid = a.pkstockid set b.fkbarcodeid = a.fkbarcodeid , b.tradeprice = a.priceinrs , b.retailprice = a.retailprice";
$stmt = $dbh->prepare($sql);
$ret = $stmt->execute();


$sql="update $dbname_detail.barcodeprice b left join $dbname_detail.pricechange a on b.fkbarcodeid = a.fkbarcodeid set  b.retailprice = a.price";
$stmt = $dbh->prepare($sql);
$ret = $stmt->execute();



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$from = "Esajee Solutions <kohsar@esajee.com>";
$to="notify@esajeesolutions.com";
$subject = "BarcodePrice Table Is Updated On Kohsar";
$body="BarcodePrice Table Cron Is Runing";
$headers = "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From:".$from;
$msent=mail($to,$subject,$body,$headers);
?>