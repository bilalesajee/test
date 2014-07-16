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
$var=1;
$date=time();
$var2=$var+1;
 $var=-($var);
 $var2=-($var2);
 $date	=	date('m-Y',(strtotime ( $var.'month' , $date ) ));

$date1=date('F Y',(strtotime ( $var.'month' , time()) ));

$prevmonth = date('t-m-Y', strtotime($var2.'months'));
$prevmonth=strtotime($prevmonth);

//$date1='08-2013';

//$date='08-2013';

$from = "Kohsar SHOP System <kohsar@esajee.com>";

//$to = "hesajee@gmail.com,m_esajee@hotmail.com,esajeeco@gmail.com";

$to = "accounts@esajee.com,hesajee@gmail.com";

//$to = "fahadbuttqau@gmail.com";

$subject = "Esajee Kohsar Monthly Stock Report of ".$date1;

//////////////////////////////////////////////////////////stock query///////////////////////////////////////////////////////////////////////////////////

 /* $query_supplier = "SELECT  s.fksupplierid , s.fksupplierinvoiceid ,round(SUM(s.quantity * s.priceinrs),2) as amount_value , sl.companyname , si.billnumber from  $dbname_detail.stock s , mydb.supplier sl, $dbname_detail.supplierinvoice si
	 where s.fksupplierid=sl.pksupplierid and s.fksupplierinvoiceid=si.pksupplierinvoiceid and FROM_UNIXTIME(s.addtime,'%m-%Y') = '$date' group by  s.fksupplierid , s.fksupplierinvoiceid ";

*/	 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $dbname_main="main";
	 $dbname_detail="main_kohsar";
	  $query_supplier = "SELECT  s.fksupplierid ,

	   s.fksupplierinvoiceid ,

	   round(SUM(s.quantity * s.priceinrs),2) as amount_value ,
      round(SUM(s.quantity),2) as quantity ,
	    sl.companyname ,

		 si.billnumber,

	 (select sum(r.quantity*s.priceinrs) from $dbname_detail.returns r where r.fkstockid=s.pkstockid)  return_value

	 from  $dbname_detail.stock s left join  $dbname_main.supplier sl on (s.fksupplierid=sl.pksupplierid), $dbname_detail.supplierinvoice si

	 where   s.fksupplierinvoiceid=si.pksupplierinvoiceid and si.invoice_status=1 and FROM_UNIXTIME(s.addtime,'%m-%Y') = '$date' group by  s.fksupplierid  ";
	 $reportresult_supplier = $AdminDAO->queryresult($query_supplier);
     $row_run_supplier=count($reportresult_supplier);
	 
////2/////////////////////////////////////////////////////////Item Wise///////////////////////////////////////////////////////////////////////////////////////
	  $query_item = "SELECT  slp.productname,slb.brandname,s.fkbarcodeid,
sl.itemdescription as fksupplierid ,
  s.fksupplierinvoiceid ,
  round(SUM(s.quantity * s.priceinrs),2) as amount_value ,
 round(SUM(s.quantity),2) as quantity , 
