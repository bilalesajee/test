<?php
session_start();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
if($_REQUEST['clid']>0){
$closingID=$_REQUEST['clid'];	
}else{
$closingID = $_REQUEST['cid'];
$idarr	= 	explode(",", $closingID);
if($idarr[1]==''){
	
	echo $msq= 'Plz select any row';
	exit;
	
}else{
    $closingID=$idarr[1];	
	}
}	
	
$sdate=date('d-m-Y');
$dbname_detail = 'main_kohsar';
if($closingID > 0){
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
$sql_cid="SELECT countername,from_unixtime(openingdate, '%d-%m-%Y') date from $dbname_detail.closinginfo where   pkclosingid='$closingID' ";
$stmt_cid = $dbh->prepare($sql_cid);
$ret_cid = $stmt_cid->execute();
$row = $stmt_cid->fetch();
    $rt=$row['countername'];
	$sdate=$row['date'];



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$query = "SELECT cf.pkclosingid cid,sum(cf.totalsale+advance_bk) sale,cf.countername,(SELECT sum(s.globaldiscount) as discount from $dbname_detail.sale s
where s.fkclosingid='$closingID' AND s.status=1 AND s.globaldiscount>0  group by s.fkclosingid) as discount,sum(cf.cashsale) as cashsale, sum(cf.creditsale) as creditsale, (SELECT (sum(p.amount)) as sale from $dbname_detail.sale s ,$dbname_detail.payments p
where s.fkclosingid='$closingID' and s.status = 1 and p.fkcctypeid!=0  and s.pksaleid=p.fksaleid group by s.fkclosingid) as creditcardsale,
(SELECT sum(sd.quantity*sd.saleprice) from $dbname_detail.sale s ,$dbname_detail.saledetail sd , $dbname_detail.stock st
where s.fkclosingid='$closingID' and s.status = 1  and s.pksaleid=sd.fksaleid  and sd.fkstockid=pkstockid and fkbarcodeid in (70115,85692,85691,12014,12037,12044,3902,3904,3905,3903,3906,3907,3910,3909,3915,3917,3918, 
3916,12425,3911,3913,3914,12762,12782,85275,56445,56446,11269,13223,13224,13225,13226,13328,13365,11324,11325,11326,13864,14141,14146,11404,11426,48946,56398,56517,56518,56519,56520,56521,85271,85693,85690,85272,85273,85274,85694,85687,85685,85688,70725,85684) group by s.fkclosingid) as mobilecardsale,advance_bk as advance_booking,used_bk as used_booking
from $dbname_detail.closinginfo cf
where cf.pkclosingid='$closingID' group by cf.pkclosingid";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$booking_voucher = $stmt->fetchAll(PDO::FETCH_ASSOC);

$query = "(SELECT 0 as return1,(sum(sd.quantity*sd.saleprice)) - s.globaldiscount as sale,s.pksaleid,s.fkaccountid from $dbname_detail.sale s ,$dbname_detail.saledetail sd
where s.fkclosingid='$closingID' and s.status = 1 and sd.quantity > 0 and s.pksaleid=sd.fksaleid and s.fkaccountid <> 0  group by s.pksaleid,s.fkaccountid)
UNION
(SELECT 1 as return1, (sum(sd.quantity*sd.saleprice)*-1) as sale,s.pksaleid,s.fkaccountid from $dbname_detail.sale s ,$dbname_detail.saledetail sd
where s.fkclosingid='$closingID' and s.status = 1 and sd.quantity < 0 and s.pksaleid=sd.fksaleid and s.fkaccountid <> 0  group by s.pksaleid,s.fkaccountid) ";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$credit_customer_sale = $stmt->fetchAll(PDO::FETCH_ASSOC);//SALEID

$query = "SELECT (sum(p.amount)) as sale,p.fkcctypeid from $dbname_detail.sale s ,$dbname_detail.payments p
where s.fkclosingid='$closingID' and s.status = 1 and p.fkcctypeid!=0  and s.pksaleid=p.fksaleid    group by p.fkcctypeid ";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$credit_card_sale = $stmt->fetchAll(PDO::FETCH_ASSOC);//CreditCardSale


 $query = " SELECT   pkaccountpaymentid id,(ap.amount) payment, description,AccountCode account_code,fksupplierid supplier_code, customer_code, tran_type from $dbname_detail.accountpayment ap left join $dbname_detail.account ac on ac.id = ap.fkaccountid
where ap.fkclosingid='$closingID' group by ap.pkaccountpaymentid";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);//ACCOUNTPAYMENTID


$query = " SELECT sum(c.amount) amount, c.fkaccountid fkaccountid, c.paymentmethod  from $dbname_detail.collection4acc c
where c.fkclosingid='$closingID'   GROUP BY fkaccountid ";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$collections = $stmt->fetchAll(PDO::FETCH_ASSOC);


	
	
$a[$rt]['booking']=$booking_voucher;
$a[$rt]['payouts']=$payments;
$a[$rt]['creditsale']=$credit_customer_sale;
$a[$rt]['creditcardsale']=$credit_card_sale;
$a[$rt]['collection']=$collections;
/*echo "<pre>";
print_r($a);
*/
$jdata=urlencode(json_encode($a));

$accsent=file_get_contents("http://accounts.esajee.com/accounts/sync_closing.php?data=$jdata&location=0&sdate=$sdate");
if(is_numeric($accsent)){
echo "Data Sent to Accounts";
$query = " update $dbname_detail.closinginfo set accdatasent=1 where pkclosingid='$closingID'";
$stmt = $dbh->prepare($query);
$stmt->execute();
}else{
    echo "Data Not Sent to Accounts";
	}

}
?>