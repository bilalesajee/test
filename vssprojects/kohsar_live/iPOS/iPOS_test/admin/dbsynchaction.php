<?php
session_start();
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
//sleep(2);
if(sizeof($_REQUEST)>0)
{
	$locationid	=	$_REQUEST['storeid'];
	$numrecs	=	$_REQUEST['numrecs'];
	if($locationid=='' || $numrecs=='')
	{
		$msg	=	"<li>Please select a location</li>";
	}
	if($numrecs == '')
	{
		$msg	=	"<li>Please select records to update</li>";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	else
	{
		// fetch location data
		if($numrecs=='a')
		{
			$limit	=	"";
		}
		else
		{
			$limit	=	"LIMIT 0,$numrecs";
		}
		$locationdata	=	$AdminDAO->getrows('store',"storedb,storeip,username,password,queryloggerposition","pkstoreid='$locationid'");
		$storedb		=	$locationdata[0]['storedb'];
		$storeip		=	$locationdata[0]['storeip'];
		$username		=	$locationdata[0]['username'];
		$password		=	$locationdata[0]['password'];
		$logposition	=	$locationdata[0]['queryloggerposition'];
		mysql_connect($storeip,$username,$password);
		mysql_select_db($storedb);
		// processing requests
		$db			=	$storedb.".stock";
		$db2		=	$storedb.".pricechange";
		$db3		=	$storedb.".pricechangehistory";
		$query		=	"SELECT * from $storedb.querylogger WHERE `pkqueryloggerid`>$logposition AND `table` IN ('$db','$db2','$db3') AND type in ('i','u') $limit;";
		/*echo $query;
		exit;*/
		$results	=	mysql_query($query);
		if($results)
		{
			$syncrecs	=	0;
			while($array_results	=	mysql_fetch_assoc($results))
			{
				echo "<pre>";
				print_r($array_results);
				echo "</pre>";
				$tablename		=	$array_results['table'];
				$logquery		=	$array_results['query'];
				$logpos			=	$array_results['pkqueryloggerid'];
				// update records
				$logupdateid	=	$AdminDAO->queryresult($logquery);
				if($tablename=="$db")
				{
					// updating stock, setting queryloggerposition
					if($array_results['type']=='u')
					{
						$stockid	=	explode('pkstockid =',$logquery);
						$fkstockid	=	$stockid[1];
						// updating stock record update case
						$AdminDAO->queryresult("UPDATE $db SET fkqueryloggerid='$logpos' WHERE pkstockid=$fkstockid");
					}
					else
					{
						// updating stock record insert case
						$AdminDAO->queryresult("UPDATE $db SET fkqueryloggerid='$logpos' WHERE pkstockid='$logupdateid'");
					}
				}
				else if($tablename=="$db2")
				{
					// updating pricel, setting queryloggerposition
					if($array_results['type']=='u')
					{
						$priceid			=	explode('fkbarcodeid=',$logquery);
						$fkpricechangeid	=	$priceid[1];
						// updating stock record update case
						$AdminDAO->queryresult("UPDATE $db2 SET fkqueryloggerid='$logpos' WHERE pkpricechangeid=$fkpricechangeid");
					}
					else
					{
						// updating price record insert case
						$AdminDAO->queryresult("UPDATE $db2 SET fkqueryloggerid='$logpos' WHERE pkpricechangeid='$logupdateid'");
					}
				}
				// update query logger position
				$updatelogquery	=	"UPDATE store SET queryloggerposition='$logpos' WHERE pkstoreid='$locationid'"; 
				$AdminDAO->queryresult($updatelogquery);
				// updated query logger position successfully
				$syncrecs++;
			}
			// updated records successfully
			// update dbsynch table
			$synchfields	=	array('fkaddressbookid','updatetime','fkstoreid','totalupdates');
			$synchdata		=	array($_SESSION['addressbookid'],time(),$locationid,$syncrecs);
			$AdminDAO->insertrow("dbsynch",$synchfields,$synchdata);
			//echo "Imported records successfully.";
			exit;
		}
		else
		{
			echo "Errors occured while importing records, please try again";
			exit;
		}
	}
}
else
{
	echo "Invalid data";
}
?>