(select sum(r.quantity*s.priceinrs) from $dbname_detail.returns r where r.fkstockid=s.pkstockid)  return_value


	 from  $dbname_detail.stock s left join $dbname_main.barcode sl on (s.fkbarcodeid=sl.pkbarcodeid) left join $dbname_main.brand slb on (s.fkbrandid=pkbrandid) left join $dbname_main.product slp on (s.fkproduct_id=pkproductid), $dbname_detail.supplierinvoice si

	 where   s.fksupplierinvoiceid=si.pksupplierinvoiceid and si.invoice_status=1 and FROM_UNIXTIME(s.addtime,'%m-%Y') = '$date' group by  s.fkbarcodeid order by slp.productname,slb.brandname  ";
	 $reportresult_item = $AdminDAO->queryresult($query_item);
     $row_run_item=count($reportresult_item);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////2/////////////////////////////////////////////////////////Brand Wise///////////////////////////////////////////////////////////////////////////////////////
	  $query_brand = "SELECT  s.fkbrandid,sl.brandname as fksupplierid ,  s.fksupplierinvoiceid ,  round(SUM(s.quantity * s.priceinrs),2) as amount_value ,  
      round(SUM(s.quantity),2) as quantity ,
	 (select sum(r.quantity*s.priceinrs) from $dbname_detail.returns r where r.fkstockid=s.pkstockid)  return_value

	 from  $dbname_detail.stock s left join $dbname_main.brand sl on (s.fkbrandid=pkbrandid), $dbname_detail.supplierinvoice si

	 where   s.fksupplierinvoiceid=si.pksupplierinvoiceid and si.invoice_status=1 and FROM_UNIXTIME(s.addtime,'%m-%Y') = '$date'  group by  s.fkbrandid  ";
	 $reportresult_brand = $AdminDAO->queryresult($query_brand);
     $row_run_brand=count($reportresult_brand);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////2/////////////////////////////////////////////////////////Product Wise///////////////////////////////////////////////////////////////////////////////////////
	  $query_product = "SELECT  s.fkproduct_id,sl.productname as fksupplierid ,  s.fksupplierinvoiceid ,  round(SUM(s.quantity * s.priceinrs),2) as amount_value ,  
	 round(SUM(s.quantity),2) as quantity ,
	 (select sum(r.quantity*s.priceinrs) from $dbname_detail.returns r where r.fkstockid=s.pkstockid)  return_value
	 from  $dbname_detail.stock s left join $dbname_main.product sl on (s.fkproduct_id=pkproductid), $dbname_detail.supplierinvoice si
	 where   s.fksupplierinvoiceid=si.pksupplierinvoiceid and si.invoice_status=1 and FROM_UNIXTIME(s.addtime,'%m-%Y') = '$date'  group by  s.fkproduct_id  ";
	 $reportresult_product = $AdminDAO->queryresult($query_product);
     $row_run_product=count($reportresult_product);
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////2/////////////////////////////////////////////////////////Movement Wise///////////////////////////////////////////////////////////////////////////////////////
	  $query_item2 = "SELECT  s.fkbarcodeid,sl.itemdescription as fksupplierid , round(SUM(s.quantity * s.priceinrs),2) as amount_value ,  srcstoreid,
     round(SUM(s.quantity),2) as quantity ,
	 (select sum(r.quantity*s.priceinrs) from $dbname_detail.returns r where r.fkstockid=s.pkstockid)  return_value

	 from  $dbname_detail.stock s left join $dbname_main.barcode sl on (s.fkbarcodeid=sl.pkbarcodeid)

	 where fkconsignmentdetailid > 0 and FROM_UNIXTIME(s.addtime,'%m-%Y') = '$date' group by  s.fkbarcodeid  ";
	 $reportresult_item2 = $AdminDAO->queryresult($query_item2);
     $row_run_item2=count($reportresult_item2);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////Stock Difference///////////////////////////////////////////////////////////////////////////////////////
   $query_item_diff = "SELECT b.pkbarcodeid,
                   b.itemdescription,
                   (select round(SUM(st.quantity * st.priceinrs),2) as open_value from $dbname_detail.stock st where st.fkbarcodeid=b.pkbarcodeid and  st.addtime  between 1356935826 and $prevmonth ) open_value,
                  (select round(SUM(stt.quantity),2) as open_quantity from $dbname_detail.stock stt where stt.fkbarcodeid=b.pkbarcodeid and  stt.addtime  between 1356935826 and $prevmonth ) open_quantity,
                   round(SUM(s.quantity * s.priceinrs),2) as stock_value,
                   sum(s.quantity)  as stock_quantity,
                   round(SUM(sd.quantity * sd.saleprice),2) as sale_value,
                   round(SUM(sd.quantity),2) as sale_quantity  
                   from $dbname_detail.stock s left join $dbname_main.barcode b on (s.fkbarcodeid=b.pkbarcodeid) left join $dbname_detail.saledetail sd on (s.pkstockid=sd.fkstockid) 
where FROM_UNIXTIME(s.addtime,'%m-%Y') = '$date' and FROM_UNIXTIME(sd.timestamp,'%m-%Y') = '$date' group by s.fkbarcodeid ";
	 $reportresult_item_diff = $AdminDAO->queryresult($query_item_diff);
     $row_run_item_diff=count($reportresult_item_diff);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////Higest Sale Return Items///////////////////////////////////////////////////////////////////
$query = "SELECT 

					s.pksaleid,

					CONCAT(a.firstname,' ',a.lastname) cashiername,

					FROM_UNIXTIME(s.datetime,'%d-%m-%Y') datetime,

					b.barcode,

					b.itemdescription,

					sum(sd.quantity) as quantity,

					sum(sd.saleprice) as saleprice

					

				FROM 

					$dbname_detail.sale s

					left join $dbname_detail.saledetail sd on sd.fksaleid = s.pksaleid 

					left join $dbname_detail.stock stk on stk.pkstockid = sd.fkstockid 

					left join $dbname_main.barcode b on b.pkbarcodeid = stk.fkbarcodeid

					left join $dbname_main.addressbook a on a.pkaddressbookid = s.fkuserid

				WHERE

					

					s.status = 1 AND

					sd.quantity<0  AND  FROM_UNIXTIME(s.datetime,'%m-%Y') = '$date'

				GROUP BY 

					stk.fkbarcodeid

				ORDER BY 

					sum(sd.quantity) ASC limit 100";



$reportresult_return = $AdminDAO->queryresult($query);

$row_run_return=count($reportresult_return);



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

 $query_purchase = "SELECT 

					r.pkreturnid,

					sl.companyname,

					FROM_UNIXTIME(r.returndate,'%d-%m-%Y') datetime,

					b.barcode,

					b.itemdescription,

					sum(r.quantity) as quantity,

					round(sum(stk.priceinrs),2) as purchaseprice

					

				FROM 

					$dbname_detail.stock stk,$dbname_main.barcode b ,$dbname_detail.returns r,$dbname_main.supplier sl

						                               				

				WHERE

				  FROM_UNIXTIME(r.returndate,'%m-%Y') = '$date' and b.pkbarcodeid = stk.fkbarcodeid and r.fkstockid=stk.pkstockid and stk.fksupplierid=sl.pksupplierid

				GROUP BY 

					stk.fkbarcodeid

				ORDER BY 

					sum(r.quantity) DESC limit 100";



$reportresult_purchase = $AdminDAO->queryresult($query_purchase);

$row_run_purchase=count($reportresult_purchase);

/////////////////////////////////////////////////////////////////////////////////////////

 $query22 = "select cl.countername,
