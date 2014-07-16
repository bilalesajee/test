<html>

<head>

<title>Report</title>

<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />

</head>
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
<body>

<?php

include("../includes/security/adminsecurity.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
global $AdminDAO;

$reporttype			=	$_GET['reporttype'];

$addressbookid				=	$_GET['addressbookid'];
$CounterName=$_GET['countername'];  //Added By Fahad 06-06-2012

if($CounterName!=''){
	$cond= " AND countername='$CounterName' ";
	}

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

if($_GET['todate']=='')

{

	echo "Select To Date";

	exit;

}

$todate				=	explode("-",$_GET['todate']);

$today				=	$todate[0];

$tomon				=	$todate[1];

$toyr				=	$todate[2];

$todate			=	mktime(23,59,59,$tomon,$today,$toyr);

/*$sql="select 

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

$pkaddressbookid=	$emparr[0]['pkaddressbookid'];*/

//}

if($addressbookid=='')

{

	$addressbook	=	" ";

}

else

{

	$addressbook	=	" and fkaddressbookid='$addressbookid'";

}



// Removed -(cashcollect+fccollect) from cashdiffernce by Yasir 09-12-11

// Removed +(cashcollect+fccollect) from net cash by yasir 09-12-11

$query	=	"SELECT 

			pkclosingid,

			round((SELECT SUM(amount) FROM $dbname_detail.accountpayment WHERE fkclosingid = pkclosingid) ,2) as payouts,

			(SELECT CONCAT(firstname,' ',lastname) from addressbook where pkaddressbookid=fkaddressbookid) as username,

			countername,

			round(openingbalance,2) as openingbalance,

			round(cashsale,2) as cashsale,

			round(creditsale,2) as creditsale,

			round(creditcardsale,2) as creditcardsale,

			round(chequesale,2) as chequesale,

			round(foreigncurrencysale,2) as foreigncurrencysale,

			round(netcash,2) as netcash,

			round(declaredamount,2) as declaredamount,

			IF( cashdiffirence > 0,round(cashdiffirence,2),CONCAT(round(cashdiffirence,2)) ) as cashdiffirence,  						

			round(totalsale,2) as totalsale,

			totalitems

	FROM 

		$dbname_detail.closinginfo 

	WHERE 

		closingstatus='a' $addressbook and

		closingdate between '$fromdate' and '$todate' $cond

	ORDER BY

		closingdate ASC

	";

	//echo $query;

	$closingarr			=	$AdminDAO->queryresult($query);

	/*echo "<pre>";

	print_r($closingarr);

	echo "</pre>";*/

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

	<th colspan="12">Closing Report</th>

</tr>

<tr>

	

	<td colspan="12" align="center"><b>From <?php echo date("d-m-Y",$fromdate);?> To Date <?php echo $_GET['todate'];?></b></td>

</tr>



<tr>

    <th>sr #</th>

    <th>ID</th>

    <th>Counter</th>

    <th>Opening Bal</th>

    <th>Cash</th>

    <th>Payout</th>

    <th>CC</th>

    <th>FC</th>

    <th>Cheque</th>

    <th>Net Cash</th>

    <th>Declared</th>

    <th>Difference</th>

</tr>

<?php

for($i=0;$i<count($closingarr);$i++)

	{

		$pkclosingid		=	$closingarr[$i]['pkclosingid'];	

		$openingbalance		=	$closingarr[$i]['openingbalance'];	

		$countername		=	$closingarr[$i]['countername'];	

		$cashsale			=	$closingarr[$i]['cashsale'];	

		$payouts			=	$closingarr[$i]['payouts'];	

		$creditcardsale		=	$closingarr[$i]['creditcardsale'];	

		$foreigncurrencysale=	$closingarr[$i]['foreigncurrencysale'];	

		$chequesale			=	$closingarr[$i]['chequesale'];	

		$closingdate		=	$closingarr[$i]['closingdate'];

		$netcash			=	$closingarr[$i]['netcash'];

		$declaredamount		=	$closingarr[$i]['declaredamount'];	

		$cashdiffirence		=	$closingarr[$i]['cashdiffirence'];	

?>

<tr>

  <td><?php echo $i+1;?></td>

  <td><?php echo $pkclosingid;?></td>

  <td><?php echo $countername;?></td>

  <td align="right"><?php echo $openingbalance;?></td>

  <td align="right"><?php echo $cashsale;?></td>

  <td align="right"><?php echo $payouts;?></td>

  <td align="right"><?php echo $creditcardsale;?></td>

  <td align="right"><?php echo $foreigncurrencysale;?></td>

  <td align="right"><?php echo $chequesale;?></td>

  <td align="right"><?php echo $netcash;?></td>

  <td align="right"><?php echo $declaredamount;?></td>

  <td align="right"><?php echo $cashdiffirence;?></td>

</tr>

<?php

$totalopeningbalance		+=	$openingbalance;

$totalcashsale				+=	$cashsale;

$totalpayouts				+=	$payouts;

$totalcreditcardsale		+=	$creditcardsale;

$totalforeigncurrencysale	+=	$foreigncurrencysale;

$totalchequesale			+=	$chequesale;

$totalnetcash				+=	$netcash;

$totaldeclaredamount		+=	$declaredamount;

$totalcashdiffirence		+=	$cashdiffirence;

}

?>

<tr>

  <td colspan="3"><b>Total</b></td>

  <td align="right"><b><?php echo number_format($totalopeningbalance,2);?></b></td>

  <td align="right"><b><?php echo number_format($totalcashsale,2);?></b></td>

  <td align="right"><b><?php echo number_format($totalpayouts,2);?></b></td>

  <td align="right"><b><?php echo number_format($totalcreditcardsale,2);?></b></td>

  <td align="right"><b><?php echo number_format($totalforeigncurrencysale,2);?></b></td>

  <td align="right"><b><?php echo number_format($totalchequesale,2);?></b></td>

  <td align="right"><b><?php echo number_format($totalnetcash,2);?></b></td>

  <td align="right"><b><?php echo number_format($totaldeclaredamount,2);?></b></td>

  <td align="right"><b><?php echo number_format($totalcashdiffirence,2);?></b></td>

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