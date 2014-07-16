<?php
include_once("includes/security/adminsecurity.php");
include_once("includes/classes/bill.php");
global $AdminDAO;
$storesarray	=	$AdminDAO->getrows("store,city","billfooter,storeaddress,cityname"," pkstoreid = '$storeid' AND pkcityid = fkcityid");
$fulladdress	=	$storesarray[0]['storeaddress'];//.$storesarray[0]['cityname'];
$billfooter		=	$storesarray[0]['billfooter'];
//$customerbalance=	$_SESSION['payable'];
//$customername	=	$_SESSION['customername'];
$customerid		=	$_SESSION['customerid'];
$paymentdetails	=	$_SESSION['paymentdetails'];
//print_r($paymentdetails);
//print_r($_POST);

$id = $_GET['collectionid'];

$updatebill_query 	=  "Update $dbname_detail.collection
						 SET billnum = billnum + 1
					   WHERE pkcollectionid = '$id'";
					   
$updatebill_array	=	$AdminDAO->queryresult($updatebill_query);					   

$collection_query	=	"SELECT fkcustomerid, amount, paymentmethod, from_unixtime(datetime,'%d-%m-%y %h:%i:%s') as paytime, billnum
					  	   FROM $dbname_detail.collection
					      WHERE pkcollectionid = '$id' ";
					 
$collection_array	=	$AdminDAO->queryresult($collection_query);

$amount 		= 	$collection_array[0]['amount'];

$meth 			= 	$collection_array[0]['paymentmethod'];

$customerid		=	$collection_array[0]['fkcustomerid']; 

$paymentdate	=	$collection_array[0]['paytime'];

$billnum	=	$collection_array[0]['billnum'];

if ($meth == 'c'){
 $method = 'Cash';
}

if ($meth == 'cc'){
 $method = 'Credit Card';
}

if ($meth == 'fc'){
 $method = 'Foreign Currency';
}

if ($meth == 'ch'){
 $method = 'Cheque';
}

$paymentdetails = array ("Payment Method"  => $method,   "Paid" => $amount);

//print_r($paymentdetails);
//-(SELECT sum(globaldiscount) FROM sale s1 WHERE s1.fkcustomerid =  pkcustomerid)
$query 	= 	"SELECT 
	pkcustomerid,
        CONCAT(firstname,', ',lastname) as name,
        CONCAT(address1,', ',address2) as address,
        email,
        companyname,
        CONCAT(phone ,', ',mobile) as phone,
        nic,
		round(
		 (SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as am FROM $dbname_detail.payments ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid
    ),2) as paid
		,
		round((SELECT sum(globaldiscount) FROM $dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid),2) as discount
		,
		round((SELECT sum(saleprice * quantity)-(SELECT SUM(globaldiscount) FROM $dbname_detail.sale WHERE s1.fkcustomerid=pkcustomerid) as subtotal
                FROM $dbname_detail.saledetail,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid)-(SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as am FROM $dbname_detail.payments ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid
    ),2)  as pending,
		(SELECT round(SUM(saleprice*quantity),2) as Total FROM $dbname_detail.saledetail sdt,$dbname_detail.sale st2 WHERE st2.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid) AS total,pksaleid

FROM
	$dbname_detail.customer c LEFT JOIN $dbname_detail.addressbook  ON (c.fkaddressbookid = pkaddressbookid)
      LEFT JOIN $dbname_detail.sale s2 ON (fkcustomerid = pkcustomerid)
WHERE
		pkcustomerid='$customerid' AND
		isdeleted <> 1  
GROUP BY pkcustomerid
					";

$customer_array		=	$AdminDAO->queryresult($query);
$customername		=	$customer_array[0]['name'];
$companyname		=	$customer_array[0]['companyname'];
$total				=	$customer_array[0]['total'];
$discount			=	$customer_array[0]['discount'];
$totalpaid			=	floor($customer_array[0]['paid']);
$remainingprice		=	ceil($customer_array[0]['pending']);
$rem=$total-$discount-$totalpaid;
?>
<link rel="stylesheet" type="text/css" href="includes/css/style.css" />
<div style="width:3.0in; margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif;" align="left">
<div style="width:2.6in;padding:0px;font-size:17px;" align="center">
<b>ESAJEE'S</b><br />
<span style="font-size:11px;font-family:'Comic Sans MS', cursive;">
<b>Think globally shop locally</b>
</span>
</div>
<div style="width:2.6in;padding:2px;margin-top:5px;" align="center"><?php echo $fulladdress; ?><br />
</div>
<div style="width:1.3in; padding:2px; float:left; margin-top:5px;" align="center">
Counter: <?php echo $countername;?>
</div>
<div style="width:1.3in; padding:2px; float:right; margin-top:5px;" align="center">
Cashier: <?php echo $empid;?>
</div>
<div style="clear:both;font-size:12px;text-align:center;font-weight:bold;text-transform:uppercase; background-color:#000;color:#FFF;margin-top:5px;">
    <?php echo "Duplicate Copy ($billnum)";?>
  </div><br />
<table class="simple" width="275" align="left">
	<tr align="right">
    	<td>
        	Customer
        </td>
        <td>
        	<?php
				echo "($customerid) $customername ,$companyname";
			?>
        </td>
    </tr>
    <tr align="right">
        <td>Total</td>
        <td><?php echo numbers($total);?></td>
    </tr>
    
    	<?php
		foreach($paymentdetails as $key => $value)
		{
			?>
			<tr align="right"><td>
        	<?php
				echo "$key";
			?>
        	</td>
        	<td>
			<?php 
				if($key == 'Paid')
				{
					echo $paid	=	numbers($value);
					$amountpaid=$value;
				}
				else
				{
					echo "$value";
				}
			?>
        </td>
        </tr>
       <?php
		}//foreach
	   ?>
	    <tr align="right">
        <td>Payment Date</td>
        <td><?php echo $paymentdate;?></td>
   		 </tr>
       <tr align="right">
       	<td>Current Balance    
        </td>
        <td>
        	<?php
				echo numbers($rem);
			?>
        </td>
       </tr>
</table>

<div align="center" style="clear:both; float:left;margin-bottom:5px;">
<?php
	echo $billfooter;
?>
<br /><br />
<?php
	echo date("h:i:s d:m:Y");
	$_SESSION['payable'] 		=	'';
	$_SESSION['customername']	=	'';
	$_SESSION['customerid']		=	'';
	$_SESSION['paymentdetails']	=	'';
?>
</div>
<div align="center">
<img src="collect.png" />
</div>
</div>
<script language="javascript">
	window.print();
	//window.close();
</script>