count(cl.countername) as numclosing,
sum(cl.openingbalance) as openingbalance,
sum(cl.cashsale) as cashsale,
sum(cl.creditsale) as creditsale,
sum(cl.creditcardsale) as creditcardsale, 
sum(cl.chequesale) as chequesale,
sum(cl.foreigncurrencysale) as foreigncurrencysale,
sum(cl.totalbills) as totalbills,
sum(cl.totalsale) as totalsale ,
IF( sum(cashdiffirence) > 0,CONCAT(round(sum(cashdiffirence),2),' Extra'),CONCAT(round(sum(cashdiffirence),2),' Short') ) as cashdiffirence  
FROM $dbname_detail.closinginfo cl  WHERE cl.closingstatus='a' and FROM_UNIXTIME(cl.openingdate,'%m-%Y') = '$date' group by cl.countername";
$reportresult = $AdminDAO->queryresult($query22);
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







$table1 ='';
$table2 ='';
$table3 ='';
$table4 ='';
$table5 ='';
$table6 ='';
$table7 ='';
$table8 ='';
$table9 ='';


$sql="SELECT 	storephonenumber,storeaddress	from store where pkstoreid='3'";

$storearray=	$AdminDAO->queryresult($sql);

$storenameadd=	$storearray[0]['storeaddress'].', '.$storearray[0]['storephonenumber'];





$table1 .="<link rel='stylesheet' type='text/css' href='https://kohsar.esajee.com/includes/css/style.css' />

<div align='left'>

<div > <img src='https://kohsar.esajee.com/images/esajeelogo.jpg' width='150' height='50'><br />

<b>Think globally shop locally</b> <br />". $storenameadd."</span> </div>";

$table1.='

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>

<style>

.Table {

	float: left; width: 100%;

}

.Row {

	float: left; width: 100%;

}

.Column {

	float: left; width: 33%; margin:5px 0 2px 0;

}

/*********** Simple Table ****/

.simple,th,td{

	font-size:11px;

	border:1px solid #666;

	border-collapse:collapse;

}

.simple table{

	border:none;

	border-top:1px solid #000;

}

.simple thead th{

	background:#fff;

	padding:3px 7px;

	text-transform:uppercase;

	color:#333;

}

.simple tbody td,.simple tbody th{

	padding:3px 7px;

	

}

/*add from store_style.css by ahsan 21/02/2012*//*

.simple_store tbody th{

	background:#333;

	color:#FFF;

	font-weight:bold;

}

/*end add*/

.simple tbody th{

	background:#333;

	color:#FFF;

	font-weight:bold;

}

.simple tbody tr.odd td{

	background:#ddd;

}

.simple tbody tr.odd th{

	background:#fff;

	font-weight:bold;/*line added from store_style.css by ahsan 21/02/2012*/

	color:#333;

}

.simple tfoot td,.simple tfoot th{

	border:none;

	padding-top:10px;

}

.simple caption{

	font-family:Tahoma;

	text-align:left;

	text-transform:uppercase;

	padding:10px 0;

	color:#036;

}

.simple table a:link{

	color:#369;

}

.simple table a:visited{

	color:#036;

}

.simple table a:hover{

	color:#000;

	text-decoration:none;

}

.simple table a:active{

	color:#000;

}

.simple2, .simple2 th, .simple2 td{

	font-size:10px;

	border:1px solid #666;

	border-collapse:collapse;

	padding:2px 4px 2px 2px;

	

}

.simple2 table{

	border:none;

	border-top:1px solid #000;

}

.simplebold, .simplebold td {

	border:1px solid #666;

	border-collapse:collapse;

	padding:2px 4px 2px 2px;

}

.simplebold th {

	padding:2px 4px 2px 2px;

	font-size:11px;

	text-transform:uppercase;

	font-weight:bold;

	color:#fff;

	background:#000;

}

.simplebold table{

	border:none;

	border-top:1px solid #000;

}

</style>



</head>

<table width="100%"  align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">

 



 

 <tr>

    <th height="50" colspan="17" align="center" class="simple" style="font-size:14px; border:none;color:#FFFFFF " bgcolor="#333" ><span style="font-size:24px; ">Monthly Counter Wise Sale Summary</th>

  </tr>

  <tr>

    <th height="18" colspan="17" align="right" style="font-size:14px; border:none;color:#FFFFFF " class="simple" bgcolor="#333" ></th>

  </tr>

  <tr>

    <th height="18" colspan="17" align="center" style="font-size:14px;color:#FFFFFF " class="simple" bgcolor="#333" >Month:'.$date.'</th>

  </tr>';

 $table1 .=' <tr>

    <th width="235" height="31" class="simple" bgcolor="#333" style="color:#FFFFFF"  >Counter</th>
     <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF"  >No of Closings</th>
    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF"  >No of Bills</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" >Sale</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" >Discount</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" >Sale Return</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF"  >Coupon Sold</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF"  >Coupon Used</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF"  >Payouts ( petty cash)</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF"  >Cash</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" >Credit Sale</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" >Credit Sale Return</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" >Credit Card</th>
    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF"  >Cheque</th>
    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" >Forigen Currency</th>
    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF"  >Collection</th>
	 <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF"  >Cash Diff</th>

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
$total_diff=0;
 

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
		$total_diff += $reportresult[$i]["cashdiffirence"];

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

		

		$table1 .='<tr>

   <td width="235" align="center"> '.$reportresult[$i]["countername"].'</td>
