<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id	=	$_GET['id'];
$query		=	"SELECT
					pkshiplistdetailsid,
					barcode,
					itemdescription,
					sd.quantity,
					sl.lastpurchaseprice,
					weight,
					GROUP_CONCAT(companyname) companyname
				FROM 
					shiplistdetails sd,shiplist sl LEFT JOIN shiplistsupplier LEFT JOIN supplier ON (fksupplierid=pksupplierid) ON (fkshiplistid=pkshiplistid)
				WHERE
					pkshiplistid	=	sd.fkshiplistid	AND
				sd.fkshipmentid		=	'$id'
				GROUP BY pkshiplistdetailsid
				";
$reportresult		=	$AdminDAO->queryresult($query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Shopping List</title>
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
<body>
<div style="width:2.6in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>Shopping List</b>
</div>
<p><a href="export.php?id=<?php echo $id;?>">Export to Excel</a><br />
</p>
<table style="font-size:12px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
  <tr>
  	<th>Sr #</th>
    <th>Item</th>
    <th>Qty</th>
    <th>Price</th>
    <th>Weight</th>
    <th>Supplier</th>
  </tr>
  <?php
  	for($i=0;$i<sizeof($reportresult);$i++)
  	{
		  //fetching records
		  $pkshiplistdetailsid			=	$reportresult[$i]['pkshiplistdetailsid'];
		  $barcode			=	$reportresult[$i]['barcode'];
		  $item				=	$reportresult[$i]['itemdescription'];
		  $quantity			=	$reportresult[$i]['quantity'];
		  $price			=	$reportresult[$i]['lastpurchaseprice'];
		  $weight			=	$reportresult[$i]['weight'];
		  $companyname		=	$reportresult[$i]['companyname'];
  ?>
  <tr>
  	<td><?php echo $i+1;?></td>
    <td><?php echo "$item <b>($barcode)</b>";?></td>
    <td><?php echo $quantity;?></td>
    <td><?php echo $price;?></td>
    <td><?php echo $weight;?></td>
    <td><?php echo $companyname;?></td>
  </tr>
  <?php
  	}// end for
  ?>
</table>
</body>
</html>