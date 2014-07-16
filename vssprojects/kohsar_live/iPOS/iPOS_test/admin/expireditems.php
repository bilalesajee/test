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
	$date = " expiry >= $sdate AND expiry <= $edate";
}
else if($sdate && !$edate)
{
	$date = " expiry >= $sdate";
}
else if(!$sdate && $edate )
{
	$date = " expiry <= $edate";
}

$storeaddress	=	$AdminDAO->getrows("store","storename,storeaddress","pkstoreid='$storeid'");
$storename		=	$storeaddress[0]['storename'];
$storeaddress1	=	$storeaddress[0]['storeaddress'];

/**************************************************************/
$query		=	"SELECT 
					FROM_UNIXTIME(expiry,'%d-%m-%Y') datetime,
					pkbarcodeid,
					barcode,
					itemdescription,
					stk.unitsremaining,
					stk.costprice
				FROM 
					$dbname_detail.stock stk,
					barcode
				WHERE
					stk.fkbarcodeid=pkbarcodeid AND 
					$date 					
				ORDER BY 
					expiry DESC";
$reportresult	=	$AdminDAO->queryresult($query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Expired Items Report</title>
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
Expired Items Report</div>
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
  	<th>Expiry Date</th>
    <th>Barcode</th>
    <th>Item</th>
    <th>Quantity</th>
    <th>Unit Cost</th>
    </tr>
  <?php
for($i=0;$i<sizeof($reportresult);$i++)
{
	$costprice	=	$reportresult[$i]['costprice'];
	$datetime	=	$reportresult[$i]['expiry'];
	$remaining	=	$reportresult[$i]['unitsremaining'];
	$item		=	$reportresult[$i]['itemdescription'];
?>
  <tr>
  	<td><?php echo $i+1;?></td>
  	<td align="center"><?php echo $reportresult[$i]['datetime']; ?></td>
    <td><?php echo $reportresult[$i]['barcode']; ?></td>
    <td><?php echo  $item;?></td>
    <td align="right"><?php echo $remaining; ?></td>
    <td align="right"><?php echo number_format($costprice,2); ?></td>    
  </tr>
  <?php
}
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