<td width="235" align="center"> '.$reportresult[$i]["numclosing"].'</td>
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
	<td width="275" align="right">'.$reportresult[$i]["cashdiffirence"].'</td>

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



//$AdminDAO->insertrow("$dbname_detail.collective_mail_data",$fields,$values);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	 

}



$table1 .='<tr>

   <td width="235" colspan="3" align="right" style="font-weight:bold"> Total</td>

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
	<td width="275" align="right">'.$total_diff.'</td>

  </tr>';

$table1.='</table><p></p>';
$body1 = addslashes($table1);	
$time_to_send=time();
$queryrp="INSERT INTO $dbname_detail.monthly_reports (searchdate,subject,body,datetime) VALUES ('$date','Monthly Counter Wise Sale Summary','{$body1}','$time_to_send')";
$AdminDAO->queryresult($queryrp);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table2 .='

<table width="100%"  align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">
<tr>

    <th height="50" colspan="4" align="center" style="font-size:14px; border:none;color:#FFFFFF" class="simple" bgcolor="#333"  ><span style="font-size:24px; ">Stock Report of '.$date1.' Supplier Wise</span></th>

  </tr>';

 $table2 .=' <tr>

    <th width="435" height="31" class="simple" bgcolor="#333" style="color:#FFFFFF" >Supplier</th>';

  /*  <th width="204" bgcolor="#C0944B"  align="left">Invoice#</th>

    <th width="275" bgcolor="#C0944B" >Bill #</th>*/

      $table2 .='<th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Invoice Amount</th>
	            <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Quantity</th>

	            <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Return Amount</th>

                </tr>';

   $totalvalue = 0;

  

 

for($i=0;$i<$row_run_supplier;$i++)

{

		$stotalvalue += $reportresult_supplier[$i]["amount_value"];

		$totalrvalue += $reportresult_supplier[$i]["return_value"];
		$tq += $reportresult_supplier[$i]["quantity"];

		

		$table2 .='<tr>

   <td width="435" align="left"> '.$reportresult_supplier[$i]["companyname"].' [ '.$reportresult_supplier[$i]["fksupplierid"].' ]</td>';

  

/*    <td align="left"> '.$reportresult_supplier[$i]["fksupplierinvoiceid"].'</td>

    <td width="275" align="left">'.$reportresult_supplier[$i]["billnumber"].'</td>

*/	$table2 .='<td width="275" align="right">'.round($reportresult_supplier[$i]["amount_value"],2).'</td>
<td width="275" align="right">'.round($reportresult_supplier[$i]["quantity"],2).'</td>

              <td width="275" align="right">'.round($reportresult_supplier[$i]["return_value"],2).'</td>

	 </tr>';

}



$table2 .='<tr>

   <td width="235"  align="right" style="font-weight:bold"> Total</td>

   <td width="275" align="right">'.round($stotalvalue,2).'</td>
   <td width="275" align="right"></td>
   <td width="275" align="right">'.round($totalrvalue,2).'</td>

	 </tr>';

$table2.='</table><p>&nbsp;&nbsp;</p>';
$body2 = addslashes($table2);	
$time_to_send=time();
$queryrp="INSERT INTO $dbname_detail.monthly_reports (searchdate,subject,body,datetime) VALUES ('$date','Supplier Wise','{$body2}','$time_to_send')";
$AdminDAO->queryresult($queryrp);
$table22 .='
<table width="100%"  align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">
 <tr>

    <th height="50" colspan="6" align="center" style="font-size:14px; border:none;color:#FFFFFF" class="simple" bgcolor="#333"  ><span style="font-size:24px; ">Stock Report of '.$date1.' Item Wise</span></th>

  </tr>';

 $table22 .=' <tr>
 <th width="435" height="31" class="simple" bgcolor="#333" style="color:#FFFFFF" >Product</th>
  <th width="435" height="31" class="simple" bgcolor="#333" style="color:#FFFFFF" >Brand</th>
    <th width="435" height="31" class="simple" bgcolor="#333" style="color:#FFFFFF" >Item</th>';

  /*  <th width="204" bgcolor="#C0944B"  align="left">Invoice#</th>

    <th width="275" bgcolor="#C0944B" >Bill #</th>*/

      $table22 .='<th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Invoice Amount</th>
                <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Quantity</th> 
	            <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Return Amount</th>

                </tr>';

   $totalvalue = 0;

  

 

for($i=0;$i<$row_run_item;$i++)

{

		
		$tname=$reportresult_item[$i]["fksupplierid"];
		if($tname==''){
			$tname="No Name";
			}else{
			$tname=$reportresult_item[$i]["fksupplierid"];	
				}
				
				$ptname=$reportresult_item[$i]["productname"];
		if($ptname==''){
			$ptname="No Name";
			}else{
			$ptname=$reportresult_item[$i]["productname"];	
				}
				
				$btname=$reportresult_item[$i]["brandname"];
		if($btname==''){
			$btname="No Name";
			}else{
			$btname=$reportresult_item[$i]["brandname"];	
				}
		$itotalvalue += $reportresult_item[$i]["amount_value"];

		$totalrvalue += $reportresult_item[$i]["return_value"];
        $tq += $reportresult_item[$i]["quantity"];
		if($ptname==$reportresult_item[$i-1]["productname"]){
			$ptname='';
		}
		if($btname==$reportresult_item[$i-1]["brandname"]){
			$btname='';
		}
		

		$table22.='<tr>

   <td width="435" align="left"> '.$ptname.' </td><td width="435" align="left"> '.$btname.' </td><td width="435" align="left"> '.$tname.' </td>';

  

/*    <td align="left"> '.$reportresult_supplier[$i]["fksupplierinvoiceid"].'</td>

    <td width="275" align="left">'.$reportresult_supplier[$i]["billnumber"].'</td>

*/	$table22 .='<td width="275" align="right">'.round($reportresult_item[$i]["amount_value"],2).'</td>
              <td width="275" align="right">'.round($reportresult_item[$i]["quantity"],2).'</td> 
              <td width="275" align="right">'.round($reportresult_item[$i]["return_value"],2).'</td>

	 </tr>';

}



