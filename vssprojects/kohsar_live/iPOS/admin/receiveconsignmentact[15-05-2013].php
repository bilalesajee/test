<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$addressbookid	=	$_SESSION['addressbookid'];
if(sizeof($_POST)>0)
{
	$id				=	$_POST['id'];
	//checking price changes
	$updateprice	=	$_POST['updateprice'];
	$src_store		=	$AdminDAO->getrows("store,consignment","store.*,consignmentname","pkstoreid = fkstoreid AND pkconsignmentid = '$id'");
	$dest_store		=	$AdminDAO->getrows("store,consignment","store.*,consignmentname","pkstoreid = fkdeststoreid AND pkconsignmentid = '$id'");
	/****************************************DEST STORE data***************************/
	$dest_storedb 	=	$dest_store[0]['storedb'];
	$dest_storeid	=	$dest_store[0]['pkstoreid'];
	$dest_storeip	=	$dest_store[0]['storeip'];
	$dest_username	=	$dest_store[0]['username'];
	$dest_password	=	$dest_store[0]['password'];
	/**************************************SRC store data**********************************/
	$src_storedb 	=	$src_store[0]['storedb'];
	$src_storeid	=	$src_store[0]['pkstoreid'];
	$src_storeip	=	$dest_store[0]['storeip'];
	$src_username	=	$dest_store[0]['username'];
	$src_password	=	$dest_store[0]['password'];
	/*************************************Match barcodes for sourse and destinatin By yasir 22-08-11**********/	
	$src_barcode	=	$AdminDAO->getrows("consignmentdetail","COUNT(fkbarcodeid) as num","fkconsignmentid = '$id'");
    $src_barcode_count = $src_barcode[0]['num'];
	$link_	=	mysql_connect($dest_storeip,$dest_username,$dest_password);
	$seldb	=	mysql_select_db($dest_storedb,$link_);
	$remotequery	=	"SELECT COUNT(fkbarcodeid) as num FROM main.consignmentdetail WHERE fkconsignmentid='$id'";
	$dest_barcode	=	mysql_query($remotequery);//->getrows("main_move.consignmentdetail","COUNT(fkbarcodeid) as num","fkconsignmentid = '$id'");
	$dest_barcode	=	mysql_fetch_array($dest_barcode);
    $dest_barcode_count = $dest_barcode['num'];
		
	if ($src_barcode_count	!=	$dest_barcode_count){
		echo 'Can not receive Consignment, replication errors occured. Please contact administrator.';
		$error = 1;
		exit;
	}	
	/**************************************************POSTED**************************************************/
	$cdids			=	$_POST['cdid'];
	$receiveds		=	$_POST['received'];
	$damageds		=	$_POST['damaged'];
	$newsaleprices	=	$_POST['newsaleprice'];
	$error	=	0;
	for($i=0;$i<sizeof($cdids);$i++)
	{
		$cdid				=	$cdids[$i];
		$received			=	$receiveds[$i];
		$newsaleprice		=	$newsaleprices[$i];
		$damaged			=	$damageds[$i];
		$damagetype			=	$_POST['damagetypes'.$i];
		$consign_details	=	$AdminDAO->getrows("barcode b,consignmentdetail cd",
											   "barcode,itemdescription,cd.*",
											   "fkbarcodeid = pkbarcodeid AND pkconsignmentdetailid = '$cdid'");
		$consignmentdetailstatus			=	$consign_details[0]['consignmentdetailstatus'];
		if($consignmentdetailstatus !=0)
		{
			continue;
		}
		$quantity			=	$consign_details[0]['quantity'];
		$expiry				=	$consign_details[0]['expiry'];
		if($newsaleprice)
		{
			$retailprice	=	$newsaleprice;
		}
		else
		{
			$retailprice		=	$consign_details[0]['retailprice'];
		}
		$costprice			=	$consign_details[0]['costprice'];
		$priceinrs			=	$consign_details[0]['priceinrs'];
		$shipmentcharges	=	$consign_details[0]['shipmentcharges'];
		$suggestedsaleprice	=	$consign_details[0]['suggestedsaleprice'];
		$fkshipmentgroupid	=	$consign_details[0]['fkshipmentgroupid'];
		$fkshipmentid		=	$consign_details[0]['fkshipmentid'];
		$fkbarcodeid		=	$consign_details[0]['fkbarcodeid'];
		$fkorderid			=	$consign_details[0]['fkorderid'];
		$fkagentid			=	$consign_details[0]['fkagentid'];
		$fkcountryid		=	$consign_details[0]['fkcountryid'];
		$fkstoreid			=	$dest_storeid;
		$fkbrandid			=	$consign_details[0]['fkbrandid'];
		$time				=	time();
		$shipmentpercentage	=	$consign_details[0]['shipmentpercentage'];
		$boxprice			=	$consign_details[0]['boxprice'];
		$batch				=	$consign_details[0]['batch'];
		$purchaseprice		=	$consign_details[0]['purchaseprice'];
		$shipmentcharges	=	$consign_details[0]['shipmentcharges'];
		$fkshipmentid		=	$consign_details[0]['fkshipmentid'];
		$fkbarcodeid		=	$consign_details[0]['fkbarcodeid'];
		$fksupplierid		=	$consign_details[0]['fksupplierid'];
		$fkagentid			=	$consign_details[0]['fkagentid'];
		$fkcountryid		=	$consign_details[0]['fkcountryid'];
		$fkbrandid			=	$consign_details[0]['fkbrandid'];
		$shipmentpercentage	=	$consign_details[0]['shipmentpercentage'];
		/***********************************************************************************************************/
		$stockquery	=	"INSERT INTO $dest_storedb.stock SET batch = '$batch', quantity = '$quantity', unitsremaining = '$received', expiry = '$expiry', purchaseprice = '$purchaseprice', costprice = '$costprice', retailprice = '$retailprice', priceinrs = '$priceinrs', shipmentcharges = '$shipmentcharges', suggestedsaleprice = '$suggestedsaleprice',fkshipmentgroupid = '$fkshipmentgroupid', fkshipmentid = '$fkshipmentid', fkbarcodeid = '$fkbarcodeid', fkorderid = '$fkorderid', fksupplierid = '$fksupplierid',fkagentid = '$fkagentid',fkcountryid = '$fkcountryid', fkstoreid = '$fkstoreid',fkemployeeid = '$addressbookid',fkbrandid = '$fkbrandid', updatetime = '$time', unitsreserved = '0', shipmentpercentage = '$shipmentpercentage', boxprice = '$boxprice', fkconsignmentdetailid= '$cdid', addtime = '".time()."'";
		$queryx	=	"INSERT INTO 
								$dest_storedb.querylogger
							SET 
								`query` = \"$stockquery\",
								`type` = 'i',
								`table` = '$table',
								`pk` = 'pkstockid',
								`pkvalue` = (SELECT MAX(pkstockid) FROM $dest_storedb.stock),
								`fkstoreid` = '$dest_storeid',
								`querytime` = '$querytime',
								`fkemployeeid` = '$addressbookid'
					";	
		if($damaged  > 0)
		{
			$damagesquery	=	"INSERT INTO $dest_storedb.damages SET fkstockid = (SELECT MAX(pkstockid) FROM $dest_storedb.stock),quantity = '$damaged', fkstoreid =	'$dest_storeid', fkemployeeid = '$addressbookid', damagedate = '$time', damagestatus = 'p', fkdamagetypeid = '$damagetype'";					
		}//if
		$updatetime	=	time();
		$pricechangequery	=	"DELETE FROM $dest_storedb.pricechange WHERE fkbarcodeid = '$fkbarcodeid'";
		$pricechangequery2	=	"INSERT INTO $dest_storedb.pricechange SET fkbarcodeid = '$fkbarcodeid', price = '$retailprice'";
		$pricechangehistory	=	"INSERT INTO $dest_storedb.pricechangehistory SET fkpricechangeid = (SELECT MAX(pkpricechangeid) FROM $dest_storedb.pricechange), fkaddressbookid = $addressbookid, updatetime ='$updatetime'";
		if($updateprice==1)
		{
			if($damagesquery)
			{
				$query_log	=	$stockquery.';'.$damagesquery.';'.$pricechangequery.';'.$pricechangequery2.';'.$pricechangehistory.';';
				//$query_log	=	$stockquery.';'.$damagesquery.';';
			}
			else
			{
				$query_log	=	$stockquery.';'.$pricechangequery.';'.$pricechangequery2.';'.$pricechangehistory.';';
				//$query_log	=	$stockquery.';';
			}
		}
		else
		{
			if($damagesquery)
			{
				//$query_log	=	$stockquery.';'.$damagesquery.';'.$pricechangequery.';'.$pricechangequery2.';'.$pricechangehistory.';';
				$query_log	=	$stockquery.';'.$damagesquery.';';
			}
			else
			{
				//$query_log	=	$stockquery.';'.$pricechangequery.';'.$pricechangequery2.';'.$pricechangehistory.';';
				$query_log	=	$stockquery.';';
			}
		}
//			$query_log	=	$stockquery.';'.$damagesquery.';';
		$log		.=	$query_log;
		$error		=	0;
	}//for
	if($error==0)
	{
		$link	=	mysqli_connect($dest_storeip,$dest_username,$dest_password,$dest_storedb);
		if(mysqli_connect_errno()) 
		{
			echo "encountered errors".mysqli_connect_error();
			$error	=	1;
		}
		else
		{
			if($log)
			{
				if(mysqli_multi_query($link,$log))
				{
					// updating consignment items
					$fields	=	array('consignmentdetailstatus','receivedquantity','receivedby','receivetime','fkdamagetypeid','newretailprice');
					for($i=0;$i<sizeof($cdids);$i++)
					{
						// have to receive data again
						$cdid				=	$cdids[$i];
						$received			=	$receiveds[$i];
						$damagetype			=	$_POST['damagetypes'.$i];
						$consign_details	=	$AdminDAO->getrows("barcode b,consignmentdetail cd",
											   "barcode,itemdescription,cd.*",
											   "fkbarcodeid = pkbarcodeid AND pkconsignmentdetailid = '$cdid'");
						$consignmentdetailstatus			=	$consign_details[0]['consignmentdetailstatus'];
						if($consignmentdetailstatus !=0)
						{
							continue;
						}
						$retailprice			=	$consign_details[0]['retailprice'];
						$newsalepricedata		=	$_POST['newsaleprice'];
						$newsaleprice		=	$newsalepricedata[$i];
						if($newsaleprice)
						{
							$retailprice	=	$newsaleprice;
						}
						$values	=	array('1',$received,$addressbookid,time(),$damagetype,$retailprice);
						$where	=	"pkconsignmentdetailid = '$cdid'";
						$AdminDAO->updaterow('consignmentdetail',$fields,$values,$where);
					}
					// updating consignment status to received
					$fields	=	array('fkstatusid');
					$values	=	array('7');
					$AdminDAO->updaterow('consignment',$fields,$values,"pkconsignmentid = '$id'");
					// updating log
					$log	=	addslashes($log);
					$log_query	=	"UPDATE consignment SET log = '$log' WHERE pkconsignmentid = '$id'";
					//$AdminDAO->queryresult($log_query);
			
			$tblj	= 	'consignment';
			$field	=	array('log');
			$value	=	array($log);
			
			$AdminDAO->updaterow($tblj,$field,$value,"pkconsignmentid = '$id'");	
								
				}
				else
				{
					$error	=	1;
				}
			}
			else
			{
				echo "Nothing to receive...";
				exit;
			}
		}
	}
	else
	{
		$error	=	1;
	}
	if($error	==	1)
	{
		$log	=	addslashes($log);
		$log_query	=	"UPDATE consignment SET log = '$log' WHERE pkconsignmentid = '$id'";
		//$AdminDAO->queryresult($log_query);
		
			$tblj	= 	'consignment';
			$field	=	array('log');
			$value	=	array($log);
			
			$AdminDAO->updaterow($tblj,$field,$value,"pkconsignmentid = '$id'");		
		
		// updating consignment status to receive failure
		$fields	=	array('fkstatusid');
		$values	=	array('8');
		$AdminDAO->updaterow('consignment',$fields,$values,"pkconsignmentid = '$id'");
		echo "Errors encountered while receiving units!";
	}
}//if post
?>