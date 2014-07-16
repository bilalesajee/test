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
$cashier	=	$_GET['cashier'];
if($cashier)
{
	$filter	=	" AND s.fkuserid='$cashier'";
	//fetch cashier info 
	$cashierinfo	=	$AdminDAO->getrows("addressbook","CONCAT(firstname,' ',lastname) name","pkaddressbookid='$cashier'");
	$cashiername	=	$cashierinfo[0]['name'];
}
$counter	=	$_GET['counter'];
if($counter)
{
	$filter.=	" AND s.countername='$counter'";
}
$storeaddress	=	$AdminDAO->getrows("store","storename,storeaddress","pkstoreid='$storeid'");
$storename		=	$storeaddress[0]['storename'];
$storeaddress1	=	$storeaddress[0]['storeaddress'];

/**************************************************************/
$query		=	"SELECT 
					pksaleid,
					CONCAT(firstname,' ',lastname) cashiername,
					FROM_UNIXTIME(datetime,'%d-%m-%Y') datetime,
					pkbarcodeid,
					barcode,
					itemdescription,
					sum(sd.quantity) as quantity,
					unitsremaining,
					sum(costprice) as costprice,
					sum(sd.saleprice) as saleprice,
					sum(sd.quantity*sd.saleprice) sp,
					sum(sd.quantity*stk.costprice) pf 
				FROM 
					$dbname_detail.sale s, 
					$dbname_detail.saledetail sd, 
					$dbname_detail.stock stk, 
					barcode,
					addressbook
				WHERE
					s.fkuserid=pkaddressbookid AND
					sd.fkstockid=pkstockid AND 
					stk.fkbarcodeid=pkbarcodeid AND 
					pksaleid= sd.fksaleid AND 
					s.status = 1 AND
					sd.quantity<0 AND 
					$date 
					$filter 
				GROUP BY 
					pkstockid,pksaleid
				ORDER BY 
					pksaleid ASC";
$reportresult		=	$AdminDAO->queryresult($query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Sales Returns Report</title>
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
<div style="width:8.0in;font-size:12px;font-weight:bold;" align="center"><?php echo $storename."<br />".$storeaddress1;?><br /><br />Goods Returned Report</div>
  <br />
<table style="font-size:12px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple" >
  <tr>
    <td colspan="4">Date: <?php echo date('d-m-Y',time());?></td>
  </tr>
  <tr>
    <td>From: <?php echo implode("-",array_reverse(explode("-",$_GET['sdate'])));?></td>
    <td>To: <?php echo implode("-",array_reverse(explode("-",$_GET['edate'])));?></td>
    <td>Cashier: <?php echo $cashiername;?></td>
    <td>Counter: <?php echo $counter;?></td>
  </tr>
</table>
<table style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
  <tr>
  	<th>Sr #</th>
  	<th>Bill #</th>
    <th>Date</th>
    <th>Barcode</th>
    <th>Item</th>
    <th>Quantity</th>
    <th>Amount</th>
    <th>Cashier</th>
  </tr>
  <?php
for($i=0;$i<sizeof($reportresult);$i++)
{
	$costprice	=	$reportresult[$i]['costprice'];
	$pksaleid	=	$reportresult[$i]['pksaleid'];
	$datetime	=	$reportresult[$i]['datetime'];
	$saleprice	=	$reportresult[$i]['saleprice'];
	$quantity	=	$reportresult[$i]['quantity'];
	$remaining	=	$reportresult[$i]['unitsremaining'];
	$cashiername=	$reportresult[$i]['cashiername'];
	//$profit		=	$saleprice	-	$costprice;
	$pf			=	$reportresult[$i]['pf'];
	$saleprice	=	$reportresult[$i]['sp'];
	if($pf==0)
	{
		//calculating profits from previous stocks
		$barcodeid	=	$reportresult[$i]['pkbarcodeid'];
		$bcstock	=	$AdminDAO->getrows("$dbname_detail.stock","costprice,retailprice","fkbarcodeid='$barcodeid' AND retailprice<>0 ORDER BY pkstockid DESC LIMIT 0,1");
		if(sizeof($bcstock)>0)
		{
			$cprice		=	$bcstock[0]['costprice'];
			$sprice		=	$bcstock[0]['retailprice'];
			$profit		=	$sprice-$cprice;
		}
		else
		{
			$profit		=	0;
		}
	}
	else
	{
		$profit		=	$saleprice-$pf;
	}
	$percent	=	$profit/$costprice*100;
	$percent	=	round($percent,2);
	$profit		=	round($profit,2);
	$saleprice	=	round($saleprice,2);
	$item		=	$reportresult[$i]['itemdescription'];
	if($productcat ==1)
	{
		$product		=	explode("(",$item);
		$productname	=	$product[0];
		if($oldname	!= $productname)
		{
			//
			if($flag==1)
			{
			?>
  <tr>
    <td colspan="4" align="right"><strong>Group Total</strong></td>
    <td align="right"><?php echo number_format($grouptotal,2); ?></td>
    <td align="right"><?php echo number_format($groupprofit,2); ?></td>
    <td>&nbsp;</td>
  </tr>
  <?php
			}
			$grouptotal		=	0;
			$groupprofit	=	0;
			$flag			=	0;
		}
		$oldname		=	$productname;
		$grouptotal		+=	$saleprice;
		$groupprofit	+=	$profit;
	}
	if(in_array($productname,$product) && $flag == 0)
	{
		?>
  <tr style="margin-bottom:10px;">
    <td colspan="7"><?php
		echo "<b>$productname</b>";
		$flag	= 	1;
		?></td>
  </tr>
  <?php
	}
?>
  <tr>
  	<td><?php echo $i+1;?></td>
  	<td><?php echo $reportresult[$i]['pksaleid']; ?></td>
    <td align="center"><?php echo $reportresult[$i]['datetime']; ?></td>
    <td><?php echo $reportresult[$i]['barcode']; ?></td>
    <td><?php echo  $item;?></td>
    <td align="right"><?php echo $quantity; ?></td>
    <td align="right"><?php echo number_format($saleprice,2); ?></td>
    <td><?php echo $cashiername;?></td>
  </tr>
  <?php
$totalprofit	+=	$profit;
$totalsale		+=	$saleprice;
}
?>
  <tr>
    <td colspan="6" align="right"><strong>Total</strong></td>
    <td align="right"><?php echo number_format($totalsale,2); ?></td>
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