$table22 .='<tr>

   <td width="235"  align="right" style="font-weight:bold" colspan="3"> Total</td>

   <td width="275" align="right">'.round($itotalvalue,2).'</td>
   <td width="275" align="right"></td>
   <td width="275" align="right">'.round($totalrvalue,2).'</td>

	 </tr>';

$table22.='</table><p>&nbsp;&nbsp;</p>';
$body22 = addslashes($table22);	
$time_to_send=time();
$queryrp="INSERT INTO $dbname_detail.monthly_reports (searchdate,subject,body,datetime) VALUES ('$date','Item Wise','{$body22}','$time_to_send')";
$AdminDAO->queryresult($queryrp);

$table3 .='

<table width="100%"  align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">

 



 

 <tr>

    <th height="50" colspan="4" align="center" style="font-size:14px; border:none;color:#FFFFFF" class="simple" bgcolor="#333"  ><span style="font-size:24px; ">Stock Report of '.$date1.' Brand Wise</span></th>

  </tr>';

 $table3 .=' <tr>

    <th width="435" height="31" class="simple" bgcolor="#333" style="color:#FFFFFF" >Brand</th>';

  /*  <th width="204" bgcolor="#C0944B"  align="left">Invoice#</th>

    <th width="275" bgcolor="#C0944B" >Bill #</th>*/

      $table3 .='<th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Invoice Amount</th>
                <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Quantity</th>
	            <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Return Amount</th>

                </tr>';

   $totalvalue = 0;

  

 

for($i=0;$i<$row_run_brand;$i++)

{
	
			$tname=$reportresult_brand[$i]["fksupplierid"];
		if($tname==''){
			$tname="No Name";
			}else{
			$tname=$reportresult_brand[$i]["fksupplierid"];	
				}


		$btotalvalue += $reportresult_brand[$i]["amount_value"];

		$totalrvalue += $reportresult_brand[$i]["return_value"];
		$tq += $reportresult_brand[$i]["quantity"];

		

		$table3 .='<tr>

   <td width="435" align="left"> '.$tname.' </td>';

  

/*    <td align="left"> '.$reportresult_brand[$i]["fksupplierinvoiceid"].'</td>

    <td width="275" align="left">'.$reportresult_brand[$i]["billnumber"].'</td>

*/	$table3 .='<td width="275" align="right">'.round($reportresult_brand[$i]["amount_value"],2).'</td>
              <td width="275" align="right">'.round($reportresult_brand[$i]["quantity"],2).'</td>
              <td width="275" align="right">'.round($reportresult_brand[$i]["return_value"],2).'</td>

	 </tr>';

}



$table3 .='<tr>

   <td width="235"  align="right" style="font-weight:bold"> Total</td>

   <td width="275" align="right">'.round($btotalvalue,2).'</td>
    <td width="275" align="right"></td>
   <td width="275" align="right">'.round($totalrvalue,2).'</td>

	 </tr>';

$table3.='</table><p>&nbsp;&nbsp;</p>';

$body3 = addslashes($table3);	
$time_to_send=time();
$queryrp="INSERT INTO $dbname_detail.monthly_reports (searchdate,subject,body,datetime) VALUES ('$date','Brand Wise','{$body3}','$time_to_send')";
$AdminDAO->queryresult($queryrp);

$table4 .='

<table width="100%"  align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">

 



 

 <tr>

    <th height="50" colspan="4" align="center" style="font-size:14px; border:none;color:#FFFFFF" class="simple" bgcolor="#333"  ><span style="font-size:24px; ">Stock Report of '.$date1.' Product Wise</span></th>

  </tr>';

 $table4 .=' <tr>

    <th width="435" height="31" class="simple" bgcolor="#333" style="color:#FFFFFF" >Product</th>';

  /*  <th width="204" bgcolor="#C0944B"  align="left">Invoice#</th>

    <th width="275" bgcolor="#C0944B" >Bill #</th>*/

      $table4 .='<th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Invoice Amount</th>
                 <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Quantity</th>
	            <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Return Amount</th>

                </tr>';

   $totalvalue = 0;

  

 

for($i=0;$i<$row_run_product;$i++)

{  

     	$tname=$reportresult_product[$i]["fksupplierid"];
		if($tname==''){
			$tname="No Name";
			}else{
			$tname=$reportresult_product[$i]["fksupplierid"];	
				}

		$ptotalvalue += $reportresult_product[$i]["amount_value"];

		$totalrvalue += $reportresult_product[$i]["return_value"];
		$tq += $reportresult_product[$i]["quantity"];

		

		$table4 .='<tr>

   <td width="435" align="left"> '.$tname.' </td>';

  

/*    <td align="left"> '.$reportresult_supplier[$i]["fksupplierinvoiceid"].'</td>

    <td width="275" align="left">'.$reportresult_supplier[$i]["billnumber"].'</td>

*/	$table4 .='<td width="275" align="right">'.round($reportresult_product[$i]["amount_value"],2).'</td>
              <td width="275" align="right">'.round($reportresult_product[$i]["quantity"],2).'</td> 
              <td width="275" align="right">'.round($reportresult_product[$i]["return_value"],2).'</td>

	 </tr>';

}



