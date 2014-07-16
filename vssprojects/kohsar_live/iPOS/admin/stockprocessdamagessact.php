<?php
//error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$empid		=	$_SESSION['addressbookid'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$addressbookid		=	$_SESSION['addressbookid'];
	$employeeids		=	$AdminDAO->getrows("employee","*","fkaddressbookid = '$addressbookid'");
	$employeeid			=	$employeeids[0]['pkemployeeid'];
	$storeid			=	$_SESSION['storeid'];
	$returntype			=	$_POST['returntype'];
	$return				=	$_POST['return'];
	$stockid			= 	$_POST['stockid'];
	$totalrecs			=	$_POST['totalrecs'];
	$returnstatus		=	$_POST['returnstatus'];
	// this is the add section		
	$fields	=	array("fkstockid","quantity","fkstoreid","fkemployeeid","damagedate","damagestatus","fkdamagetypeid");	
	for($i=0;$i<$totalrecs;$i++)
	{
		$returntyp	=	$returntype[$i];
		$returnamt	=	$return[$i];
		if($returnamt	== 0 || $returnamt == '')
		{
			continue;
		}
		if($returnamt > 0){
			//echo $returnamt;
		$stock		=	$stockid[$i];
		$status		=	$returnstatus[$i];
		$data		=	array($stock,$returnamt,$storeid,$employeeid,$status,time(),$returntyp);
		$AdminDAO->insertrow("$dbname_detail.damages",$fields,$data);
		//adjusting stocks
		$stockquery	=	"UPDATE $dbname_detail.stock SET unitsremaining=(unitsremaining-$returnamt) WHERE pkstockid='$stock'";
		$AdminDAO->queryresult($stockquery);

//	$fields		=	array('unitsremaining');
//	$values		=	array("unitsremaining-$returnamt");
//	$table		=	"$dbname_detail.stock";

//	$AdminDAO->updaterow($table,$fields,$values,"pkstockid='$stock'");
		}
	}
exit;
}// end post
?>