<?php
include_once("../includes/security/adminsecurity.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
include_once("dbgrid.php");
global $AdminDAO;
/*************************DATE CHECKS**************************/
$sdate				=	strtotime($_GET['sdate'].'00:00:00'); 
$edate				=	strtotime($_GET['edate']."23:59:59");
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
					pkstockid,
					barcode, 
					itemdescription, 
					companyname,
					stk.quantity quantity, 
					unitsremaining, 
					companyname,
					costprice,
					from_unixtime(updatetime,'%d-%m-%Y') datetime,
					retailprice
				FROM 
					$dbname_detail.stock stk, 
					supplier, 
					barcode
				WHERE
					$supplier
					$product
					stk.fkbarcodeid		=	pkbarcodeid AND
					stk.fksupplierid	= 	pksupplierid AND
					$date
				ORDER BY pkstockid DESC
				";
				//echo $query;
$reportresult		=	$AdminDAO->queryresult($query);
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
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
<body>
<div style="width:2.6in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>Supplier Report</b>
</div>
<br />
From <?php echo "<b>".implode("-",array_reverse(explode("-",$_GET['sdate'])))."</b>";?> To <?php echo "<b>".implode("-",array_reverse(explode("-",$_GET['edate'])))."</b><br />";?>
<br />
<table style="font-size:12px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
  <tr>
  	<th>Sr #</th>
  	<th>Stock ID</th>
    <th>Date</th>
    <th>Item</th>
     <th>Supplier</th>
    <th>Description</th>
    <th>Quantity</th>
    <th>Trade Price</th>
    <th>Sale Price</th>
 
  </tr>
  <?php
  for($i=0;$i<sizeof($reportresult);$i++)
  {
	  //fetching records
	  $pkstockid		=	$reportresult[$i]['pkstockid'];
	  $datetime			=	$reportresult[$i]['datetime'];
	  $barcode			=	$reportresult[$i]['barcode'];
	  $companyname		=	$reportresult[$i]['companyname'];
	  $itemdescription	=	$reportresult[$i]['itemdescription'];
	  $quantity			=	$reportresult[$i]['quantity'];
	  $costprice		=	$reportresult[$i]['costprice'];
	  $saleprice		=	$reportresult[$i]['retailprice'];
	 
  ?>
  <tr>
    <td><?php echo $i+1;?></td>
  	<td><?php echo $pkstockid;?></td>
    <td align="center"><?php echo $datetime;?></td>
    <td><?php echo $barcode;?></td>
    <td><?php echo $companyname;?></td>
    <td><?php echo $itemdescription;?></td>
    <td align="right"><?php echo $quantity;?></td>
    <td align="right"><?php echo number_format($costprice,2);?></td>
    <td align="right"><?php echo number_format($saleprice,2);?></td>
  
  </tr>
  <?php
  }// end for
  ?>
</table>
</form> <!--end form-->
</body>
</html>
<?php 
//////////////////////add by wajid for excel export/////////////////////////
echo $exporactions;
//////////////////////////////////////////////////////////////////////////
?>