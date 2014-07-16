<?php
include_once("includes/security/adminsecurity.php");
include_once("includes/classes/bill.php");
include_once("includes/bc/barcode.php");
global $AdminDAO,$Bill;
$Bill			=	new Bill($AdminDAO);
// starting to print the bill
$saleid				=	$_REQUEST['tempsaleid'];
/************search paramaters***************/
$fromdate			=	$_REQUEST['fromdate'];

if($fromdate!='')
{
	$fromdatex			=	explode("-",$fromdate);
	$fromday			=	$fromdatex[0];
	$frommon			=	$fromdatex[1];
	$fromyr				=	$fromdatex[2];
	
	$fromdatex			=	mktime(0,0,0,$frommon,$fromday,$fromyr);
}		
$todate				=	$_REQUEST['todate'];
if($todate!='')
{
	$todatex				=	explode("-",$todate);
	$today				=	$todatex[0];
	$tomon				=	$todatex[1];
	$toyr				=	$todatex[2];
	$todatex				=	@mktime(23,59,59,$tomon,$today,$toyr);
}	
$customeridbill		=	$_REQUEST['customerid'];
$itembarcode		=	$_REQUEST['itembarcode'];
$productname1		=	$_REQUEST['productname1'];
$index				=	$_REQUEST['index'];
$act				=	$_REQUEST['act'];
if($act=='fresh')
{
	$_SESSION['saleidsarray']=array();
	$_SESSION['bilindex']=0;
	$_SESSION['customeridbill']=$customeridbill;
	$_SESSION['itembarcode']=$itembarcode;
	$_SESSION['productname1']=$productname1;
	$_SESSION['fromdate']=$fromdate;
	$_SESSION['todate']=$todate;
	$_SESSION['customeridbill']=$customeridbill;
}
/************End of search paramaters***************/
if($saleid	== "")
{
	//for duplicate prints
	$saleid	=	$_GET['saleid'];
}

