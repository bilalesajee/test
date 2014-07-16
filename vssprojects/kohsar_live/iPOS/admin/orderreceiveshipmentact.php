<?php
session_start();
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 		= 	$_POST['id'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$barcode			=	$_POST['barcodeid'];
	$purchaseprice		=	$_POST['purchaseprice'];
	$shipmentpercentage	=	$_POST['shipmentpercentage'];
	$shipmentcharges	=	$_POST['shipmentcharges'];
	$costprice			=	$_POST['costprice'];
	$retailpercentage	=	$_POST['retailpercentage'];
	$retailprice		=	$_POST['retailprice'];
	$quantity			=	$_POST['quantity'];
	$damaged			=	$_POST['damageqty'];
	$damagetype			=	$_POST['damagetype'];
	$returned			=	$_POST['returnqty'];
	$returntype			=	$_POST['returntype'];
	$purchaseid			=	$_POST['purchaseid'];
	$receivingid		=	$_POST['receivingid'];
	$batch				=	$_POST['batch'];
	$expiry				=	$_POST['expiry'];
	$shipmentid			=	$_POST['shipmentid'];
	$supplier			=	$_POST['supplier'];
	$country			=	$_POST['country'];
	$agent				=	$_POST['agent'];
	$currencyrate		=	$_POST['currencyrate'];
	$exchangerate		=	$currencyrate[0];

	// priceinrs = purchaseprice * currencyrate;
	// preparing field lists
	// step 1 stock
	$sfields 	=	array('batch','quantity','unitsremaining','expiry','purchaseprice','costprice','retailprice','priceinrs','shipmentcharges','fkshipmentid','fkbarcodeid','fkpurchaseid','fksupplierid','fkagentid','fkcountryid','fkstoreid','fkemployeeid','updatetime','shipmentpercentage','addtime');
	// step 2 damages
	$dfields	=	array('fkstockid','quantity','fkstoreid','fkemployeeid','damagedate','damagestatus','fkdamagetypeid');
	// step 3 returns
	$rfields	=	array('fkstockid','quantity','fkstoreid','fkemployeeid','returndate','returnstatus','fkreturntypeid');
	for($i=0;$i<sizeof($barcode);$i++)
	{
		// preparing data
		$priceinrs	=	$purchaseprice[$i]*$exchangerate;
		// Step 1 Stocks
		//stock same expiry, barcode and remainingunits in minus
		$expstock	=	$AdminDAO->getrows("$dbname_detail.stock","*","FROM_UNIXTIME(expiry,'%Y-%m-%d') = '$expiry[$i]' AND fkbarcodeid = '$barcode[$i]' AND unitsremaining<0");
		if(sizeof($expstock)>0)
		{
			$xstockid			=	$expstock[0]['pkstockid'];
			$xstockunits		=	$expstock[0]['unitsremaining'];
			$remainingunits		=	$quantity[$i]-$damaged[$i];
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
		$sdata 		=	array($batch[$i],$quantity[$i],$remainingunits,strtotime($expiry[$i]),$purchaseprice[$i],$costprice[$i],$retailprice[$i],$priceinrs,$shipmentcharges[$i],$shipmentid,$barcode[$i],$purchaseid[$i],$supplier[$i],$agent[$i],$country[$i],$storeid,$_SESSION['addressbookid'],time(),$shipmentpercentage[$i],time());
		// inserts records in stock table
		$stockid 	=	$AdminDAO->insertrow("$dbname_detail.stock",$sfields,$sdata);
	
		// adjusting price change table to accomodate new price
		//selecting price change records for updation
		$pricechanges	=	$AdminDAO->getrows("$dbname_detail.pricechange","pkpricechangeid,countername,price","fkbarcodeid='$barcode[$i]'");
		$pricechangeid	=	$pricechanges[0]['pkpricechangeid'];
		$pricecounter	=	$pricechanges[0]['countername'];
		$changeprice	=	$pricechanges[0]['price'];
		if($pricechangeid)
		{
			// updating price change data if the record is there
			$pfields		=	array('price');
			$pdata			=	array($saleprice[$i]);
			// checking priviliged counter
			$priviliges		=	$AdminDAO->getrows("$dbname_detail.counter","previlliged","countername='$pricecounter'");	
			$priviliged		=	$priviliges[0]['previlliged'];
			if(!$priviliged)
			{
				$AdminDAO->updaterow("$dbname_detail.pricechange",$pfields,$pdata,"fkbarcodeid='$barcode[$i]'");
			}
		}
		else
		{
			// taking previous value from stock
			$oldstockprice	=	$AdminDAO->getrows("$dbname_detail.stock","MAX(retailprice) retailprice","fkbarcodeid='$barcode[$i]'");
			$changeprice	=	$oldstockprice[0]['retailprice'];
			// inserting the new record for price change
			$prfields		=	array('fkbarcodeid','price');
			$prdata			=	array($barcode[$i],$saleprice[$i]);
			$pricechangeid	=	$AdminDAO->insertrow("$dbname_detail.pricechange",$prfields,$prdata);
		}
		// entering price change history data
		$phfields		=	array('fkpricechangeid','fkaddressbookid','updatetime','oldprice');
		$phdata			=	array($pricechangeid,$_SESSION['addressbookid'],time(),$changeprice);
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
		// insert damages
		if($damaged[$i] >0)
		{
			// p = pending
			$ddata	=	array($stockid,$damaged[$i],$storeid,$_SESSION['addressbookid'],time(),'p',$damagetype[$i]);
			$AdminDAO->insertrow("$dbname_detail.damages",$dfields,$ddata);
		}
		// insert returns
		if($returned[$i]>0)
		{
			// p = pending
			$rdata	=	array($stockid,$returned[$i],$storeid,$_SESSION['addressbookid'],time(),'p',$returntype[$i]);
			$AdminDAO->insertrow("$dbname_detail.returns",$rfields,$rdata);
		}
	}
}
?>