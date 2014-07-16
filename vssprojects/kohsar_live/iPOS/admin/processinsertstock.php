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
	$numb				=	$_GET['numb'];
	//1. Constant Values
	$storeid			=	$_SESSION['storeid'];
	$boxid				=	$_SESSION['boxid'];
	$addressbookid		=	$_SESSION['addressbookid'];
	$totalitems			=	$_POST['totalitems'];
	$invoice			=	$_POST['invoice'];
	$screen				=	$_POST['lock'];	
	$fkshipmentid		= 	$_POST['shipment'];
	$fksupplierid		= 	$_POST['supplier'];
	//2. Variables
	$barcodeid			=	$_POST['bc'];
	$units				=	$_POST['units'];
	$damaged			=	$_POST['damaged'];		
	$damagetype			= 	$_POST['damagetype'];
	$purchaseprice		=	$_POST['purchaseprice'];
	$priceinrs			=	$_POST['priceinrs'];
	$shipmentpercent	=	$_POST['shipmentpercentage'];	
	$shipmentcharge		= 	$_POST['shipmentcharges'];	
	$costprice			=	$_POST['costprice'];	
	$saleprice			=	$_POST['saleprice'];
	$boxprice			=	$_POST['boxprice'];
	$batch				=	$_POST['batch'];	
	$expirydate			=	$_POST['expiry'];	


	
	$fkshipmentgroupid	= 	$_POST['shipmentgroup'];