if(empty($_SESSION['saleidsarray']))
{
	if($fromdate!='' && $todate!='')
	{
		
		$datesql="AND datetime>$fromdatex and datetime<$todatex ";
	}
	if($_SESSION['customeridbill']!='')
	{
		//$customeridbill	=	@$_SESSION['customeridbill'];
		$customersql=" AND s.fkcustomerid=$customeridbill ";
	}
	if($_SESSION['itembarcode']!='')
	{
			$itembarcode	=	$_SESSION['itembarcode'];
			$barcodesql=" AND fkstockid=pkstockid and fkbarcodeid=pkbarcodeid and barcode='$itembarcode' and s.pksaleid=fksaleid  ";
			$tables=" ,barcode, $dbname_detail.stock , $dbname_detail.saledetail";//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
	}
	   $qrysrch=" select s.pksaleid from $dbname_detail.sale s $tables where 1 $datesql $customersql  $barcodesql and status=1 order by s.pksaleid DESC";//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
		$saleidsarray	=	$AdminDAO->queryresult($qrysrch);
		//store this aarray in the session also send the current saleid index in the session
		$saleid=	$saleidsarray[0]['pksaleid'];
		$_SESSION['saleidsarray']=$saleidsarray;
		$totalbilsnow	=	sizeof($saleidsarray);
		$_SESSION['bilindex']=0;
		$_SESSION['totalbilsnow']=$totalbilsnow;
}
$totalbills	=	sizeof($_SESSION['saleidsarray']);
if($act!='fresh')
{	
	if(sizeof($_SESSION['saleidsarray'])>0 )
	{
		$saleidsarray	=	$_SESSION['saleidsarray'];
		$firstsaleid	=	$saleidsarray[0]['pksaleid'];
		$lastsaleid		=	$saleidsarray[(sizeof($_SESSION['saleidsarray'])-1)]['pksaleid'];
		$nextid			=	$saleidsarray[1]['pksaleid'];
		$previd			=	$saleidsarray[0]['pksaleid'];
		$_SESSION['bilindex']=0;
	}
}//if act !fresh	
$index			=	$_GET['index'];
if($act=='back')
{
	if($_SESSION['totalbilsnow']<2)
	{
		$_SESSION['totalbilsnow']	=	1;
		$saleid						=	$saleidsarray[$_SESSION['totalbilsnow']-1]['pksaleid'];
	}
	else
	{
		$_SESSION['totalbilsnow']	=	($_SESSION['totalbilsnow']-1);
		$saleid						=	$saleidsarray[$_SESSION['totalbilsnow']-1]['pksaleid'];
	}
}
elseif($act=='next')
{
	if($_SESSION['totalbilsnow']>=$totalbills)
	{
		$_SESSION['totalbilsnow']	=	$totalbills;
		$saleid						=	$saleidsarray[$_SESSION['totalbilsnow']-1]['pksaleid'];
	}
	else
	{
		$_SESSION['totalbilsnow']	=	($_SESSION['totalbilsnow']+1);
		$saleid						=	$saleidsarray[$_SESSION['totalbilsnow']-1]['pksaleid'];
	}
}	
?>
	<!--<script src="includes/js/shortcut.js"></script>-->
	<link href="includes/css/autocomplete.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="includes/js/common.js"></script>
	<script type="text/javascript" src="includes/autocomplete/billserachproduct.js"></script>
	<script language="javascript">
	$(function($)
	{
		$('#fromdate').mask("99-99-9999");
		$('#todate').mask("99-99-9999");
	});	
	function searchbill()
	{
		loading('Loading...');
		
		var fromdate	=	document.getElementById('fromdate').value;
		var todate		=	document.getElementById('todate').value;
		var customerid	= 	document.getElementById('customerid').value;
		var itembarcode	=	document.getElementById('itembarcode').value;
		var productname1=	document.getElementById('productname1').value;
		jQuery('#billsadvancesearch').load('billadvancesearch.php?fromdate='+fromdate+'&todate='+todate+'&customerid='+customerid+'&itembarcode='+itembarcode+'&act=fresh&productname1='+productname1);
	}
	function getbills(index,act)
	{
		//alert(act);
		jQuery('#billsadvancesearch').load('billadvancesearch.php?index='+index+'&act='+act);
	}
	/*function addresults(resid)
	{
		
			resultid	=	resid.substring(11);
			document.getElementById('res'+i).innerHTML	=	'';
		
			document.getElementById('res'+resultid).innerHTML	=	"<div id=\"results\" style=\"width:50px\">";
			document.getElementById('productname1'+resultid).focus();
	}*/
	</script>
	<table width="644" class="simplebold" id="advancebillingsearching">
		<tr>
			<td height="28" colspan="2" bgcolor="#0066FF">
				<span style="color:#FFF;font-weight:bold;font-size:14px;"> Advanced Bill Searching </span><div style="float:right"><img src="includes/images/cross.png" title="Close this window" alt="X"  onclick="cancelbillsearch();"/></div></td>
		</tr>
		<tr>
		  <td width="111"><strong>Date Range: </strong> </td>
		  <td width="403">From: 
		  <input type="text" id="fromdate" name="fromdate" value="<?php echo $_SESSION['fromdate'];?>"/> To: 
		  <input type="text" id="todate" name="todate"  value="<?php echo $_SESSION['todate'];?>"/></td>
	  </tr>
		<tr>
		  <td><strong>Customer:</strong></td>
		  <td>
			<?php
			//changed $dbname_main to $dbname_detail on line 174 by ahsan 22/02/2012
			$sql=" SELECT CONCAT( firstname,' ', lastname,' (',nic,')') as customername, pkcustomerid
			FROM $dbname_detail.customer, $dbname_detail.addressbook
			WHERE fkaddressbookid = pkaddressbookid  order by customername ASC
			
				";
						
				$customer_array	=	$AdminDAO->queryresult($sql);
				
				?>
				<select name="customerid" id="customerid" style="font-size:16px; font-weight:bold; color:#999; border:none;">
					<option value="">Select Customer</option>
				<?php
				for($a=0;$a<count($customer_array);$a++)
				{
					$customername	=	$customer_array[$a]['customername'];
					$id				=	$customer_array[$a]['pkcustomerid'];		
					//echo "$customername|$id\n";
				?>
				
					<option value="<?php echo $id;?>" <?php if($_SESSION['customeridbill']==$id){print"selected";}?>><?php echo $customername;?></option>
			   
				<?php
				} 
				?>
			</select>
			  <input type="hidden" name="customerid" id="customerid" />	  
		  </td>
	  </tr>
		<tr>
		  <td><strong>Item:</strong></td>
		  <td>
		  <input name="productname1" id="productname1" type="text" onkeyup="suggestnow(event,'1')" class="text" autocomplete="off" onfocus="this.select();" value="<?php echo $_SESSION['productname1'];?>" size="200px"/>
        <input name="itembarcode" id="itembarcode" type="hidden"/>
         
        
		 <div id="res1" class="results"></div>
        </div>
		   </td>
	  </tr>
	  <tr>
		  <td></td>		  
          <td>
          <span class="buttons" style="font-size:12px;">
			<button type="button" onclick="searchbill();" > <img src="images/tick.png" alt=""/> Search </button>
				 <?php
				 if($saleid!='')
				 {
				 ?>
				  <button type="button" name="button2" id="button2" onclick="printaleinvice(<?php echo $saleid;?>);" title="Ctrl+alt+p"> 
				  <img src="images/printer.png" alt=""/> Print </button>	
				  <?php
				  }
				  ?>
				  <button type="button" name="button2" id="button2" onclick="cancelbillsearch();" title="(End)Cancel This Section"> <img src="images/cross.png" alt=""/> Cancel </button>	  
             </span>     
                  </td>
	  </tr>
	  <?php
	  if(!empty($_SESSION['saleidsarray']))
	  {
	  ?>
	  <tr>
		<td colspan="2">
			<div style="float:left;font-weight:bold;font-size:18px;vertical-align:baseline;">Total Bills: 
			<?php echo ($_SESSION['totalbilsnow']);?>&nbsp;of&nbsp;&gt;&gt;<?php echo sizeof($_SESSION['saleidsarray']);?>
			
			</div>
			<a href="javascript:getbills(<?php echo $_SESSION['totalbilsnow'];?>,'back');"><img src="images/back.gif" border="0" alt="Back" /></a> 
			<a href="javascript:getbills(<?php echo $_SESSION['totalbilsnow'];?>,'next');"><img src="images/forward.gif" border="0" alt="Forward" /></a> 
		</td>
	  </tr>
	  <?php
	  }
	  else
	  {
	  ?>
	  	<tr>
			<td  colspan="2" align="center" style="color:#FF0000;"><h2>No bills found in this criteria.</h2></td>
		</tr>
	  <?php
	  }
	  ?>
	</table>
