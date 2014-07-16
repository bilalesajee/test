<?php
include_once("../includes/security/adminsecurity.php");
include_once("../export/exportdata.php");
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	include_once("dbgrid.php");
}//end edit
global $AdminDAO;

?>
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<?php
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
/**************************************************************/
$arrange		=	$_GET['arrangement'];
$productcat		=	$_GET['cat'];
$productname	=	$_GET['pro'];
$ship		=	trim($_GET['ship'],',');
$invoice		=	trim($_GET['invoice'],',');
//filtering for product wise grouping

if($productcat	== 1)
{
	$arrange	=	"itemdescription";
}
//product search filter

if($productname)
{
	$like		=	"AND itemdescription LIKE '%$productname%'";
}
$by 		=	$_GET['sortorder'];
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	 
	if($invoice!='' and $ship!=''){
	
	$ship	=	trim($_GET['ship'],',');
    $invoice	=	trim($_GET['invoice'],',');

	$query="SELECT
 sh.pkshipmentid,
  sh.shipmentname ,
 sl.pksaleid,
FROM_UNIXTIME(sd.timestamp,'%d-%m-%Y') datetime,
b.pkbarcodeid,
b.barcode,
b.itemdescription,
sum(sd.quantity) as quantity,
st.unitsremaining,
sum(st.costprice) as costprice,
sum(sd.saleprice) as saleprice,
sum(sd.quantity*sd.saleprice) sp,
sum(sd.quantity*st.costprice) pf
FROM $dbname_detail.`stock` st left join $dbname_detail.saledetail sd on  (st.pkstockid=sd.fkstockid) 
left join $dbname_detail.sale sl on (sl.pksaleid=sd.fksaleid)
left join $dbname_main.barcode b on (st.fkbarcodeid=b.pkbarcodeid) 
left join $dbname_main.shipment sh on (sh.pkshipmentid=st.fkshipmentid)
left join $dbname_detail.supplierinvoice inv on (st.fksupplierinvoiceid = inv.pksupplierinvoiceid) 
WHERE `fkshipmentid`='$ship' AND inv.pksupplierinvoiceid = '$invoice' and sl.status=1  group by st.fkbarcodeid ORDER BY $arrange $by";

		
}else if($ship!=''){
	
	$ship	=	trim($_GET['ship'],',');
    $query="SELECT
 sh.pkshipmentid,
  sh.shipmentname ,
 sl.pksaleid,
FROM_UNIXTIME(sd.timestamp,'%d-%m-%Y') datetime,
b.pkbarcodeid,
b.barcode,
b.itemdescription,
sum(sd.quantity) as quantity,
st.unitsremaining,
sum(st.costprice) as costprice,
sum(sd.saleprice) as saleprice,
sum(sd.quantity*sd.saleprice) sp,
sum(sd.quantity*st.costprice) pf
FROM $dbname_detail.`stock` st left join $dbname_detail.saledetail sd on  (st.pkstockid=sd.fkstockid) 
left join $dbname_detail.sale sl on (sl.pksaleid=sd.fksaleid)
left join $dbname_main.barcode b on (st.fkbarcodeid=b.pkbarcodeid) 
left join $dbname_main.shipment sh on (sh.pkshipmentid=st.fkshipmentid)
WHERE `fkshipmentid`='$ship' and sl.status=1  group by st.fkbarcodeid ORDER BY $arrange $by";
 
	
}
else if($invoice!=''){
	$invoice	=	trim($_GET['invoice'],',');
  $query	="SELECT inv.pksupplierinvoiceid,inv.billnumber,pksaleid,FROM_UNIXTIME(sd.timestamp,'%d-%m-%Y') datetime,b.pkbarcodeid,b.barcode,b.itemdescription,sum(sd.quantity) as quantity,unitsremaining,sum(costprice) as costprice,sum(sd.saleprice) as saleprice,sum(sd.quantity*sd.saleprice) sp,sum(sd.quantity*stk.costprice) pf FROM $dbname_detail.sale s, 
	$dbname_detail.saledetail sd, 
	$dbname_detail.stock stk, 
	$dbname_detail.supplierinvoice inv, 
	$dbname_main.barcode b 
	WHERE sd.fkstockid=pkstockid AND inv.pksupplierinvoiceid = '$invoice'  AND stk.fkbarcodeid=b.pkbarcodeid AND pksaleid= sd.fksaleid AND             stk.fksupplierinvoiceid = inv.pksupplierinvoiceid AND s.status = 1  GROUP BY b.barcode ORDER BY $arrange $by";
	
		
}else{
	 $query		=	"SELECT pksaleid,
	FROM_UNIXTIME(sd.timestamp,'%d-%m-%Y') datetime,
	b.pkbarcodeid,
	b.barcode,
	b.itemdescription,
	sum(sd.quantity) as quantity,
	unitsremaining,
	sum(costprice) as costprice,
	sum(sd.saleprice) as saleprice,
	sum(sd.quantity*sd.saleprice) sp,
	sum(sd.quantity*stk.costprice) pf 
	FROM $dbname_detail.sale s left join $dbname_detail.saledetail sd on (s.pksaleid= sd.fksaleid) left join $dbname_detail.stock stk on (sd.fkstockid=stk.pkstockid) left join 	$dbname_main.barcode b on (stk.fkbarcodeid=b.pkbarcodeid)
	WHERE  $like  s.status = 1 AND $date GROUP BY pkstockid,pksaleid ORDER BY $arrange $by";
	
	
	}
