<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$fields 		=	array('received');
	$packinglist	=	$_POST['packlistitem'];
	$shipmentid		=	$_POST['shipmentid'];
	for($i=0;$i<=sizeof($packinglist);$i++)
	{
		$packlist		=	$packinglist[$i];
		$xpackinglist	=	explode("_",$packlist);
		$received		=	$xpackinglist[0];
		$packinglistid	=	$xpackinglist[1];
		$values			=	array($received);
		$AdminDAO->updaterow("packinglist",$fields,$values," pkpackinglistid='$packinglistid'");
	}
	//****************************** remote after completion *********************************/
	exit;
	//****************************** remote after completion *********************************/
	//getting data to receive units and adjusting stocks
	$shiplist			=	$AdminDAO->getrows("shiplist,packinglist","barcode,itemdescription,expiry,SUM(received) as received,SUM(reserved) as reserved, SUM(damaged) as damaged","fkshipmentid='$shipmentid' AND fkshiplistid=pkshiplistid GROUP by pkshiplistid");
	for($j=0;$j<sizeof($shiplist);$j++)
	{
		$barcode			=	$shiplist[$j]['barcode'];
		$itemdescription	=	$shiplist[$j]['itemdescription'];
		$expiry				=	$shiplist[$j]['expiry'];
		$units				=	$shiplist[$j]['received'];
		$damaged			=	$shiplist[$j]['damaged'];
		$purchaseprice		=	$shiplist[$j]['purchaseprice'];
		$salestax			=	$shiplist[$j]['salestax'];
		$surcharge			=	$shiplist[$j]['surcharge'];
		$chargesinrs		=	$shiplist[$j]['chargesinrs'];
		$costprice			=	$purchaseprice+$salestax+$surcharge+$chargesinrs;
		$saleprice			=	$costprice;//$shiplist[$j]['chargesinrs']; don't know about this one yet
		$priceinrs			=	$costprice;	 // this can be entered at transit and receival time	
		$shipmentcharges	=	"";//not sure -- fetch from shipment table
		$fkshipmentgroupid	=	"";//not sure -- fetch from shipment table
		$fkshipmentid		=	$shipmentid;
		$fksupplierid		= 	"";// fetch from brand if exists
		$storeid			=	"";// current store where stock is entered session
		$employeeid			=	"";// session
		$fkbrandid			=	$shiplist[$j]['fkbrandid']; 
		$shipmentpercent	=	""; //shipment table
		$boxprice			=	""; //not .....
		//checking if barcode exists in barcode table
		$barcodes			=	$AdminDAO->getrows("barcode","*","barcode='$barcode'");
		/**** case when barcode exists ****/
		if(sizeof($barcodes)>0)
		{
			$barcodeid	=	$barcodes[0]['pkbarcodeid'];
			//stock same expiry, barcode and remainingunits in minus
			$expstock	=	$AdminDAO->getrows("stock","*","FROM_UNIXTIME(expiry,'%Y-%m-%d') = '$expiry' AND fkbarcodeid = '$fkbarcodeid' AND unitsremaining<0");
			if(sizeof($expstock)>0)
			{
				$xstockid			=	$expstock[0]['pkstockid'];
				$xstockunits		=	$expstock[0]['unitsremaining'];
				$remainingunits		=	$units-$damaged;
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
			$fields				=	array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","shipmentpercentage","boxprice");
			$values				= 	array($batch[$i],$units[$i],$remainingunits,strtotime($expdate[$i]),$purchaseprice[$i],$costprice[$i],$saleprice[$i],$priceinrs[$i],$shipmentcharges[$i],$fkshipmentgroupid,$fkshipmentid,$barcodeid,$fksupplierid,$storeid,$employeeid,$fkbrandid,time(),$shipmentpercent[$i],$boxprice[$i]);
			// inserts records in stock table
			$stockid 	=	$AdminDAO->insertrow("stock",$fields,$values);
			// negative stock adjustments if needed
			if($xstockid !='')
			{
				$xvalues	=	array($xstockid,$stockid,$adjustable,time());
				$AdminDAO->insertrow("stockadjustment",$xfields,$xvalues);
				$newfields	=	array('unitsremaining');
				$newvalues	=	array($xstockunits);
				$AdminDAO->updaterow("stock",$newfields,$newvalues,"pkstockid = '$xstockid'");
			}
			if($damaged[$i] >0)
			{
				$fields	=	array("fkstockid","quantity","fkstoreid","fkemployeeid","damagedate","fkdamagetypeid");
				$data	=	array($stockid,$damaged[$i],$storeid,$employeeid,time(),$damagetype[$i]);
				$AdminDAO->insertrow("damages",$fields,$data);
			}
			$attributesarray	=	$_POST['productattributeid'];
			$fields=array('fkproductatributeid','fkattributeoptionid','fkstockid');
			//see if it is needed --------------- \-/-/*/*/*/*/*/*/////////////
			if(count($attributesarray)>0)
			{
				foreach($attributesarray as $at)
				{
					$values		=	array($at,$_POST['attribute_'.$at],$stockid);
					$AdminDAO->insertrow("instancestock",$fields,$values);
				}
			}
		}//end if
		/**** case when the barcode doesn't exist and we are adding a completely new item ****/
		else
		{
			//1. creating the new product
			$productstatus	=	'a'; // is available
			$pfields		=	array('productname','productstatus');
			$pdata			=	array($itemdescription,$productstatus);
			$fkproductid	=	$AdminDAO->insertrow("product",$pfields,$pdata);
			//2. inserting the data into the barcode table
			$bfields		=	array('barcode','itemdescription','fkproductid');
			$bdata			=	array($barcode,$itemdescription,$fkproductid);
			$barcodeid		=	$AdminDAO->insertrow("barcode",$bfields,$bdata);
			//preparing entry for stock
			$fields				=	array("quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","shipmentpercentage","boxprice");
			$values				= 	array($units,$units,strtotime($expiry),$purchaseprice[$i],$costprice[$i],$saleprice[$i],$priceinrs[$i],$shipmentcharges[$i],$fkshipmentgroupid,$fkshipmentid,$barcodeid,$fksupplierid,$storeid,$employeeid,$fkbrandid,time(),$shipmentpercent[$i],$boxprice[$i]);
			// inserts records in stock table
			$stockid 	=	$AdminDAO->insertrow("stock",$fields,$values);
		}
	}//end for
}// end post
?>