$table4 .='<tr>

   <td width="235"  align="right" style="font-weight:bold"> Total</td>

   <td width="275" align="right">'.round($ptotalvalue,2).'</td>
   <td width="275" align="right"></td>
   <td width="275" align="right">'.round($totalrvalue,2).'</td>

	 </tr>';

$table4.='</table><p>&nbsp;&nbsp;</p>';
$body4 = addslashes($table4);	
$time_to_send=time();
$queryrp="INSERT INTO $dbname_detail.monthly_reports (searchdate,subject,body,datetime) VALUES ('$date','Product Wise','{$body4}','$time_to_send')";
$AdminDAO->queryresult($queryrp);

$table5.='

<table width="100%"  align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">

 



 

 <tr>

    <th height="50" colspan="5" align="center" style="font-size:14px; border:none;color:#FFFFFF" class="simple" bgcolor="#333"  ><span style="font-size:24px; ">Stock Movement Report of '.$date1.' </span></th>

  </tr>';

 $table5.=' <tr>

    <th width="435" height="31" class="simple" bgcolor="#333" style="color:#FFFFFF" >Item</th>';

  /*  <th width="204" bgcolor="#C0944B"  align="left">Invoice#</th>

    <th width="275" bgcolor="#C0944B" >Bill #</th>*/

      $table5.='<th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right"> Amount</th>
                  <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Location</th>
				  <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Quantity</th>
	            <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Return Amount</th>

                </tr>';

   $totalvalue = 0;

  

 

for($i=0;$i<$row_run_item2;$i++)

{

		
		$tname=$reportresult_item2[$i]["fksupplierid"];
		$locid=$reportresult_item2[$i]["srcstoreid"];
		if($tname==''){
			$tname="No Name";
			}else{
			$tname=$reportresult_item2[$i]["fksupplierid"];	
				}
				
				if($locid==1){
			$locv="DHA";
			}else if($locid==2){
			$locv="Gulberg";	
				}else if($locid==3){
				$locv="Kohsar";
				}else if($locid==4){
			$locv="Warehouse";	
				}else if($locid==5){
				$locv="Pharma";	
					}else{
					$locv= "Unknown";	
						}
		$itotalvalue2 += $reportresult_item2[$i]["amount_value"];

		$totalrvalue += $reportresult_item2[$i]["return_value"];
		
		$tq += $reportresult_item2[$i]["quantity"];

		

		$table5 .='<tr>

   <td width="435" align="left"> '.$tname.' </td>';

  

/*    <td align="left"> '.$reportresult_supplier[$i]["fksupplierinvoiceid"].'</td>

    <td width="275" align="left">'.$reportresult_supplier[$i]["billnumber"].'</td>

*/	$table5.='<td width="275" align="right">'.round($reportresult_item2[$i]["amount_value"],2).'</td>
               <td width="275" align="right">'.$locv.'</td>   
			   <td width="275" align="right">'.round($reportresult_item2[$i]["quantity"],2).'</td>
              <td width="275" align="right">'.round($reportresult_item2[$i]["return_value"],2).'</td>

	 </tr>';

}



$table5.='<tr>

   <td width="235"  align="right" style="font-weight:bold"> Total</td>


   <td width="275" align="right">'.round($itotalvalue2,2).'</td>
    <td width="275" align="right"></td>
   <td width="275" align="right"></td>
   <td width="275" align="right">'.round($totalrvalue,2).'</td>

	 </tr>';

$table5.='</table><p>&nbsp;&nbsp;</p>';
$body5 = addslashes($table5);	
$time_to_send=time();
$queryrp="INSERT INTO $dbname_detail.monthly_reports (searchdate,subject,body,datetime) VALUES ('$date','Movement Wise','{$body5}','$time_to_send')";
$AdminDAO->queryresult($queryrp);

$table6.='<table width="100%"  align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">

 <tr>

    <th height="50" colspan="16" align="center" class="simple" style="font-size:14px; border:none;color:#FFFFFF " bgcolor="#333" ><span style="font-size:24px; ">Monthly Stock Summary</th>

  </tr>';

 $table6.=' <tr>

    <th width="235" height="31" class="simple" bgcolor="#333" style="color:#FFFFFF"  >Supplier Wise</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF"  >Item Wise</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" >Brand Wise</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" >Product Wise</th>

    <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" >Movement</th>

    </tr>';
		$table6.='<tr>

   <td width="235" align="center"> '.round($stotalvalue,2).'</td>

  <td width="275" align="left">'.round($itotalvalue,2).'</td>

	<td width="275" align="right">'.round($btotalvalue,2).'</td>

	<td width="275" align="right">'.round($ptotalvalue,2).'</td>

	<td width="275" align="right">'.round($itotalvalue2,2).'</td>

	 </tr>';
