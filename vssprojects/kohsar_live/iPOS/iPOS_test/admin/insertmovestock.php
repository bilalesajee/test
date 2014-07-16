<?php

//error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 			= 	$_REQUEST['id'];
$qs				=	$_SESSION['qstring'];
$store			=	$_POST['storeiddest'];
$srcstoreid		=	$_POST['srcstoreid'];
if(!$srcstoreid)
{
	$srcstoreid	=	$_SESSION['storeid'];
}
$sql			=	"SELECT storedb,storename from store where pkstoreid='$srcstoreid'";
$starr			=	$AdminDAO->queryresult($sql);
$sourcestoredb	=	$starr[0]['storedb'];
$storename		=	$starr[0]['storename'];

if(sizeof($_POST)>0)
{
		$stockid			= 	filter($_POST['stockid']);
		$storeiddest 		= 	filter($_POST['storeiddest']);
		$moveunits			= 	filter($_POST['moveunits']);
		$codeid				= 	filter($_POST['codeid']);
		$fkbrandid			= 	$_POST['fkbrandid'];
		$deadline			= 	$_POST['deadline'];
		$comments			= 	$_POST['comments'];
		$action				= 	$_POST['action'];
		$damagetype			= 	$_POST['damage'];
		
		if($action=='damage')
		{
			if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
				if($damagetype=='')
				{
					$damagetype=2;	
				}
				$fields = array('fkstockid','quantity','fkstoreid','fkaddressbookid','damagedate','fkdamagetypeid');
				$values = array($stockid,$moveunits,$storeid,$empid,time(),$damagetype);
				$AdminDAO2 = new AdminDAO();
				$AdminDAO2->dbname = $dbname_detail;
				/*$dmgqry="INSERT 
								INTO 
									damages
								SET
									fkstockid='$stockid',
									quantity='$moveunits',
									fkstoreid='$storeid',
									fkemployeeid='$empid',
									damagedate='".time()."',
									fkdamagetypeid='$damagetype'";
				//$affectedid	=	$AdminDAO2->queryresult($dmgqry);*/
				$fields		=	array('fkstockid','quantity','fkstoreid','fkemployeeid','damagedate','fkdamagetypeid');
				$values		=	array($stockid,$moveunits,$storeid,$empid,time(),$damagetype);
				$affectedid	=	$AdminDAO2->insertrow('damages',$fields,$values);		
				//$AdminDAO->logquery2db($dmgqry,'i','damages','pkdamageid',$affectedid,$storeid,time(),'',$sourcestoredb);
				//$AdminDAO->insertrow("$sourcestoredb.damages",$fields,$values);
				$AdminDAO2->dbname = $sourcestoredb;
				/*$sql="UPDATE 
							stock 
						SET 
							unitsremaining=(unitsremaining-$moveunits) 
						WHERE 
							pkstockid='$stockid' ";
				//$AdminDAO2->queryresult($sql);*/
	
				$fields		=	array('unitsremaining');
				$values		=	array("unitsremaining-$moveunits");
				$table		=	"stock";
			
				$AdminDAO2->updaterow($table,$fields,$values,"pkstockid='$stockid'");
				//$AdminDAO->logquery2db($sql,'u','stock','pkstockid',$stockid,$storeid,time(),'',$sourcestoredb);
				exit;	
			}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
				if($damagetype=='')
				{
					$damagetype=2;	
				}
				$fields = array('fkstockid','quantity','fkstoreid','fkaddressbookid','damagedate','fkdamagetypeid');
				$values = array($stockid,$moveunits,$storeid,$empid,time(),$damagetype);
				$dmgqry="INSERT 
								INTO 
							$sourcestoredb.damages
								SET
									fkstockid='$stockid',
									quantity='$moveunits',
									fkstoreid='$storeid',
									fkemployeeid='$empid',
									damagedate='".time()."',
									fkdamagetypeid='$damagetype'";
				
				/*$affectedid	 =	$AdminDAO->queryresult($dmgqry);*/
				
				$damagedatej =	time();		
				$tblj		 = 	"$sourcestoredb.damages";
				$field		 =	array('fkstockid','quantity','fkstoreid','fkemployeeid','damagedate','fkdamagetypeid');
				$value		 =	array($stockid,$moveunits,$storeid,$empid,$damagedatej,$damagetype);
				
				$affectedid	 =	$AdminDAO->insertrow($tblj,$field,$value);				
				
				$AdminDAO->logquery2db($dmgqry,'i','damages','pkdamageid',$affectedid,$storeid,time(),'',$sourcestoredb);
				//$AdminDAO->insertrow("$sourcestoredb.damages",$fields,$values);
				
				$sql="UPDATE 
							$sourcestoredb.stock 
						SET 
							unitsremaining=(unitsremaining-$moveunits) 
						WHERE 
							pkstockid='$stockid' ";
				//$AdminDAO->queryresult($sql);
				
				$tblj	= 	"$sourcestoredb.stock";
				$field	=	array('unitsremaining');
				$value	=	array('(unitsremaining-$moveunits)');
				
				$AdminDAO->updaterow($tblj,$field,$value,"pkstockid='$stockid'");	
				
				$AdminDAO->logquery2db($sql,'u','stock','pkstockid',$stockid,$storeid,time(),'',$sourcestoredb);
				exit;	
			}//end edit
		}
		
		if($storeid)
		{
			$storeinfo		=	explode("|",$store);
			$storeid		=	$storeinfo[0];
			$dbname_dest	=	$storeinfo[1];
		}
		else
		{
			echo"Please select destination store.";
			exit;
		}
		if($storeiddest=='')
		{
			$msg.="<li>Please select Destination Store to move Units.</li>";
			exit;
		}
		if($moveunits=='')
		{
			$msg.="</li>Please Enter Units Quantitiy to Move.</li>";
			exit;
		}
		/*$demandrow = $AdminDAO->getrows("demand","MAX(pkdemandid) as pkdemandid");
		$demandid	=	$demandrow[0]['pkdemandid'];
		$demandid	=	$demandid+1;
		$demandname	=$storeiddest.'-'.date('my').'-'.$demandid;			*/
		/*$sql="SELECT MAX(pkdemandid) as demandid from $dbname_dest.demand order by pkdemandid DESC";
		$demand	=	$AdminDAO->queryresult($sql);
		$demandid	=$demand[0]['demandid'] + 1;
		$demandname	=$storeiddest.'-'.date('my').'-'.$demandid;	
		$fields = array('demandname','fkstoreid','demanddate','fkaddressbookid');
		$values = array($demandname,$storeiddest,date('y-m-d'),$empid);*/

	if($stockid!='')	
	{
		//inserts values in demand
		//$demandid = $AdminDAO->insertrow("$dbname_dest.demand",$fields,$values);// inserts records in demand table
		//inserts values in demanddetaols
		//$fields = array('fkdemandid','fkbarcodeid','fkstockid','deadline','comments','fkbrandid','units');
		//$values = array($demandid,$codeid,$stockid,$deadline,$comments,$fkbrandid,$moveunits);
		
		//$demanddetailsid = $AdminDAO->insertrow("$dbname_dest.demanddetails",$fields,$values);// inserts records in stock table
		//inserts values in instancedemanddetails
		//$sql="UPDATE stock SET unitsremaining=(unitsremaining-'$moveunits'),unitsreserved=(unitsreserved+'$moveunits') WHERE pkstockid='$stockid' ";
		//$pkstockid	=	$AdminDAO->pkey("$dbname_main.stock",'pkstockid');
		$pkstockid	=	"";	
		/*	$sql="INSERT 
						INTO 
							$dbname_dest.stock(SELECT 
								  		'$pkstockid' as pkstockid,
										batch,
										'$moveunits' as quantity,
										$moveunits as unitsremaining,
										expiry,
										purchaseprice,
										costprice,
										retailprice,
										priceinrs,
										shipmentcharges,
										suggestedsaleprice,
										fkshipmentgroupid,
										fkshipmentid,
										'$codeid' as fkbarcodeid,
										fkorderid,
										fksupplierid,
										fkagentid,
										fkcountryid,
										'$storeiddest' as fkstoreid,
										'$empid' as fkemployeeid,
										fkbrandid,".time()." as updatetime,
										0 as unitsreserved,
										shipmentpercentage,
										boxprice,
										'$stockid' as refstockid
									FROM 
										$dbname_detail.stock 
									WHERE 
										pkstockid='$stockid'
									)";*/
	 $sql="SELECT 
				'$pkstockid' as pkstockid,
				batch,
				'$moveunits' as quantity,
				$moveunits as unitsremaining,
				expiry,
				purchaseprice,
				costprice,
				retailprice,
				priceinrs,
				shipmentcharges,
				suggestedsaleprice,
				fkshipmentgroupid,
				fkshipmentid,
				'$codeid' as fkbarcodeid,
				fkorderid,
				fksupplierid,
				fkagentid,
				fkcountryid,
				'$storeiddest' as fkstoreid,
				'$empid' as fkemployeeid,
				fkbrandid,".time()." as updatetime,
				0 as unitsreserved,
				shipmentpercentage,
				boxprice,
				'$stockid' as refstockid
			FROM 
				$sourcestoredb.stock 
			WHERE 
				pkstockid='$stockid'";
							
			$sourcestockrow	=	$AdminDAO->queryresult($sql);
			$batch			=	$sourcestockrow[0]['batch'];
			$quantity		=	$sourcestockrow[0]['quantity'];
			$unitsremaining	=	$sourcestockrow[0]['unitsremaining'];
			$expiry			=	$sourcestockrow[0]['expiry'];
			$purchaseprice	=	$sourcestockrow[0]['purchaseprice'];
			$costprice		=	$sourcestockrow[0]['costprice'];
			$retailprice	=	$sourcestockrow[0]['retailprice'];
			$priceinrs		=	$sourcestockrow[0]['priceinrs'];
			$shipmentcharges=	$sourcestockrow[0]['shipmentcharges'];
			$suggestedsaleprice=$sourcestockrow[0]['suggestedsaleprice'];
			$fkshipmentgroupid=	$sourcestockrow[0]['fkshipmentgroupid'];
			$fkshipmentid	=	$sourcestockrow[0]['fkshipmentid'];
			$fkbarcodeid	=	$sourcestockrow[0]['fkbarcodeid'];
			$fkorderid		=	$sourcestockrow[0]['fkorderid'];
			$fksupplierid	=	$sourcestockrow[0]['fksupplierid'];
			$fkagentid		=	$sourcestockrow[0]['fkagentid'];
			$fkcountryid	=	$sourcestockrow[0]['fkcountryid'];
			$fkstoreid		=	$sourcestockrow[0]['fkstoreid'];
			$fkemployeeid	=	$sourcestockrow[0]['fkemployeeid'];
			$fkbrandid		=	$sourcestockrow[0]['fkbrandid'];
			$updatetime		=	$sourcestockrow[0]['updatetime'];
			$unitsreserved	=	$sourcestockrow[0]['unitsreserved'];
			$shipmentpercentage=	$sourcestockrow[0]['shipmentpercentage'];
			$boxprice		=	$sourcestockrow[0]['boxprice'];
			$refstockid		=	$sourcestockrow[0]['refstockid'];
			
	/*		$fields	=	array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","suggestedsaleprice","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fkorderid","fksupplierid","fkagentid","fkcountryid","fkstoreid","fkemployeeid","fkbrandid","updatetime","shipmentpercentage","boxprice","refstockid","srcstoreid");
			$values	= 	array($batch,$quantity,$unitsremaining,$expiry,$purchaseprice,$costprice,$retailprice,$priceinrs,$shipmentcharges,$suggestedsaleprice,$fkshipmentgroupid,$fkshipmentid,$fkbarcodeid,$fkorderid,$fksupplierid,$fkagentid,$fkcountryid,$fk
							  ,$fkemployeeid,$fkbrandid,time(),$shipmentpercentage,$boxprice,$refstockid,$srcstoreid);*/
					// inserts records in stock table
			//$AdminDAO->insertrow("$dbname_dest.stock",$fields,$values);
/*			$insertstock="
			INSERT INTO
						stock
					SET 
						batch ='$batch',
						quantity ='$quantity',
						unitsremaining ='$unitsremaining',
						expiry ='$expiry',
						
						purchaseprice ='$purchaseprice',
						costprice ='$costprice',
						retailprice ='$retailprice',
						priceinrs ='$priceinrs',
						
						shipmentcharges ='$shipmentcharges',
						suggestedsaleprice ='$suggestedsaleprice',
						fkshipmentgroupid ='$fkshipmentgroupid',
						fkshipmentid ='$fkshipmentid',

						fkbarcodeid ='$fkbarcodeid',
						fkorderid ='$fkorderid',
						fksupplierid ='$fksupplierid',
						fkagentid ='$fkagentid',
						
						fkcountryid ='$fkcountryid',
						fkstoreid ='$fkstoreid',
						fkemployeeid ='$fkemployeeid',
						fkbrandid ='$fkbrandid',
						
						updatetime ='".time()."',
						shipmentpercentage ='$shipmentpercentage',
						boxprice ='$boxprice',
						refstockid ='$refstockid',
						srcstoreid ='$srcstoreid'
			
			";*/
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
			$AdminDAO2->dbname = $dbname_dest;
			//$afectedid	=	$AdminDAO2->queryresult($insertstock);

			$fields		=	array('batch','quantity','unitsremaining','expiry','purchaseprice','costprice','retailprice','priceinrs','shipmentcharges','suggestedsaleprice','fkshipmentgroupid','fkshipmentid','fkbarcodeid','fkorderid','fksupplierid','fkagentid','fkcountryid','fkstoreid','fkemployeeid','fkbrandid','updatetime','shipmentpercentage','boxprice','refstockid','srcstoreid');
			$values		=	array($batch,$quantity,$unitsremaining,$expiry,$purchaseprice,$costprice,$retailprice,$priceinrs,$shipmentcharges,$suggestedsaleprice,$fkshipmentgroupid,$fkshipmentid,$fkbarcodeid,$fkorderid,$fksupplierid,$fkagentid,$fkcountryid,$fkstoreid,$fkemployeeid,$fkbrandid,time(),$shipmentpercentage,$boxprice,$refstockid,$srcstoreid);
			$table		=	"stock";
		
			$afectedid 	=	$AdminDAO2->insertrow($table,$fields,$values);			
			
			//$AdminDAO->logquery2db($insertstock,'i','stock','pkstockid',$afectedid,$storeid,time(),'',$dbname_dest);
			/*$sql="UPDATE 
							stock 
						SET 
							unitsremaining=(unitsremaining-$moveunits) 
						WHERE 
							pkstockid='$stockid'";*/
			$AdminDAO2->dbname = $sourcestoredb;
			//$AdminDAO2->queryresult($sql);
			
			$fields		=	array('unitsremaining');
			$values		=	array("unitsremaining-$moveunits");
			$table		=	"stock";
		
			$AdminDAO2->updaterow($table,$fields,$values,"pkstockid='$stockid'");	
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
			$insertstock="
			INSERT INTO
						$dbname_dest.stock
					SET 
						batch ='$batch',
						quantity ='$quantity',
						unitsremaining ='$unitsremaining',
						expiry ='$expiry',
						purchaseprice ='$purchaseprice',
						costprice ='$costprice',
						retailprice ='$retailprice',
						priceinrs ='$priceinrs',
						shipmentcharges ='$shipmentcharges',
						suggestedsaleprice ='$suggestedsaleprice',
						fkshipmentgroupid ='$fkshipmentgroupid',
						fkshipmentid ='$fkshipmentid',
						fkbarcodeid ='$fkbarcodeid',
						fkorderid ='$fkorderid',
						fksupplierid ='$fksupplierid',
						fkagentid ='$fkagentid',
						fkcountryid ='$fkcountryid',
						fkstoreid ='$fkstoreid',
						fkemployeeid ='$fkemployeeid',
						fkbrandid ='$fkbrandid',
						updatetime ='".time()."',
						shipmentpercentage ='$shipmentpercentage',
						boxprice ='$boxprice',
						refstockid ='$refstockid',
						srcstoreid ='$srcstoreid',
						addtime = '".time()."'
			
			";
			//$afectedid	 =	$AdminDAO->queryresult($insertstock);
			
			$updatetimej =	time();		
			$tblj		 = 	$dbname_dest."stock";
			$field		 =	array('batch','quantity','unitsremaining','expiry','purchaseprice','costprice','retailprice','priceinrs','shipmentcharges','suggestedsaleprice','fkshipmentgroupid','fkshipmentid','fkbarcodeid','fkorderid','fksupplierid',						'fkagentid','fkcountryid','fkstoreid','fkemployeeid','fkbrandid','updatetime','shipmentpercentage','boxprice','refstockid','srcstoreid','addtime');
			$value		 =	array(
						$batch,
						$quantity,
						$unitsremaining,
						$expiry,
						$purchaseprice,
						$costprice,
						$retailprice,
						$priceinrs,
						$shipmentcharges,
						$suggestedsaleprice,
						$fkshipmentgroupid,
						$fkshipmentid,
						$fkbarcodeid,
						$fkorderid,
						$fksupplierid,
						$fkagentid,
						$fkcountryid,
						$fkstoreid,
						$fkemployeeid,
						$fkbrandid,
						$updatetimej,
						$shipmentpercentage,
						$boxprice,
						$refstockid,
						$srcstoreid,
						time()			
			);
			
			$afectedid	 =	$AdminDAO->insertrow($tblj,$field,$value);	
			
						
			$AdminDAO->logquery2db($insertstock,'i','stock','pkstockid',$afectedid,$storeid,time(),'',$dbname_dest);
			$sql="UPDATE 
							$sourcestoredb.stock 
						SET 
							unitsremaining=(unitsremaining-$moveunits) 
						WHERE 
							pkstockid='$stockid'";
			//$AdminDAO->queryresult($sql);
			
			$tblj	= 	"$sourcestoredb.stock";
			$field	=	array('unitsremaining');
			$value	=	array('(unitsremaining-$moveunits)');
			
			$AdminDAO->updaterow($tblj,$field,$value,"pkstockid='$stockid'");	
						
	
			$AdminDAO->logquery2db($sql,'u','stock','pkstockid',$stockid,$storeid,time(),'',$sourcestoredb);
	}//end edit
						//$AdminDAO->logquery2db($sql,'u','stock','pkstockid',$stockid,$storeid,time(),'',$sourcestoredb);
				
					/*$query="UPDATE 
								$dbname_dest.demand 
							SET
								demandstatus ='f' 
							WHERE 
								pkdemandid='$demandid'
							";
					
					$AdminDAO->queryresult($query);
					
					$query="UPDATE 
								$dbname_dest.demandfulfilment 
							SET
								demandstatus ='c' 
							WHERE 
								fkdemanddetailid='$pkdemanddetailid'
							";
					$AdminDAO->queryresult($query);*/
		
		/*$instncestock = $AdminDAO->getrows("instancestock","fkproductatributeid, fkattributeoptionid"," fkstockid='$stockid' ");
		
		if(count($instncestock)>0)
		{
			foreach($instncestock as $insstock)
			{
			
				$fkproductatributeid	=	$insstock['fkproductatributeid'];
				$fkattributeoptionid	=	$insstock['fkattributeoptionid'];
				$fields = array('fkdemanddetailsid','fkproductattributeid','fkattributeoptionid');
				$values = array($demanddetailsid,$fkproductatributeid,$fkattributeoptionid);
				$demanddetailsid = $AdminDAO->insertrow("instancedemanddetails",$fields,$values);
			
			}//end of foreach
		}// end of if*/
	}//end of if
	
exit;
}// end post
?>