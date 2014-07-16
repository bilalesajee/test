<?php

include("includes/security/adminsecurity.php");

// fetch tax percentage from db

global $AdminDAO;

//$taxpercentage	=	16; //set later

//selecting tax percentage from gst table

$customerid		=	$_GET['customerid'];

$tempsaleid		= 	$_GET['tempsaleid'];

// query changed by Yasir -- 14-07-11 . Previous was $gst	=	$AdminDAO->getrows("$dbname_main.gst","amount","1 ORDER BY pkgstid DESC LIMIT 0,1");

$gst			=	$AdminDAO->getrows("$dbname_detail.creditinvoices, $dbname_detail.sale","taxpercentage as amount","pksaleid = '$tempsaleid' AND creditinvoiceno = pkcreditinvoiceid");

$taxpercentage	=	$gst[0]['amount']; //set now 

//echo $taxpercentage;



$customersql	=	"SELECT 

						CONCAT(firstname,' ',lastname) as customername,

						companyname,taxnumber,ntn

					FROM 

						customer

					WHERE

						
						pkcustomerid='$customerid'";

$custarr		=	$AdminDAO->queryresult($customersql);

$customername	=	$custarr[0]['customername'];

$companyname	=	$custarr[0]['companyname'];

$taxno			=	$custarr[0]['taxnumber'];

$ntn			=	$custarr[0]['ntn'];

if($companyname	==	'')

{

	$companyname=$customername;

}

// checking if the sale is delivery challan

$dchallans			=	$AdminDAO->getrows("$dbname_detail.sale","status","pksaleid='$tempsaleid'");

$dcstatus			=	$dchallans[0]['status'];

//$deliverychalan	=	$_SESSION['deliverychalan'];

if($dcstatus==4)

 {

 	$status =	" IN (0,4)";

	$invoicetype="Delivery Chalan";

 }

 else

 {

 	$status	= " IN (0,1)";

	$invoicetype="Sales Tax Invoice";

 }

 $query			=	"SELECT

						s.pksaleid,

						sum(sd.saleprice*sd.quantity) as amount,

						sd.saleprice,

						sd.quantity,

						b.itemdescription,

						customdescription,

						FROM_UNIXTIME(s.datetime,'%d-%m-%Y') as trdatetime,

						sd.taxable

					FROM

						$dbname_detail.sale s,

						$dbname_detail.saledetail sd LEFT JOIN $dbname_detail.podetail ON (sd.fkpodetailid	=	pkpodetailid),

						customer c,

						barcode b,

						$dbname_detail.stock st

					WHERE

						s.fkaccountid	=	c.pkcustomerid AND 

						pkcustomerid				=	'$customerid' AND 

						s.pksaleid		=	'$tempsaleid' AND

						sd.fksaleid		=	pksaleid AND 

						sd.fkstockid	=	st.pkstockid AND

						s.status		$status AND

						b.pkbarcodeid	=	st.fkbarcodeid  

					GROUP BY 

						sd.pksaledetailid

						";

$customerinfo	=	$AdminDAO->queryresult($query);

//$_SESSION['deliverychalan']='';

//print_r($customerinfo);

// fetching date

$invdate	=	$customerinfo[0]['trdatetime'];

$sqlinv		=	"select  serialno from $dbname_detail.sale where pksaleid='$tempsaleid'";

$resarrinv	=	$AdminDAO->queryresult($sqlinv);

$serialno	=	$resarrinv[0]['serialno'];		

?>

