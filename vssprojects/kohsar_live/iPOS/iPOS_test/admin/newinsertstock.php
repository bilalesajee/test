<?php
//error_reporting(0);
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;
*/
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$changetimeu = time();
if(sizeof($_POST)>0)
{
	//1. Constant Values
	$totalitems			=	$_POST['totalitems'];
	$screen				=	$_POST['lock'];
	$storeid			=	$_SESSION['storeid'];
	$boxid				=	$_SESSION['boxid'];
	$addressbookid		=	$_SESSION['addressbookid'];
	$invoice			=	$_POST['invoice'];
	$fkshipmentid		= 	$_POST['shipment'];
	$fksupplierid		= 	$_POST['supplier'];
	//2. Variables
	$expirydate			=	$_POST['expiry'];
	$barcodeid			=	$_POST['bc'];
	$shipmentpercent	=	$_POST['shipmentpercentage'];
	$purchaseprice		=	$_POST['purchaseprice'];
	$priceinrs			=	$_POST['priceinrs'];
	$costprice			=	$_POST['costprice'];
	$saleprice			=	$_POST['saleprice'];
	$batch				=	$_POST['batch'];
	$units				=	$_POST['units'];
	$damaged			=	$_POST['damaged'];
	$damagetype			= 	$_POST['damagetype'];
	$shipmentcharge		= 	$_POST['shipmentcharges'];		
	$fkshipmentgroupid	= 	$_POST['shipmentgroup'];
//	$fkbrandid			= 	$_POST['brand'];
	$boxprice			=	$_POST['boxprice'];
	$xfields			=	array('fkstockid1','fkstockid2','quantity','adjtime');		
	if($fkshipmentid=='')
	{
		$msg	.=	"<li>Please select shipment to continue.</li>";
	}
	if($invoice=='')
	{
		$msg	.=	"<li>Please select an invoice.</li>";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	// this is the add section
	// checking if some barcodes were scanned with complusory info
	$flag	=	0;
	for($ch=0;$ch<$totalitems;$ch++)
	{
		if($barcodeid[$ch]!='')
		{
			if($units[$ch] == '' || $saleprice[$ch]== '' || $expirydate[$ch]=='')
			{
				$flag	=	1;
			}
		}
	}
	if($flag	==	1)
	{
		echo "Please make sure you have entered complete item information.";
		exit;
	}
	for($i=0;$i<$totalitems;$i++)
	{
		if($barcodeid[$i]!='')
		{
			//start setting up variables
			//expiry date section
			$xstockid			=	'';
			$remainingunits 	= 	$units[$i]-$damaged[$i];
			$adjustable			=	0;
			$expiry				=	explode("-",$expirydate[$i]);
			$expd				=	$expiry[0];
			$expm				=	$expiry[1];
			$expy				=	$expiry[2];
			$expdate			=	mktime(0,0,0,$expm,$expd,$expy);
			//special case 
			//stock same expiry, barcode and remainingunits in minus
			$expstock	=	$AdminDAO->getrows("$dbname_detail.stock","*","FROM_UNIXTIME(expiry,'%d-%m-%Y') = '$expirydate[$i]' AND fkbarcodeid = '$barcodeid[$i]' AND unitsremaining<0");
			if(sizeof($expstock)>0)
			{
				$xstockid			=	$expstock[0]['pkstockid'];
				$xstockunits		=	$expstock[0]['unitsremaining'];
				$remainingunits		=	$units[$i]-$damaged[$i];
				$xstockunits		=	$remainingunits+$xstockunits;
				//adjusting units
				if($xstockunits>0)
				{
					$adjustable			=	$remainingunits-$xstockunits;
					$remainingunits		=	$xstockunits;
					$xstockunits 		=	0;
				}
				else if($xstockunits<=0)
				{
					$adjustable		=	$remainingunits;
					$remainingunits = 	0;
				}
			}
			$fields				=	array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","shipmentpercentage","boxprice","fksupplierinvoiceid","addtime");
			$values				= 	array($batch[$i],$units[$i],$remainingunits,$expdate,$purchaseprice[$i],$costprice[$i],$saleprice[$i],$priceinrs[$i],$shipmentcharges[$i],$fkshipmentgroupid[$i],$fkshipmentid,$barcodeid[$i],$fksupplierid,$storeid,$addressbookid,$fkbrandid,time(),$shipmentpercent[$i],$boxprice[$i],$invoice,time());
			// inserts records in stock table
			$stockid 	=	$AdminDAO->insertrow("$dbname_detail.stock",$fields,$values);
			// adjusting price change table to accomodate new price
			//selecting price change records for updation
			$pricechanges	=	$AdminDAO->getrows("$dbname_detail.pricechange","pkpricechangeid,countername,price","fkbarcodeid='$barcodeid[$i]'");
			$pricechangeid	=	$pricechanges[0]['pkpricechangeid'];
			$pricecounter	=	$pricechanges[0]['countername'];
			$changeprice	=	$pricechanges[0]['price'];
			if($pricechangeid)
			{
				// updating price change data if the record is there
				$pfields		=	array('price','pupdatetime');
				$pdata			=	array($saleprice[$i],$changetimeu);
				// checking priviliged counter
				$priviliges		=	$AdminDAO->getrows("$dbname_detail.counter","previlliged","countername='$pricecounter'");	
				$priviliged		=	$priviliges[0]['previlliged'];
				if(!$priviliged)
				{
					$AdminDAO->updaterow("$dbname_detail.pricechange",$pfields,$pdata,"fkbarcodeid='$barcodeid[$i]'");
				}
			}
			else
			{
				// taking previous value from stock
				$oldstockprice	=	$AdminDAO->getrows("$dbname_detail.stock","MAX(retailprice) retailprice","fkbarcodeid='$barcodeid[$i]'");
				$changeprice	=	$oldstockprice[0]['retailprice'];
				// inserting the new record for price change
				$prfields		=	array('fkbarcodeid','price','inserttime','pupdatetime');
				$prdata			=	array($barcodeid[$i],$saleprice[$i],$changetimeu,$changetimeu);
				$pricechangeid	=	$AdminDAO->insertrow("$dbname_detail.pricechange",$prfields,$prdata);
			}
			// entering price change history data
			$phfields		=	array('fkpricechangeid','fkaddressbookid','updatetime','oldprice');
			$phdata			=	array($pricechangeid,$addressbookid,time(),$changeprice);
			$historyid		=	$AdminDAO->insertrow("$dbname_detail.pricechangehistory",$phfields,$phdata);
			// negative stock adjustments if needed
			if($xstockid !='')
			{
				$xvalues	=	array($xstockid,$stockid,$adjustable,time());
				$AdminDAO->insertrow("$dbname_detail.stockadjustment",$xfields,$xvalues);
				$newfields	=	array('unitsremaining');
				$newvalues	=	array($xstockunits);
				$AdminDAO->updaterow("$dbname_detail.stock",$newfields,$newvalues,"pkstockid = '$xstockid'");
			}
			if($damaged[$i] >0)
			{
				$fields	=	array("fkstockid","quantity","fkstoreid","fkemployeeid","damagedate","fkdamagetypeid");
				$data	=	array($stockid,$damaged[$i],$storeid,$addressbookid,time(),$damagetype[$i]);
				$AdminDAO->insertrow("$dbname_detail.damages",$fields,$data);
			}
			$attributesarray	=	$_POST['productattributeid'];
			$fields=array('fkproductatributeid','fkattributeoptionid','fkstockid');
			if(count($attributesarray)>0)
			{
				foreach($attributesarray as $at)
				{
					$values		=	array($at,$_POST['attribute_'.$at],$stockid);
					$AdminDAO->insertrow("$dbname_detail.instancestock",$fields,$values);
				}
			}
		}
		// trying to retain the screen if required	
	}
	if($screen == 'locked')
	{
		echo 'locked';
		exit;
	}
}// end post
?>