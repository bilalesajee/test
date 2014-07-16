<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$statuscheck	=	$_POST['constatus'];
	if($statuscheck!=1)
	{
		echo "This consignment is not pending and you can not add more items.";
		exit;
	}
	$poststock				=	$_POST['stock'];
	$postquantity			=	$_POST['quantity'];
	$postcostprice			=	$_POST['costprice'];
	$postretailprice		=	$_POST['retailprice'];
	$postexpiry				=	$_POST['expiry'];
	$postbatch				=	$_POST['batch'];
	$postpurchaseprice		=	$_POST['purchaseprice'];
	$postpriceinrs			=	$_POST['priceinrs'];
	$postshipmentcharges	=	$_POST['shipmentcharges'];
	$postfkshipmentid		=	$_POST['fkshipmentid'];
	$postfkbarcodeid		=	$_POST['fkbarcodeid'];
	$postfksupplierid		=	$_POST['fksupplierid'];
	$postfkagentid			=	$_POST['fkagentid'];
	$postfkcountryid		=	$_POST['fkcountryid'];
	$postfkbrandid			=	$_POST['fkbrandid'];
	$postshipmentpercentage	=	$_POST['shipmentpercentage'];
	$postboxprice			=	$_POST['boxprice'];
	
	//existing units for localstock updates
	$exunits		=	$_POST['existingunits'];
	$consignment	=	$_POST['id'];
	$fkstoreid		=	$_POST['store'];
	//retrieving store info for local updates
	$stores			=	$AdminDAO->getrows("store","*","pkstoreid='$fkstoreid'");
	$host			=	$stores[0]['storeip'];
	$username		=	$stores[0]['username'];
	$password		=	$stores[0]['password'];
	$storedb		=	$stores[0]['storedb'];
	$detailid		=	$_POST['detailid'];
	$addressbookid	=	$_SESSION['addressbookid'];
	$fields 		= 	array('fkconsignmentid','fkstockid','quantity','fkaddressbookid','updatetime','expiry','costprice','priceinrs','retailprice','batch','purchaseprice','shipmentcharges','fkshipmentid','fkbarcodeid','fksupplierid','fkagentid','fkcountryid','fkbrandid','shipmentpercentage','boxprice');
	for($i=0;$i<sizeof($poststock);$i++)
	{
		$stock		=	$poststock[$i];
		$quantity	=	$postquantity[$i];
		if($quantity=="")
		{
			continue;// to skip the empty units
		}
		$expiry		=	$postexpiry[$i];
		$priceinrs	=	$postpriceinrs[$i];
		$costprice	=	$postcostprice[$i];
		$retailprice=	$postretailprice[$i];
		
		$batch				=	$postbatch[$i];
		$purchaseprice		=	$postpurchaseprice[$i];
//		$priceinrs			=	$postpriceinrs[$i];
		$shipmentcharges	=	$postshipmentcharges[$i];
		$fkshipmentid		=	$postfkshipmentid[$i];
		$fkbarcodeid		=	$postfkbarcodeid[$i];
		$fksupplierid		=	$postfksupplierid[$i];
		$fkagentid			=	$postfkagentid[$i];
		$fkcountryid		=	$postfkcountryid[$i];
		$fkbrandid			=	$postfkbrandid[$i];
		$shipmentpercentage	=	$postshipmentpercentage[$i];
		$boxprice			=	$postboxprice[$i];
		
		$data		=	array($consignment,$stock,$quantity,$addressbookid,$updatetime,$expiry,$costprice,$priceinrs,$retailprice,$batch,$purchaseprice,$shipmentcharges,$fkshipmentid,$fkbarcodeid,$fksupplierid,$fkagentid,$fkcountryid,$fkbrandid,$shipmentpercentage,$boxprice);
		
		// this is the edit section
		if($detailid!=-1)
		{
			//updating local stocks
			//selecting existing units
			$select			=	"SELECT unitsremaining FROM stock WHERE pkstockid='$stock'";
			//connecting to the local db
			mysql_connect($host,$username,$password) or die("Failed connecting to local database".mysql_error());
			mysql_select_db($storedb) or die("Failed selecting local database".mysql_error());
			$existingunits	=	mysql_query($select) or die("Failed selecting units for update".mysql_error());
			$units			=	mysql_fetch_assoc($existingunits);
			$remainingunits	=	$units['unitsremaining'];
			//case 1: units>existingunits
			if($quantity>$exunits)
			{
				//we need to reduce stock in localdb
				//calculating units
				$remainingquantity	=	$quantity-$exunits;
				$remaining			=	$remainingunits-$remainingquantity;
			}

			//case 2: units<existingunits
			else if($quantity<$exunits)
			{
				//we need to increase stock in localdb
				//calculating units
				$ex2units	=	$exunits-$quantity;
				$remaining	=	$remainingunits+$ex2units;
			}
			else
			{
				$remaining=0;
			}
			if($remaining<0)
			{
				echo "Selected quantity can not be more than existing stock.";
				exit;
			}
			//updating units
			//echo $remaining;
			$query		=	"UPDATE stock 
							SET
							unitsremaining	=	'$remaining'
							WHERE
							pkstockid='$stock'";
			mysql_query($query) or die("Failed updating units");
			$query		=	addslashes($query);
			//logging queries
			$updatetime	=	time();
			$querylog	=	"INSERT INTO querylogger (`query`, `type`, `table`, `fkstoreid`, `querytime`,`fkemployeeid`)
							 VALUES ('$query', 'u', 'stock', '$fkstoreid', '$updatetime','$addressbookid');";
			mysql_query($querylog) or die("Failed updating database logs");
			$cfields	=	array('quantity');
			$cdata		=	array($quantity);
			$AdminDAO->updaterow("consignmentdetail",$cfields,$cdata,"pkconsignmentdetailid='$detailid'");
		}
		else
		{
			//updating local stocks
			//selecting existing units
			$select			=	"SELECT unitsremaining FROM stock WHERE pkstockid='$stock'";

//connecting to the local db
			//echo "$host,$username,$password";
			mysql_connect($host,$username,$password) or die("Failed connecting to local database");
			mysql_select_db($storedb) or die("Failed selecting local database");
			$existingunits			=	mysql_query($select) or die("Failed selecting units for update");
			$units					=	mysql_fetch_assoc($existingunits);
			$remainingunits			=	$units['unitsremaining'];
			$remaining				=	$remainingunits-$quantity;
			if($remaining<0)
			{
				echo "Selected quantity can not be more than existing stock.";
				exit;
			}
			//updating units
			$query		=	"UPDATE stock 
							SET
							unitsremaining	=	'$remaining'
							WHERE
							pkstockid='$stock'";
			mysql_query($query) or die("Failed updating units");
			$query		=	addslashes($query);
			//logging queries
			$updatetime		=	time();
			$querylog	=	"INSERT INTO querylogger (`query`, `type`, `table`, `fkstoreid`, `querytime`,`fkemployeeid`) 
											VALUES ('$query', 'u', 'stock', '$fkstoreid', '$updatetime','$addressbookid');";
			mysql_query($querylog) or die("Failed updating database logs");
			$AdminDAO->insertrow("consignmentdetail",$fields,$data);
		}
	}
}// end post
?>
