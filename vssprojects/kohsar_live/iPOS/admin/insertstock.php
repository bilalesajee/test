<?php

//error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 			= 	$_REQUEST['id'];
$qs				=	$_SESSION['qstring'];
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
	$storeid		=	$_SESSION['storeid'];
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$storeid	=	$_POST['store'];
	if($storeid)
	{
		$storeinfo		=	explode("|",$storeid);
		$storeid		=	$storeinfo[0];
		$dbname_detail	=	$storeinfo[1];
	}
	else
	{
		echo"Please select destination store.";
		exit;
	}
}//end edit
$boxid			=	$_SESSION['boxid'];
$addressbookid	=	$_SESSION['addressbookid'];

if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
	if(sizeof($_POST)>0)
	{
			$storeid			=	$_SESSION['storeid'];
			$productname		=	$_POST['productname'];
			$fkbarcodeid		=	trim($_POST['barcode']," ");
			$invoice			=	$_POST['invoice'];
			$barcodes			=	$AdminDAO->getrows("barcode","pkbarcodeid"," barcode = '$fkbarcodeid'");
			$barcodeid			=	$barcodes[0]['pkbarcodeid'];
			$shipmentpercent	=	$_POST['shipmentpercentage'];
			$purchaseprice		=	$_POST['purchaseprice'];
			$priceinrs			=	$_POST['priceinrs'];
			$costprice			=	$_POST['costprice'];
			$saleprice			=	$_POST['saleprice'];
			$batch				=	$_POST['batch'];
			//working on dates
			$dd					=	$_POST['dd'];
			$mm					=	$_POST['mm'];
			$yy					=	$_POST['yy'];
			for($i=0;$i<10;$i++)
			{
				//adding year value
				if($dd[$i]!="" && $mm[$i]!="" && $yy[$i]!="")
				{
					$yr			=	$yy[$i]+2000;
					$expdate[]		=	$yr."-".$mm[$i]."-".$dd[$i];
				}
			}
			$units				=	$_POST['units'];
			$damaged			=	$_POST['damaged'];
			$damagetype			= 	$_POST['damagetype'];
			$fkshipmentid		= 	$_POST['shipment'];
			$shipmentcharges	= 	$_POST['shipmentcharges'];		
			$fkshipmentgroupid	= 	$_POST['shipmentgroup'];
			$fkbrandid			= 	$_POST['brand'];
			$fksupplierid		= 	$_POST['brandsupplier'];
			$boxprice			=	$_POST['boxprice'];
			$screen				=	$_POST['lock'];
			if($fkbarcodeid=='')
			{
				echo"Please select barcode to continue.";
				exit;
			}
			if($fkshipmentid=='')
			{
				echo"Please select shipment to continue.";
				exit;
			}
			if($invoice=='')
			{
				echo"Please select an invoice.";
				exit;
			}
			if($units=='')
			{
				echo"Units can not be left Blank.";
				exit;
			}
			if($expdate=='')
			{
				echo"Expiry date can not be left Blank.";
				exit;
			}
			$xfields	=	array('fkstockid1','fkstockid2','quantity','adjtime');
			// this is the add section	
			for($i=0;$i<10;$i++)
			{
				$xstockid		=	'';
				$remainingunits = 	$units[$i]-$damaged[$i];
				$adjustable		=	0;
				if($batch[$i]!=='' || $units[$i]!='' || $expdate[$i]!='' || $purchaseprice[$i]!='' || $costprice[$i]!='' || $saleprice[$i] != '' || $priceinrs[$i] != '' || $shipmentcharges[$i]!='')
				{
					if($units[$i] == '' || $saleprice[$i]== '')
					{
						echo "Please make sure you have entered Units and Sale Price";
						exit;
					}
					else
					{
						//special case 
						//stock same expiry, barcode and remainingunits in minus
						$expstock	=	$AdminDAO->getrows("$dbname_detail.stock","*","FROM_UNIXTIME(expiry,'%Y-%m-%d') = '$expdate[$i]' AND fkbarcodeid = '$fkbarcodeid' AND unitsremaining<0");
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
						$values				= 	array($batch[$i],$units[$i],$remainingunits,strtotime($expdate[$i]),$purchaseprice[$i],$costprice[$i],$saleprice[$i],$priceinrs[$i],$shipmentcharges[$i],$fkshipmentgroupid,$fkshipmentid,$barcodeid,$fksupplierid,$storeid,$addressbookid,$fkbrandid,time(),$shipmentpercent[$i],$boxprice[$i],$invoice,time());
						// inserts records in stock table
						$stockid 	=	$AdminDAO->insertrow("$dbname_detail.stock",$fields,$values);
						// adjusting price change table to accomodate new price
						//selecting price change records for updation
						$pricechanges	=	$AdminDAO->getrows("$dbname_detail.pricechange","pkpricechangeid,countername,price","fkbarcodeid='$barcodeid'");
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
							$priviliged	=	$priviliges[0]['previlliged'];
							if(!$priviliged)
							{
								$AdminDAO->updaterow("$dbname_detail.pricechange",$pfields,$pdata,"fkbarcodeid='$barcodeid'");
							}
						}
						else
						{
							// taking previous value from stock
							$oldstockprice	=	$AdminDAO->getrows("$dbname_detail.stock","MAX(retailprice) retailprice","fkbarcodeid='$barcodeid'");
							$changeprice		=	$oldstockprice[0]['retailprice'];
							// inserting the new record for price change
							$prfields		=	array('fkbarcodeid','price');
							$prdata			=	array($barcodeid,$saleprice[$i]);
							$pricechangeid	=	$AdminDAO->insertrow("$dbname_detail.pricechange",$prfields,$prdata);
							// the price change id for the first time is incorrect (bug)
						}
						// entering price change history data
						$phfields		=	array('fkpricechangeid','fkaddressbookid','updatetime','oldprice');
						$phdata			=	array($pricechangeid,$addressbookid,time(),$changeprice);
						$historyid		=	$AdminDAO->insertrow("$dbname_detail.pricechangehistory",$phfields,$phdata);
						// negative stock adjustments if needed
						if($xstockid !='')
						{
							$xvalues	=	array($xstockid,$stockid,$adjustable,time());
							$AdminDAO->insertrow("stockadjustment",$xfields,$xvalues);
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
				}
				// trying to retain the screen if required	
				if($screen == 'locked')
				{
					echo 'locked';
					exit;
				}
			}
	exit;
	}// end post
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	if(sizeof($_POST)>0)
	{
			$addressbookid		=	$_SESSION['addressbookid'];
			$employeeids		=	$AdminDAO->getrows("employee","*","fkaddressbookid = '$addressbookid'");
			$employeeid			=	$employeeids[0]['pkemployeeid'];
			//$storeid			=	$_SESSION['storeid'];
			$productname		=	$_POST['productname'];
			$fkbarcodeid		=	$_POST['barcode'];
			$barcodes			=	$AdminDAO->getrows("barcode","pkbarcodeid"," barcode = '$fkbarcodeid'");
			$barcodeid			=	$barcodes[0]['pkbarcodeid'];
			$shipmentpercent	=	$_POST['shipmentpercentage'];
			$purchaseprice		=	$_POST['purchaseprice'];
			$priceinrs			=	$_POST['priceinrs'];
			$costprice			=	$_POST['costprice'];
			$saleprice			=	$_POST['saleprice'];
			$batch				=	$_POST['batch'];
			//working on dates
			$dd					=	$_POST['dd'];
			$mm					=	$_POST['mm'];
			$yy					=	$_POST['yy'];
			for($i=0;$i<10;$i++)
			{
				//adding year value
				if($dd[$i]!="" && $mm[$i]!="" && $yy[$i]!="")
				{
					$yr			=	$yy[$i]+2000;
					$expdate[]		=	$yr."-".$mm[$i]."-".$dd[$i];
				}
			}
	/*		echo "<pre>";
			echo (date("Y-m-d",1252897620));
			echo "</pre>";
			exit;*/
			$units				=	$_POST['units'];
			$damaged			=	$_POST['damaged'];
			$damagetype			= 	$_POST['damagetype'];
			$fkshipmentid		= 	$_POST['shipment'];
			$shipmentcharges	= 	$_POST['shipmentcharges'];		
			$fkshipmentgroupid	= 	$_POST['shipmentgroup'];
			$fkbrandid			= 	$_POST['brand'];
			$fksupplierid		= 	$_POST['brandsupplier'];
			$boxprice			=	$_POST['boxprice'];
			$screen				=	$_POST['lock'];
			if($units=='')
			{
				echo"Units can not be left Blank.";
				exit;
			}
			if($expdate=='')
			{
				echo"Expiry date can not be left Blank.";
				exit;
			}
			$xfields	=	array('fkstockid1','fkstockid2','quantity','adjtime');
			// this is the add section	
			for($i=0;$i<10;$i++)
			{
				$xstockid		=	'';
				$remainingunits = 	$units[$i]-$damaged[$i];
				$adjustable		=	0;
				if($batch[$i]!=='' || $units[$i]!='' || $expdate[$i]!='' || $purchaseprice[$i]!='' || $costprice[$i]!='' || $saleprice[$i] != '' || $priceinrs[$i] != '' || $shipmentcharges[$i]!='')
				{
					if($units[$i] == '' || $saleprice[$i]== '')
					{
						echo "Please make sure you have entered Units and Sale Price";
						exit;
					}
					else
					{
						//special case 
						//stock same expiry, barcode and remainingunits in minus
						$expstock	=	$AdminDAO->getrows("$dbname_detail.stock","*","FROM_UNIXTIME(expiry,'%Y-%m-%d') = '$expdate[$i]' AND fkbarcodeid = '$fkbarcodeid' AND unitsremaining<0");
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
						$fields				=	array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","shipmentpercentage","boxprice","addtime");
						$values				= 	array($batch[$i],$units[$i],$remainingunits,strtotime($expdate[$i]),$purchaseprice[$i],$costprice[$i],$saleprice[$i],$priceinrs[$i],$shipmentcharges[$i],$fkshipmentgroupid,$fkshipmentid,$barcodeid,$fksupplierid,$storeid,$employeeid,$fkbrandid,time(),$shipmentpercent[$i],$boxprice[$i],time());
						// inserts records in stock table
						
						//chnage connection here to this Database $dbname_detail
						//get_Connection($db_server, $database_name, $database_password, $database_user);
						/*mysql_connect(,$username,$pwd) or die("can not connect to store DB: $storeip");
						mysql_select_db($storedb) or die("can not select DB");
						mysql_query($storequery) or die("can not run $storequery");*/
						$AdminDAO2 = new AdminDAO();
						$AdminDAO2->dbname = $dbname_detail;
						$stockid 	=	$AdminDAO2->insertrow("stock",$fields,$values);
						// negative stock adjustments if needed
						if($xstockid !='')
						{
							$xvalues	=	array($xstockid,$stockid,$adjustable,time());
							$AdminDAO2->insertrow("stockadjustment",$xfields,$xvalues);
							$newfields	=	array('unitsremaining');
							$newvalues	=	array($xstockunits);
							//chnage connection here
							$AdminDAO2->updaterow("stock",$newfields,$newvalues,"pkstockid = '$xstockid'");
						}
						if($damaged[$i] >0)
						{
							$fields	=	array("fkstockid","quantity","fkstoreid","fkemployeeid","damagedate","fkdamagetypeid");
							$data	=	array($stockid,$damaged[$i],$storeid,$employeeid,time(),$damagetype[$i]);
							//chnage connection here
							$AdminDAO2->insertrow("damages",$fields,$data);
						}
						$attributesarray	=	$_POST['productattributeid'];
						$fields=array('fkproductatributeid','fkattributeoptionid','fkstockid');
						if(count($attributesarray)>0)
						{
							foreach($attributesarray as $at)
							{
								$values		=	array($at,$_POST['attribute_'.$at],$stockid);
								//chnage connection here
								$AdminDAO2->insertrow("instancestock",$fields,$values);
							}
						}
					}
				}
				// trying to retain the screen if required	
				if($screen == 'locked')
				{
					echo 'locked';
					exit;
				}
			}
	exit;
	}// end post
}//end edit
?>