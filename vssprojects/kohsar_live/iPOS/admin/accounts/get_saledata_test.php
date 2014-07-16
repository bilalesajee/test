<?php
session_start();
$option = $_REQUEST['option'];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$counter = $_REQUEST['counter'];

$start_datee				=	strtotime($start_date.'00:00:00'); 
$end_datee				=	strtotime($end_date."23:59:59");

if ($start_date == '' and $end_date=='')
{
$date=time();
 $date	=	date('d-m-Y',(strtotime ( '-1 day' , $date ) ));
$cand="and (openingdate='$date')";
}
else if ($end_date=='')
{
$cand="and (FROM_UNIXTIME(openingdate,'%d-%m-%Y') = '$start_date'  )";
}else{
$cand="and (openingdate between '$start_datee' and '$end_datee') ";	
	}
if($counter!=''){
	$and2="and countername='$counter'";
	}
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

 $sql="SELECT pkclosingid from $dbname_detail.closinginfo where  closingstatus='a' $and2 $cand ";
$stmt = $dbh->prepare($sql);
$ret = $stmt->execute();
$count = $stmt->rowCount();
if($count > 0){
$closing_Id = $stmt->fetchAll(PDO::FETCH_ASSOC);
$values = array_map('array_pop', $closing_Id);
$Closingidz = implode(',', $values);

for($rt=1;$rt<=4;$rt++){
	
	if($counter > 0)
	{
		if($counter != $rt)
		{
			continue;
		}
	}

if($rt!=4){
	 $sql_cid="SELECT pkclosingid from $dbname_detail.closinginfo where  countername='$rt' and closingstatus='a' $and2 $cand";
$stmt_cid = $dbh->prepare($sql_cid);
$ret_cid = $stmt_cid->execute();
while($row = $stmt_cid->fetch()) {
    $arrcid[]=$row['pkclosingid'];
}
 $Cidz = "'".implode(',', $arrcid)."'";
 
 $query = "SELECT cf.pkclosingid cid,sum(cf.totalsale) sale,(SELECT sum(s.globaldiscount) as discount from $dbname_detail.sale s
where s.fkclosingid in ({$Closingidz}) AND s.status=1 AND s.globaldiscount>0 and s.countername='$rt' group by s.fkclosingid) as discount,sum(cf.cashsale) as cashsale, sum(cf.creditsale) as creditsale, (SELECT (sum(p.amount)) as sale from $dbname_detail.sale s ,$dbname_detail.payments p
where s.fkclosingid in ({$Closingidz}) and s.status = 1 and p.fkcctypeid!=0  and s.pksaleid=p.fksaleid  and s.countername='$rt'  group by s.fkclosingid) as creditcardsale,
(SELECT sum(sd.quantity*sd.saleprice) from $dbname_detail.sale s ,$dbname_detail.saledetail sd , $dbname_detail.stock st
where s.fkclosingid in ({$Closingidz}) and s.status = 1  and s.pksaleid=sd.fksaleid  and sd.fkstockid=pkstockid and s.countername='$rt' and fkbarcodeid in (70115,85692,85691,12014,12037,12044,3902,3904,3905,3903,3906,3907,3910,3909,3915,3917,3918, 
3916,12425,3911,3913,3914,12762,12782,85275,56445,56446,11269,13223,13224,13225,13226,13328,13365,11324,11325,11326,13864,14141,14146,11404,11426,48946,56398,56517,56518,56519,56520,56521,85271,85693,85690,85272,85273,85274,85694,85687,85685,85688,70725,85684) group by s.countername,s.fkclosingid) as mobilecardsale
from $dbname_detail.closinginfo cf
where cf.pkclosingid in ({$Closingidz})  and cf.countername='$rt' group by cf.pkclosingid";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$booking_voucher = $stmt->fetchAll(PDO::FETCH_ASSOC);

$query = "(SELECT 0 as return1,(sum(sd.quantity*sd.saleprice)) - s.globaldiscount as sale,s.pksaleid,s.fkaccountid from $dbname_detail.sale s ,$dbname_detail.saledetail sd
where s.fkclosingid in ({$Closingidz}) and s.status = 1 and sd.quantity > 0 and s.pksaleid=sd.fksaleid and s.fkaccountid <> 0 and s.countername='$rt' group by s.countername,s.fkclosingid,s.pksaleid,s.fkaccountid)
UNION
(SELECT 1 as return1, (sum(sd.quantity*sd.saleprice)*-1) as sale,s.pksaleid,s.fkaccountid from $dbname_detail.sale s ,$dbname_detail.saledetail sd
where s.fkclosingid in ({$Closingidz}) and s.status = 1 and sd.quantity < 0 and s.pksaleid=sd.fksaleid and s.fkaccountid <> 0 and s.countername='$rt' group by s.countername,s.fkclosingid,s.pksaleid,s.fkaccountid) ";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$credit_customer_sale = $stmt->fetchAll(PDO::FETCH_ASSOC);//SALEID

$query = "SELECT (sum(p.amount)) as sale,p.fkcctypeid from $dbname_detail.sale s ,$dbname_detail.payments p
where s.fkclosingid in ({$Closingidz}) and s.status = 1 and p.fkcctypeid!=0  and s.pksaleid=p.fksaleid  and s.countername='$rt'  group by s.countername,s.fkclosingid,p.fkcctypeid ";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$credit_card_sale = $stmt->fetchAll(PDO::FETCH_ASSOC);//CreditCardSale


/*$query = "SELECT sd.quantity,sd.saleprice,s.pksaleid,itemdescription from $dbname_detail.sale s ,$dbname_detail.saledetail sd , $dbname_detail.stock st,main.barcode   
where s.fkclosingid in ({$Closingidz}) and s.status = 1  and s.pksaleid=sd.fksaleid  and sd.fkstockid=pkstockid and st.fkbarcodeid=pkbarcodeid and s.countername='$rt' and fkbarcodeid in (70115,85692,85691,12014,12037,12044,3902,3904,3905,3903,3906,3907,3910,3909,3915,3917,3918, 
3916,12425,3911,3913,3914,12762,12782,85275,56445,56446,11269,13223,13224,13225,13226,13328,13365,11324,11325,11326,13864,14141,14146,11404,11426,48946,56398,56517,56518,56519,56520,56521,85271,85693,85690,85272,85273,85274,85694,85687,85685,85688,70725,85684)  ";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$mob_card_sale = $stmt->fetchAll(PDO::FETCH_ASSOC);//Mobile Card Sale*/
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
	where c.fkclosingid in ({$Closingidz}) and c.status = 1 and cf.countername='$rt'

ORDER BY paymentmethod";

$stmt = $dbh->prepare($query);

$ret = $stmt->execute();

$receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);
*/
 $query = " SELECT   (ap.amount) payment, description,AccountCode account_code,fksupplierid supplier_code, customer_code, tran_type from $dbname_detail.accountpayment ap left join $dbname_detail.account ac on ac.id = ap.fkaccountid
