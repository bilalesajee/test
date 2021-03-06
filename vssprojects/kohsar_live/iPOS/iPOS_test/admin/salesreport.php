<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
$employeeid				=	$_SESSION['addressbookid'];
global $AdminDAO;
/*************************DATE CHECKS**************************/
// Days Calculator Function
function count_days( $a, $b )
{
	// First we need to break these dates into their constituent parts:
	$gd_a = getdate( $a );
	$gd_b = getdate( $b );
	// Now recreate these timestamps, based upon noon on each day
	$a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
	$b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );
	// Subtract these two numbers and divide by the number of seconds in a
	return round( abs( $a_new - $b_new ) / 86400 );
}
$sdate		=	$_GET['sdate'];
$edate		=	$_GET['edate'];
$newsdate	=	strtotime($_GET['sdate'].'00:00:00'); 
if($employeeid==1928)
{ 
//echo $employeeid;
$tyo= explode('-',$_GET['sdate']);

 if($tyo[2] == 2013){
	echo "Please Select date after Dec 2013";
	exit;
	}
	//$and='';
}


$newedate	=	strtotime($_GET['edate']."23:59:59");

$dateinterval	=	count_days($newsdate,$newedate);
for($d=0;$d<=$dateinterval;$d++)
{
	$newdate[] 	= 	date("d-m-Y",strtotime(date("d-m-Y", strtotime($sdate)) . " +$d day"));
}
$daterange		=	implode(",",$newdate);
/*echo "<pre>";
print_r($newdate);
echo "</pre>";*/
if($newsdate && $newedate)
{
	//$date = " timestamp >= $newsdate AND timestamp <= $newedate";
	$date = " datetime BETWEEN '$newsdate' AND '$newedate'";
}
else if($newsdate && !$newedate)
{
	$date = " timestamp >= $newsdate";
}
else if(!$newsdate && $newedate )
{
	$date = " timestamp <= $newedate";
}
/**************************************************************/
$arrange		=	$_GET['arrangement'];
$productcat		=	$_GET['cat'];
$productname	=	$_GET['pro'];
$barcode	=	$_GET['bar'];
$sday			=	$_GET['startday'];
$eday			=	$_GET['endday'];
$shour			=	$_GET['starthour'];
$ehour			=	$_GET['endhour'];
//filtering on hour of day
if($shour!="")
{
	if($ehour=="")
	{
		$ehour	=	"23";
	}
	$hourday	=	" AND FROM_UNIXTIME(datetime,'%H') BETWEEN '$shour' AND '$ehour'";
}
//filtering on day of week
if($sday!="")
{
	if($eday=="")
	{
		$eday	=	"7";
	}
	$dayweek	=	" AND DAYOFWEEK(FROM_UNIXTIME(datetime,'%Y-%m-%d')) BETWEEN '$sday' AND '$eday'";
}
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
if($barcode !='')
	{
	$cond_barcode = " and barcode ='$barcode'  ";
    }
$by 		=	$_GET['sortorder'];
$query		=	"SELECT 
					pksaleid,
					pkstockid,
					pkbarcodeid,
					barcode,
					itemdescription,
					sum(sd.quantity) as quantity,
					FROM_UNIXTIME(datetime,'%d-%m-%Y %h:%i %p %W') datetime,
					FROM_UNIXTIME(datetime,'%H') hourofday,
					DAYOFWEEK(FROM_UNIXTIME(datetime,'%Y-%m-%d')) dayofweek,
					FROM_UNIXTIME(datetime,'%d-%m-%Y') dayofmonth,
					remainingstock,
					round(stk.costprice,2) costprice,
					round(sum(sd.saleprice*sd.quantity),2) amount,
					round(sum(stk.costprice*sd.quantity),2) originalprice,
					sum(sd.quantity) qty					
				FROM 
					$dbname_detail.sale s,
					$dbname_detail.saledetail sd,
					$dbname_detail.stock stk,
					barcode
				WHERE 
					sd.fkstockid=pkstockid $like AND 
					stk.fkbarcodeid=pkbarcodeid AND 
					pksaleid= sd.fksaleid AND 
					s.status = 1 AND 
					$date $dayweek $hourday $cond_barcode
				GROUP BY 
					pkbarcodeid,pksaledetailid
				ORDER BY 
					 $arrange $by ,pksaledetailid DESC";
				//echo $query;
