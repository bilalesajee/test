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
$date	=	date('m-Y',(strtotime ( '-1 month' , $date ) ));
$date1=date('F Y',(strtotime ( '-1 month' , time()) ));
$from = "Kohsar SHOP System <kohsar@esajee.com>";
//$to = "hesajee@gmail.com,m_esajee@hotmail.com,esajeeco@gmail.com";
//$to = "fahadbuttqau@gmail.com,accounts@esajee.com,hesajee@gmail.com";

$to = "fahadbuttqau@gmail.com";
$subject = "Esajee Kohsar Monthly Sale Report of ".$date1;
 $query = "select cl.countername,
sum(cl.openingbalance) as openingbalance,
sum(cl.cashsale) as cashsale,
sum(cl.creditsale) as creditsale,
sum(cl.creditcardsale) as creditcardsale, 
sum(cl.chequesale) as chequesale,
sum(cl.foreigncurrencysale) as foreigncurrencysale,
    sum(cl.totalbills) as totalbills,
	 sum(cl.totalsale) as totalsale 
FROM $dbname_detail.closinginfo cl  WHERE cl.closingstatus='a' and FROM_UNIXTIME(cl.openingdate,'%m-%Y') = '$date' group by cl.countername ";
$reportresult = $AdminDAO->queryresult($query);
$row_run=count($reportresult);


 $query1 = "SELECT SUM(ac.amount) as payouts FROM $dbname_detail.accountpayment ac,$dbname_detail.closinginfo cl WHERE ac.fkclosingid = cl.pkclosingid 
   and cl.closingstatus='a' and FROM_UNIXTIME(cl.openingdate,'%m-%Y') = '$date' group by cl.countername ";
$reportresult1 = $AdminDAO->queryresult($query1);
$row_run1=count($reportresult1);

 $query2 = "SELECT SUM(cm.amount) as used_coupon FROM $dbname_detail.coupon_management cm,$dbname_detail.closinginfo cl where cm.fkclosingid=cl.pkclosingid and cm.status=2   and cl.closingstatus='a' and FROM_UNIXTIME(cl.openingdate,'%m-%Y') = '$date' group by cl.countername ";
$reportresult2 = $AdminDAO->queryresult($query2);
$row_run2=count($reportresult2);
 $query3 = "SELECT SUM(c.amount) as coupon_sold FROM $dbname_detail.coupon_management c,$dbname_detail.closinginfo cl where c.fkclosingid=cl.pkclosingid and c.status = 1 and     cl.closingstatus='a' and FROM_UNIXTIME(cl.openingdate,'%m-%Y') = '$date' group by cl.countername ";
$reportresult3 = $AdminDAO->queryresult($query3);
$row_run3=count($reportresult3);

 $query4 = "SELECT sum( c.amount ) as collectionamount from $dbname_detail.collection c,$dbname_detail.closinginfo cl WHERE c.fkclosingid = cl.pkclosingid and c.amount >0 and cl.closingstatus='a' and FROM_UNIXTIME(cl.openingdate,'%m-%Y') = '$date' group by cl.countername ";
$reportresult4 = $AdminDAO->queryresult($query4);
$row_run4=count($reportresult4);

 $query5 = "SELECT sum(sd.quantity*sd.saleprice)*-1 as returnsale from $dbname_detail.sale ss ,$dbname_detail.saledetail sd,$dbname_detail.closinginfo cl WHERE ss.fkclosingid = cl.pkclosingid  and ss.status = 1 and sd.quantity < 0 and ss.pksaleid=sd.fksaleid and FROM_UNIXTIME(cl.openingdate,'%m-%Y') = '$date' group by cl.countername";
$reportresult5 = $AdminDAO->queryresult($query5);
$row_run5=count($reportresult5);


 $query6 = "SELECT sum(sd.quantity*sd.saleprice)*-1 as returncrediitsale from $dbname_detail.sale sss ,$dbname_detail.saledetail sd,$dbname_detail.closinginfo cl WHERE sss.fkclosingid = cl.pkclosingid  and sss.status = 1 and sd.quantity < 0 and sss.pksaleid=sd.fksaleid and sss.fkaccountid > 0 and FROM_UNIXTIME(cl.openingdate,'%m-%Y') = '$date' group by cl.countername";
