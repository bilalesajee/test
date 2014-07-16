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
$date=time();
 $date	=	date('d-m-Y',(strtotime ( '-1 day' , $date ) ));
 
$from = "Kohsar SHOP System <kohsar@esajee.com>";

//$to = "hesajee@gmail.com,m_esajee@hotmail.com,esajeeco@gmail.com";

$to = "accounts@esajee.com,hesajee@gmail.com";
//$to = "fahadbuttqau@gmail.com,siddique.ahmad@gmail.com";
$subject = "Esajee Kohsar Collective Closing on $date ";
 $query = "select cl.pkclosingid,
 round((SELECT SUM(ac.amount) FROM $dbname_detail.accountpayment ac WHERE ac.fkclosingid = cl.pkclosingid) ,2) as payouts,
  (SELECT SUM(cm.amount) FROM $dbname_detail.coupon_management cm where cm.fkclosingid=cl.pkclosingid and cm.status=2) as used_coupon,
(SELECT SUM(c.amount) FROM $dbname_detail.coupon_management c where c.fkclosingid=cl.pkclosingid and c.status = 1 ) as coupon_sold, 
cl.countername,
 round(cl.openingbalance,2) as openingbalance,
(SELECT  sum( c.amount ) payment from $dbname_detail.collection c WHERE c.fkclosingid = cl.pkclosingid AND c.amount >0) as collectionamount,
(SELECT (sum(sd.quantity*sd.saleprice)*-1) as returnsale from $dbname_detail.sale ss ,$dbname_detail.saledetail sd
where ss.fkclosingid = cl.pkclosingid and ss.status = 1 and sd.quantity < 0 and ss.pksaleid=sd.fksaleid ) as returnsale,
cl.cashsale as cashsale,
cl.creditsale as creditsale,
(SELECT (sum(sd.quantity*sd.saleprice)*-1) as returncrediitsale from $dbname_detail.sale sl ,$dbname_detail.saledetail sd
where sl.fkclosingid = cl.pkclosingid and sl.status = 1 and sd.quantity < 0 and sl.pksaleid=sd.fksaleid and sl.fkaccountid > 0 ) as returncrediitsale,
round((SELECT SUM(sa.globaldiscount) FROM $dbname_detail.sale sa WHERE sa.fkclosingid = cl.pkclosingid) ,2) as discount,
cl.creditcardsale as creditcardsale, 
 cl.chequesale as chequesale,
  cl.foreigncurrencysale as foreigncurrencysale,
    cl.totalbills,
	 cl.totalsale as totalsale 
FROM $dbname_detail.closinginfo cl  WHERE cl.closingstatus='a' and FROM_UNIXTIME(cl.openingdate,'%d-%m-%Y') = '$date' group by cl.pkclosingid ";
$reportresult = $AdminDAO->queryresult($query);
$row_run=count($reportresult);
//////////////////////////////////////////////////////////stock query///////////////////////////////////////////////////////////////////////////////////
 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$table ='';
$sql="SELECT 	storephonenumber,storeaddress	from store where pkstoreid='3'";
$storearray=	$AdminDAO->queryresult($sql);
$storenameadd=	$storearray[0]['storeaddress'].', '.$storearray[0]['storephonenumber'];

$table="<!DOCTYPE html>
<html>
  <head>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link href='https://kohsar.esajee.com/admin/accounts/bs/css/bootstrap.min.css' rel='stylesheet'>
     <script src=\"https://code.jquery.com/jquery.js\"></script>
    <script src=\"https://kohsar.esajee.com/admin/accounts/bs/js/bootstrap.min.js\"></script>
  </head>
<div align='left'>
<div > <img src='https://kohsar.esajee.com/images/esajeelogo.jpg' width='150' height='50'><br />
<b>Think globally shop locally</b> <br />". $storenameadd."</span> </div>";
$table .='
<table width="100%" border="1"  cellpadding="0" cellspacing="0" class="table table-bordered table-hover" >
 

 
 <tr>
    <th colspan="16" align="center" ><span style="font-size:24px; ">Counter Wise Summary</th>
  </tr>
  <tr>
    <th ></th>
  </tr>
  <tr>
    <th colspan="16" align="center">Date:'.$date.'</th>
  </tr>';
 $table .=' <tr>
    <th>Counter</th>
    <th>Closing #</th>
    <th>No of Bills</th>
    <th>Sale</th>
    <th>Discount</th>
    <th>Sale Return</th>
    <th>Coupon Sold</th>
    <th>Coupon Used</th>
    <th>Payouts ( petty cash)</th>
    <th>Cash</th>
    <th>Credit Sale</th>
    <th>Credit Sale Return</th>
    <th>Credit Card</th>
    <th>Cheque</th>
    <th>Forigen Currency</th>
    <th>Collection</th>
    </tr>';
   $totalsale = 0;
   $returnsale_total=0;
   $coupon_sold_total=0;
   $used_coupon_total=0;
   $payouts_total=0;
   $cashsale_total =0;
   $creditcardsale_total=0;
   $chequesale_total=0;
   $foreigncurrencysale_total =0;
   $collectionamount_total=0;
 
