<?php
include_once("../includes/security/adminsecurity.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	include_once("dbgrid.php");
}//end edit
global $AdminDAO;
/*************************DATE CHECKS**************************/
 $sdate				=	strtotime($_GET['sdate']. ' 00:00:00'); 
 $edate				=	strtotime($_GET['edate'].' 23:59:59');

 if($sdate != '' && $edate!='')
  {
   $cond = "  cl.openingdate  between $sdate and $edate "; 
  }


$query = "select cl.countername,FROM_UNIXTIME(cl.openingdate,'%d-%m-%Y') as openingdate,
 
 cl.payout ,
cl.openingbalance as openingbalance,
cl.cashsale as cashsale,
cl.creditsale as creditsale,
cl.chequesale as chequesale,
round((SELECT SUM(sa.globaldiscount) as globaldiscount FROM $dbname_detail.sale sa WHERE sa.fkclosingid = cl.pkclosingid) ,2) as discount,

cl.creditcardsale as creditcardsale, IF( cashdiffirence > 0,CONCAT(round(cashdiffirence,2),' Extra'),CONCAT(round(cashdiffirence,2),' Short') ) as cashdiffirence ,cl.totalsale as totalsale 
FROM $dbname_detail.closinginfo cl  WHERE cl.closingstatus='a' and $cond order by cl.openingdate, cl.countername asc";
$reportresult = $AdminDAO->queryresult($query);



/**************************************************************/

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Daily Sales Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
<link rel="stylesheet" type="text/css" href="../includes/css/style.css">
</head>
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
<body>
<div style="width:8.0in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>ESAJEE'S</b>
</div>
<div style="width:8.0in;font-size:11px;font-family:Comic Sans MS, cursive;" align="center"><b>Think globally shop locally</b></div><br />
<div style="width:8.0in;font-size:12px;font-weight:bold;" align="center"><?php echo $storename."<br />".$storeaddress1;?><br />
<br />
Daily Sales Report</div>
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

	<table class="simple">
    <?php
	$old_date ='';
	$totalsale = 0;
   $returnsale_total=0;
   $coupon_sold_total=0;
   $used_coupon_total=0;
   $payouts_total=0;
   $cashsale_total =0;
   $creditsale_total=0;
   $creditcardsale_total=0;
   $credit_sale_return_total=0;
   $discount_total=0;
   $chequesale_total=0;
   $foreigncurrencysale_total =0;
   $collectionamount_total=0;
   $openingbalance_total = 0;
   $cashdiffirence_total = 0;
   $cheque_sale_total=0;
		for($i=0;$i<count($reportresult);$i++)
		{
			$totalsale +=$reportresult[$i]['totalsale'];
			$payouts_total +=$reportresult[$i]['payout'];
			$cashsale_total +=$reportresult[$i]['cashsale'];
			$creditsale_total +=$reportresult[$i]['creditsale'];
			$creditcardsale_total +=$reportresult[$i]['creditcardsale'];
		    $discount_total +=$reportresult[$i]['discount'];
			$chequesale_total +=$reportresult[$i]['chequesale'];
			$openingbalance_total +=$reportresult[$i]['openingbalance'];
			$cashdiffirence_total +=$reportresult[$i]['cashdiffirence'];
			$openingdate =$reportresult[$i]['openingdate'];
			$cheque_sale_total +=$reportresult[$i]['chequesale'];
         
		if($openingdate!=$old)	{?>
	<tr>
		<th  style="background-color:#F8C107; color:#000"><?php echo $openingdate;?></th>
		<th >Opening Balance</th>
		<th >Sale</th>
		<th >Payouts</th>
		<th >Cash Sale</th>
        <th >Credit Sale</th>
        <th >Credit Card</th>
        <th >Cheque Sale</th>
        <th >Total Discount</th>
        <th >Difference</th>
      </tr>
   <?php }?>
	
			<tr>
				<td bgcolor="#FFFF00">Counter <?php echo $reportresult[$i]['countername'];?>&nbsp;</td>
				<td align="right"><?php echo $reportresult[$i]['openingbalance'];?>&nbsp;</td>
				<td align="right"><?php echo $reportresult[$i]['totalsale'];?>&nbsp;</td>
			    <td align="right"><?php echo $reportresult[$i]['payout'];?>&nbsp;</td>
                <td align="right"><?php echo $reportresult[$i]['cashsale'];?>&nbsp;</td>
                <td align="right"><?php echo $reportresult[$i]['creditsale'];?>&nbsp;</td>
                <td align="right"><?php echo $reportresult[$i]['creditcardsale'];?>&nbsp;</td>
                <td align="right"><?php echo $reportresult[$i]['chequesale'];?>&nbsp;</td>
                <td align="right"><?php echo $reportresult[$i]['discount'];?>&nbsp;</td>
                <td align="right"><?php echo $reportresult[$i]['cashdiffirence'];?>&nbsp;</td>
            </tr>
            <?php 
		
	if($reportresult[$i+1]['openingdate']!=$openingdate)	{?>
	
			<tr>
			  <td align="left" bgcolor="#E6F9FF"><strong>Total</strong></td>
			  <td align="right" bgcolor="#E6F9FF"><?php echo $openingbalance_total;?>&nbsp;</td>
			  <td align="right" bgcolor="#E6F9FF"><?php echo $totalsale; ?>&nbsp;</td>
			  <td align="right" bgcolor="#E6F9FF"><?php echo $payouts_total; ?>&nbsp;</td>
			  <td align="right" bgcolor="#E6F9FF"><?php echo $cashsale_total; ?>&nbsp;</td>
			  <td align="right" bgcolor="#E6F9FF"><?php echo $creditsale_total; ?>&nbsp;</td>
			  <td align="right" bgcolor="#E6F9FF"><?php echo $creditcardsale_total; ?>&nbsp;</td>
			  <td align="right" bgcolor="#E6F9FF"><?php echo $cheque_sale_total; ?>&nbsp;</td>
			  <td align="right" bgcolor="#E6F9FF"><?php echo $discount_total; ?>&nbsp;</td>
			  <td align="right" bgcolor="#E6F9FF"><?php echo $cashdiffirence_total;?>&nbsp;</td>
      </tr>
		<?php 
		$old_date ='';
	$totalsale = 0;
   $returnsale_total=0;
   $coupon_sold_total=0;
   $used_coupon_total=0;
   $payouts_total=0;
   $cashsale_total =0;
   $creditsale_total=0;
   $creditcardsale_total=0;
   $credit_sale_return_total=0;
   $discount_total=0;
   $chequesale_total=0;
   $foreigncurrencysale_total =0;
   $collectionamount_total=0;
   $openingbalance_total = 0;
   $cashdiffirence_total = 0;
   $cheque_sale_total=0;
		
		}
		$old =$reportresult[$i]['openingdate'];
		}
		
		?>	   
   
   

	
	</table>
   
	</form> 
	<!--end form-->
    </body>
    </html>
    <?php 
//////////////////////add by wajid for excel export/////////////////////////
echo $exporactions;
//////////////////////////////////////////////////////////////////////////
?>
