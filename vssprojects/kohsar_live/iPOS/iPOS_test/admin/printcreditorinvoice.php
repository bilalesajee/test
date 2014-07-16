<html>
    <head>
    <title>Report</title>
    <link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
    <style type="text/css">
<!--
.style1 {
	font-size: 16px;
	font-weight: bold;
}
.style2 {font-size: 18px}
.style4 {font-size: 14px; font-weight: bold; }
-->
    </style>
    </head>
    <body>
<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['id'];
$sql	=	"SELECT
					pksaleid,
					sd.saleprice,
					sd.quantity,
					b.itemdescription,
					FROM_UNIXTIME(s.datetime,'%d-%m-%Y') trdatetime,
					FROM_UNIXTIME(invoicedate,'%d-%m-%Y') invoicedate,
					sd.taxable,
					sd.taxamount,
					s.fkaccountid,
					creditinvoices.serialno serial
					FROM
						$dbname_detail.sale s,
						$dbname_detail.saledetail sd,
						$dbname_detail.creditinvoices,
						$dbname_detail.account c,
						main.barcode b,
						$dbname_detail.stock st
					WHERE
						s.fkaccountid=c.id AND 
						s.status=1 AND  
						fkcreditinvoiceid='$id' AND
						fkcreditinvoiceid=pkcreditinvoiceid AND
						sd.fksaleid=pksaleid AND 
						sd.fkstockid=st.pkstockid AND 
						b.pkbarcodeid=st.fkbarcodeid AND 
						st.fkbarcodeid<>62007
						GROUP BY sd.pksaledetailid
						order by s.pksaleid ASC
					";
			$customerinfo	=	$AdminDAO->queryresult($sql);
			$serialno		=	$customerinfo[0]['serial'];
			$cid			=	$customerinfo[0]['fkcustomerid'];
			$dateoninvoice	=	$customerinfo[0]['invoicedate'];
			$customersql	=	"SELECT 
									CONCAT(firstname,' ',lastname) as customername,
									title,taxnumber, ntn
								FROM 
									$dbname_detail.account,$dbname_detail.addressbook
								WHERE
									fkaddressbookid=pkaddressbookid AND
									id='$cid'";
			$custarr			=	$AdminDAO->queryresult($customersql);
			$customername		=	$custarr[0]['customername'];
			$companyname		=	$custarr[0]['title'];
			$taxno				=	$custarr[0]['taxnumber'];
			$ntn				=	$custarr[0]['ntn'];
?>
			<div align="center">
			<span class="style2">Sales Tax Invoice</span>
			<br> 
		 	 <span id='copytd'>(Customer Copy)</span></th>
			</div>
	<span id="serial" style="position:absolute; margin-top:40px; margin-left:400px">
	<br>
		<b>Serial No: <?php echo $serialno;?></b>
		<br>
        <br>
        <b>Date:</b> <?php echo $dateoninvoice;?>
		<br>
		<strong>Sales Tax Registration No: </strong><br>07-01-2100-082-55
		<br>
	<strong>NTN No: 01-01-2754403-6    </strong></span>
	<div style="width:8in;padding:0px;margin-left:-200px;font-size:17px;" align="center">
	<img src="../images/esajeelogo.jpg" width="286" height="77">
	<br />
	<span style="font-size:11px; line-height:15px">
	
	<span class="style1">Think globally shop locally</span><br />
    <span class="style4">Importers & General Order Suppliers </span><strong><br />
    </strong>Shop # 9, Kohsar Market, F-6/3, Islamabad<br />
    Phone: 051-2872041, Fax: 051-2279919<br />
    Email: esajee@esajee.com <br />
    Website: www.esajee.com<br />
	</span>	</div>
	<div>
	<br>
		Buyer's Name: <b><span style="text-transform:uppercase"><?php echo $companyname;?></span></b>
		<br>
		Buyer's NTN:<b><?php if($taxno!='0'){echo $taxno;}else{?>__________<?php } ?></b>
		<br>
	</div>
    <table class="simple">
	<tr>
	  	<th>SaleID</th>
		<th>itemdescription</th>
		<th>Quantity</th>
		<th>Sale Price</th>
		<th>Amount</th>
        <th>S.Tax</th>
		<th>Total Value</th>
	</tr>
	<?php
		for($i=0;$i<count($customerinfo);$i++)
		{
			$pksaleid		=	$customerinfo[$i]['pksaleid'];
			$amount			=	$customerinfo[$i]['quantity']*$customerinfo[$i]['saleprice'];
			$saleprice		=	$customerinfo[$i]['saleprice'];
			$quantity		=	$customerinfo[$i]['quantity'];
			$itemdescription=	$customerinfo[$i]['itemdescription'];
			$type			=	$customerinfo[$i]['type'];
			$reportdate		=	$customerinfo[$i]['trdatetime'];
			$taxable		=	$customerinfo[$i]['taxable'];
			$taxamount		=	$customerinfo[$i]['taxamount'];
	?>
		<tr>
		 	<td style="text-transform:capitalize"><?php echo $pksaleid;?></td>
			<td style="text-transform:capitalize"><?php echo ucfirst(strtolower($itemdescription));?></td>
			<td align="right"><?php echo $quantity;?></td>
			<td align="right"><?php echo number_format($saleprice,2);?></td>
            <td align="right"><?php  echo number_format($amount,2);?></td>
            <td align="right"><?php echo number_format($taxamount,2);?></td>
		    <td align="right"><?php echo number_format($amount+$taxamount,2);?></td>
		</tr>
		<?php
		$totalamount+=$amount;
		$totaltax+=$taxamount;
		}//end of for
	?>
    	<tr>
			<td colspan="4" align="right"><b>Grand Total:</b></td>
			<td align="right"><b><?php  echo number_format($totalamount,2);?></b></td>
			<td align="right"><b><?php echo number_format($totaltax,2);?></b></td>
			<td align="right"><b><?php echo number_format($totalamount+$totaltax,2);?></b></td>
	  	</tr>
	</table>
</body>
</html>