for($i=0;$i<$row_run;$i++)
{
		$totalsale += $reportresult[$i]["totalsale"];
		$returnsale_total +=$reportresult[$i]["returnsale"];
		$coupon_sold_total +=$reportresult[$i]["coupon_sold"];
		$used_coupon_total +=$reportresult[$i]["used_coupon"];
		$payouts_total +=$reportresult[$i]["payouts"];
		$cashsale_total +=$reportresult[$i]["cashsale"];
		$crdsale_total +=$reportresult[$i]["creditsale"];		
		$creditcardsale_total +=$reportresult[$i]["creditcardsale"];
		$creditreturnsale_total +=$reportresult[$i]["returncrediitsale"];
		$discount_total +=$reportresult[$i]["discount"];
		$chequesale_total +=$reportresult[$i]["chequesale"];
		$foreigncurrencysale_total +=$reportresult[$i]["foreigncurrencysale"];
		$collectionamount_total += $reportresult[$i]["collectionamount"];
	$rs=$reportresult[$i]["returnsale"];
	if($rs==''){
		$rs=0;
		}  
		
		$rs1=$reportresult[$i]["coupon_sold"];
	if($rs1==''){
		$rs1=0;
		}  
		
		$rs2=$reportresult[$i]["used_coupon"];
	if($rs2==''){
		$rs2=0;
		}  
		
		$rs3=$reportresult[$i]["collectionamount"];
	if($rs3==''){
		$rs3=0;
		}  
		
		$table .='<tr>
   <td  align="center"> '.$reportresult[$i]["countername"].'</td>
  
    <td > '.$reportresult[$i]["pkclosingid"].'</td>
    <td >'.$reportresult[$i]["totalbills"].'</td>
	<td >'.$reportresult[$i]["totalsale"].'</td>
	<td >'.$reportresult[$i]["discount"].'</td>
	<td >'.$rs.'</td>
	<td >'.$rs1.'</td>
	<td >'.$rs2.'</td>
	<td >'.$reportresult[$i]["payouts"].'</td>
	<td >'.$reportresult[$i]["cashsale"].'</td>
	<td >'.$reportresult[$i]["creditsale"].'</td>
	<td >'.$reportresult[$i]["returncrediitsale"].'</td>	
	<td >'.$reportresult[$i]["creditcardsale"].'</td>
	<td >'.$reportresult[$i]["chequesale"].'</td>
	<td >'.$reportresult[$i]["foreigncurrencysale"].'</td>
	<td >'.$rs3.'</td>
	 </tr>';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 
$countername=$reportresult[$i]["countername"];
$closingnumber=$reportresult[$i]["pkclosingid"];
$totalbills=$reportresult[$i]["totalbills"];
$totalsal=$reportresult[$i]["totalsale"];
$returnsale=$reportresult[$i]["returnsale"];
$coupon_sold=$reportresult[$i]["coupon_sold"];
$used_coupon=$reportresult[$i]["used_coupon"];
$payout=$reportresult[$i]["payouts"];
$cashsale=$reportresult[$i]["cashsale"];
$creditsale=$reportresult[$i]["creditsale"];
$creditcardsale=$reportresult[$i]["creditcardsale"];
$credit_sale_return=$reportresult[$i]["returncrediitsale"];
$discount=$reportresult[$i]["discount"];
$chequesale=$reportresult[$i]["chequesale"];
$foreigncurrencysale=$reportresult[$i]["foreigncurrencysale"];
$collectionamount=$reportresult[$i]["collectionamount"];
////////////////////////////////////////////////////////////////Total Discount//////////////////////////////////////////
	$fields = array('countername','closingnumber','totalbills','totalsale','returnsale','coupon_sold','used_coupon','payout','cashsale','creditsale','creditcardsale','credit_sale_return','discount','chequesale','foreigncurrencysale','collectionamount','datetime');
	$values = array($countername,$closingnumber,$totalbills,$totalsal,$returnsale,$coupon_sold,$used_coupon,$payout,$cashsale,$creditsale,$creditcardsale,$credit_sale_return,$discount,$chequesale,$foreigncurrencysale,$collectionamount,time());

	$AdminDAO->insertrow("$dbname_detail.collective_mail_data",$fields,$values);
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
}