$table6.='</table><p>&nbsp;&nbsp;</p>';
$body6 = addslashes($table6);	
$time_to_send=time();
$queryrp="INSERT INTO $dbname_detail.monthly_reports (searchdate,subject,body,datetime) VALUES ('$date','Stock Summary','{$body6}','$time_to_send')";
$AdminDAO->queryresult($queryrp);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table7.='<table width="100%"  align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">
 <tr>
    <th height="50" colspan="9" align="center" style="font-size:14px; border:none;color:#FFFFFF" class="simple" bgcolor="#333"  ><span style="font-size:24px; ">Stock Balance Report </span></th>

  </tr>';

 $table7.=' <tr>

    <th width="435" height="31" class="simple" bgcolor="#333" style="color:#FFFFFF" >Item</th>';
      $table7.='<th width="275" class="simple"  colspan="2" bgcolor="#333" style="color:#FFFFFF" align="center">Opening</th>
	             <th width="275" class="simple"  colspan="2" bgcolor="#333" style="color:#FFFFFF" align="center">Stock</th>
                <th width="275" class="simple"  colspan="2" bgcolor="#333" style="color:#FFFFFF" align="center">Sale</th> 
	            <th width="275" class="simple"  colspan="2" bgcolor="#333" style="color:#FFFFFF" align="center">Balance</th>
                </tr>
	             <tr>
                  <th width="435" height="31" class="simple" bgcolor="#333" style="color:#FFFFFF" ></th>';

      $table7.='<th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Quantity</th>
                <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Value</th>
	            <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Quantity</th>
                <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Value</th> 
	            <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Quantity</th>
                  <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Value</th> 
	            <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Quantity</th>
				 <th width="275" class="simple" bgcolor="#333" style="color:#FFFFFF" align="right">Value</th>   </tr>';

   $totalvalue = 0;

  

 $diff=0;

for($i=0;$i<$row_run_item_diff;$i++)

{

		
		$tname=$reportresult_item_diff[$i]["itemdescription"];
		if($tname==''){
			$tname="No Name";
			}else{
			$tname=$reportresult_item_diff[$i]["itemdescription"];	
				}
		$itotalvalue += $reportresult_item_diff[$i]["stock_value"];

		$totalrvalue += $reportresult_item_diff[$i]["sale_value"];
        $tq += $reportresult_item_diff[$i]["stock_quantity"];
		$tsq += $reportresult_item_diff[$i]["sale_quantity"];
		$topp+=round($reportresult_item_diff[$i]["open_value"],2);
      $diff+=(round(($reportresult_item_diff[$i]["stock_value"]-$reportresult_item_diff[$i]["sale_value"]),2));
		$table7.='<tr>

   <td width="435" align="left"> '.$tname.' </td>';

  

/*    <td align="left"> '.$reportresult_supplier[$i]["fksupplierinvoiceid"].'</td>

    <td width="275" align="left">'.$reportresult_supplier[$i]["billnumber"].'</td>

*/	$table7.='
<td width="275" align="right">'.round($reportresult_item_diff[$i]["open_quantity"],2).'</td> 
              <td width="275" align="right">'.round($reportresult_item_diff[$i]["open_value"],2).'</td>
			  
              <td width="275" align="right">'.round($reportresult_item_diff[$i]["stock_quantity"],2).'</td> 
              <td width="275" align="right">'.round($reportresult_item_diff[$i]["stock_value"],2).'</td>
			  <td width="275" align="right">'.round($reportresult_item_diff[$i]["sale_quantity"],2).'</td>
              <td width="275" align="right">'.round($reportresult_item_diff[$i]["sale_value"],2).'</td> 
               
               <td width="275" align="right">'.round(($reportresult_item_diff[$i]["stock_quantity"]-$reportresult_item_diff[$i]["sale_quantity"]),2).'</td>
			   <td width="275" align="right">'.round(($reportresult_item_diff[$i]["stock_value"]-$reportresult_item_diff[$i]["sale_value"]),2).'</td>
	 </tr>';

}



$table7.='<tr>

   <td width="235"  colspan="2" align="right" style="font-weight:bold"> Total</td>
   <td width="275" align="right">'.round($topp,2).'</td>
   <td width="275" align="right"></td>
   <td width="275" align="right">'.round($itotalvalue,2).'</td>
   <td width="275" align="right"></td>
   <td width="275" align="right">'.round($totalrvalue,2).'</td>
  <td width="275" align="right"></td>
   <td width="275" align="right">'.round(($diff+$topp),2).'</td>
	 </tr>';

$table7.='</table><p>&nbsp;&nbsp;</p>';
$body7 = addslashes($table7);	
$time_to_send=time();
$queryrp="INSERT INTO $dbname_detail.monthly_reports (searchdate,subject,body,datetime) VALUES ('$date','Stock Balance','{$body7}','$time_to_send')";
$AdminDAO->queryresult($queryrp);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$table8.='<table width="100%"  align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">

<tr>



<th colspan="8" align="center" class="simple" bgcolor="#333" style="color:#FFFFFF"><span style="font-size:16px; ">Heighest Sale Return Of '.$date1.'</span></th>

</tr>



<tr>

<th width="50" class="simple" bgcolor="#333" style="color:#FFFFFF">Sr #</th>

<th width="100" class="simple" bgcolor="#333" style="color:#FFFFFF">Bill #</th>

<th width="100" class="simple" bgcolor="#333" style="color:#FFFFFF">Date</th>

<th width="150" class="simple" bgcolor="#333" style="color:#FFFFFF">Barcode</th>