$reportresult		=	$AdminDAO->queryresult($query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Sales Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
</head>
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
<body>
<br />
<!-- the chart container -->
<div id="container" style="width: 1000px; height: 400px; margin: 0 auto"></div>
<div>
Date: <?php echo date('d-m-Y',time());?><br>
From: <?php echo $_GET['sdate'];?>	To: <?php echo $_GET['edate'];;?>
</div><br>
<table style="font-size:12px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
  <tr>
  	<th>SALE ID</th>
    <th>DATE/TIME</th>
    <th>BARCODE</th>
    <th>ITEM</th>
    <th>QUANTITY</th>
    <th>REMAINING</th>
    <th>TRADE</th>
    <th>AMOUNT</th>
    <th>PROFIT</th>
    <th>PERCENTAGE</th>
  </tr>
  <?php

for($i=0;$i<sizeof($reportresult);$i++)
{
	$quantity	=	$reportresult[$i]['quantity'];
	$remaining	=	0;	
	$remaining	=	$reportresult[$i]['remainingstock'];
	$saleprice	=	$reportresult[$i]['amount'];
	$costprice	=	$reportresult[$i]['costprice'];
	$pf			=	$reportresult[$i]['amount']-$reportresult[$i]['originalprice'];
	$daymonth	=	$reportresult[$i]['dayofmonth'];
	$looparr[]	=	$i+1;
	if($costprice==0)
	{
		//calculating profits from previous stocks
		$barcodeid	=	$reportresult[$i]['pkbarcodeid'];
		$bcstock	=	$AdminDAO->getrows("$dbname_detail.stock","costprice,retailprice","fkbarcodeid='$barcodeid' AND retailprice<>0 ORDER BY pkstockid DESC LIMIT 0,1");
		if(sizeof($bcstock)>0)
		{
			// commented by yasir 22-12-2011
			/*$cprice		=	$bcstock[0]['costprice'];
			$sprice		=	$bcstock[0]['retailprice'];
			$profit		=	$sprice-$cprice;*/
			// added by yasir 22-12-2011
			$profit		=	0;
		}
		else
		{
			$profit		=	0;
		}
	}
	else
	{
		$profit		=	$pf;
	}
	if($shour!="")
	{
		$hourday	=	$reportresult[$i]['hourofday'];
		for($hours=$shour;$hours<=$ehour;$hours++)
		{
			if($hourday==$hours)
			{
				$salearr[$hours]	=	$salearr[$hours]+$saleprice; //adding sale price
				$profitarr[$hours]	=	$profitarr[$hours]+$profit; //adding profit
				$qtyarr[$hours]		=	$qtyarr[$hours]+$quantity; //adding quantity
			}
			else
			{
				$salearr[$hours]	=	$salearr[$hours]+0; //adding sale price
				$profitarr[$hours]	=	$profitarr[$hours]+0; //adding profit
				$qtyarr[$hours]		=	$qtyarr[$hours]+0; //adding quantity
			}
		}
	}
	else if($sday!="")
	{
		$dayweek	=	$reportresult[$i]['dayofweek'];
		for($days=$sday;$days<=$eday;$days++)
		{
			if($dayweek==$days)
			{
				$salearr[$days]		=	$salearr[$days]+$saleprice; //adding sale price
				$profitarr[$days]	=	$profitarr[$days]+$profit; //adding profit
				$qtyarr[$days]		=	$qtyarr[$days]+$quantity; //adding quantity
			}
			else
			{
				$salearr[$days]		=	$salearr[$days]+0; //adding sale price
				$profitarr[$days]	=	$profitarr[$days]+0; //adding profit
				$qtyarr[$days]		=	$qtyarr[$days]+0; //adding quantity
			}
		}
	}
	else
	{
		/*echo "<pre>";
		echo "$daymonth,...";
		print_r($newdate);
		echo "</pre><br>";*/
		for($nd=0;$nd<sizeof($newdate);$nd++)
		{
			$ndate	=	$newdate[$nd];
			//echo $daymonth."==".$ndate;
			if($daymonth==$newdate[$nd])
			{
				$salearr[$daymonth]		+=	$saleprice; //adding sale price
				$profitarr[$daymonth]	+=	$profit; //adding profit
				$qtyarr[$daymonth]		+=	$quantity; //adding quantity
			}
			else
			{
				$salearr[$ndate]	+=	0; //adding sale price
				$profitarr[$ndate]	+=	0; //adding profit
				$qtyarr[$ndate]		+=	0; //adding quantity	
			}
		}
	}
	$percent	=	$profit/($costprice*$quantity)*100; // $costprice replaced with ($costprice*$quantity) by Yasir 22-12-2011
	$percent	=	round($percent,2);
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
    <td colspan="6" align="right"><strong>Group Total</strong></td>
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
    <td colspan="9"><?php
		echo "<b>$productname</b>";
		$flag	= 	1;
		?></td>
  </tr>
  <?php
	}
?>
  <tr>
  	<td><?php echo $reportresult[$i]['pksaleid'];?></td>
  	<td align="center"><?php echo $reportresult[$i]['datetime']." <br>".$reportresult[$i]['dtime'];?></td>
    <td><?php echo $reportresult[$i]['barcode']; ?></td>
    <td><?php echo  $item;?></td>
    <td align="right"><?php echo $quantity; ?></td>
    <td align="right"><?php echo $remaining; ?></td>
    <td align="right"><?php echo number_format($costprice,2); ?></td>
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
    <td colspan="6" align="right"><strong>Group Total</strong></td>
    <td align="right"><?php echo number_format($grouptotal,2); ?></td>
    <td align="right"><?php echo number_format($groupprofit,2); ?></td>
    <td>&nbsp;</td>
  </tr>
  <?php
}
?>
  <tr>
    <td colspan="7" align="right"><strong>Total</strong></td>
    <td align="right"><strong><?php echo number_format($totalsale,2); ?></strong></td>
    <td align="right"><strong><?php echo number_format($totalprofit,2); ?></strong></td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php