$table .='<tr>
   <td  colspan="3" > Total</td>
	<td >'.$totalsale.'</td>
		<td >'.$discount_total.'</td>
	<td >'.$returnsale_total.'</td>
	<td >'.$coupon_sold_total.'</td>
	<td >'.$used_coupon_total.'</td>
	<td >'.$payouts_total.'</td>
	<td >'.$cashsale_total.'</td>
	<td >'.$crdsale_total.'</td>
	<td >'.$creditreturnsale_total.'</td>

	<td >'.$creditcardsale_total.'</td>
	<td >'.$chequesale_total.'</td>
	<td >'.$foreigncurrencysale_total.'</td>
	<td >'.$collectionamount_total.'</td>
  </tr>';
$table.='</table></body></html>';
$body = $table;
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From:" . $from;
$msent=mail($to,$subject,$body,$headers);
/////////////////////////////////////
 file_get_contents("https://kohsar.esajee.com/admin/accounts/dailystockreportmail.php");	 
 file_get_contents("https://dha.esajee.com/admin/accounts/counter_wise_dha.php");	 
 file_get_contents("https://gulberg.esajee.com/admin/accounts/counter_wise_gulberg.php");	 
 file_get_contents("https://pharmadha.esajee.com/admin/accounts/counter_wise_pharma.php");
 file_get_contents("https://warehouse.esajee.com/admin/accounts/wdailymail.php");	
 file_get_contents("https://kohsar.esajee.com/admin/accounts/get_counter_demands.php");
 //file_get_contents("https://main.esajee.com/admin/accounts/combinedmonthlyreport.php");
////////////////////////////////////////////////////Combined Reports//////////////////////////////////////////////////////////////////////////////////////////////////////
/*file_get_contents("https://main.esajee.com/admin/lowest_selling_items_report.php?sdate=01-04-2014&edate=15-04-2014&loc=All&nzero=1&email=1");

file_get_contents("https://main.esajee.com/admin/heighest_selling_items_report.php?sdate=01-04-2014&edate=15-04-2014&loc=All&nzero=1&email=1");

file_get_contents("https://main.esajee.com/admin/profit_new_report.php?sdate=01-04-2014&edate=15-04-2014&loc=All&email=1");

file_get_contents("https://main.esajee.com/admin/stock_by_sypplier_report.php?sdate=01-04-2014&edate=15-04-2014&supplier=&loc=All&email=1");

file_get_contents("https://main.esajee.com/admin/stock_by_brand_report.php?sdate=01-04-2014&edate=15-04-2014&brandname=&loc=All&email=1");

file_get_contents("https://main.esajee.com/admin/stock_by_product_report.php?sdate=01-04-2014&edate=15-04-2014&productname=&loc=All&email=1"); */
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$body = addslashes($body);	
$time_to_send=date('Y-m-d h:i:s');
$created=date('Y-m-d');
$query= mysql_query("INSERT INTO $dbname_detail.closing_email (subject, body, `to`, `from`, time_to_send, login_id,created_at)
VALUES ('$subject','".$body."','$to','$from','$time_to_send','$addressbookid','$time_to_send')");
 
/////////////////////////////////////////////////Update Stock Quries/////////////////////////////////////////////////////////////////////////////////////////////////////////// 
 $query = "SELECT pkclosingid from $dbname_detail.closinginfo where closingstatus='a' and (FROM_UNIXTIME(openingdate,'%d-%m-%Y') = '$date'  or FROM_UNIXTIME(closingdate,'%d-%m-%Y') = '$date' ) ";
$updateserverstock = $AdminDAO->queryresult($query);
$row_closing=count($updateserverstock);
for($ix=0;$ix<$row_closing;$ix++)
{
 $Closingid=$updateserverstock[$ix]["pkclosingid"];
 $get_quantity = "SELECT  sum(quantity) soldquantity, fkstockid from $dbname_detail.saledetail where fkclosingid='$Closingid' group by fkstockid";
 $result = $AdminDAO->queryresult($get_quantity);
 $row_=count($result);
for($x=0;$x<$row_;$x++)
{
	 $Units=$result[$x]["soldquantity"];
	 $Sid=$result[$x]["fkstockid"];
	 if($Units<1000 and $Units > -1000){
     $set_quantity = "update $dbname_detail.stock  set unitsremaining=(unitsremaining-$Units),updatetime='".time()."' where pkstockid='$Sid'";
     $AdminDAO->queryresult($set_quantity);
	 }
}
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>