<th width="300" class="simple" bgcolor="#333" style="color:#FFFFFF">Item</th>

<th width="100" class="simple" bgcolor="#333" style="color:#FFFFFF">Quantity</th>

<th width="100" class="simple" bgcolor="#333" style="color:#FFFFFF">Amount</th>

<th width="150" class="simple" bgcolor="#333" style="color:#FFFFFF">Cashier</th>



</tr>';



 $ii=1 ;

for($i=0;$i<$row_run_return;$i++)

{



$table8.='<tr>

<td align="left" width="50">'.$ii.'</td>

<td width="100" align="left">'.$reportresult_return[$i]['pksaleid'].'</td>

<td width="100" align="left">'. $reportresult_return[$i]['datetime'].'</td>

<td width="150" align="left">'.  $reportresult_return[$i]['barcode'].'</td>

<td width="300" align="left">'.$reportresult_return[$i]['itemdescription'].'</td>

<td width="100" align="left">'.$reportresult_return[$i]['quantity'].'</td>

<td width="100" align="right">'.$reportresult_return[$i]['saleprice'].'</td>

<td width="150" align="left">'.$reportresult_return[$i]['cashiername'].'</td>



</tr>';

$ii++;

}



$table8.='</table><p>&nbsp;&nbsp;</p>';
$body8 = addslashes($table8);	
$time_to_send=time();
$queryrp="INSERT INTO $dbname_detail.monthly_reports (searchdate,subject,body,datetime) VALUES ('$date','heighest sale return','{$body8}','$time_to_send')";
$AdminDAO->queryresult($queryrp);

$table9.='<table width="100%"  align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif; ">

<tr>

<th colspan="8" align="center" class="simple" bgcolor="#333" style="color:#FFFFFF"><span style="font-size:16px; ">Highest Purchase Return Of '.$date1.'</span></th>

</tr>

<tr>

<th width="50" class="simple" bgcolor="#333" style="color:#FFFFFF">Sr #</th>

<th width="100" class="simple" bgcolor="#333" style="color:#FFFFFF">Bill #</th>

<th width="100" class="simple" bgcolor="#333" style="color:#FFFFFF">Supplier</th>

<th width="100" class="simple" bgcolor="#333" style="color:#FFFFFF">Date</th>

<th width="150" class="simple" bgcolor="#333" style="color:#FFFFFF">Barcode</th>

<th width="300" class="simple" bgcolor="#333" style="color:#FFFFFF">Item</th>

<th width="100" class="simple" bgcolor="#333" style="color:#FFFFFF">Quantity</th>

<th width="100"class="simple"  bgcolor="#333" style="color:#FFFFFF">Amount</th>



</tr>';





 $j=1 ;

for($i=0;$i<$row_run_purchase;$i++)

{



$table9.='<tr>

<td align="left" width="50">'.$j.'</td>

<td width="100" align="left">'.$reportresult_purchase[$i]['pkreturnid'].'</td>

<td width="100" align="left">'.$reportresult_purchase[$i]['companyname'].'</td>

<td width="100" align="left">'. $reportresult_purchase[$i]['datetime'].'</td>

<td width="150" align="left">'.  $reportresult_purchase[$i]['barcode'].'</td>

<td width="300" align="left">'.$reportresult_purchase[$i]['itemdescription'].'</td>

<td width="100" align="left">'.$reportresult_purchase[$i]['quantity'].'</td>

<td width="100" align="right">'.$reportresult_purchase[$i]['purchaseprice'].'</td>





</tr>';

$j++;

}

$table9.='</table>';

////////////////////////
$body9 = addslashes($table9);	
$time_to_send=time();
$queryrp="INSERT INTO $dbname_detail.monthly_reports (searchdate,subject,body,datetime) VALUES ('$date','Highest Purchase Return','{$body9}','$time_to_send')";
$AdminDAO->queryresult($queryrp);



 $body = $table1.$table2.$table22.$table3.$table4.$table5.$table6.$table7.$table8.$table9;
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From:" . $from;

/*$queryrpy="select * from $dbname_detail.monthly_reports where searchdate='$date'";
$reslt=$AdminDAO->queryresult($queryrpy);
$row_r=count($reslt);
for($i=0;$i<$row_r;$i++)

{
	echo "<br>";
echo stripslashes($reslt[$i]['body']);
echo "<br>";
}
*/$msent=mail($to,$subject,$body,$headers);
/////////////////////////////////////
  file_get_contents("https://dha.esajee.com/admin/accounts/monthlyreportd.php?dvar=1");	 
  file_get_contents("https://gulberg.esajee.com/admin/accounts/monthlyreportg.php?dvar=1");	 
  file_get_contents("https://pharmadha.esajee.com/admin/accounts/monthlyreportp.php?dvar=1");
  file_get_contents("https://warehouse.esajee.com/admin/accounts/monthlyreportw.php?dvar=1");
//  file_get_contents("https://main.esajee.com/admin/accounts/combinedmonthlyreport.php");
  file_get_contents("https://main.esajee.com/admin/accounts/newcmonthlyrpt.php");	
//////////////////////////////////////
/////////////////////////////////////Delete Messages //////////////////////////////////////////////////////////////////////////////////////////////////
$AdminDAO->queryresult("TRUNCATE $dbname_detail.`messages`");
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
unset($_SESSION);

session_destroy();

//echo $body;

?>