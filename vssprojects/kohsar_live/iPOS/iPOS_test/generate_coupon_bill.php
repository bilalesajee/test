<?php
include_once("includes/security/adminsecurity.php");
include_once("includes/classes/bill.php");
global $AdminDAO;
$storesarray	=	$AdminDAO->getrows("store,city","billfooter,storeaddress,cityname"," pkstoreid = '$storeid' AND pkcityid = fkcityid");
$fulladdress	=	$storesarray[0]['storeaddress'].$storesarray[0]['cityname'];
$billfooter		=	$storesarray[0]['billfooter'];
//print_r($paymentdetails);
//-(SELECT sum(globaldiscount) FROM sale s1 WHERE s1.fkcustomerid =  pkcustomerid)
$countername=$_SESSION['countername'];
?>
<link rel="stylesheet" type="text/css" href="includes/css/style.css" />
<div style="width:3.0in; margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif;" align="left">
<div style="width:2.6in;padding:0px;font-size:17px;" align="center">
<img src="images/esajeelogo.jpg" width="150" height="50"><br />
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
<?php

	 $id		=	$_GET['id'];
	// Removed fkclosingid = '$closingsession' AND by Yasir - 21-07-11
	

					   
   
	 $query 	= 	"SELECT * from
						
						$dbname_detail.coupon_management
						where pkcouponid = '$id'
  ";
  	$couponarray	=	$AdminDAO->queryresult($query);
	$couponid			=	$couponarray[0]['couponid'];
	$amount			=	$couponarray[0]['amount'];
	$reason	=	$couponarray[0]['reason'];
	



?>
<table class="simple" width="275" align="left">
	<tr align="right">
    	<td width="127" align="left">
        	CouponID
        </td>
        <td width="136" align="left">
        	<?php
				echo $id;
			?>
        </td>
    </tr>
    <tr align="left">
    <td align="left">
    	Amount
    </td>
    <td align="left">
    	<?php
			echo $amount;
		?>
    </td>
    </tr>
	<tr align="right">
    	<td align="left">
        	Reason
        </td>
        <td align="left">
        	<?php
				echo $reason;
			?>
        </td>
    </tr>
	<!--<tr align="right">
    	<td>
        	Payment Method
        </td>
        <td>
        	<?php
				//if($paymentmethod=='c')
				//{
				//	print"Cash";
				//}
			//	else
				//{
				//	print"Cheque";
				//}
			?>
        </td>-->
    </tr>
</table>

<div align="center" style="clear:both; float:left;margin-bottom:5px;">
<?php echo $billfooter; ?><br /><br />
<?php echo date("h:i:s d:m:Y"); ?>
<?php 
	//$_SESSION['payout'] = "";
?>
</div>
<div align="center">
<img src="pay.png" />
</div>
</div>
<script language="javascript">
	window.print();
	//window.close();
</script>