<?php

include_once("includes/security/adminsecurity.php");

include_once("includes/classes/bill.php");

include_once("includes/bc/barcode.php");
include_once("surl.php");
global $AdminDAO,$Bill;

$Bill			=	new Bill($AdminDAO);

// starting to print the bill

$saleid			=	$_REQUEST['tempsaleid'];

if($saleid	== "")

{

	//for duplicate prints

	$saleid	=	$_GET['saleid'];

}

genBarCode($saleid,'bc.png');

$printnumbers	=	$AdminDAO->getrows("$dbname_detail.sale","printid,fkuserid,status","pksaleid='$saleid'");

$printnumber	=	$printnumbers[0]['printid'];

$userid			=	$printnumbers[0]['fkuserid'];

$status			= 	$printnumbers[0]['status']; // $status added by Yasir -- 05-07-11



// counting the previous bills for bill copy calculation

$billcount		=	$Bill->totalbills($saleid,$status); // $status added by Yasir -- 05-07-11

//*****************************************************

if(!isset($userid) || $userid=='' || $userid==0)

{

	$userid			=	$_SESSION['addressbookid'];

}

// getting bill data 

if ($billcount > 0){ // added by Yasir -- 07-07-11

	$billdetails	=	$Bill->duplicatebilldetails($saleid);

} else {

	$billdetails	=	$Bill->billdetails($saleid); 

}

$billdetails	=	explode("_",$billdetails);

$cash			=	$billdetails[0];

$creditcard		=	$billdetails[1];

$cashflag		=	0;

if($creditcard)

{

	$cashflag	=	1;

}

$fcurrency		=	$billdetails[2];

if($fcurrency)

{

	$cashflag	=	1;

}

$cheque			=	$billdetails[3];

if($cheque)

{

	$cashflag	=	1;

}

$totalbillcost	=	$cash+$creditcard+$fcurrency+$cheque;

// added by Yasir -- 07-07-11

if ($billcount > 0){

	$billamount		=	$Bill->duplicatebillamount($saleid);

	$billamt		=	explode("_",$billamount);

	$casht			=	$billamt[0];

	$creditct		=	$billamt[1];

	$fcurrencyt		=	$billamt[2];

	$chequet		=	$billamt[3];	

	$tenderedamount	=	$casht+$creditct+$fcurrencyt+$chequet;

} else {

	$billamount		=	$Bill->billamount($saleid);

	$billamt		=	explode("_",$billamount);

	$casht			=	$billamt[0];

	$creditct		=	$billamt[1];

	$fcurrencyt		=	$billamt[2];

	$chequet		=	$billamt[3];

	$adjt			=	$billamt[4]; // added by Yasir -- 08-07-11

	$tenderedamount	=	$casht+$creditct+$fcurrencyt+$chequet+$adjt; // added by Yasir -- 08-07-11 +$adjt

}



$tenderedamount	=	$tenderedamount;

$totalbillcost	=	$totalbillcost;

// this is the place to generate bill and fetch relevant data for the bill

$storeinfo		=	$Bill->getsalestore($saleid);

$address1		=	$storeinfo[0]['storeaddress'];

$city			=	$storeinfo[0]['cityname'];

$country		=	$storeinfo[0]['countryname'];

$zipcode		=	$storeinfo[0]['zipcode'];

$fulladdress	=	$address1." ".$city;

$phone			=	$storeinfo[0]['storephonenumber'];

$fax			=	$storeinfo[0]['fax'];

$email			=	$storeinfo[0]['email'];

$billfooter		=	$storeinfo[0]['billfooter'];

$billtime		=	$Bill->getsaletime($saleid);

$gdisc			=	$Bill->globaldiscount($saleid);

$gdiscandadj	=	explode("_",$gdisc);

$globaldiscount	=	$gdiscandadj[0];

$adjustment		=	$gdiscandadj[1];

//

$total			=	$totalbillcost - $globaldiscount;

$totalitems		=	$Bill->getitemstotal($saleid);

$totalqty		=	$Bill->getqtytotal($saleid);

$countername	=	$Bill->countername($saleid);

