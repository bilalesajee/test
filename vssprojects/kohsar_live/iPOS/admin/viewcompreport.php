<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
/*************************DATE CHECKS**************************/
$sdate		=	strtotime($_GET['sdate'].'00:00:00'); 
$edate		=	strtotime($_GET['edate']."23:59:59");
//echo "Start date is ".$sdate."<br>";
//echo "End date is ".$sdate;
$supplier_id=$_GET['sup_id'];
$productdesc	=	$_GET['pro'];
if($sdate && $edate)
{
	$date = " stk.updatetime >= $sdate AND stk.updatetime <= $edate";
}
else if($sdate && !$edate)
{
	$date = " stk.updatetime >= $sdate";
}
else if(!$sdate && $edate )
{
	$date = " stk.updatetime <= $edate";
}
if($supplier_id)
{
	$supplier	=	"stk.fksupplierid	=	'$supplier_id' AND";
}
if($productdesc)
{
	$product	=	"itemdescription	LIKE	'%$productdesc%' AND";
}
$query		=	"SELECT 
					SUM(stk.quantity * stk.retailprice) as amount,
					companyname,
					stk.fksupplierid
				FROM 
					$dbname_detail.stock stk, 
					supplier
				WHERE
					stk.fksupplierid	= 	pksupplierid AND
					stk.updatetime BETWEEN $sdate AND $edate 
				GROUP BY fksupplierid
				ORDER BY updatetime DESC
				";		//echo $query;
$query2		=	"SELECT 
					SUM(sale.totalamount) as amount2,
					creditinvoiceno
				FROM 
					$dbname_detail.sale sale 
				WHERE
					sale.status	=	1 AND
					sale.updatetime BETWEEN $sdate AND $edate
				GROUP BY creditinvoiceno	
				ORDER BY updatetime DESC
				";		//echo $query2;
$query3		=	"SELECT 
					SUM(accpayment.amount) as amount3,
					acc.title as description,
					id
				FROM 
					$dbname_detail.account acc,
					$dbname_detail.accountpayment accpayment
				WHERE
					fkaccountid	= id AND
					accpayment.paymentdate BETWEEN $sdate AND $edate
				GROUP BY fkaccountid					
				ORDER BY paymentdate DESC
				";		//echo $query3;								
$reportresult		=	$AdminDAO->queryresult($query);
$reportresult2		=	$AdminDAO->queryresult($query2);
$reportresult3		=	$AdminDAO->queryresult($query3);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Supplier Report</title>
<style>
body{
	font-family:Verdana, Geneva, sans-serif;
	font-size:12px;
}
table {
	border:1px solid #000;
	border-collapse:collapse;
}
table td,th{
	padding:3px;
	border:1px solid #000;
}
table th{
	font-weight:bold;
	color:#fff;
	background-color:#000;
}
</style>
</head>
<body>
<div style="width:6.2in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>Purchase, Sale and Expenses Comparison Report</b>
</div>
<br />
From <?php echo "<b>".implode("-",array_reverse(explode("-",$_GET['sdate'])))."</b>";?> To <?php echo "<b>".implode("-",array_reverse(explode("-",$_GET['edate'])))."</b><br />";?>
<br />
<table width="98%" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
  <tr>
  	<td colspan="3" style="background-color:#999;font-size:15px;"><strong>PURCHASE</strong></td>
  </tr>
  <tr>
 	<th width="10%">S.NO</th>
 	<!--<th>Date</th>-->
 	<th width="70%">Supplier Name</th>
 	<th>Amount</th>
  </tr>
  <?php
  for($i=0;$i<sizeof($reportresult);$i++)
  {
	  //fetching records
	  //$datetime			=	$reportresult[$i]['datetime'];
	  $amount			=	$reportresult[$i]['amount'];
	  $suppliername		=	$reportresult[$i]['companyname'];
  ?>
  <tr>
    <td><?php echo $i+1;?></td>
  	<!--<td><?php //echo $datetime;?></td>-->
    <td><?php echo $suppliername;?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
  </tr>
  <?php
	$total		+=	$amount;  
  }// end for
  ?>  
  <tr>
    <td colspan="2" align="right"><strong>Total</strong></td>
    <td align="right"><?php echo number_format($total,2); ?></td>
  </tr>  
</table>





<table width="98%" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;padding:5px; margin:10px 0px;">
  <tr>
    <td colspan="3" style="background-color:#999;font-size:15px;"><strong>SALE</strong></td>
  </tr>
  <tr>
 	<th width="10%">S.NO</th>
 	<!--<th>Date</th>-->
 	<th width="70%">Retail Sale / Supply</th>
 	<th>Amount</th>
  </tr>

  <?php
  for($i=0;$i<sizeof($reportresult2);$i++)
  {
	  /*$datetime2		=	$reportresult2[$i]['datetime2'];
	  $itemdescription	=	$reportresult2[$i]['itemdescription'];*/
	  $amount2			=	$reportresult2[$i]['amount2'];
	  $creditinvoiceno	=	$reportresult2[$i]['creditinvoiceno'];
  ?>
  <tr>
    <td><?php echo $i+1;?></td>
    <!--<td><?php //echo $datetime2;?></td>-->
    <td><?php echo $creditinvoiceno;?></td>
    <td align="right"><?php echo number_format($amount2,2); ?></td>
  </tr>
  <?php
	$total2	+=	$amount2;  
  }// end for
  ?>  
  <tr>
    <td colspan="2" align="right"><strong>Total</strong></td>
    <td align="right"><?php echo number_format($total2,2); ?></td>
  </tr>  
</table>




<table width="98%" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
  <tr>
    <td colspan="3" style="background-color:#999;font-size:15px;"><strong>EXPENSE</strong></td> 
  </tr>
  <tr>
 	<th width="10%">S.NO</th>
 	<!--<th>Date</th>-->
 	<th width="70%">Description</th>
 	<th>Amount</th>
  </tr>

  <?php
  for($i=0;$i<sizeof($reportresult3);$i++)
  {
	  //fetching records
	  //$datetime3		=	$reportresult3[$i]['datetime3'];
	  $amount3			=	$reportresult3[$i]['amount3'];
	  $description3		=	$reportresult3[$i]['description'];
  ?>
  <tr>
    <td><?php echo $i+1;?></td>
  	<!--<td><?php //echo $datetime3;?></td>-->
    <td><?php echo $description3;?></td>
    <td align="right"><?php echo number_format($amount3,2);?></td>
  </tr>
  <?php
	$total3		+=	$amount3;  
  }// end for
  ?>  
  <tr>
    <td colspan="2" align="right"><strong>Total</strong></td>
    <td align="right"><?php echo number_format($total3,2); ?></td>
  </tr>  
</table>
</body>
</html>