$reportresult6 = $AdminDAO->queryresult($query6);
$row_run6=count($reportresult6);

 $query7 = "SELECT SUM(sa.globaldiscount) as discount FROM $dbname_detail.sale sa,$dbname_detail.closinginfo cl WHERE sa.fkclosingid = cl.pkclosingid and cl.closingstatus='a' and FROM_UNIXTIME(cl.openingdate,'%m-%Y') = '$date' group by cl.countername";
$reportresult7 = $AdminDAO->queryresult($query7);
$row_run7=count($reportresult7);


$table ='';
$sql="SELECT 	storephonenumber,storeaddress	from store where pkstoreid='3'";
$storearray=	$AdminDAO->queryresult($sql);
$storenameadd=	$storearray[0]['storeaddress'].', '.$storearray[0]['storephonenumber'];

$table="<link rel='stylesheet' type='text/css' href='https://kohsar.esajee.com/includes/css/style.css' />
<div align='left'>
<div > <img src='https://kohsar.esajee.com/images/esajeelogo.jpg' width='150' height='50'><br />
<b>Think globally shop locally</b> <br />". $storenameadd."</span> </div>";
$table .='
<table width="100%" border="1" align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">
 

 
 <tr>
    <th height="50" colspan="16" align="center" style="font-size:14px; border:none " bgcolor="#FFFFFF" ><span style="font-size:24px; ">Counter Wise Sale Summary</th>
  </tr>
  <tr>
    <th height="18" colspan="16" align="right" style="font-size:14px; border:none " bgcolor="#FFFFFF" ></th>
  </tr>
  <tr>
    <th height="18" colspan="16" align="center" style="font-size:14px; " bgcolor="#FFFFFF" >Date:'.$date.'</th>
  </tr>';
 $table .=' <tr>
    <th width="235" height="31" bgcolor="#C0944B" >Counter</th>
    <th width="275" bgcolor="#C0944B" >No of Bills</th>
    <th width="275" bgcolor="#C0944B" >Sale</th>
    <th width="275" bgcolor="#C0944B" >Discount</th>
    <th width="275" bgcolor="#C0944B" >Sale Return</th>
    <th width="275" bgcolor="#C0944B" >Coupon Sold</th>
    <th width="275" bgcolor="#C0944B" >Coupon Used</th>
    <th width="275" bgcolor="#C0944B" >Payouts ( petty cash)</th>
    <th width="275" bgcolor="#C0944B" >Cash</th>
    <th width="275" bgcolor="#C0944B" >Credit Sale</th>
    <th width="275" bgcolor="#C0944B" >Credit Sale Return</th>
    <th width="275" bgcolor="#C0944B" >Credit Card</th>
    <th width="275" bgcolor="#C0944B" >Cheque</th>
    <th width="275" bgcolor="#C0944B" >Forigen Currency</th>
    <th width="275" bgcolor="#C0944B" >Collection</th>
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
		$returnsale_total +=$reportresult5[$i]["returnsale"];
		$coupon_sold_total +=$reportresult3[$i]["coupon_sold"];
		$used_coupon_total +=$reportresult2[$i]["used_coupon"];
		$payouts_total +=$reportresult1[$i]["payouts"];
		$cashsale_total +=$reportresult[$i]["cashsale"];
		$crdsale_total +=$reportresult[$i]["creditsale"];		
		$creditcardsale_total +=$reportresult[$i]["creditcardsale"];
		$creditreturnsale_total +=$reportresult6[$i]["returncrediitsale"];
		$discount_total +=$reportresult7[$i]["discount"];
		$chequesale_total +=$reportresult[$i]["chequesale"];
		$foreigncurrencysale_total +=$reportresult[$i]["foreigncurrencysale"];
		$collectionamount_total += $reportresult4[$i]["collectionamount"];
	$rs=$reportresult5[$i]["returnsale"];
	if($rs==''){
		$rs=0;
		}  
		
		$rs1=$reportresult3[$i]["coupon_sold"];
	if($rs1==''){
		$rs1=0;
		}  
		
		$rs2=$reportresult2[$i]["used_coupon"];
	if($rs2==''){
		$rs2=0;
		}  
		
		$rs3=$reportresult4[$i]["collectionamount"];
	if($rs3==''){
		$rs3=0;
		}  
		
		$table .='<tr>
   <td width="235" align="center"> '.$reportresult[$i]["countername"].'</td>
  <td width="275" align="left">'.$reportresult[$i]["totalbills"].'</td>
	<td width="275" align="right">'.$reportresult[$i]["totalsale"].'</td>
	<td width="275" align="right">'.$reportresult7[$i]["discount"].'</td>
	<td width="275" align="right">'.$rs.'</td>
	<td width="275" align="right">'.$rs1.'</td>
	<td width="275" align="right">'.$rs2.'</td>
	<td width="275" align="right">'.$reportresult1[$i]["payouts"].'</td>
	<td width="275" align="right">'.$reportresult[$i]["cashsale"].'</td>
	<td width="275" align="right">'.$reportresult[$i]["creditsale"].'</td>
	<td width="275" align="right">'.$reportresult6[$i]["returncrediitsale"].'</td>	
	<td width="275" align="right">'.$reportresult[$i]["creditcardsale"].'</td>
	<td width="275" align="right">'.$reportresult[$i]["chequesale"].'</td>
	<td width="275" align="right">'.$reportresult[$i]["foreigncurrencysale"].'</td>
	<td width="275" align="right">'.$rs3.'</td>
	 </tr>';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 