// setting chart values
$title		=	"Sales Report";
//	sale array
$salesvals	=	implode(",",$salearr);
// profit array
$profitvals	=	implode(",",$profitarr);
// quantity array
$qtyvals	=	implode(",",$qtyarr);
$xasixvals	=	array();
if($shour!="")
{
	// calculate on the basis of hour dates
	for($start=$shour;$start<=$ehour;$start++)
	{
		$calchour		=	date("g a", strtotime("$start:00:00"));
		$xaxisvals[]	=	"'".$calchour."'";
	}
}
else if($sday!="")
{
	// calculate on the basis of day dates
	for($start=$sday;$start<=$eday;$start++)
	{
		if($start==1)
		{
			$calcday	=	'Sunday';
		}
		else if($start==2)
		{
			$calcday	=	'Monday';	
		}
		else if($start==3)
		{
			$calcday	=	'Tuesday';	
		}
		else if($start==4)
		{
			$calcday	=	'Wednesday';	
		}
		else if($start==5)
		{
			$calcday	=	'Thursday';	
		}
		else if($start==6)
		{
			$calcday	=	'Friday';	
		}
		else if($start==7)
		{
			$calcday	=	'Saturday';	
		}
		$xaxisvals[]	=	"'".$calcday."'";
	}
}
else
{
	//echo $dateinterval."is the date interval<br>";
	$ilimit	=	0;
	for($x=0;$x<=$dateinterval;$x++)
	{
		if($dateinterval>=10)
		{
			if($x%5!=0)
			{
				$s	=	date("d-m-Y",strtotime(date("d-m-Y", strtotime($sdate)) . " +$x day"));
				$newsalearr[$ilimit]	+=	$salearr[$s];
				$newprofitarr[$ilimit]	+=	$profitarr[$s];
			}
			else
			{
				$s	=	date("d-m-Y",strtotime(date("d-m-Y", strtotime($sdate)) . " +$x day"));
				$newsalearr[$ilimit]	+=	$salearr[$s];
				$newprofitarr[$ilimit]	+=	$profitarr[$s];
				$n 				=	date("d-m-Y",strtotime(date("d-m-Y", strtotime($sdate)) . " +$ilimit day"));
				$xaxisvals[]	=	"'".$n."'";
				$ilimit+=5;
			}
		}
		else
		{
			//echo "in interval $x....$salesvals....$profitvals<br>";
			$n 				=	date("d-m-Y",strtotime(date("d-m-Y", strtotime($sdate)) . " +$x day"));
			$xaxisvals[]	=	"'".$n."'";
		}
	}
	if($dateinterval>=10)
	{
		$n				=	date("d-m-Y",strtotime(date("d-m-Y", strtotime($sdate)) . " +$ilimit day"));
		$xaxisvals[]	=	"'".$n."'";
		$salesvals		=	implode(",",$newsalearr);
		$profitvals		=	implode(",",$newprofitarr);
	}
}
print"<b>$totalsaleprice</b>";
//categories
$xaxisvals	=	$xaxisvals;
$loopvals	=	implode(",",$xaxisvals);
$xaxis		=	"[".$loopvals."]";
$yaxislabel	=	"Sales (Rs)";
$yaxisparam	=	"Rs";
$valstring	=	"{name: 'Sales',data: [".$salesvals."]},{name: 'Profit',data: [".$profitvals."]}";
include_once("../includes/chart/line.php");
?>
</form> <!--end form-->
</body>
</html>
<?php 
//////////////////////add by wajid for excel export/////////////////////////
echo $exporactions;
//////////////////////////////////////////////////////////////////////////
?>