$saledetails	=	$Bill->getsaledetails($saleid);

// inserting bill data

$billfields		=	array('fksaleid','cash','cc','fc','ch','cr','ptime','status');

$billdata		=	array($saleid,$cash,$creditcard,$fcurrency,$cheque,$credit,time(),$status);

$AdminDAO->insertrow("$dbname_detail.bill",$billfields,$billdata);

// added by Yasir -- 07-07-11

$billcount 		= 	 $billcount + 1;

//

$customersid	=	$AdminDAO->getrows("$dbname_detail.sale","fkaccountid as fkcustomerid,customerbalance","pksaleid='$saleid'");

$customerid		=	$customersid[0]['fkcustomerid'];

$customerbalance_ =	$customersid[0]['customerbalance']; // $customerbalance_ added by Yasir -- 05-07-11

$querycust = 	"SELECT 

		 pkcustomerid,

        CONCAT(firstname,', ',lastname) as name,

        CONCAT(address1,', ',address2) as address,

        email,        

        CONCAT(phone ,', ',mobile) as phone,

        nic,
		ctype

		FROM

		customer

		WHERE

		pkcustomerid='$customerid' AND

		isdeleted <> 1  

		";

		$queryresultscust		=	$AdminDAO->queryresult($querycust);

		$customername		=	$queryresultscust[0]['name'];
		$ctype		=	$queryresultscust[0]['ctype'];

// to get customer total sales and payments -- Added By Yasir 27-06-11

// delete the block after testing

if ($billcount > 1 && $customername != '') {



    $query_salespayment = "SELECT

							 (SELECT SUM(amount)

							   FROM $dbname_detail.sale, $dbname_detail.payments

							  WHERE pksaleid = fksaleid								

								AND pksaleid = '$saleid'

								AND paymenttype <> 'c') as totalpayments,								

							    totalamount						

						FROM $dbname_detail.sale												

							  WHERE pksaleid = '$saleid'							    								

							";

							

		$salespayment	=	$AdminDAO->queryresult($query_salespayment);

	    $balance	 	=	$salespayment[0]['totalamount'] - $salespayment[0]['totalpayments'];

}

////
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


?>

<script src="includes/js/shortcut.js"></script>

<script language="javascript">

shortcut.add("End",function() 

{

	//fnchotelmode();

	window.close();

	return false;

});

</script>

<link rel="stylesheet" type="text/css" href="includes/css/style.css" />

<body onLoad="window.print(); window.close();">

<div style="width:3.0in; margin-left:0px; margin-right:auto;font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif;" align="left">

<div style="width:2.6in;padding:0px;font-size:17px;" align="center">

<img src="images/esajeelogo.jpg" width="197" height="58">

<br />

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

Cashier: <?php echo $userid;?>

</div>

<div style="width:1.3in; padding:2px; float:left; margin-top:5px;" align="center">

Transaction:<?php echo " <b>$printnumber</b> ($saleid)"; ?>

</div>

<div style="width:1.3in; padding:2px; float:right; margin-top:5px;" align="center">

Items:

<?php echo $totalitems." ($totalqty)"; ?>

</div>
<?php if($ctype==1){ ?>
	<div style="width:1.3in; padding:2px; float:left; margin-top:5px;" align="center">
PO No:<b><?php echo $customerinfo_ponum[0][ponum]; ?></b>
</div>
<?php	}

if($billcount>1)

{

?>

<div style="clear:both;font-size:12px;text-align:center;font-weight:bold;text-transform:uppercase; background-color:#000;color:#FFF;margin-top:5px;">

    <?php echo "Duplicate Copy ($billcount)";?>

  </div><br />

<?php

}

?>

<table class="simple" width="275" align="left" style="margin-left:5px;" >

<tr>

<th>Item</th>

<th>Qty</th>

<th>Unit Price</th>

<th>Amount</th>

</tr>

<?php

$taxesandcharges	=	array("Sales Tax","Exempt Tax","Delivery Charges");

$taxitemsarr		=	array();

$starflag			=	0;

for($i=0;$i<sizeof($saledetails);$i++)

