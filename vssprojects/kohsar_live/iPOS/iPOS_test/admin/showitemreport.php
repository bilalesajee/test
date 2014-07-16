<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*************************DATE CHECKS**************************/
$sdate		=	strtotime($_GET['sdate'].'00:00:00'); 
$edate		=	strtotime($_GET['edate']."23:59:59");
$barcode	=	$_GET['barcode'];
if($sdate && $edate)
{
	$date = " timestamp >= $sdate AND timestamp <= $edate";
}
else if($sdate && !$edate)
{
	$date = " timestamp >= $sdate";
}
else if(!$sdate && $edate )
{
	$date = " timestamp <= $edate";
}
/**************************************************************/
$query		=	"SELECT CONCAT(firstname,' ',lastname) name, sd.originalprice,sd.saleprice,sd.quantity,FROM_UNIXTIME(sd.timestamp,'%d-%m-%Y %H:%i:%s') saletime,sd.fksaleid,IF(s.status=1,'Completed','Cancelled') status,b.itemdescription FROM barcode b, $dbname_detail.saledetail sd,$dbname_detail.sale s,$dbname_detail.closinginfo ci,addressbook , $dbname_detail.stock st WHERE sd.fkstockid = pkstockid AND st.fkbarcodeid = pkbarcodeid AND barcode = '$barcode' AND pksaleid=sd.fksaleid AND pkaddressbookid=ci.fkaddressbookid AND pkclosingid=sd.fkclosingid AND $date";
$reportresult		=	$AdminDAO->queryresult($query);
	$item			=	$reportresult[0]['itemdescription'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Sales Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
</head>
<body>
<div style="width:2.6in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>ESAJEE'S</b><br />
</div>
<span style="font-size:11px;font-family:Comic Sans MS, cursive;"><b>Think globally shop locally</b></span><br />
<br />
<table class="simple" width="100%" >
  <tr>
    <td colspan="4">Date: <?php echo date('d-m-Y',time());?></td>
  </tr>
  <tr>
    <td>From: <?php echo $_GET['sdate'];?></td>
    <td>To: <?php echo $_GET['edate'];;?></td>
    <td>Item: <?php echo $item;?></td>
    <td>Barcode: <?php echo $_GET['barcode'];;?></td>
  </tr>
</table>
<p>&nbsp;</p>
<table style="font-size:16px;font-family:Arial, Helvetica, sans-serif;padding:5px; width:100%">
  <tr>
	<th>Date</th>
    <th>Bill #</th>
    <th>Quantity</th>
    <th>Original Price</th>
    <th>Sale Price</th>
    <th>Cashier</th>
  </tr>
  <?php
for($i=0;$i<sizeof($reportresult);$i++)
{
	if($bgcolor == '#D1D1D1')
	{
		$bgcolor	=	'#E5E5E5';
	}
	else
	{
		$bgcolor	=	'#D1D1D1';
	}
	$saletime		=	$reportresult[$i]['saletime'];
	$saleid			=	$reportresult[$i]['fksaleid'];
	$originalprice	=	$reportresult[$i]['originalprice'];
	$saleprice		=	$reportresult[$i]['saleprice'];
	$quantity		=	$reportresult[$i]['quantity'];
	$saletime		=	$reportresult[$i]['saletime'];
	$status			=	$reportresult[$i]['status'];
	$cashier		=	$reportresult[$i]['name'];
?>
  <tr bgcolor="<?php echo $bgcolor;?>">
    <td><?php echo $saletime; ?></td>
    <td><?php echo  $saleid;?></td>
    <td><?php echo $quantity; ?></td>
    <td align="right"><?php echo number_format($originalprice,2); ?></td>
 	<td align="right"><?php echo number_format($saleprice,2); ?></td>
    <td><?php echo $cashier;?></td>
  </tr>
<?php
}
?>
</table>
</body>
</html>