$reportresult	=	$AdminDAO->queryresult($query);


$storeaddress	=	$AdminDAO->getrows("store","storename,storeaddress","pkstoreid='$storeid'");
$storename		=	$storeaddress[0]['storename'];
$storeaddress1	=	$storeaddress[0]['storeaddress'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Sales Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
</head>
<body>
<div style="width:8.0in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>ESAJEE'S</b> </div>
<div style="width:8.0in;font-size:11px;font-family:Comic Sans MS, cursive;" align="center"><b>Think globally shop locally</b></div>
<br />
<div style="width:8.0in;font-size:12px;font-weight:bold;" align="center"><?php echo $storename."<br />".$storeaddress1;?><br />
  <br />
  Sales Report</div>
<div style="width:8.0in;font-size:12px;padding-left:10px;" align="left"><br />
  <br />
  <?php
if(empty($reportresult))
{
	echo "No Records Found";
}else{

if($ship){?>
<div> Shipment Name:<span style="font-weight:bold;"> <?php echo  $reportresult[0]['shipmentname'];?> </span></div><br>
<?php } ?>

<?php
if($invoice)
{
?>
<div> Invoice No:<span style="font-weight:bold;"> <?php echo  $reportresult[0]['billnumber'];?> </span>
</div>
<?php } if($invoice=='' and $ship=='' ){ ?>
<br />
<table style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
  <tr>
    <td colspan="2">Date: <?php echo date('d-m-Y',time());?></td>
  </tr>
  <tr>
    <td>From: <?php echo $_GET['sdate'];?></td>
    <td>To: <?php echo $_GET['edate'];;?></td>
  </tr>
</table>
<?php }}?>
<table style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
  <tr>
    <th>Sale ID</th>
    <th>Date</th>
    <th>Item</th>
    <th>Description</th>
    <th>Quantity</th>
    <th>Remaining</th>
    <th>Amount</th>
    <th>Profit</th>
    <th>Percentage</th>
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
	$shipmentname	=	$reportresult[$i]['shipmentname'];
	$billnumber	=	$reportresult[$i]['billnumber'];
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
			$profit		=	($sprice-$cprice)*$quantity;
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
    <td><?php echo $reportresult[$i]['pksaleid']; ?></td>
    <td><?php echo $reportresult[$i]['datetime']; ?></td>
    <td><?php echo $reportresult[$i]['barcode']; ?></td>
    <td><?php echo  $item;?></td>
    <td><?php echo $quantity; ?></td>
    <td><?php echo $remaining; ?></td>
    <td align="right"><?php echo number_format($saleprice,2); ?></td>
    <td align="right"><?php echo number_format($profit,2); ?></td>
    <td align="right"><?php echo $percent;?></td>
  </tr>
  <?php
$totalprofit	+=	$profit;
$totalsale		+=	$saleprice;
}
if($i==sizeof($reportresult) && $productcat==1)
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
?>
  <tr>
    <td colspan="6" align="right"><strong>Total</strong></td>
    <td align="right"><?php echo number_format($totalsale,2); ?></td>
    <td align="right"><?php echo number_format($totalprofit,2); ?></td>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	
	
	
	
	
		if($invoice!='' and $ship!=''){
	
	$ship	=	trim($_GET['ship'],',');
    $invoice	=	trim($_GET['invoice'],',');

	$query="SELECT
 sh.pkshipmentid,
  sh.shipmentname ,
 sl.pksaleid,
FROM_UNIXTIME(sl.datetime,'%d-%m-%Y') datetime,
b.pkbarcodeid,
b.barcode,
b.itemdescription,
sum(sd.quantity) as quantity,
st.unitsremaining,
sum(st.costprice) as costprice,
sum(sd.saleprice) as saleprice,
sum(sd.quantity*sd.saleprice) sp,
sum(sd.quantity*st.costprice) pf
FROM $dbname_detail.`stock` st left join $dbname_detail.saledetail sd on  (st.pkstockid=sd.fkstockid) 
left join $dbname_detail.sale sl on (sl.pksaleid=sd.fksaleid)
left join $dbname_main.barcode b on (st.fkbarcodeid=b.pkbarcodeid) 
left join $dbname_main.shipment sh on (sh.pkshipmentid=st.fkshipmentid)
left join $dbname_detail.supplierinvoice inv on (st.fksupplierinvoiceid = inv.pksupplierinvoiceid) 
WHERE `fkshipmentid`='$ship' AND inv.pksupplierinvoiceid = '$invoice' and sl.status=1  group by st.fkbarcodeid ORDER BY $arrange $by";

	 $query		=	"SELECT  sh.pkshipmentid,
  sh.shipmentname ,b.barcode,b.itemdescription,sum(sd.quantity) as quantity,sum(unitsremaining) as unitsremaining,sum(costprice) as costprice,sum(sd.saleprice) as 	saleprice FROM 
 $dbname_detail.sale s, 
 $dbname_detail.saledetail sd,
  $dbname_detail.stock, 
  $dbname_main.barcode b ,
  $dbname_main.shipment sh,
  $dbname_detail.supplierinvoice inv
  WHERE `fkshipmentid`='$ship' and pksupplierinvoiceid = '$invoice'  and inv.pksupplierinvoiceid=stock.fksupplierinvoiceid and sh.pkshipmentid=stock.fkshipmentid and  sd.fkstockid=pkstockid  AND fkbarcodeid=b.pkbarcodeid AND s.status = 1 AND pksaleid= sd.fksaleid  GROUP BY stock.fkbarcodeid ORDER BY $arrange $by";
 	
}else if($ship!=''){
	
	$ship	=	trim($_GET['ship'],',');
    $query		=	"SELECT  sh.pkshipmentid,
  sh.shipmentname ,b.barcode,b.itemdescription,sum(sd.quantity) as quantity,sum(unitsremaining) as unitsremaining,sum(costprice) as costprice,sum(sd.saleprice) as 	saleprice FROM 
 $dbname_detail.sale s, 
 $dbname_detail.saledetail sd,
  $dbname_detail.stock, 
  $dbname_main.barcode b ,
  $dbname_main.shipment sh,
  
  WHERE `fkshipmentid`='$ship' and sh.pkshipmentid=stock.fkshipmentid and  sd.fkstockid=pkstockid  AND fkbarcodeid=b.pkbarcodeid AND s.status = 1 AND pksaleid= sd.fksaleid  GROUP BY stock.fkbarcodeid ORDER BY $arrange $by";
 
 

	
}
else if($invoice!=''){
	$invoice	=	trim($_GET['invoice'],',');

	
	  $query		=	"SELECT   inv.pksupplierinvoiceid,inv.billnumber ,b.barcode,b.itemdescription,sum(sd.quantity) as quantity,sum(unitsremaining) as unitsremaining,sum(costprice) as costprice,sum(sd.saleprice) as 	saleprice FROM 
 $dbname_detail.sale s, 
 $dbname_detail.saledetail sd,
  $dbname_detail.stock, 
  $dbname_main.barcode b ,
  $dbname_detail.supplierinvoice inv
  WHERE pksupplierinvoiceid = '$invoice'  and inv.pksupplierinvoiceid=stock.fksupplierinvoiceid and  sd.fkstockid=pkstockid  AND fkbarcodeid=b.pkbarcodeid AND s.status = 1 AND pksaleid= sd.fksaleid  GROUP BY stock.fkbarcodeid ORDER BY $arrange $by";
 
		
}else{
	$query		=	"SELECT barcode,itemdescription,sum(sd.quantity) as quantity,sum(unitsremaining) as unitsremaining,sum(costprice) as costprice,sum(sd.saleprice) as 	saleprice FROM $dbname_detail.sale s, $dbname_detail.saledetail sd, $dbname_detail.stock, barcode WHERE sd.fkstockid=pkstockid $like AND fkbarcodeid=pkbarcodeid AND s.status = 1 AND pksaleid= sd.fksaleid AND $date GROUP BY pkstockid ORDER BY $arrange $by";
	
	
	}
	