{

	

	$itemname	=	$Bill->getsaleproduct($saledetails[$i]['fkstockid']);

	if($saledetails[$i]['fkdiscountid']!=0)

	{

		$starflag	=	1;

		$itemname	=	"* ".$itemname;

	}

	$boxsize	=	$saledetails[$i]['boxsize'];

	$quantity	=	$saledetails[$i]['quantity'];

	if($quantity==0)//-ive and +ives are equal

	{

		continue;

	}

	if(in_array($itemname,$taxesandcharges))

	{

		$taxitems['itemname']=$itemname;

		$taxitems['saleprice']=$saledetails[$i]['saleprice'];

		

		$taxsaleprice+=$saledetails[$i]['saleprice'];

		array_push($taxitemsarr,$taxitems);

		continue;

	}

?>

    <tr>

    <td><?php echo str_replace(',',', ',ucfirst(strtolower($itemname))); ?></td>

    <td><?php if($boxsize>0){echo $quantity.'X'.$boxsize;}else{echo $quantity;} ?></td>

    <td><?php echo numbers($saledetails[$i]['saleprice']); ?></td>

    <td align="right"><?php if($saledetails[$i]['fkdiscountid']==0) {echo numbers($saledetails[$i]['saleprice']*$saledetails[$i]['quantity']);} else {echo 0.00;} ?></td>

    </tr>

<?php

	// condition for discount

	if($saledetails[$i]['fkdiscountid']==0)

	{

		$totalprice		+=	$saledetails[$i]['quantity']*$saledetails[$i]['saleprice'];

	}

}

// calculating credit amount

$credit			=	$totalprice-$totalbillcost-$globaldiscount;

//displaying returned items

$saledetails2	=	$Bill->getsaledetails2($saleid);

for($x=0;$x<sizeof($saledetails2);$x++)

{

	$itemname	=	$Bill->getsaleproduct($saledetails2[$x]['fkstockid']);

	$boxsize	=	$saledetails2[$x]['boxsize'];

	$quantity	=	$saledetails2[$x]['qty']."R";

	?>

    <tr>

    <td><?php echo $itemname; ?></td>

    <td><?php if($boxsize>0){echo $quantity.'X'.$boxsize;}else{echo $quantity;} ?></td>

    <td><?php echo numbers($saledetails2[$x]['saleprice']); ?></td>

    <td align="right"><?php echo numbers($saledetails2[$x]['saleprice']*$saledetails2[$x]['qty']); ?></td>

    </tr>

    <?php

	$totalprice2		+=	$saledetails2[$x]['qty']*$saledetails2[$x]['saleprice'];

}

if($totalprice2)

{

	$totalprice3	=	$totalprice+($totalprice2);

	?>

<tr align="right">

	<td colspan="3">Total</td>

	<td><?php echo numbers($totalprice);?></td>

</tr>

<?php

}

if($totalprice2)

{

	?>

<tr align="right">

	<td colspan="3">Adj & Returns</td>

	<td><?php echo numbers($totalprice2); ?> </td>

</tr>

<?php

}

?>

<tr align="right">

	<td colspan="3">Sub Total</td>

	<td><?php 

	echo numbers($totalprice+$totalprice2); ?> </td>

</tr>

 <?php

 

 //Shows the Taxes and charges section which are actualy items 

if(sizeof($taxitems)>0)

{

	//$taxsaleprice	=	$taxitems['saleprice'];

	for($t=0;$t<sizeof($taxitemsarr);$t++)

	{

?>

<tr align="right">

	<td colspan="3"><?php echo $taxitemsarr[$t]['itemname'];?></td>

	<td><?php echo numbers($taxitemsarr[$t]['saleprice']);?></td>

</tr>

<?php

	}//for

}//if

if($globaldiscount)

{

?>

<tr align="right">

	<td colspan="3">Discount</td>

	<td><?php echo numbers($globaldiscount); ?> </td>

</tr>

	<tr align="right">

	<td colspan="3">To be Paid</td>

	<td>

	<?php 

	//chnaged By riz 14-12-2009 was not showing correct amount in case of discount

	$subtotdis	=	$totalprice+$totalprice2+$taxsaleprice;

	echo numbers($subtotdis-$globaldiscount);

	//echo numbers($totalprice-$globaldiscount);

	 ?> </td>

</tr>



<?php

}

