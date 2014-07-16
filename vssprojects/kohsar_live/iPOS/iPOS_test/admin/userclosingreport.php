<html>
<head>
<title>Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
</head>
<body>
<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$reporttype			=	$_GET['reporttype'];
$userid				=	$_GET['userid'];
if($_GET['fromdate']=='')
{
	$fromdatex			=	date('d-m-Y');
	$fromdate			=	explode("-",$fromdatex);
	$fromday			=	$fromdate[0];
	$frommon			=	$fromdate[1];
	$fromyr				=	$fromdate[2];
	$fromdate			=	mktime(0,0,0,$frommon,$fromday,$fromyr);
}
else
{
	$fromdate			=	explode("-",$_GET['fromdate']);
	$fromday			=	$fromdate[0];
	$frommon			=	$fromdate[1];
	$fromyr				=	$fromdate[2];
	$fromdate			=	mktime(0,0,0,$frommon,$fromday,$fromyr);
}
	$todate				=	explode("-",$_GET['todate']);
	$today				=	$todate[0];
	$tomon				=	$todate[1];
	$toyr				=	$todate[2];
	$todate				=	@mktime(23,59,59,$tomon,$today,$toyr);
/*$balance	=	$AdminDAO->queryresult($query);
$totalpaid	=	$balance[0]['amount'];
$totalpaid	+=	$balance[1]['amount'];
$totalpaid	+=	$balance[2]['amount'];
$totalpaid	+=	$balance[3]['amount'];*/
$sql="select 
				firstname,
				lastname,
				pkaddressbookid 
			from 
				addressbook,
				employee 
			where 
				pkaddressbookid=fkaddressbookid and 
				pkemployeeid='$userid'";
$emparr	=	$AdminDAO->queryresult($sql);
$employeename	=	$emparr[0]['firstname'].' '.$emparr[0]['lastname'];
$pkaddressbookid=	$emparr[0]['pkaddressbookid'];
?>
<div id="writeoffdiv"></div>
<div style="width:8in;padding:0px;font-size:17px;" align="center">
<img src="../images/esajeelogo.jpg" width="197" height="58">
<br />
<span style="font-size:11px;font-family:'Comic Sans MS', cursive;">
<b>Think globally shop locally</b>
</span>
</div>
<table class="simple" style="width:8in">
<tr>
	<th colspan="7">User Closing Report of <?php echo $employeename;?></th>
</tr>
<tr>
	
	<td colspan="7" align="center"><b>From <?php echo date("d-m-Y",$fromdate);?> To Date <?php echo $_GET['todate'];?></b></td>
</tr>

<tr>
  <th>Sr. #</th>
  <th>ID</th>
	<th>Date</th>
	<th>Declared</th>
	<th>Cash + </th>
	<th>Cash - </th>
	<th>Difference</th>
