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
if($sdate && $edate)
{
	$date = " datetime >= $sdate AND datetime <= $edate";
}
else if($sdate && !$edate)
{
	$date = " datetime >= $sdate";
}
else if(!$sdate && $edate )
{
	$date = " datetime <= $edate";
}
$cashier	=	$_GET['cashier'];
if($cashier)
{
	$filter	=	" AND s.fkuserid='$cashier'";
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
					pksaleid,
					CONCAT(firstname,' ',lastname) cashiername,
					FROM_UNIXTIME(datetime,'%d-%m-%Y') datetime,
					globaldiscount,
					amountdiscount
				FROM 
					$dbname_detail.sale s,
					addressbook
				WHERE
					s.fkuserid=pkaddressbookid AND
					s.status = 1 AND
					$date 
					$filter 
				ORDER BY 
					pksaleid ASC";
$reportresult	=	$AdminDAO->queryresult($query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Goods Discounted Report</title>
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
Goods Discounted Report</div>
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
  	<th>Bill #</th>
    <th>Date</th>
    <th>Global Discount</th>
    <th>Amount Discount</th>
    <th>Discount Name</th>
    <th>Discount Type</th>
    <th>Amount</th>
    <th>Sold by</th>
  </tr>
  <?php
  $iterator			=	1;
  $totaldiscamount	=	0;
for($i=0;$i<sizeof($reportresult);$i++)
{
	$pksaleid		=	$reportresult[$i]['pksaleid'];
	$datetime		=	$reportresult[$i]['datetime'];
	$cashiername	=	$reportresult[$i]['cashiername'];
	$globaldiscount	=	$reportresult[$i]['globaldiscount'];
	$amountdiscount	=	$reportresult[$i]['amountdiscount'];
	// getting sale discounts
	$discounts		=	$AdminDAO->getrows("$dbname_detail.salediscount sd,discount,discounttype","sd.amount,discountname,typename","fksaleid = '$pksaleid' AND fkdiscountid = pkdiscountid AND fkdiscounttypeid = pkdiscounttypeid AND fkdiscountid<>0");
	/*echo "<pre>";
	print_r($discounts);
	echo "</pre>";*/
	for($j=0;$j<sizeof($discounts);$j++)
	{
		$discountname	=	$discounts[$j]['discountname'];
		$discounttype	=	$discounts[$j]['typename'];
		$damount			=	$discounts[$j]['amount'];
		?>
        <tr>
            <td><?php echo $iterator;?></td>
            <td><?php echo $pksaleid; ?></td>
            <td align="center"><?php echo $datetime; ?></td>
          <td align="right"><?php echo number_format(0,2); ?></td>
            <td align="right"><?php echo number_format(0,2);?></td>
            <td><?php echo $discountname; ?></td>
            <td><?php echo $discounttype; ?></td>
            <td align="right"><?php echo number_format($damount,2);?></td>
            <td><?php echo $cashiername;?></td>
        </tr>
	<?php
	$iterator++;
	$totaldiscamount+=$damount;
	}
	// getting product discounts
	$qtydiscounts	=	$AdminDAO->getrows("$dbname_detail.saledetail sd,$dbname_detail.stock st,discount,discounttype","st.costprice,sd.quantity,discountname,typename","fksaleid = '$pksaleid' AND fkstockid=pkstockid AND fkdiscountid = pkdiscountid AND fkdiscounttypeid = pkdiscounttypeid AND fkdiscountid<>0");
	for($k=0;$k<sizeof($qtydiscounts);$k++)
	{
		$discountname	=	$qtydiscounts[$k]['discountname'];
		$discounttype	=	$qtydiscounts[$k]['typename'];
		$qamount		=	$qtydiscounts[$k]['costprice']*$qtydiscounts[$k]['quantity'];
	?>
    <tr>
        <td><?php echo $iterator;?></td>
        <td><?php echo $pksaleid; ?></td>
        <td align="center"><?php echo $datetime; ?></td>
      <td align="right"><?php echo number_format(0,2); ?></td>
        <td align="right"><?php echo number_format(0,2);?></td>
        <td><?php echo $discountname; ?></td>
        <td><?php echo $discounttype; ?></td>
        <td align="right"><?php echo number_format($qamount,2);?></td>
        <td><?php echo $cashiername;?></td>
    </tr>
  <?php
  $iterator++;
  $totaldiscamount+=$qamount;
	}
	if($globaldiscount>0 || $amountdiscount>0)
	{
		$gamount	=	$globaldiscount+$amountdiscount;
	?>
    <tr>
        <td><?php echo $iterator;?></td>
        <td><?php echo $pksaleid; ?></td>
        <td align="center"><?php echo $datetime; ?></td>
      <td align="right"><?php echo number_format($globaldiscount,2); ?></td>
        <td align="right"><?php echo number_format($amountdiscount,2);?></td>
        <td><?php echo "Global/Amount"; ?></td>
        <td><?php echo "Sale Discount"; ?></td>
        <td align="right"><?php echo number_format($gamount,2);?></td>
        <td><?php echo $cashiername;?></td>
    </tr>
    <?php
	$iterator++;
	$totaldiscamount+=$gamount;
	}
}
?>
  <tr>
    <td colspan="7" align="right"><strong>Total</strong></td>
    <td align="right"><?php echo number_format($totaldiscamount,2); ?></td>
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