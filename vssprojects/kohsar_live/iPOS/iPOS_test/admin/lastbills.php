<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
<table>
<?php
include_once("../includes/security/adminsecurity.php");
include_once("../includes/classes/bill.php");
error_reporting(7);
global $AdminDAO,$Bill;
$closingid		=	$_GET['id'];
$Bill			=	new Bill($AdminDAO);
// fetching last 10 bills from the closing
$lastbills		=	$AdminDAO->getrows("$dbname_detail.sale","pksaleid","fkclosingid='$closingid' ORDER BY pksaleid DESC LIMIT 0,10");
/*echo "<pre>";
print_r($lastbills);
echo "</pre>";*/
$xxj	=	0;
for($xpp=0;$xpp<sizeof($lastbills);$xpp++)
{
	$saleid			=	$lastbills[$xpp]['pksaleid'];
	$printnumbers	=	$AdminDAO->getrows("$dbname_detail.sale","printid","pksaleid='$saleid'");
	$printnumber	=	$printnumbers[0]['printid'];
	$userid			=	$_SESSION['addressbookid'];
	// getting bill data
	$billdetails	=	$Bill->billdetails($saleid);
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
	$billamount		=	$Bill->billamount($saleid);
	$billamt		=	explode("_",$billamount);
	$casht			=	$billamt[0];
	$creditct		=	$billamt[1];
	$fcurrencyt		=	$billamt[2];
	$chequet		=	$billamt[3];
	$tenderedamount	=	$casht+$creditct+$fcurrencyt+$chequet;
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
	$total			=	$totalbillcost - $globaldiscount;
	$totalitems		=	$Bill->getitemstotal($saleid);
	$totalqty		=	$Bill->getqtytotal($saleid);
	$countername	=	$Bill->countername($saleid);
	$saledetails	=	$Bill->getsaledetails($saleid);
	// inserting bill data
	$billfields		=	array('fksaleid','cash','cc','fc','ch','cr','ptime');
	$billdata		=	array($saleid,$cash,$creditcard,$fcurrency,$cheque,$credit,time());
	$AdminDAO->insertrow("$dbname_detail.bill",$billfields,$billdata);
	// counting the previous bills for bill copy calculation
	$billcount		=	$Bill->totalbills($saleid,'1');
	$customersid	=	$AdminDAO->getrows("$dbname_detail.sale","fkaccountid","pksaleid='$saleid'");
	$customerid		=	$customersid[0]['fkaccountid'];
	$query 	= 	"SELECT 
		id pkcustomerid,
			CONCAT(firstname,', ',lastname) as name,
			CONCAT(address1,', ',address2) as address,
			email,
			title companyname,
			CONCAT(phone ,', ',mobile) as phone,
			nic,
			round(
			 (SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as am FROM $dbname_detail.payments ,$dbname_detail.sale s1 WHERE s1.fkaccountid =  id AND pksaleid = fksaleid AND paymenttype <> 'c'),2) as paid
			,
			round((SELECT sum(globaldiscount) FROM $dbname_detail.sale s1 WHERE s1.fkaccountid =  id),2) as discount
			,
			round((SELECT sum(saleprice * quantity)-(SELECT SUM(globaldiscount) FROM $dbname_detail.sale WHERE s1.fkaccountid=id) as subtotal
					FROM $dbname_detail.saledetail,$dbname_detail.sale s1 WHERE s1.fkaccountid =  id AND pksaleid = fksaleid)-(  	SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as am FROM $dbname_detail.payments ,$dbname_detail.sale s1 WHERE s1.fkaccountid =  id AND pksaleid = fksaleid AND paymentmethod <> 'c'),2)  as pending,
			(SELECT round(SUM(saleprice*quantity),2) as Total FROM $dbname_detail.saledetail sdt,$dbname_detail.sale st2 WHERE st2.fkaccountid =  id AND pksaleid = fksaleid) AS total,pksaleid
	
	FROM
		$dbname_detail.account c LEFT JOIN $dbname_detail.addressbook  ON (c.fkaddressbookid = pkaddressbookid)
		  LEFT JOIN $dbname_detail.sale s2 ON (fkaccountid = id)
	WHERE
			id='$customerid' AND
			isdeleted <> 1  
	GROUP BY id
						";
	$queryresults		=	$AdminDAO->queryresult($query);
	$customerbalance	=	$queryresults[0]['pending'];
	$customername		=	$queryresults[0]['name'];
	$total				=	$queryresults[0]['total'];
	$discount			=	$queryresults[0]['discount'];
	$totalpaid			=	floor($queryresults[0]['paid']);
	if($xxj==0)
		echo "<tr>";
	?>
    <td style="border:1px solid #FFF;" valign="top">
    <div style="width:3.0in; margin-left:0px; margin-right:auto;font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif;" align="left">
    <div style="width:2.6in;padding:0px;font-size:17px;" align="center">
    <img src="../images/esajeelogo.jpg" width="197" height="58">
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
    <?php 
    if($billcount>1)
    {
    ?>
    <div style="clear:both;font-size:12px;text-align:center;font-weight:bold;text-transform:uppercase; background-color:#000;color:#FFF;margin-top:5px;">
        <?php echo "Duplicate Copy ($billcount)";?>
      </div><br />
    <?php
    }
    ?>
	<table class="simple" width="275" align="left">
	<tr>
	<th>Item</th>
	<th>Qty</th>
	<th>Unit Price</th>
	<th>Amt</th>
	</tr>
	<?php
	for($i=0;$i<sizeof($saledetails);$i++)
	{
		$itemname	=	$Bill->getsaleproduct($saledetails[$i]['fkstockid']);
		$boxsize	=	$saledetails[$i]['boxsize'];
		$quantity	=	$saledetails[$i]['quantity'];
		if($quantity==0)//-ive and +ives are equal
		{
			continue;
		}
	?>
		<tr>
		<td><?php echo $itemname; ?></td>
		<td><?php if($boxsize>0){echo $quantity.'X'.$boxsize;}else{echo $quantity;} ?></td>
		<td><?php echo numbers($saledetails[$i]['saleprice']); ?></td>
		<td align="right"><?php echo numbers($saledetails[$i]['saleprice']*$saledetails[$i]['quantity']); ?></td>
		</tr>
	<?php
		$totalprice		+=	$saledetails[$i]['quantity']*$saledetails[$i]['saleprice'];
	}
	// calculating credit amount
	$credit			=	$totalprice-$totalbillcost-$globaldiscount;
	// displaying returned items
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
		$subtotdis	=	$totalprice+$totalprice2;
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
		<td><?php $grandtotal	=	$totalprice-($tenderedamount+$globaldiscount-$adjustment);
			if($totalprice3)
			{
				$grandtotal	=	$totalprice3-$totalbillcost;
			}
			echo numbers($grandtotal); ?> </td>
	</tr>
	<?php 
	if($customername)
	{
	?>
	<tr align="right">
		<td colspan="3">Previous Balance</td>
		<td><?php 
		$bal=$total-$totalpaid-$grandtotal;//added by riz
		echo numbers($bal); 
		//echo numbers($customerbalance-$grandtotal); 
		?> </td>
	</tr>
	<tr align="right">
		<td colspan="3"><?php echo $customername."'s";?> Total Balance</td>
		<td>
		<?php 
			
			$rem=$total-$discount-$totalpaid;//added by riz
			echo numbers($rem);
		//echo numbers($customerbalance); 
		?> </td>
	</tr>
	<?php
	}
	?>
	</table>
	<div align="center"><?php echo $billtime; ?></div>
    </td>
<?php
	$xxj++;
	if($xxj==3)
	{
		$xxj	=	0;
		echo "</tr>";
	}
}
?>
</table>