$countername=$reportresult[$i]["countername"];
$closingnumber=$reportresult[$i]["pkclosingid"];
$totalbills=$reportresult[$i]["totalbills"];
$totalsal=$reportresult[$i]["totalsale"];
$returnsale=$reportresult5[$i]["returnsale"];
$coupon_sold=$reportresult3[$i]["coupon_sold"];
$used_coupon=$reportresult2[$i]["used_coupon"];
$payout=$reportresult1[$i]["payouts"];
$cashsale=$reportresult[$i]["cashsale"];
$creditsale=$reportresult[$i]["creditsale"];
$creditcardsale=$reportresult[$i]["creditcardsale"];
$credit_sale_return=$reportresult6[$i]["returncrediitsale"];
$discount=$reportresult7[$i]["discount"];
$chequesale=$reportresult[$i]["chequesale"];
$foreigncurrencysale=$reportresult[$i]["foreigncurrencysale"];
$collectionamount=$reportresult4[$i]["collectionamount"];

	$fields = array('countername','closingnumber','totalbills','totalsale','returnsale','coupon_sold','used_coupon','payout','cashsale','creditsale','creditcardsale','credit_sale_return','discount','chequesale','foreigncurrencysale','collectionamount','datetime');
	$values = array($countername,$closingnumber,$totalbills,$totalsal,$returnsale,$coupon_sold,$used_coupon,$payout,$cashsale,$creditsale,$creditcardsale,$credit_sale_return,$discount,$chequesale,$foreigncurrencysale,$collectionamount,time());

//	$AdminDAO->insertrow("$dbname_detail.collective_mail_data",$fields,$values);
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
}

$table .='<tr>
   <td width="235" colspan="2" align="right" style="font-weight:bold"> Total</td>
	<td width="275" align="right">'.$totalsale.'</td>
		<td width="275" align="right">'.$discount_total.'</td>
	<td width="275" align="right">'.$returnsale_total.'</td>
	<td width="275" align="right">'.$coupon_sold_total.'</td>
	<td width="275" align="right">'.$used_coupon_total.'</td>
	<td width="275" align="right">'.$payouts_total.'</td>
	<td width="275" align="right">'.$cashsale_total.'</td>
	<td width="275" align="right">'.$crdsale_total.'</td>
	<td width="275" align="right">'.$creditreturnsale_total.'</td>

	<td width="275" align="right">'.$creditcardsale_total.'</td>
	<td width="275" align="right">'.$chequesale_total.'</td>
	<td width="275" align="right">'.$foreigncurrencysale_total.'</td>
	<td width="275" align="right">'.$collectionamount_total.'</td>
  </tr>';
$table.='</table>';
echo $body = $table;
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From:" . $from;
$msent=mail($to,$subject,$body,$headers);
/////////////////////////////////////
 //////////////////////////////////////
$body = addslashes($body);	
$time_to_send=date('Y-m-d h:i:s');
$created=date('Y-m-d');
//$query= mysql_query("INSERT INTO $dbname_detail.closing_email (subject, body, `to`, `from`, time_to_send, login_id,created_at)
//VALUES ('$subject','".$body."','$to','$from','$time_to_send','$addressbookid','$time_to_send')");

?>