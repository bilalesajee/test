<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
global $AdminDAO;
/*************************DATE CHECKS**************************/
$sdate				=	strtotime($_GET['sdate'].'00:00:00'); 
$edate				=	strtotime($_GET['edate']."23:59:59");
if($sdate && $edate)
{
	$date = " s.datetime >= $sdate AND s.datetime <= $edate";
}
else if($sdate && !$edate)
{
	$date = " s.datetime >= $sdate";
}
else if(!$sdate && $edate )
{
	$date = " s.datetime <= $edate";
}
$query		=	"SELECT 
					pksaleid,
					FROM_UNIXTIME(datetime,'%d-%m-%Y') datetime, 
					barcode, 
					itemdescription, 
					sd.quantity quantity, 
					unitsremaining, 
					costprice, 
					sd.saleprice saleprice, 
					CONCAT(firstname,' ',lastname) cashier 
				FROM 
					$dbname_detail.sale s, 
					$dbname_detail.saledetail sd, 
					$dbname_detail.stock stk, 
					barcode,
					addressbook 
				WHERE
					sd.fksaleid		=	s.pksaleid AND
					sd.fkstockid	=	stk.pkstockid AND
					stk.fkbarcodeid	=	pkbarcodeid AND
					s.fkuserid		=	pkaddressbookid AND
					s.status		=	1 AND
					sd.saleprice	<=	stk.costprice AND
					$date
				ORDER BY pksaleid DESC
				";
$reportresult		=	$AdminDAO->queryresult($query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Sales Report</title>
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
<div style="width:2.6in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>Trade Price Report</b>
</div>
<br />
From <?php echo "<b>".implode("-",array_reverse(explode("-",$_GET['sdate'])))."</b>";?> To <?php echo "<b>".implode("-",array_reverse(explode("-",$_GET['edate'])))."</b><br />";?>
<br />
<table style="font-size:12px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
  <tr>
  	<th>Bill Number</th>
    <th>Date</th>
    <th>Item</th>
    <th>Description</th>
    <th>Quantity</th>
    <th>Trade Price</th>
    <th>Sale Price</th>
    <th>Cashier</th>
  </tr>
  <?php
  for($i=0;$i<sizeof($reportresult);$i++)
  {
	  //fetching records
	  $saleid			=	$reportresult[$i]['pksaleid'];
	  $datetime			=	$reportresult[$i]['datetime'];
	  $barcode			=	$reportresult[$i]['barcode'];
	  $itemdescription	=	$reportresult[$i]['itemdescription'];
	  $quantity			=	$reportresult[$i]['quantity'];
	  $costprice		=	$reportresult[$i]['costprice'];
	  $saleprice		=	$reportresult[$i]['saleprice'];
	  $cashier			=	$reportresult[$i]['cashier'];
  ?>
  <tr>
  	<td><?php echo $saleid;?></td>
    <td align="center"><?php echo $datetime;?></td>
    <td><?php echo $barcode;?></td>
    <td><?php echo $itemdescription;?></td>
    <td align="right"><?php echo $quantity;?></td>
    <td align="right"><?php echo number_format($costprice,2);?></td>
    <td align="right"><?php echo number_format($saleprice,2);?></td>
    <td><?php echo $cashier;?></td>
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