$reportresult		=	$AdminDAO->queryresult($query);
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
 <?php
if(empty($reportresult))
{
	echo "No Records Found";
}else{

if($ship){?>
Shipment Name:<span style="font-weight:bold;"> <?php echo  $reportresult[0]['shipmentname'];?> </span></div>
<?php } ?>
<?php
if($invoice)
{
?>
Invoice No:<span style="font-weight:bold;"> <?php echo  $reportresult[0]['billnumber'];?> </span>
</div>
<?php } if($invoice=='' and $ship=='' ){ ?>
<table style="font-size:12px;font-family:Arial, Helvetica, sans-serif;" >
  <tr>
    <td colspan="2">Date: <?php echo date('d-m-Y',time());?></td>
  </tr>
  <tr>
    <td>From: <?php echo $_GET['sdate'];?></td>
    <td>To: <?php echo $_GET['edate'];;?></td>
  </tr>
</table>
<?php }}?>
<table style="font-size:12px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
  <tr>
    <th>Item</th>
    <th>Description</th>
    <th>Quantity</th>
    <th>Remaining</th>
    <th>Amount</th>
    <th>Profit</th>
    <th>Percentage</th>
  </tr>
  <?php
for($i=0;$i<sizeof($reportresult);$i++)
{
	$costprice	=	$reportresult[$i]['costprice'];
	$saleprice	=	$reportresult[$i]['saleprice'];
	$quantity	=	$reportresult[$i]['quantity'];
	$profit		=	$saleprice	-	$costprice;
	$percent	=	$profit/$costprice*100;
	$percent	=	round($percent,2);
	$profit		=	$quantity*$profit;
	$profit		=	round($profit,2);
	$saleprice	=	$quantity*$saleprice;
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
    <td><?php echo $reportresult[$i]['barcode']; ?></td>
    <td><?php echo  $item;?></td>
    <td><?php echo $quantity; ?></td>
    <td><?php echo $reportresult[$i]['unitsremaining']; ?></td>
    <td align="right"><?php echo number_format($saleprice,2); ?></td>
    <td align="right"><?php echo number_format($profit,2); ?></td>
    <td align="right"><?php echo $percent;?></td>
  </tr>
  <?php
$totalprofit	+=	$profit;
$totalsale		+=	$saleprice;
}
if($i==sizeof($reportresult) && $productcat==1)
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
?>
  <tr>
    <td colspan="4" align="right"><strong>Total</strong></td>
    <td align="right"><?php echo number_format($totalsale,2); ?></td>
    <td align="right"><?php echo number_format($totalprofit,2); ?></td>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
<?php }//end edit?>
</form>
<?php echo $exporactions;?>