</tr>
<?php
//}

			$query	=	"SELECT 
						pkclosingid,
						
						from_unixtime(closingdate,'%Y-%m-%d') as closingdate,
						from_unixtime(closingdate,'%Y-%m') as closingdatemonth,
						round((SELECT SUM(amount) FROM $dbname_detail.accountpayment WHERE fkclosingid = pkclosingid) ,2) as payouts,
						(SELECT CONCAT(firstname,' ',lastname) from addressbook where pkaddressbookid=fkaddressbookid) as username,
						countername,
						round(openingbalance,2) as openingbalance,
						round(cashsale,2) as cashsale,
						round(creditsale,2) as creditsale,
						round(creditcardsale,2) as creditcardsale,
						round(chequesale,2) as chequesale,
						round(foreigncurrencysale,2) as foreigncurrencysale,
						round(netcash+(cashcollect+cccollect+fccollect+chequecollect),2) as netcash,
						round(declaredamount,2) as declaredamount,
						IF( cashdiffirence > 0,round(cashdiffirence-(cashcollect+cccollect+fccollect+chequecollect),2),CONCAT(round(cashdiffirence-(cashcollect+cccollect+fccollect+chequecollect),2),' Short') ) as cashdiffirence,  						
						round(totalsale,2) as totalsale,
						totalitems
				FROM 
					$dbname_detail.closinginfo 
				WHERE 
					closingstatus='a' and 
					fkaddressbookid='$pkaddressbookid'
				ORDER BY
					closingdate ASC
				";
	$closingarr			=	$AdminDAO->queryresult($query);
	for($i=0;$i<count($closingarr);$i++)
	{
		$pkclosingid	=	$closingarr[$i]['pkclosingid'];	
		$closingdate	=	$closingarr[$i]['closingdate'];	
		$declaredamount	=	$closingarr[$i]['declaredamount'];	
		$cashdiffirence	=	$closingarr[$i]['cashdiffirence'];	
		$closingdatemonth=$closingarr[$i]['closingdatemonth'];	
		if($i==0)
		{
			$month=$closingdatemonth;
		}//checking the month change
		if($closingdatemonth!=$month)
		{
			$month=$closingdatemonth;
			$declaredamountmonth	=	number_format($declaredamountmonth,2);
			$plusmonth				=	number_format($plusmonth,2);
			$minusmonth				=	number_format($minusmonth,2);
			$plusminus				=	number_format($plusminus,2);
			$monthhtml="<tr style='background-color:#999999'>
			  <td colspan=\"3\" align=\"right\"><b>Total</b></td>
			  <td align='right'>$declaredamountmonth</td>
			  <td align='right'> $plusmonth</td>
			  <td align='right'>$minusmonth</td>
			  <td align='right'>$plusminus</td>
			</tr>";
			echo $monthhtml;
			$declaredamountmonth	=	0;
			$plusmonth				=	0;
			$minusmonth				=	0;
		}
		if($cashdiffirence<0)
		{
			$minus	=$cashdiffirence;
			$plus=0;
			$totalminus+=$minus;
			if($closingdatemonth==$month)
			{
				$monthtotalminus+=$minus;
			}
		}
		else
		{
			$plus	=$cashdiffirence;
			$minus=0;
			$totalplus+=$plus;
			if($closingdatemonth==$month)
			{
				$monthtotalplus+=$plus;
			}
		}	
		$declaredamountmonth+=$declaredamount;
		$plusmonth+=$plus;
		$minusmonth+=$minus;
		$plusminus	=	$plusmonth+$minusmonth;
		$totaldeclared+=$declaredamount;
		
		
?>
<tr>
  <td><?php echo $i+1;?></td>
  <td><?php echo $pkclosingid;?></td>
	<td><?php echo $closingdate;?></td>
	<td align="right"><?php  echo $declaredamount;?></td>
	<td align="right"><?php if($plus>0){echo number_format($plus,2);}?></td>
	<td align="right"><?php if($minus<0){echo number_format($minus,2);}?></td>
	<td align="right">&nbsp;</td>
</tr>
<?php
$monthhtml='';
}//for	
?>
<tr style='background-color:#999999'>
  <td colspan="3" align="right"><b>Total</b></td>
  <td align='right'><?php echo number_format($declaredamountmonth,2);?></td>
  <td align='right'><?php echo number_format($plusmonth,2);?></td>
  <td align='right'><?php echo number_format($minusmonth,2);?></td>
  <td align='right'><?php echo number_format($plusminus,2);?></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td align="right">
  <strong>
    <?php if($totaldeclared>0){echo number_format($totaldeclared,2);}?>
  </strong></td>
  <td align="right">
    <strong>
      <?php if($totalplus>0){echo number_format($totalplus,2);}?>
    </strong>  </td>
  <td align="right"><strong>
    <?php if($totalminus<0){echo number_format($totalminus,2);}?>
  </strong></td>
  <td align="right"><strong><?php echo number_format($totalminus+$totalplus,2);?></strong></td>
</tr>
</table>
</body>
</html>