<html>

    <head>

    <title>Report</title>

    <link rel="stylesheet" type="text/css" href="includes/css/style2.css" />

    </head>

    <body onLoad="if(location.href.indexOf('reload')==-1) location.replace(location.href+'&reload')">

	<div align="center" style="font-size:18px;"><?php echo $invoicetype."<br />";?></div>

    <div align="center">(Customer Copy)</div>

	<div id="serial" style="float:right;margin-top:70px;font-size:14px;">

	<br>

		<b>Serial No: <?php echo $serialno;?></b>

		<br>

		<b>Date: <?php echo $invdate;?></b><br><br>

		<strong>Sales Tax Registration No: </strong><br>07-01-2100-082-55

		<br>

	<strong>NTN: 0344289-6</strong></div>

	<div style="width:8in;padding:0px;margin-left:-200px;font-size:17px;" align="center">

	<img src="images/esajeecologo.jpg" width="286" height="77">

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

		Customer NTN: 

		<?php if($ntn!='0' || $ntn!=''){echo $ntn;}else{?>__________<?php } ?></span>

	</div><br />

	<table class="simplereport" width="100%">

	<tr>

		<th width="35%">DESCRIPTION</th>

		<th>QTY</th>

		<th>RATE</th>

		 <?php  

		 if($taxpercentage>0)//when the tax percentage is is 0 will not show these two columns

		 {

		 ?>

		<th >AMOUNT</th>

        <th >S.TAX <?php echo $taxpercentage;?>%</th>

        <?php

		}

		?>

		<th >TOTAL VALUE</th>

	</tr>

	<?php

		for($i=0;$i<count($customerinfo);$i++)

		{

			$pksaleid			=	$customerinfo[$i]['pksaleid'];

			$amount				=	$customerinfo[$i]['amount'];

			$saleprice			=	$customerinfo[$i]['saleprice'];

			$quantity			=	$customerinfo[$i]['quantity'];

			$customdescription	=	$customerinfo[$i]['customdescription'];

			if($customdescription!='')

			{

				$itemdescription	=	$customdescription;

			}

			else

			{

				$itemdescription	=	$customerinfo[$i]['itemdescription'];

			}

			$type				=	$customerinfo[$i]['type'];

			$reportdate			=	$customerinfo[$i]['trdatetime'];

			$taxable			=	$customerinfo[$i]['taxable'];

			if($taxable!=1)

			{

				$tax=$taxpercentage/100*$amount;

			}

			else

			{

				$tax=0;

			}

		?>

		<tr>

			<td style="text-transform:capitalize"><?php echo ucfirst(strtolower($itemdescription));?></td>

			<td align="right"><?php echo $quantity;?></td>

			<td align="right"><?php echo number_format($saleprice,2);?></td>

			<?php  

		 if($taxpercentage>0)//when the tax percentage is is 0 will not show these two columns

		 {

		 ?>

            <td align="right"><?php  echo number_format($amount,2);?></td>

            <td align="right"><?php echo number_format($tax,2);?></td>

          <?php

		  }

		  ?> 

		    <td align="right"><?php echo number_format($amount+$tax,2);?></td>

		</tr>

		<?php

			$totalamount+=$amount;

			$totaltax+=$tax;

		}//end of for

		$totalamount2	=	$totalamount;

		$totaltax2		=	$totaltax;

		$gtotal2		=	$totalamount+$totaltax;

	?>

    <tr>

		

			<td colspan="3" align="right">Total</td>

			<td align="right"><b><?php  echo number_format($totalamount,2);?></b></td>

			 <?php

			 if($taxpercentage>0)//when the tax percentage is is 0 will not show these two columns

			 {

			 ?>

			 <td align="right"><b><?php echo number_format($totaltax,2);?></b></td>

             <td align="right"><b><?php echo number_format($totalamount+$totaltax,2);?></b></td>

			<?php

			}

			?>		

	  </tr>

	</table>

    <div style="page-break-after:always;"></div>

    <br />

    <div align="center" style="font-size:18px;"><?php echo $invoicetype;?></div>

        <div align="center">(Office Copy)</div>

	<div id="serial" style="float:right;margin-top:70;font-size:14px;">

	<br>

		<b>Serial No: <?php echo $serialno;?></b>

		<br>

		<b>Date: <?php echo $invdate;?></b><br><br>

		<strong>Sales Tax Registration No: </strong><br>07-01-2100-082-55

		<br>

	<strong>NTN: 0344289-6</strong></div>

	<div style="width:8in;padding:0px;margin-left:-200px;font-size:17px;" align="center">

	<img src="images/esajeecologo.jpg" width="286" height="77">

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

		Customer NTN: 

		<?php if($ntn!='0' || $ntn!=''){echo $ntn;}else{?>__________<?php } ?></span>

	</div><br /></span>

	<table class="simplereport" width="100%">

	<tr>

		<th width="35%">DESCRIPTION</th>

		<th>QTY</th>

		<th>RATE</th>

		 <?php  

		 if($taxpercentage>0)//when the tax percentage is is 0 will not show these two columns

		 {

		 ?>

		<th >AMOUNT</th>

        <th >S.TAX <?php echo $taxpercentage;?>%</th>

        <?php

		}

		?>

		<th >TOTAL VALUE</th>

	</tr>

	<?php

		for($i=0;$i<count($customerinfo);$i++)

		{

			$pksaleid			=	$customerinfo[$i]['pksaleid'];

			$amount				=	$customerinfo[$i]['amount'];

			$saleprice			=	$customerinfo[$i]['saleprice'];

			$quantity			=	$customerinfo[$i]['quantity'];

			$customdescription	=	$customerinfo[$i]['customdescription'];

			if($customdescription!='')

			{

				$itemdescription	=	$customdescription;

			}

			else

			{

				$itemdescription	=	$customerinfo[$i]['itemdescription'];

			}

			$type				=	$customerinfo[$i]['type'];

			$reportdate			=	$customerinfo[$i]['trdatetime'];

			$taxable			=	$customerinfo[$i]['taxable'];

			if($taxable!=1)

			{

				$tax=$taxpercentage/100*$amount;

			}

			else

			{

				$tax=0;

			}

		?>

		<tr>

			<td style="text-transform:capitalize"><?php echo ucfirst(strtolower($itemdescription));?></td>

			<td align="right"><?php echo $quantity;?></td>

			<td align="right"><?php echo number_format($saleprice,2);?></td>

			<?php  

		 if($taxpercentage>0)//when the tax percentage is is 0 will not show these two columns

		 {

		 ?>

            <td align="right"><?php  echo number_format($amount,2);?></td>

            <td align="right"><?php echo number_format($tax,2);?></td>

          <?php

		  }

		  ?> 

		    <td align="right"><?php echo number_format($amount+$tax,2);?></td>

		</tr>

		<?php

			$totalamount+=$amount;

			$totaltax+=$tax;

		}//end of for

	?>

    <tr>

		

			<td colspan="3" align="right">Total</td>

			<td align="right"><b><?php  echo number_format($totalamount2,2);?></b></td>

			 <?php

			 if($taxpercentage>0)//when the tax percentage is is 0 will not show these two columns

			 {

			 ?>

			 <td align="right"><b><?php echo number_format($totaltax2,2);?></b></td>

             <td align="right"><b><?php echo number_format($gtotal2,2);?></b></td>

			<?php

			}

			?>		

	  </tr>

	</table>

    <br>

	</body>

    </html>

    <script language="javascript">

	if(location.href.indexOf('reload')!=-1)

	{

		window.print();

	}

	</script>