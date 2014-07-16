<?php

include_once("../includes/security/adminsecurity.php");

include_once("dbgrid.php");

global $AdminDAO;

//date range selection

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

$cid			=	$_GET['cid'];

if($cid)

{

	$and	=	"AND id = '$cid'";

}

//$tempsaleid		= 	$_GET['tempsaleid'];

//selecting tax percentage from gst table

$gst			=	$AdminDAO->getrows("$dbname_detail.gst","amount","1 ORDER BY pkgstid DESC LIMIT 0,1");

$taxpercentage	=	$gst[0]['amount']; //set now 

$customersql	=	"SELECT

						id,

						CONCAT(firstname,' ',lastname) as customername,

						title,taxnumber,ntn

					FROM 

						$dbname_detail.account,$dbname_detail.addressbook

					WHERE

						fkaddressbookid=pkaddressbookid AND

						ctype	=	1

						$and

						";

$custarr		=	$AdminDAO->queryresult($customersql);

for($i=0;$i<sizeof($custarr);$i++)

{

	$customername	=	$custarr[$i]['customername'];

	$companyname	=	$custarr[$i]['title'];

	$taxno			=	$custarr[$i]['taxnumber'];

	$ntn			=	$custarr[$i]['ntn'];

	$customerid		=	$custarr[$i]['id'];

	if($companyname	==	'')

	{

		$companyname=$customername;

	}

	// checking if the sale is delivery challan

	$invoicetype="Sales Tax Invoice";

	$query			=	"SELECT

							s.pksaleid,

							s.serialno,

							FROM_UNIXTIME(s.datetime,'%d-%m-%Y') as trdatetime

						FROM

							$dbname_detail.sale s

						WHERE

							fkaccountid	=	'$customerid' AND

							s.status		=	1 				AND

							datetime BETWEEN $fromdate and $todate

							";

	$customerinfo	=	$AdminDAO->queryresult($query);
/////////////////////////////////////////Added By Fahad 8-11-2012////////////////////////////////////////////////////	
	$query_ponumber		=	"SELECT
							ponum	FROM
							$dbname_detail.purchaseorder
						WHERE
							fkaccountid	=	'$customerid' AND
							status		=	2
							";
	$customerinfo_ponum	=	$AdminDAO->queryresult($query_ponumber);
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	for($j=0;$j<sizeof($customerinfo);$j++)

	{

		$invdate		=	$customerinfo[$j]['trdatetime'];

		$tempsaleid		=	$customerinfo[$j]['pksaleid'];

		$serialno		=	$customerinfo[$j]['serialno'];		

		?>

		<link rel="stylesheet" type="text/css" href="../includes/css/style2.css" />

		<div align="center" style="font-size:18px;"><?php echo $invoicetype."<br />";?></div>

		<div align="center"></div>

		<div id="serial" style="float:right;margin-top:70px;font-size:14px;">

		<br>
             <b>PO No:<?php echo $customerinfo_ponum[0][ponum];?></b>

			<br>

			<b>Serial No: <?php echo $serialno;?></b>

			<br>

			<b>Date: <?php echo $invdate;?></b><br><br>

			<strong>Sales Tax Registration No: </strong><br>07-01-2100-082-55

			<br>

		<strong>NTN: 0344289-6</strong></div>

		<div style="width:8in;padding:0px;margin-left:-200px;font-size:17px;" align="center">

		<img src="../images/esajeelogo.jpg" width="286" height="77">

		<br />

		<span style="font-size:14px; line-height:15px">

		

		<span style="font-weight:bold;font-size:16px;">Think globally shop locally</span><br />

		<span style="font-weight:bold;font-size:14px;">Importers & General Order Suppliers </span><strong><br />

		</strong>H. No. 19, Masjid Road, Sector F-6/3, Islamabad.<br />

    Tel:- 2825100 Ext 102, Fax: 051-2279919<br />

    Email: esajee@esajee.com <br />

    Website: www.esajee.com<br />

		</span>	</div>

		<div>

		<br>

			 <span style="text-transform:uppercase;font-size:14px;font-weight:bold;">Customer: <?php echo $companyname;?>

			<br>

			Customer NTN: <?php if($ntn!='0' || $ntn!=''){echo $ntn;}else{?>__________<?php } ?></span>

		</div><br />

		<table class="simplereport" width="100%">

		<tr>

			<th width="35%">DESCRIPTION</th>

			<th>QTY</th>

			<th>RATE</th>

			<th >AMOUNT</th>

			<th >S.TAX <?php echo $taxpercentage;?>%</th>

			<th >TOTAL VALUE</th>

		</tr>

		<?php

			$sdquery	=	"SELECT

								sum(sd.saleprice*sd.quantity) as amount,

								sd.saleprice,

								sd.quantity,

								b.itemdescription,

								customdescription

							FROM

								$dbname_detail.saledetail sd LEFT JOIN $dbname_detail.podetail ON (sd.fkpodetailid	=	pkpodetailid),

								barcode b,

								$dbname_detail.stock st

							WHERE

								sd.fksaleid		=	'$tempsaleid' AND

								sd.fkstockid	=	st.pkstockid AND

								b.pkbarcodeid	=	st.fkbarcodeid  

							GROUP BY 

								sd.fkstockid,sd.saleprice

							";

			$saledetail	=	$AdminDAO->queryresult($sdquery);

			for($k=0;$k<count($saledetail);$k++)

			{

				$amount				=	$saledetail[$k]['amount'];

				$saleprice			=	$saledetail[$k]['saleprice'];

				$quantity			=	$saledetail[$k]['quantity'];

				$tax				=	$taxpercentage/100*$amount;

				$customdescription	=	$saledetail[$k]['customdescription'];

				if($customdescription!='')

				{

					$itemdescription	=	$customdescription;

				}

				else

				{

					$itemdescription	=	$saledetail[$k]['itemdescription'];

				}

			?>

			<tr>

				<td style="text-transform:capitalize"><?php echo ucfirst(strtolower($itemdescription));?></td>

				<td align="right"><?php echo $quantity;?></td>

				<td align="right"><?php echo number_format($saleprice,2);?></td>

				<td align="right"><?php  echo number_format($amount,2);?></td>

				<td align="right"><?php echo number_format($tax,2);?></td>

				<td align="right"><?php echo number_format($amount+$tax,2);?></td>

			</tr>

			<?php

				$totalamount+=$amount;

				$totaltax+=$tax;

			}//end of for

		?>

		<tr>

			

				<td colspan="3" align="right">Total</td>

				<td align="right"><b><?php  echo number_format($totalamount,2);?></b></td>

				<td align="right"><b><?php echo number_format($totaltax,2);?></b></td>

				<td align="right"><b><?php echo number_format($totalamount+$totaltax,2);?></b></td>

		  </tr>

		</table>

		<div style="page-break-after:always;"></div>

		<br />

<?php

	$totalamount	=	0;

	$totaltax		=	0;

	}// for sales loop

}// for customer loop

?>

<script language="javascript">

	window.print();

</script>