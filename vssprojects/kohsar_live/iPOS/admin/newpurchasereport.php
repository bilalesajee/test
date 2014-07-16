<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
global $AdminDAO;
/*************************DATE CHECKS**************************/
$start_date				=	strtotime($_GET['sdate'].'00:00:00'); 
$end_date				=	strtotime($_GET['edate']."23:59:59");
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

$query="SELECT su.invoice_status, s.fksupplierinvoiceid, FROM_UNIXTIME(su.datetime,'%d-%m-%Y') adddate, sp.companyname ,round(SUM(s.quantity * s.priceinrs),2) as invoice_value from $dbname_detail.supplierinvoice su
	 left join $dbname_detail.stock s on su.pksupplierinvoiceid = s.fksupplierinvoiceid left join main.supplier sp on (su.fksupplierid=pksupplierid)
	 where  su.datetime between '$start_date' and '$end_date'  and su.invoice_status = 1 and  su.accdatasent = 0  and s.fksupplierid > 0  and s.fksupplierinvoiceid > 0 and s.quantity > 0 group by s.fksupplierinvoiceid order by su.datetime desc ";

$reportresult		=	$AdminDAO->queryresult($query);

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Purchase Report</title>
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
<div style="width:2.6in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>Purchase Report</b></div>
<br />
From <?php echo "<b>".implode("-",array_reverse(explode("-",$_GET['sdate'])))."</b>";?> To <?php echo "<b>".implode("-",array_reverse(explode("-",$_GET['edate'])))."</b><br />";?>
<br />
<table style="font-size:12px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
  <tr>
  	<th>Sr #</th>
  	<th>Invoice #</th>
    <th>Date</th>
     <th>Supplier</th>
    <th>Amount</th>
    <th>Status</th>
 
  </tr>
  <?php
  $totalamut=0;
  for($i=0;$i<sizeof($reportresult);$i++)
  {
	  //fetching records
	  $pkstockid		=	$reportresult[$i]['fksupplierinvoiceid'];
	  $datetime			=	$reportresult[$i]['adddate'];
	  $companyname		=	$reportresult[$i]['companyname'];
	  $itemdescription	=	$reportresult[$i]['invoice_value'];
	  $totalamut+=$itemdescription;
	  
	  if($reportresult[$i]['invoice_status'] =='0')
	  {
		  $status = "Open";
		  }
		  else if($reportresult[$i]['invoice_status'] =='1')
		  {
			   $status = "Close";
			  }
			   else if($reportresult[$i]['invoice_status'] =='2')
			   {
				      $status = "Void";
				   }
	 
  ?>
  <tr>
    <td><?php echo $i+1;?></td>
  	<td><?php echo $pkstockid;?></td>
    <td align="center"><?php echo $datetime;?></td>
    <td><?php echo $companyname;?></td>
    <td align="right"><?php echo $itemdescription;?></td>
    <td align="right"><?php echo $status;?></td>
    
  </tr>
  <?php
  }// end for
  ?>
   <tr>
    <td colspan="4" align="right">Total</td>
    <td align="right"><?php echo $totalamut;?></td>
  	<td align="right">&nbsp;</td>
    
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