where ap.fkclosingid in ({$Closingidz}) and ap.countername='$rt' group by ap.fkclosingid";
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

$query = " SELECT sum(c.amount) amount, c.fkaccountid fkaccountid, c.paymentmethod  from $dbname_detail.collection4acc c
where FROM_UNIXTIME(c.datetime,'%d-%m-%Y') = '$start_date' and counter='$rt'  GROUP BY fkaccountid ";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$collections = $stmt->fetchAll(PDO::FETCH_ASSOC);


	
	
	}else{



$query = "SELECT 0 as sale,sum(s.globaldiscount) as discount,0 as cashsale,(sum(sd.quantity*sd.saleprice)) as creditsale,0 as creditcardsale from $dbname_detail.sale s ,$dbname_detail.saledetail sd
where  s.status = 1  and s.pksaleid=sd.fksaleid and s.fkaccountid <> 0 and s.countername='$rt' and FROM_UNIXTIME(sd.timestamp,'%d-%m-%Y') = '$start_date' ";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$booking_voucher = $stmt->fetchAll(PDO::FETCH_ASSOC);


 $query = "(SELECT 0 as return1,(sum(sd.quantity*sd.saleprice)) as sale,s.pksaleid,s.fkaccountid from $dbname_detail.sale s ,$dbname_detail.saledetail sd
where  s.status = 1 and sd.quantity > 0 and s.pksaleid=sd.fksaleid and s.fkaccountid <> 0 and s.countername='$rt' and FROM_UNIXTIME(sd.timestamp,'%d-%m-%Y') = '$start_date' group by s.countername,s.pksaleid,s.fkaccountid)
UNION
(SELECT 1 as return1, (sum(sd.quantity*sd.saleprice)*-1) as sale,s.pksaleid,s.fkaccountid from $dbname_detail.sale s ,$dbname_detail.saledetail sd
where  s.status = 1 and sd.quantity < 0 and s.pksaleid=sd.fksaleid and s.fkaccountid <> 0 and s.countername='$rt' and FROM_UNIXTIME(sd.timestamp,'%d-%m-%Y') = '$start_date' group by s.countername,s.pksaleid,s.fkaccountid) ";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$credit_customer_sale = $stmt->fetchAll(PDO::FETCH_ASSOC);//SALEID


$query = "SELECT (sum(p.amount)) as sale,p.fkcctypeid from $dbname_detail.sale s ,$dbname_detail.payments p
where s.fkclosingid in ({$Closingidz}) and s.status = 1 and p.fkcctypeid!=0  and s.pksaleid=p.fksaleid  and s.countername='$rt' group by s.countername,p.fkcctypeid ";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$credit_card_sale = $stmt->fetchAll(PDO::FETCH_ASSOC);//CreditCardSale


$query = " SELECT   (ap.amount) payment, description,AccountCode account_code,fksupplierid supplier_code, customer_code, tran_type from $dbname_detail.accountpayment ap left join $dbname_detail.account ac on ac.id = ap.fkaccountid
where ap.fkclosingid in ({$Closingidz}) and ap.countername='$rt' ";
$stmt = $dbh->prepare($query);
$ret = $stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);//ACCOUNTPAYMENTID




	}
$a[$rt]['booking']=$booking_voucher;
$a[$rt]['payouts']=$payments;
$a[$rt]['creditsale']=$credit_customer_sale;
$a[$rt]['creditcardsale']=$credit_card_sale;
$a[$rt]['collection']=$collections;

//$a['credit_sale'] = $credit_customer_sale;
//$a['receipts'] = $receipts;
//$a['counter'.$rt]=$a['payouts'.$rt]= $payments;

//$a['credit_sale_return'] = $returnsale;

}
}
/*echo "<pre>";
print_r($a);*/
echo json_encode($a);
?>