if($cash && $cashflag!=0)

{

?>

<tr align="right">

	<td colspan="3">Cash Collected</td>

	<td><?php echo numbers($cash);?></td>

</tr>

<?php

}

if($creditcard)

{

	//when: 05/11/09

	$ccdetails	=	$Bill->ccdetails($saleid);

	for($c=0;$c<sizeof($ccdetails);$c++)

	{

	?>

	<tr align="right">

		<td colspan="3">CC Number</td>

		<td><?php echo $ccdetails[$c]['ccno'];?></td>	

	</tr>

	<tr align="right">

		<td colspan="3">CC Amount</td>

		<td><?php echo numbers($ccdetails[$c]['amount']);?></td>	

	</tr>

	<?php

	}

	?>

<tr align="right">

	<td colspan="3">Credit Card Total</td>

	<td><?php echo numbers($creditcard);?></td>

</tr>

<?php

}

if($fcurrency)

{

	$fcurrency	=	$fcurrency;

?>

<tr align="right">

	<td colspan="3">Foreign Currency</td>

	<td><?php echo numbers($fcurrency);?></td>

</tr>

<?php

}

if($cheque)

{

?>

<tr align="right">

	<td colspan="3">Cheque</td>

	<td><?php echo numbers($cheque);?></td>

</tr>

<?php

}

?>

<?php 

//This is tendered amount row commented on 14-12-2009 on the instructions of hasnain sb

//Turned On by rizwan On demand of Hasnain Sb 18-1-2010

?> <tr align="right">

	<td colspan="3"><strong>Tendered</strong></td>

	<td><?php echo numbers($tenderedamount); ?> </td>

</tr>

<?php

if($adjustment)

{

?>

<tr align="right">

	<td colspan="3"><strong>Returned</strong></td>

	<td><?php echo numbers($adjustment); ?> </td>

</tr>

<?php

}

?>

<tr align="right">

	<td colspan="3"><strong>Balance</strong></td>

	<td><?php 

	  $grandtotal	=	$totalprice-($tenderedamount+$globaldiscount-$adjustment)+$taxsaleprice;

		if($totalprice3)

		{

			$grandtotal	=	$totalprice3-$totalbillcost;

		}

		echo numbers($grandtotal); ?> </td>

</tr>
<?php 
if($ctype==2)
{
?>
<tr align="right">
	<td colspan="3"><?php echo $customername."'s";?> Balance</td>
	<td>
	<?php 
/*$host_acc = '192.168.10.100';
$port_acc = 80;
$waitTimeoutInSeconds = 5;
$fp_acc = fsockopen($host_acc, $port_acc, $errCode, $errStr, $waitTimeoutInSeconds);
if ($fp_acc)
{

		$privious_customerbalance_=file_get_contents('http://192.168.10.100/accounts/customer_balance.php?customer='.$customerid);
	echo $privious_customerbalance_; 
	fclose($fp_acc);
}else{*/
	echo "Balance Not Available";
	//}
	 /*$cust_total= file_get_contents($serverUrl_total.$customerid);
     $cust_totale = json_decode($cust_total, true);
 echo $cust_total=$cust_totale['sale'] - $cust_totale['dicount'] -  $cust_totale['totalpaid'];*/
	?> </td>
</tr>
<?php
}
?>
</table>

<div id="discountdiv" style="clear:both; float:left;margin-bottom:5px;">

<?php if($starflag	==	1){	echo "* Discounted Products<br>";}?>

</div>

<div align="center" style="clear:both; float:left;margin-bottom:5px;">

<?php echo $billfooter; ?><br /><br />

<?php echo $billtime; ?>

<?php if($billcount>1){$printime	=	date('d-m-y h:i:s', time()); echo "<br /><br />Printing Time: ".$printime;}?>

</div>

<div align="center" style="clear:both;">

<img src="bc.png" />

</div>

</div>

</body>