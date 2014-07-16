<?php
include_once("../includes/security/adminsecurity.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
global $AdminDAO;
/*************************DATE CHECKS**************************/
$sdate				=	strtotime($_GET['sdate'].'00:00:00'); 
$edate				=	strtotime($_GET['edate']."23:59:59");
if($sdate && $edate)
{
	$date = " damagedate >= $sdate AND damagedate <= $edate";
}
else if($sdate && !$edate)
{
	$date = " damagedate >= $sdate";
}
else if(!$sdate && $edate )
{
	$date = " damagedate <= $edate";
}
$cashier	=	$_GET['cashier'];
if($cashier)
{
	$filter	=	" AND d.fkemployeeid='$cashier'";
	//fetch cashier info 
	$cashierinfo	=	$AdminDAO->getrows("addressbook","CONCAT(firstname,' ',lastname) name","pkaddressbookid='$cashier'");
	$cashiername	=	$cashierinfo[0]['name'];
}
/*$counter	=	$_GET['counter'];
if($counter)
{
	$filter.=	" AND s.countername='$counter'";
}*/
$storeaddress	=	$AdminDAO->getrows("store","storename,storeaddress","pkstoreid='$storeid'");
$storename		=	$storeaddress[0]['storename'];
$storeaddress1	=	$storeaddress[0]['storeaddress'];

/**************************************************************/
$query		=	"SELECT 
					pkdamageid,
					CONCAT(firstname,' ',lastname) cashiername,
					FROM_UNIXTIME(damagedate,'%d-%m-%Y') datetime,
					pkbarcodeid,
					barcode,
					itemdescription,
					d.quantity quantity,
					stk.unitsremaining,
					stk.costprice
				FROM 
					$dbname_detail.damages d,
					$dbname_detail.stock stk,
					barcode,
					addressbook
				WHERE
					d.fkemployeeid=pkaddressbookid AND
					d.fkstockid=pkstockid AND 
					stk.fkbarcodeid=pkbarcodeid AND 
					$date 
					$filter 
				ORDER BY 
					pkdamageid ASC";
$reportresult	=	$AdminDAO->queryresult($query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Goods Damaged Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
</head>
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
<body>
<div style="width:8.0in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>ESAJEE'S</b>
</div>
<div style="width:8.0in;font-size:11px;font-family:Comic Sans MS, cursive;" align="center"><b>Think globally shop locally</b></div><br />
<div style="width:8.0in;font-size:12px;font-weight:bold;" align="center"><?php echo $storename."<br />".$storeaddress1;?><br /><br />
Goods Damaged Report</div>
  <br />
<table style="font-size:12px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple" >
  <tr>
    <td colspan="3">Date: <?php echo date('d-m-Y',time());?></td>
  </tr>
  <tr>
    <td>From: <?php echo implode("-",array_reverse(explode("-",$_GET['sdate'])));?></td>
    <td>To: <?php echo implode("-",array_reverse(explode("-",$_GET['edate'])));?></td>
    <td>User: <?php echo $cashiername;?></td>
  </tr>
</table>
<table style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
  <tr>
  	<th>Sr #</th>
  	<th>DAMAGE #</th>
    <th>Date</th>
    <th>Barcode</th>
    <th>Item</th>
    <th>Quantity</th>
    <th>Unit Cost</th>
    <th>Loss</th>
    <th>Added by</th>
  </tr>
  <?php
for($i=0;$i<sizeof($reportresult);$i++)
{
	$costprice	=	$reportresult[$i]['costprice'];
	$pksaleid	=	$reportresult[$i]['pksaleid'];
	$datetime	=	$reportresult[$i]['datetime'];
	$quantity	=	$reportresult[$i]['quantity'];
	$remaining	=	$reportresult[$i]['unitsremaining'];
	$cashiername=	$reportresult[$i]['cashiername'];
	$item		=	$reportresult[$i]['itemdescription'];
?>
  <tr>
  	<td><?php echo $i+1;?></td>
  	<td><?php echo $reportresult[$i]['pkdamageid']; ?></td>
    <td align="center"><?php echo $reportresult[$i]['datetime']; ?></td>
    <td><?php echo $reportresult[$i]['barcode']; ?></td>
    <td><?php echo  $item;?></td>
    <td align="right"><?php echo $quantity; ?></td>
    <td align="right"><?php echo number_format($costprice,2); ?></td>
    <td align="right"><?php echo number_format($costprice*$quantity,2); ?></td>
    <td><?php echo $cashiername;?></td>
  </tr>
  <?php
$totalloss		+=	$costprice*$quantity;
}
?>
  <tr>
    <td colspan="7" align="right"><strong>Total Loss</strong></td>
    <td align="right"><?php echo number_format($totalloss,2); ?></td>
  </tr>
</table>
</form> <!--end form-->
</body>
</html>
<?php 
//////////////////////add by wajid for excel export/////////////////////////
echo $exporactions;
//////////////////////////////////////////////////////////////////////////
?>