<?php
if($saleid!='')
{
	genBarCode($saleid,'bc.png');
	/*$printnumbers	=	$AdminDAO->getrows("$dbname_detail.sale","printid","pksaleid='$saleid'");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
	$printnumber	=	$printnumbers[0]['printid'];*/
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
	/*$billfields		=	array('fksaleid','cash','cc','fc','ch','cr','ptime');
	$billdata		=	array($saleid,$cash,$creditcard,$fcurrency,$cheque,$credit,time());
	$AdminDAO->insertrow("$dbname_detail.bill",$billfields,$billdata);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
	//counting the previous bills for bill copy calculation*/
	//$billcount		=	$Bill->totalbills($saleid);
	$customersid	=	$AdminDAO->getrows("$dbname_detail.sale","fkcustomerid","pksaleid='$saleid'");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
	$customerid		=	$customersid[0]['fkcustomerid'];
	//changed $dbname_main to $dbname_detail on line 332, 335, 336, 339, 341, 342, 343, 345, 348, 349 by ahsan 22/02/2012
	  $query 	= 	"SELECT 
		pkcustomerid,
			CONCAT(firstname,', ',lastname) as name,
			CONCAT(address1,', ',address2) as address,
			email,
			companyname,
			CONCAT(phone ,', ',mobile) as phone,
			nic,
			round(
			 (SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as am FROM $dbname_detail.cashpayment ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid
		)
		+
			(SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as am FROM $dbname_detail.ccpayment ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid )
		+	(SELECT (IF (sum(amount*rate)IS NULL,0,sum(amount*rate))) as am FROM $dbname_detail.fcpayment ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid)
		+ 	(SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as am FROM $dbname_detail.chequepayment ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid),2) as paid
			,
			round((SELECT sum(globaldiscount) FROM $dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid),2) as discount
			,
			round((SELECT sum(saleprice * quantity)-(SELECT SUM(globaldiscount) FROM $dbname_detail.sale WHERE s1.fkcustomerid=pkcustomerid) as subtotal
					FROM $dbname_detail.saledetail,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid)-(  	SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as am FROM $dbname_detail.cashpayment ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid
		)-(SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as am FROM $dbname_detail.ccpayment ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid )-(SELECT (IF (sum(amount*rate)IS NULL,0,sum(amount*rate))) as am FROM $dbname_detail.fcpayment ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid)-(SELECT (IF (sum(amount)IS NULL,0,sum(amount))) as am FROM $dbname_detail.chequepayment ,$dbname_detail.sale s1 WHERE s1.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid)
		,2)  as pending,
			(SELECT round(SUM(saleprice*quantity),2) as Total FROM $dbname_detail.saledetail sdt,$dbname_detail.sale st2 WHERE st2.fkcustomerid =  pkcustomerid AND pksaleid = fksaleid AND pksaleid<='$saleid') AS total,pksaleid
	
	FROM
		$dbname_detail.customer c LEFT JOIN $dbname_detail.addressbook  ON (c.fkaddressbookid = pkaddressbookid)
		  LEFT JOIN $dbname_detail.sale s2 ON (fkcustomerid = pkcustomerid)
	WHERE
			pkcustomerid='$customerid' AND
			isdeleted <> 1  
	GROUP BY pkcustomerid
						";
	$queryresults		=	$AdminDAO->queryresult($query);
	$customerbalance	=	$queryresults[0]['pending'];
	$customername		=	$queryresults[0]['name'];
	$total				=	$queryresults[0]['total'];
	$discount			=	$queryresults[0]['discount'];
	$totalpaid			=	floor($queryresults[0]['paid']);
	
	?>
	
	<link rel="stylesheet" type="text/css" href="includes/css/style.css" />
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
	<?php 
	if($billcount>1)
	{
	?>
	<div style="clear:both;font-size:12px;text-align:center;font-weight:bold;text-transform:uppercase; background-color:#000;color:#FFF;margin-top:5px;">
		<?php //echo "Duplicate Copy ($billcount)";?>
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
		<td><?php echo str_replace(',',', ',ucfirst(strtolower($itemname))); ?></td>
		<td><?php if($boxsize>0){echo $quantity.'X'.$boxsize;}else{echo $quantity;} ?></td>
		<td><?php echo numbers($saledetails[$i]['saleprice']); ?></td>
		<td align="right"><?php echo numbers($saledetails[$i]['saleprice']*$saledetails[$i]['quantity']); ?></td>
		</tr>
	<?php
		$totalprice		+=	$saledetails[$i]['quantity']*$saledetails[$i]['saleprice'];
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
			
			$rem=$total-$totalpaid;//added by riz //removed by waq -$discount
			echo numbers($rem);
		//echo numbers($customerbalance); 
		?> </td>
	</tr>
	<?php
	}
	?>
	</table>
	<div align="center" style="clear:both; float:left;margin-bottom:5px;">
	<?php echo $billfooter; ?><br /><br />
	<?php echo $billtime; ?>
	<?php if($billcount>1){$printime	=	date('d-m-y h:i:s', time()); echo "<br /><br />Printing Time: ".$printime;}?>
	</div>
	<div align="center">
	<img src="bc.png" />
	</div>
	</div>
	<script language="javascript">
		window.print();
		window.close();
	</script>
	<input type="hidden" name="billindex" id="billindex" value="<?php echo $_SESSION['totalbilsnow'];?>" />
	
	<input type="hidden" name="currentsaleid" id="currentsaleid" value="<?php echo $saleid;?>" />
<?php
}//end of if saleid
?>