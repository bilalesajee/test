<?php
include_once("includes/security/adminsecurity.php");
include_once("includes/classes/bill.php");
include_once("surl.php");
global $AdminDAO;
$storesarray	=	$AdminDAO->getrows("store,city","billfooter,storeaddress,cityname"," pkstoreid = '$storeid' AND pkcityid = fkcityid");
$fulladdress	=	$storesarray[0]['storeaddress'];//.$storesarray[0]['cityname'];
$billfooter		=	$storesarray[0]['billfooter'];
//$customerbalance=	$_SESSION['payable'];
//$customername	=	$_SESSION['customername'];
$customerid		=	$_SESSION['customerid'];
$paymentdetails	=	$_SESSION['paymentdetails'];
//print_r($paymentdetails);
if($customerid=='')
{
	$customerid			=	$_REQUEST['customerid'];
	$amount				=	$_REQUEST['amount'];
	$chequenumber		=	$_REQUEST['chequeno'];
	$method				=	$_REQUEST['method'];
	$ccno				=	$_REQUEST['ccno'];
	$symbol				=	$_REQUEST['Currency'];
	//print_r($_POST);
	if($method=='cash')
	{
		$paymentdetails = array ("Payment Method"  => 'Cash',   "Paid" => $amount);
	}
	if($method=='cheque')
	{	
		$paymentdetails = array ("Payment Method"  => 'Cheque',   "Paid" => $amount,"Cheque Number"=>$chequenumber);
	}
	if($method=='cc')
	{	
		$paymentdetails = array ("Method"  => 'Credit Card',   "Paid" => $amount,"CC #"=>$ccno);
	}
	if($method=='fc')
	{	
		$paymentdetails = array ("Method"  => 'Foreign Currency',   "Paid" => $amount, "Currency"=>$symbol);
	}
}
 // if($_SESSION['SERVER_ACC_ONLINE']==1){
//$cust_bal = ( file_get_contents($serverUrl_.$customerid) );
$cust_total= file_get_contents($serverUrl_total.$customerid);
$cust_totale = json_decode($cust_total, true);
$cust_total=$cust_totale['sale'];
$cust_dis=$cust_totale['dicount'];
$cust_bal=$cust_totale['totalpaid'];
  //}
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
		 (SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as am FROM $dbname_detail.payments ,$dbname_detail.sale s1 WHERE s1.fkaccountid =  pkcustomerid AND pksaleid = fksaleid
          )) as paid
		,
		round((SELECT sum(globaldiscount) FROM $dbname_detail.sale s1 WHERE s1.fkaccountid =  pkcustomerid),2) as discount
		,
		round((SELECT sum(saleprice * quantity)-(SELECT SUM(globaldiscount) FROM $dbname_detail.sale WHERE s1.fkaccountid=pkcustomerid) as subtotal
                FROM $dbname_detail.saledetail,$dbname_detail.sale s1 WHERE s1.fkaccountid =  pkcustomerid AND pksaleid = fksaleid)-(  	SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as am FROM $dbname_detail.payments ,$dbname_detail.sale s1 WHERE s1.fkaccountid =  pkcustomerid AND pksaleid = fksaleid
    )
	,2)  as pending,
		(SELECT round(SUM(saleprice*quantity),2) as Total FROM $dbname_detail.saledetail sdt,$dbname_detail.sale st2 WHERE st2.fkaccountid = pkcustomerid AND pksaleid = fksaleid) AS total,pksaleid

FROM
	customer c LEFT JOIN $dbname_detail.sale s2 ON (fkaccountid = pkcustomerid)
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
$rem=$cust_total-$cust_dis-$totalpaid;
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
        <td><?php   //if($_SESSION['SERVER_ACC_ONLINE']==1){ echo numbers($cust_total);}else{echo "Balance Not Available";}
		echo numbers($cust_total);
		?></td>
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
        <td>Previous Total</td>
        <td><?php   //if($_SESSION['SERVER_ACC_ONLINE']==1){ echo $paidee= numbers($cust_total-$cust_bal-$cust_dis);}else{ echo "Balance Not Available";}
		
		echo $paidee= numbers($cust_total-$cust_bal-$cust_dis);
		?></td>
   		 </tr>
       <tr align="right">
       	<td>New Balance    
        </td>
        <td>
        	<?php
				echo numbers($paidee -$amountpaid);
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