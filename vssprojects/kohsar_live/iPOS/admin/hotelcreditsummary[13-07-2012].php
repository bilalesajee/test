<html>
<head>
<title>Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
<style>
.repstyle td{
	padding:3px;
	font-family:"Times New Roman", Times, serif;
	font-size:12px;
}
</style>
</head>
<body>
<?php
//echo $_SERVER['QUERY_STRING'].'<br>-------------------------------------------------<br>';
//tempsaleid=39058&reporttype=3&taxpercentage=16&customerid=31&customercopy=y&serialno=&adjustmentmode=0
//tempsaleid=39058&reporttype=3&taxpercentage=16&customerid=31&customercopy=y&serialno=&adjustmentmode=0
error_reporting(7);
include("../includes/security/adminsecurity.php");
	global $AdminDAO;
	$reporttype			=	$_GET['reporttype'];
	$cid				=	$_GET['customerid'];
	$taxpercentage	=	$AdminDAO->getrows("$dbname_detail.gst","amount","1 ORDER BY pkgstid DESC LIMIT 0,1");
	$salestaxper		=	$taxpercentage[0]['amount'];
	// retrieving customer type to show either serial no or bill no
	$customers		=	$AdminDAO->getrows("$dbname_detail.account","ctype","id='$cid'");
	// 1=Hotel , 2=creditor
	$ctype				=	$customers[0]['ctype'];
	$taxpercentage		=	$_GET['taxpercentage'];
	$customercopy		=	$_GET['customercopy'];
	$officecopy			=	$_GET['officecopy'];
	$salestaxcopy		=	$_GET['salestaxcopy'];
	$serialno			=	$_GET['serialno'];
	$invoicedate		=	$_GET['invoicedate'];
	$adjustmentmode		=	$_GET['adjustmentmode'];
	$writeoffmode		=	$_GET['writeoffmode'];
	$favouredbyid		=	$_GET['favouredbyid'];
	if($writeoffmode=='1')
	{
		if($favouredbyid=='')
		{
			?>
<script language="javascript">
			alert("Please select the Favoting Authority Name to give writeoff.");
			self.close();
			</script>
<?php
		}
	}	
	if($invoicedate=='')
	{
		$lastday 			= @mktime(0, 0, 0, date('m')+1, 0, date('y'));
		$lastdayofthemonth	= @strftime("%d", $lastday);

		$invoicedate=$lastdayofthemonth.date('-m-y');
	}
	$customersql="SELECT 
						CONCAT(firstname,' ',lastname) as customername,
						title,taxnumber
					FROM 
						$dbname_detail.account,$dbname_detail.addressbook
						WHERE
							fkaddressbookid=pkaddressbookid AND
							id='$cid'";
	$custarr			=	$AdminDAO->queryresult($customersql);
	$customername		=	$custarr[0]['customername'];
	$companyname		=	$custarr[0]['title'];
	$taxno				=	$custarr[0]['taxnumber'];
	if($companyname=='')
	{
		$companyname=$customername;
	}
	if($_GET['fromdate']=='')
	{
		$fromdatex			=	date('d-m-y');
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
	

		 $query		=	"SELECT paymentmethod as type,pksaleid,creditinvoiceno, SUM(amount)  amount,FROM_UNIXTIME(paytime,'%d-%m-%y') trdatetime FROM $dbname_detail.sale,$dbname_detail.payments where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' group by fkaccountid
";
		//echo $query;
		//exit;
		
		$balance	=	$AdminDAO->queryresult($query);
		$totalpaid	=	$balance[0]['amount'];
		$totalpaid	+=	$balance[1]['amount'];
		$totalpaid	+=	$balance[2]['amount'];
		$totalpaid	+=	$balance[3]['amount'];
		//print"Sale: $totalsale paid:$totalpaid<br>";
		//$balbf=$totalsale-$totalpaid;
		//$balbf=$totalpaid-$totalsale;
		
//code added by jafer 14-12-11
		$customerinfo		=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.saledetail sd,$dbname_detail.account c,$dbname_detail.addressbook a","s.pksaleid,s.creditinvoiceno,from_unixtime(s.datetime,'%d-%m-%y') as trdatetime,sum((sd.saleprice*sd.quantity)+sd.taxamount) as taxamount","s.fkaccountid=id AND s.status=1 AND c.fkaddressbookid=pkaddressbookid AND id='$cid' AND sd.fksaleid=pksaleid AND s.datetime BETWEEN '$fromdate' AND '$todate' GROUP BY pksaleid");
//code added by jafer 14-12-11	
//commented by jafer 14-12-11	
		/*$customerinfo		=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.saledetail sd,$dbname_detail.customer c,$dbname_detail.addressbook a","s.pksaleid,s.creditinvoiceno,from_unixtime(s.datetime,'%d-%m-%Y') as trdatetime,sum(sd.saleprice*sd.quantity) as amount","s.fkcustomerid=pkcustomerid AND s.status=1 AND c.fkaddressbookid=pkaddressbookid AND pkcustomerid='$cid' AND sd.fksaleid=pksaleid AND s.datetime BETWEEN '$fromdate' AND '$todate' GROUP BY pksaleid");*/
//commented by jafer 14-12-11
		
	//collections results
	/*$sql="SELECT s.pksaleid,s.creditinvoiceno,from_unixtime(s.datetime,'%d-%m-%Y') as trdatetime,sum(sd.saleprice*sd.quantity) as amount
				FROM
					$dbname_detail.sale s,$dbname_detail.saledetail sd,$dbname_detail.customer c,$dbname_detail.addressbook a
				WHERE
					s.fkcustomerid=pkcustomerid AND s.status=1 AND c.fkaddressbookid=pkaddressbookid AND pkcustomerid='$cid' AND sd.fksaleid=pksaleid AND s.datetime BETWEEN '$fromdate' AND '$todate' AND sd.fkstockid<>'94576' GROUP BY pksaleid
					";//skip sale tax item 94576 stock id */
			
	echo $sql;
	$currentmonth	=	date("F",$fromdate);
	$currentyear	=	date("y",$fromdate);
	?>
<div id="writeoffdiv"></div>
<table class="repstyle" style="width:8in;border-collapse:collapse;" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="4" align="left" style="font-family:Arial, Helvetica, sans-serif;font-weight:bold;font-size:18px;border-top:4px solid #000;border-bottom:1px solid #fff;border-left:1px solid #fff;border-right:1px solid #fff;">Hotel Billing Details</td>
  </tr>
  <tr>
    <td style="border-left:1px solid #fff;border-right:1px solid #fff;border-bottom:1px solid #FFF;"><b> <?php echo date("d/m/Y",time());?></b></td>
    <td colspan="3" style="border-left:1px solid #fff;border-right:1px solid #fff;border-bottom:1px solid #FFF;"><b><span style="text-transform:uppercase"><?php echo $companyname;?></span></b></td>
  </tr>
  <tr>
  	<td colspan="4" style="line-height:1px;border-left:1px solid #fff;border-right:1px solid #fff;">&nbsp;</td>
  </tr>
  <tr>
   <th style="border-left:1px solid #fff;border-right:1px solid #fff;border-bottom:4px solid #000;border-top:4px solid #000;">Date</th>
    <th style="border-left:1px solid #fff;border-right:1px solid #fff;border-bottom:4px solid #000;border-top:4px solid #000;">Vno</th>
   
    <th colspan="2" style="border-left:1px solid #fff;border-right:1px solid #fff;border-bottom:4px solid #000;border-top:4px solid #000;">Amount</th>
  </tr>
  <tr>
  	<td colspan="3"><h3><?php echo $currentmonth."    ".$currentyear;?></h3></td>
  </tr>
  <?php
	
	$range	=	@gregoriantojd($tomon,$today,$toyr)-@gregoriantojd($frommon,$fromday,$fromyr);
	$range	+=1;
	$stdate	=	$fromdate;
	for($j=1;$j<=$range;$j++)
	{
		$sdate	=	date("d-m-y",$stdate);
		for($k=0;$k<sizeof($customerinfo);$k++)
		{
			if($sdate== $customerinfo[$k]['trdatetime'])
			{
				$sid			=	$customerinfo[$k]['pksaleid'];
				$squery			=	"SELECT 1 FROM $dbname_detail.saledetail WHERE fksaleid='$sid' AND fkstockid='94576'";
				$squeryres		=	$AdminDAO->queryresult($squery);
				if(sizeof($squeryres)>0)
				{
					continue;
				}
				$pksaleid			=	$customerinfo[$k]['creditinvoiceno'];
				$creditinvoiceno	=	$customerinfo[$k]['creditinvoiceno'];
				$reportdate			=	$customerinfo[$k]['trdatetime'];
				$amount				=	$customerinfo[$k]['taxamount'];	//code added by jafer 14-12-11
				//$amount				=	$customerinfo[$k]['amount'];//commented by jafer 14-12-11
				$type				=	$customerinfo[$k]['type'];
				$trid				=	$customerinfo[$k]['trid'];
				//$amount=($amount*$salestaxper/100)+$amount;			//commented by jafer 14-12-11
			
			
	?>
  <tr>
  <td><?php echo str_replace("-","/",$reportdate);?></td>
    <td><?php echo $creditinvoiceno;?></td>
    
    
    <td align="right" <?php echo $bg;?>>
	<?php
			
				echo number_format($amount,2);
				
				$subtotal+=$amount;
				$grandtotal+=$amount;
			?>
			</td>
  </tr>
  
  <?php
				if($k%36==0 && $k!=0 && sizeof($customerinfo)>=36)
				{
					?>
                    	  <tr>
                            <td colspan="3" align="right"><h3>Subtotal for <?php echo $currentmonth;?>: <?php echo number_format($subtotal,2);?></h3></td>
                          </tr>
                          <tr style="page-break-after:always;">
                            <td colspan="3" align="right"><h3>Grandtotal: <?php echo number_format($grandtotal,2);?></h3></td>
                          </tr>
                           <tr>
                            <td colspan="4" align="left" style="font-family:Arial, Helvetica, sans-serif;font-weight:bold;font-size:18px;border-top:4px solid #000;border-bottom:1px solid #fff;border-left:1px solid #fff;border-right:1px solid #fff;">Hotel Billing Details</td>
                          </tr>
                          <tr>
                            <td style="border-left:1px solid #fff;border-right:1px solid #fff;border-bottom:1px solid #FFF;"><b> <?php echo date("d/m/y",time());?></b></td>
                            <td colspan="3" style="border-left:1px solid #fff;border-right:1px solid #fff;border-bottom:1px solid #FFF;"><b><span style="text-transform:uppercase"><?php echo $companyname;?></span></b></td>
                          </tr>
                          <tr>
                            <td colspan="4" style="line-height:1px;border-left:1px solid #fff;border-right:1px solid #fff;">&nbsp;</td>
                          </tr>
                          <tr>
                           
                            <th style="border-left:1px solid #fff;border-right:1px solid #fff;border-bottom:4px solid #000;border-top:4px solid #000;">Date</th>
                             <th style="border-left:1px solid #fff;border-right:1px solid #fff;border-bottom:4px solid #000;border-top:4px solid #000;">Vno</th>
                            <th colspan="2" style="border-left:1px solid #fff;border-right:1px solid #fff;border-bottom:4px solid #000;border-top:4px solid #000;">Amount</th>
                          </tr>
                          <tr>
                            <td colspan="3"><h3><?php echo $currentmonth."    ".$currentyear;?></h3></td>
                          </tr>
                    <?php
					$subtotal=0;
				}
			}
		}
		$stdate	=	mktime(0,0,0,$frommon,$fromday+$j,$fromyr);
	}
	?>
    		<?php
			if(sizeof($customerinfo<36))
			{
			?>
            <tr>
            <td colspan="3" align="right"><h3>Subtotal for <?php echo $currentmonth;?>: <?php echo number_format($subtotal,2);?></h3></td>
            </tr>
            <tr>
            <td colspan="3" align="right"><h3>Grandtotal: <?php echo number_format($grandtotal,2);?></h3></td>
            </tr>
            <?php
            $subtotal=0;
			}//if
			?>
</table>
</body>
</html>