//	$fkbrandid			= 	$_POST['brand'];

	$xfields			=	array('fkstockid1','fkstockid2','quantity','adjtime');		
	if($fkshipmentid=='')
	{
		$msg	.=	"Please select shipment to continue.\n";
	}
	if($invoice=='')
	{
		$msg	.=	"Please select an invoice.";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	// this is the add section
	// checking if some barcodes were scanned with complusory info
	$flag	=	0;
	//for($ch=0;$ch<$totalitems;$ch++)
	//{
		if($barcodeid!='')
		{
			if($units == '' || $saleprice == '' || $expirydate == '')
			{
				$flag	=	1;
			}
		}
	//}
	if($flag	==	1)
	{
		echo "Please make sure you have entered complete item information.";
		exit;
	}
	//for($i=0;$i<$totalitems;$i++)
	//{
		if($barcodeid!='')
		{
			//start setting up variables
			//expiry date section
			$xstockid			=	'';
			$remainingunits 	= 	$units-$damaged;
			$adjustable			=	0;
			$expiry				=	explode("-",$expirydate);
			$expd				=	$expiry[0];
			$expm				=	$expiry[1];
			$expy				=	$expiry[2];
			$expdate			=	mktime(0,0,0,$expm,$expd,$expy);
			//special case 
			//stock same expiry, barcode and remainingunits in minus
			$expstock	=	$AdminDAO->getrows("$dbname_detail.stock","*","FROM_UNIXTIME(expiry,'%d-%m-%Y') = '$expirydate' AND fkbarcodeid = '$barcodeid' AND unitsremaining<0");
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
			$fields				=	array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","shipmentpercentage","boxprice","fksupplierinvoiceid","addtime");
			$values				= 	array($batch,$units,$remainingunits,$expdate,$purchaseprice,$costprice,$saleprice,$priceinrs,$shipmentcharges,$fkshipmentgroupid,$fkshipmentid,$barcodeid,$fksupplierid,$storeid,$addressbookid,$fkbrandid,time(),$shipmentpercent,$boxprice,$invoice,time());
			// inserts records in stock table
			$stockid 		=	$AdminDAO->insertrow("$dbname_detail.stock",$fields,$values);
				//////////////////////////////////
			//Add by Wajid For Update amount column in  supplierinvoice table/////////////
			/////Also call accounts link for invoice//////////////
			 /*$query_invoice			=	"SELECT SUM( s.quantity * s.priceinrs ) AS invoice_amount,s.fksupplierid as supplierid,s.fksupplierinvoiceid as invoiceid,sup.billnumber
              FROM $dbname_detail.stock s
			  left join $dbname_detail.supplierinvoice sup  on sup.pksupplierinvoiceid = s.fksupplierinvoiceid
              WHERE s.fksupplierinvoiceid = '$invoice'
               GROUP BY s.fksupplierinvoiceid ";
             $result_invoice		=	$AdminDAO->queryresult($query_invoice);

             $invoice_amount			=	$result_invoice[0]['invoice_amount'];
			  $supplierid			=	$result_invoice[0]['supplierid'];
			   $invoiceid			=	$result_invoice[0]['invoiceid'];
			    $billnumber			=	$result_invoice[0]['billnumber'];
			 
			 
			    $fields_invoice		=	array('invamount');
				$data_invoice			=	array($invoice_amount);
				$AdminDAO->updaterow("$dbname_detail.supplierinvoice",$fields_invoice,$data_invoice,"pksupplierinvoiceid='$invoice'");
	*/			
			
			$result 		=	mysql_query("SELECT MAX(pkstockid) as lastid FROM $dbname_detail.stock");
			$values 		= 	mysql_fetch_assoc($result);
			$stockid 		=	$values['lastid'];
			$_SESSION['stock'][$numb]	=	$stockid;
			// adjusting price change table to accomodate new price
			//selecting price change records for updation
			$pricechanges	=	$AdminDAO->getrows("$dbname_detail.pricechange","pkpricechangeid,countername,price","fkbarcodeid='$barcodeid'");
			$pricechangeid	=	$pricechanges[0]['pkpricechangeid'];
			$pricecounter	=	$pricechanges[0]['countername'];
			$changeprice	=	$pricechanges[0]['price'];
			if($pricechangeid)
			{
				// updating price change data if the record is there
				$pfields		=	array('price','pupdatetime');
				$pdata			=	array($saleprice,$changetimeu);
				// checking priviliged counter
				$priviliges		=	$AdminDAO->getrows("$dbname_detail.counter","previlliged","countername='$pricecounter'");	
				$priviliged		=	$priviliges[0]['previlliged'];
				if(!$priviliged)
				{
					$AdminDAO->updaterow("$dbname_detail.pricechange",$pfields,$pdata,"fkbarcodeid='$barcodeid'");
				}
			}
			else
			{
				// taking previous value from stock
				$oldstockprice	=	$AdminDAO->getrows("$dbname_detail.stock","MAX(retailprice) retailprice","fkbarcodeid='$barcodeid'");
				$changeprice	=	$oldstockprice[0]['retailprice'];
				// inserting the new record for price change
				$prfields		=	array('fkbarcodeid','price','inserttime','pupdatetime');
				$prdata			=	array($barcodeid,$saleprice,$changetimeu,$changetimeu);
				$pricechangeid	=	$AdminDAO->insertrow("$dbname_detail.pricechange",$prfields,$prdata);
				$result 		=	mysql_query("SELECT MAX(pkpricechangeid) as lastid FROM $dbname_detail.pricechange");
				$values 		= 	mysql_fetch_assoc($result);
				$pricechangeid 	=	$values['lastid'];				
				$_SESSION['pricechange'][$numb]	=	$pricechangeid;
			}
			// entering price change history data
			$phfields		=	array('fkpricechangeid','fkaddressbookid','updatetime','oldprice');
			$phdata			=	array($pricechangeid,$addressbookid,time(),$changeprice);
			$historyid		=	$AdminDAO->insertrow("$dbname_detail.pricechangehistory",$phfields,$phdata);
			$_SESSION['pricechangehistory'][$numb]	=	$historyid;
			// negative stock adjustments if needed
			if($xstockid !='')
			{
				$xvalues			=	array($xstockid,$stockid,$adjustable,time());
				$stockadjustmentid	=	$AdminDAO->insertrow("$dbname_detail.stockadjustment",$xfields,$xvalues);
				$_SESSION['stockadjustment'][$numb]	=	$stockadjustmentid;
				$newfields			=	array('unitsremaining');
				$newvalues			=	array($xstockunits);
				$AdminDAO->updaterow("$dbname_detail.stock",$newfields,$newvalues,"pkstockid = '$xstockid'");
			}
			if($damaged >0)
			{
				$fields		=	array("fkstockid","quantity","fkstoreid","fkemployeeid","damagedate","fkdamagetypeid");
				$data		=	array($stockid,$damaged,$storeid,$addressbookid,time(),$damagetype);
				$damagesid	=	$AdminDAO->insertrow("$dbname_detail.damages",$fields,$data);
				$_SESSION['damages'][$numb]	=	$damagesid;
			}
			$attributesarray	=	$_POST['productattributeid'];
			$fields=array('fkproductatributeid','fkattributeoptionid','fkstockid');
			if(count($attributesarray)>0)
			{
				foreach($attributesarray as $at)
				{
					$values				=	array($at,$_POST['attribute_'.$at],$stockid);
					$instancestockid	=	$AdminDAO->insertrow("$dbname_detail.instancestock",$fields,$values);
					$_SESSION['instancestock'][$numb]	=	$instancestockid;
				}
			}
		}
	//}
	// trying to retain the screen if required	
	/*if($screen == 'locked')
	{
		echo 'locked';
		exit;
	}*/
}// end post
echo "success";
exit;
?>