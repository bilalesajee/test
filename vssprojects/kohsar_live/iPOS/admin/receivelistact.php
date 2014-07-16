<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	$storeid	=	$_POST['store'];
	if($storeid)
	{
		$storeinfo		=	explode("|",$storeid);
		$storeid		=	$storeinfo[0];
		$dbname_detail	=	$storeinfo[1];
		//retrieving store info for local updates
		$stores			=	$AdminDAO->getrows("store","*","pkstoreid='$storeid'");
		$host			=	$stores[0]['storeip'];
		$username		=	$stores[0]['username'];
		$password		=	$stores[0]['password'];
		$storedb		=	$stores[0]['storedb'];
	}
	else
	{
		echo"Please select destination store.";
		exit;
	}
}//end edit
if(sizeof($_POST)>0)
{
	$addressbookid			=	$_SESSION['addressbookid'];
	$employeeids			=	$AdminDAO->getrows("employee","*","fkaddressbookid = '$addressbookid'");
	$employeeid				=	$employeeids[0]['pkemployeeid'];
	if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
		$storeid				=	$_POST['store'];
	}//end edit
	$barcodearr				=	$_POST['barcode'];
	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
		$itemarr				=	$_POST['itemdescription'];
	}//end edit
	$unitsarr				=	$_POST['units'];
	$damagedarr				=	$_POST['damaged'];
	$damagetypearr			=	$_POST['damagetype'];
	$purchasepricearr		=	$_POST['purchaseprice'];
	$priceinrsarr			=	$_POST['priceinrs'];
	$shipmentpercentagearr	=	$_POST['shipmentpercentage'];
	$shipmentchargesarr		=	$_POST['shipmentcharges'];
	$costpricearr			=	$_POST['costprice'];
	$salepricearr			=	$_POST['saleprice'];
	$boxpricearr			=	$_POST['boxprice'];
	$batcharr				=	$_POST['batch'];
	$expiryarr				=	$_POST['expiry'];
	$detailsidarr			=	$_POST['detailsid'];
	$shiplistidarr			=	$_POST['shiplistid'];
	$shipmentid				=	$_POST['shipment'];
	$xfields				=	array('fkstockid1','fkstockid2','quantity','adjtime');
	for($i=0;$i<sizeof($barcodearr);$i++)
	{
		$detailsid			=	$detailsidarr[$i];
		$shiplistid			=	$shiplistidarr[$i];
		$shiplistdetails	=	$AdminDAO->getrows("shiplistdetails","fksupplierid","pkshiplistdetailsid='$detailsid' ");
		$shiplist			=	$AdminDAO->getrows("shiplist","fkbrandid","pkshiplistid='$shiplistid'");
		//starting to sort data
		$batch				=	$batcharr[$i];
		$units				=	$unitsarr[$i];
		$expdate			=	$expiryarr[$i];
		$barcodeid			=	$barcodearr[$i];
		$expiry				=	$expiryarr[$i];
		$damaged			=	$damagedarr[$i];
		$damagetype			=	$damagetypearr[$i];
		$purchaseprice		=	$purchasepricearr[$i];
		$chargesinrs		=	$shipmentchargesarr[$i];
		$costprice			=	$purchaseprice+$chargesinrs;
		$saleprice			=	$salepricearr[$i];
		$priceinrs			=	$priceinrsarr[$i];
		$shipmentcharges	=	$shipmentchargesarr[$i];
		$fkshipmentid		=	$shipmentid;
		$fksupplierid		= 	$shiplistdetails[0]['fksupplierid'];
		$fkbrandid			=	$shiplist[0]['fkbrandid']; 
		$shipmentpercent	=	$shipmentpercentagearr[$i];
		$boxprice			=	$boxpricearr[$i];
		if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
			$itemdescription	=	$itemarr[$i];
		}//end edit
		$xstockid			=	'';
		$remainingunits 	= 	$units-$damaged;
		$adjustable			=	0;
		
		//checking if barcode exists in barcode table
		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
			$barcodes			=	$AdminDAO->getrows("barcode","*","barcode='$barcode'");
		}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
			$barcodes			=	$AdminDAO->getrows("barcode","*","barcode='$barcodeid'");
		}//end edit
		/**** case when barcode exists ****/
		if(sizeof($barcodes)>0)
		{
			if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
				$barcodeid	=	$barcodes[0]['pkbarcodeid'];
				//stock same expiry, barcode and remainingunits in minus
				$expstock	=	$AdminDAO->getrows("$dbname_detail.stock","*","FROM_UNIXTIME(expiry,'%Y-%m-%d') = '$expiry' AND fkbarcodeid = '$fkbarcodeid' AND unitsremaining<0");
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
				$fields				=	array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","shipmentpercentage","boxprice");
				$values				= 	array($batch,$units,$remainingunits,strtotime($expdate),$purchaseprice,$costprice,$saleprice,$priceinrs,$shipmentcharges,$fkshipmentid,$barcodeid,$fksupplierid,$storeid,$employeeid,$fkbrandid,time(),$shipmentpercent,$boxprice);
				// inserts records in stock table
				$stockid 	=	$AdminDAO->insertrow("$dbname_detail.stock",$fields,$values);
				
				//adjusting shipment list values
				//$sdfields	=	array('damaged','received');
				//$sdvalues	=	array("damaged+$damaged",$units);
				//$sql="UPDATE shiplistdetails SET damaged=damaged+'$damaged',received=received+'$units' where pkshiplistdetailsid='$detailsid'";
				//$AdminDAO->queryresult($sql);
				
		$fields		=	array('damaged','received');
		$values		=	array("damaged+'$damaged'","received+'$units'");
		$table		=	"shiplistdetails";
	
		$AdminDAO->updaterow($table,$fields,$values,"pkshiplistdetailsid='$detailsid'");				
				
				//$AdminDAO->updaterow("shiplistdetails",$sdfields,$sdvalues,"pkshiplistdetailsid='$detailsid'");
				
				// negative stock adjustments if needed
				if($xstockid !='')
				{
					$xvalues	=	array($xstockid,$stockid,$adjustable,time());
					$AdminDAO->insertrow("$dbname_detail.stockadjustment",$xfields,$xvalues);
					$newfields	=	array('unitsremaining');
					$newvalues	=	array($xstockunits);
					$AdminDAO->updaterow("$dbname_detail.stock",$newfields,$newvalues,"pkstockid = '$xstockid'");
				}
				if($damaged>0)
				{
					$fields	=	array("fkstockid","quantity","fkstoreid","fkemployeeid","damagedate","fkdamagetypeid");
					$data	=	array($stockid,$damaged,$storeid,$employeeid,time(),$damagetype);
					$AdminDAO->insertrow("$dbname_detail.damages",$fields,$data);
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
			}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
				$barcodeid	=	$barcodes[0]['pkbarcodeid'];
				//stock same expiry, barcode and remainingunits in minus
				//making database changes for connectivity with remote systems
				//preparing query
				$select		=	"SELECT * FROM stock WHERE FROM_UNIXTIME(expiry,'%Y-%m-%d') = '$expiry' AND fkbarcodeid = '$fkbarcodeid' AND unitsremaining<0";
				//connecting to the local db
				mysql_connect($host,$username,$password) or die("Failed connecting to local database".mysql_error());
				mysql_select_db($storedb) or die("Failed selecting local database".mysql_error());
				$existingunits	=	mysql_query($select) or die("Failed selecting stock ".mysql_error());
				$expstock		=	mysql_fetch_assoc($existingunits);
				//$expstock	=	$AdminDAO->getrows("$dbname_detail.stock","*","FROM_UNIXTIME(expiry,'%Y-%m-%d') = '$expiry' AND fkbarcodeid = '$fkbarcodeid' AND unitsremaining<0");
				if(sizeof($expstock)>0)
				{
	//				$xstockid			=	$expstock[0]['pkstockid'];
	//				$xstockunits		=	$expstock[0]['unitsremaining'];
					$xstockid			=	$expstock['pkstockid'];
					$xstockunits		=	$expstock['unitsremaining'];
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
				//$fields				=	array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","shipmentpercentage","boxprice");
				//$values				= 	array($batch,$units,$remainingunits,strtotime($expdate),$purchaseprice,$costprice,$saleprice,$priceinrs,$shipmentcharges,$fkshipmentid,$barcodeid,$fksupplierid,$storeid,$employeeid,$fkbrandid,time(),$shipmentpercent,$boxprice);
				$expirydate			=	strtotime($expdate);
				$insertiontime			=	time();
				// inserts records in stock table
				//recreating the query for insertion to local db
				
				$insertstock	=	"INSERT INTO stock 
									SET
										batch='$batch',
										quantity='$units',
										unitsremaining='$remainingunits',
										expiry='$expirydate',
										purchaseprice='$purchaseprice',
										costprice='$costprice',
										retailprice='$saleprice',
										priceinrs='$priceinrs',
										shipmentcharges='$shipmentcharges',
										fkshipmentid='$fkshipmentid',
										fkbarcodeid='$barcodeid',
										fksupplierid='$fksupplierid',
										fkstoreid='$storeid',
										fkemployeeid='$employeeid',
										fkbrandid='$fkbrandid',
										updatetime='$insertiontime',
										shipmentpercentage='$shipmentpercent',
										boxprice='$boxprice',
										addtime = '".time()."'
									";
				//$stockid 	=	$AdminDAO->insertrow("$dbname_detail.stock",$fields,$values,$dbname_detail);
				mysql_query($insertstock) or die("Failed inserting stock ".mysql_error());
				$stockid	=	mysql_insert_id();
				//adjusting shiplist status
				$shipsql="UPDATE shiplist SET fkstatusid=4 where pkshiplistid='$shiplistid'";
				//$AdminDAO->queryresult($shipsql);
	
				$tblj	= 	'shiplist';
				$field	=	array('fkstatusid');
				$value	=	array('4');
				$AdminDAO->updaterow($tblj,$field,$value,"pkshiplistid='$shiplistid'");			
				//adjusting shipment list values
				//$sdfields	=	array('damaged','received');
				//$sdvalues	=	array("damaged+$damaged",$units);
				$sql="UPDATE shiplistdetails SET damaged=damaged+'$damaged',received=received+'$remainingunits' where pkshiplistdetailsid='$detailsid'";
				//$AdminDAO->queryresult($sql);
				
				$tblj	= 	'shiplistdetails';
				$field	=	array('damaged','received');
				$value	=	array("damaged+'$damaged'","received+'$remainingunits'");
				$AdminDAO->updaterow($tblj,$field,$value,"pkshiplistdetailsid='$detailsid'");			
				
				//$AdminDAO->updaterow("shiplistdetails",$sdfields,$sdvalues,"pkshiplistdetailsid='$detailsid'");
				//inserting details into shiplistreceive
				$slfields	=	array('fkshiplistdetailsid','fkstoreid','fkstockid','quantity','damaged','fkdamagetypeid','receivedby','receivetime');
				if($damaged==0)
				{
					$damagetype=0;
				}
				$sldata		=	array($detailsid,$storeid,$stockid,$remainingunits,$damaged,$damagetype,$_SESSION['addressbookid'],time());
				$AdminDAO->insertrow("shipliststock",$slfields,$sldata);
				// negative stock adjustments if needed
				if($xstockid !='')
				{
					//connecting to the local db
					mysql_connect($host,$username,$password) or die("Failed connecting to local database".mysql_error());
					mysql_select_db($storedb) or die("Failed selecting local database".mysql_error());
					$stocktime	=	time();
					//$xvalues	=	array($xstockid,$stockid,$adjustable,time());
					$xquery		=	"INSERT INTO stockadjustment
									SET
										fkstockid1='$xstockid',
										fkstockid2='$stockid',
										quantity='$adjustable',
										adjtime='$stocktime'
									";
					mysql_query($xquery) or die("Failed adjusting units in stockadjustment".mysql_error());
					//$AdminDAO->insertrow("$dbname_detail.stockadjustment",$xfields,$xvalues);
					//$newfields	=	array('unitsremaining');
					//$newvalues	=	array($xstockunits);
					$xuquery		=	"UPDATE stock
									SET
										unitsremaining='$xstockunits'
									WHERE
										pkstockid='$xstockid'
									";
					mysql_query($xuquery) or die("Failed updating stock after adjusting units".mysql_error());
					//$AdminDAO->updaterow("$dbname_detail.stock",$newfields,$newvalues,"pkstockid = '$xstockid'",$dbname_detail);
				}
				if($damaged>0)
				{
					//connecting to the local db
					mysql_connect($host,$username,$password) or die("Failed connecting to local database".mysql_error());
					mysql_select_db($storedb) or die("Failed selecting local database".mysql_error());
					$damagetime		=	time();
					$damagesquery	=	"INSERT INTO 
											damages
										SET
											fkstockid='$stockid',
											quantity='$damaged',
											fkstoreid='$storeid',
											fkemployeeid='$employeeid',
											damagedate='$damagetime',
											fkdamagetypeid='$damagetype'
										";
					mysql_query($damagesquery) or die("Failed adding damages".mysql_error());
					//$fields	=	array("fkstockid","quantity","fkstoreid","fkemployeeid","damagedate","fkdamagetypeid");
					//$data	=	array($stockid,$damaged,$storeid,$employeeid,time(),$damagetype);
					//$AdminDAO->insertrow("$dbname_detail.damages",$fields,$data,$dbname_detail);
				}
				$attributesarray	=	$_POST['productattributeid'];
				//$fields=array('fkproductatributeid','fkattributeoptionid','fkstockid');
				//see if it is needed --------------- \-/-/*/*/*/*/*/*/////////////
				if(count($attributesarray)>0)
				{
					//connecting to the local db
					mysql_connect($host,$username,$password) or die("Failed connecting to local database".mysql_error());
					mysql_select_db($storedb) or die("Failed selecting local database".mysql_error());
					foreach($attributesarray as $at)
					{
						$attributeids	=	$_POST['attribute_'.$at];
						$instancestock	=	"INSERT INTO 
												instancestock
											SET
												fkproductattributeid='$at',
												fkattributeoptionid='$attributeids',
												fkstockid='$stockid'";
						mysql_query($instancestock) or die("Failed inserting stock instance values ".mysql_error());
						//$values			=	array($at,$_POST['attribute_'.$at],$stockid);
						//$AdminDAO->insertrow("$dbname_detail.instancestock",$fields,$values,$dbname_detail);
					}
				}
			}//end edit
		}//end if
		/**** case when the barcode doesn't exist and we are adding a completely new item ****/
		else
		{
			if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
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
					$fields				=	array("quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","shipmentpercentage","boxprice","addtime");
					$values				= 	array($units,$units,strtotime($expiry),$purchaseprice,$costprice,$saleprice,$priceinrs,$shipmentcharges,$fkshipmentid,$barcodeid,$fksupplierid,$storeid,$employeeid,$fkbrandid,time(),$shipmentpercent,$boxprice,time());
					// inserts records in stock table
					$stockid 	=	$AdminDAO->insertrow("$dbname_detail.stock",$fields,$values);
					
					if($damaged>0)
					{
						$fields	=	array("fkstockid","quantity","fkstoreid","fkemployeeid","damagedate","fkdamagetypeid");
						$data	=	array($stockid,$damaged,$storeid,$employeeid,time(),$damagetype);
						$AdminDAO->insertrow("damages",$fields,$data);
					}
					
					//adjusting shipment list values
					
					// adjust units but also update the units that have already been entered... like in first entry i added 5 out of 15 and in the next entry i added 3
					//$sdfields	=	array('damaged','received');
					//$sdvalues	=	array("damaged+$damaged",$units);
					//$AdminDAO->updaterow("shiplistdetails",$sdfields,$sdvalues,"pkshiplistdetailsid='$detailsid'");
					//$sql="UPDATE shiplistdetails SET damaged=damaged+'$damaged',received=received+'$units' where pkshiplistdetailsid='$detailsid'";
					//$AdminDAO->queryresult($sql);
					
			$fields		=	array('damaged','received');
			$values		=	array("damaged+'$damaged'","received+'$units'");
			$table		=	"shiplistdetails";
		
			$AdminDAO->updaterow($table,$fields,$values,"pkshiplistdetailsid='$detailsid'");				
			}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
			//1. creating the new product
			$productstatus	=	'a'; // is available
			$pfields		=	array('productname','productstatus');
			$pdata			=	array($itemdescription,$productstatus);
			$fkproductid	=	$AdminDAO->insertrow("product",$pfields,$pdata);
			//2. inserting the data into the barcode table
			$bfields		=	array('barcode','itemdescription','fkproductid');
			$bdata			=	array($barcodeid,$itemdescription,$fkproductid);
			$barcodeid		=	$AdminDAO->insertrow("barcode",$bfields,$bdata);
			//3. inserting data into barcodebrand
			$brfields		=	array('fkbarcodeid','fkbrandid');
			$brdata			=	array($barcodeid,$fkbrandid);
			$barcodebrandid	=	$AdminDAO->insertrow("barcodebrand",$brfields,$brdata);
			//preparing entry for stock
			$expirydate		=	strtotime($expiry);
			$insertiontime	=	time();
			$insertstock2	=	"INSERT INTO stock 
								SET
									batch='$batch',
									quantity='$units',
									unitsremaining='$units',
									expiry='$expirydate',
									purchaseprice='$purchaseprice',
									costprice='$costprice',
									retailprice='$saleprice',
									priceinrs='$priceinrs',
									shipmentcharges='$shipmentcharges',
									fkshipmentid='$fkshipmentid',
									fkbarcodeid='$barcodeid',
									fksupplierid='$fksupplierid',
									fkstoreid='$storeid',
									fkemployeeid='$employeeid',
									fkbrandid='$fkbrandid',
									updatetime='$insertiontime',
									shipmentpercentage='$shipmentpercent',
									boxprice='$boxprice',
									addtime='".time()."'
								";
			//connecting to the local db
			mysql_connect($host,$username,$password) or die("Failed connecting to local database".mysql_error());
			mysql_select_db($storedb) or die("Failed selecting local database".mysql_error());
			mysql_query($insertstock2) or die("Failed inserting stock ".mysql_error());
			$stockid	=	mysql_insert_id();
			//$fields				=	array("quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","shipmentpercentage","boxprice");
			//$values				= 	array($units,$units,strtotime($expiry),$purchaseprice,$costprice,$saleprice,$priceinrs,$shipmentcharges,$fkshipmentid,$barcodeid,$fksupplierid,$storeid,$employeeid,$fkbrandid,time(),$shipmentpercent,$boxprice);
			// inserts records in stock table
			//$stockid 	=	$AdminDAO->insertrow("$dbname_detail.stock",$fields,$values,$dbname_detail);
			
			if($damaged>0)
			{
				$damagetime		=	time();
				$damagesquery	=	"INSERT INTO 
										damages
									SET
										fkstockid='$stockid',
										quantity='$damaged',
										fkstoreid='$storeid',
										fkemployeeid='$employeeid',
										damagedate='$damagetime',
										fkdamagetypeid='$damagetype'
									";
				//connecting to the local db
				mysql_connect($host,$username,$password) or die("Failed connecting to local database".mysql_error());
				mysql_select_db($storedb) or die("Failed selecting local database".mysql_error());
				mysql_query($damagesquery) or die("Failed adding damages".mysql_error());
				//$fields	=	array("fkstockid","quantity","fkstoreid","fkemployeeid","damagedate","fkdamagetypeid");
				//$data	=	array($stockid,$damaged,$storeid,$employeeid,time(),$damagetype);
				//$AdminDAO->insertrow("$dbname_detail.damages",$fields,$data,$dbname_detail);
			}
			
			//adjusting shiplist status
			$shipsql="UPDATE shiplist SET fkstatusid=4 where pkshiplistid='$shiplistid'";
			//$AdminDAO->queryresult($shipsql);
			$tblj	= 	'shiplist';
			$field	=	array('fkstatusid');
			$value	=	array('4');
			$AdminDAO->updaterow($tblj,$field,$value,"pkshiplistid='$shiplistid'");					
			
			//adjusting shipment list values
			
			// adjust units but also update the units that have already been entered... like in first entry i added 5 out of 15 and in the next entry i added 3
			//$sdfields	=	array('damaged','received');
			//$sdvalues	=	array("damaged+$damaged",$units);
			//$AdminDAO->updaterow("shiplistdetails",$sdfields,$sdvalues,"pkshiplistdetailsid='$detailsid'");
			$sql="UPDATE shiplistdetails SET damaged=damaged+'$damaged',received=received+'$remainingunits' where pkshiplistdetailsid='$detailsid'";
			//$AdminDAO->queryresult($sql);
			$tblj	= 	'shiplistdetails';
			$field	=	array('damaged','received');
			$value	=	array("damaged+'$damaged'","received+'$remainingunits'");
			$AdminDAO->updaterow($tblj,$field,$value,"pkshiplistdetailsid='$detailsid'");				
			//inserting details into shiplistreceive
			$slfields	=	array('fkshiplistdetailsid','fkstoreid','fkstockid','quantity','damaged','fkdamagetypeid','receivedby','receivetime');
			if($damaged==0)
			{
				$damagetype=0;
			}
			$sldata		=	array($detailsid,$storeid,$stockid,$remainingunits,$damaged,$damagetype,$_SESSION['addressbookid'],time());
			$AdminDAO->insertrow("shipliststock",$slfields,$sldata);
			}//end edit
		}
	}
}
?>