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
list($name,$amount,$description)	=	explode(":",$_REQUEST['text']);
if($amount=='')
{
	$id		=	$_GET['text'];
	// Removed fkclosingid = '$closingsession' AND by Yasir - 21-07-11
	
	$updatebill_query 	=  "Update $dbname_detail.accountpayment
							   SET billnum = billnum + 1
					         WHERE pkaccountpaymentid = '$id'";
					   
    $updatebill_array	=	$AdminDAO->queryresult($updatebill_query);	
	$query 	= 	"SELECT 
						id as pkaccountheadid,
						pkaccountpaymentid,
						description,
						title as accounttitle,
						round(sum(amount),2) as amount,
						from_unixtime(MAX(paymentdate),'%d-%m-%y  %h:%i:%s') as paymentdate,
						paymentmethod ,
						billnum
					FROM  
						addressbook,
						$dbname_detail.account 
						LEFT JOIN $dbname_detail.accountpayment ON (fkaccountid = id)
				  WHERE	
						pkaddressbookid = fkemployeeid AND						
						pkaccountpaymentid='$id'
					GROUP BY id
  "; //AND					countername	=	'$countername' 
  	$payoutarray	=	$AdminDAO->queryresult($query);
	$name			=	$payoutarray[0]['accounttitle'];
	$amount			=	$payoutarray[0]['amount'];
	$description	=	$payoutarray[0]['description'];
	$paymentmethod	=	$payoutarray[0]['paymentmethod'];
	$billnum		=	$payoutarray[0]['billnum'];
}

if ($billnum > 1){ ?>
	<div style="clear:both;font-size:12px;text-align:center;font-weight:bold;text-transform:uppercase; background-color:#000;color:#FFF;margin-top:5px;">
    <?php echo "Duplicate Copy ($billnum)";?>
  </div><br />  
<?php
}

?>
<table class="simple" width="275" align="left">
	<tr align="right">
    	<td>
        	Paid To
        </td>
        <td>
        	<?php
				echo "$name";
			?>
        </td>
    </tr>
    <tr align="right">
    <td>
    	Description
    </td>
    <td>
    	<?php
			echo stripslashes($description);
		?>
    </td>
    </tr>
	<tr align="right">
    	<td>
        	Amount Paid
        </td>
        <td>
        	<?php
				echo "$amount";
			?>
        </td>
    </tr>
	<tr align="right">
    	<td>
        	Payment Method
        </td>
        <td>
        	<?php
				if($paymentmethod=='c')
				{
					print"Cash";
				}
				else
				{
					print"Cheque";
				}
			?>
        </td>
    </tr>
</table>

<div align="center" style="clear:both; float:left;margin-bottom:5px;">
<?php echo $billfooter; ?><br /><br />
<?php echo date("h:i:s d:m:Y"); ?>
<?php 
	$_SESSION['payout'] = "";
?>
</div>
<div align="center">
<img src="pay.png" />
</div>
</div>
<script language="javascript">
	window.print();
	window.close();
</script>