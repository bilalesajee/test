<?php
session_start();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$date=time();
$date	=	date('d-m-Y',(strtotime ( '-1 day' , $date ) ));
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


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////Counter4 saledetail Insertion////////////////////////////////////////////////
 $query = "INSERT INTO $dbname_detail.saledetailcounter4 (pksaledetailid,fksaleid,fkstockid,fkpodetailid,fkaccountid, quantity, saleprice,originalprice,fkreasonid,fkdiscountid,counterdiscount,
  discountamount,timestamp,boxsize,fkclosingid,taxable,taxamount,remainingstock,fkbarcodeid2,fkprod_id,addby,editby,sreturnreason) SELECT 
pksaledetailid,fksaleid,fkstockid,fkpodetailid,fkaccountid, quantity, saleprice,originalprice,fkreasonid,fkdiscountid,counterdiscount,
  discountamount,timestamp,boxsize,fkclosingid,taxable,taxamount,remainingstock,fkbarcodeid2,fkprod_id,addby,editby,sreturnreason
 from $dbname_detail.saledetail where fkclosingid=5447 and FROM_UNIXTIME(timestamp,'%d-%m-%Y') = '$date' ";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*file_get_contents("https://kohsar.esajee.com/admin/accounts/getid.php");
file_get_contents("https://dha.esajee.com/admin/accounts/getid.php");
file_get_contents("https://gulberg.esajee.com/admin/accounts/getid.php");
file_get_contents("https://pharmadha.esajee.com/admin/accounts/getid.php");
file_get_contents("https://warehouse.esajee.com/admin/accounts/getid.php");*/


?>