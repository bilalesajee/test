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
	$units_rem		=	$_POST['urem'];
//echo var_dump($return)."this is the add section";	
	$fields	=	array("fkstockid","quantity","fkstoreid","fkemployeeid","returnstatus","returndate","fkreturntypeid");
	for($i=0;$i<$totalrecs;$i++)
	{
		$returntyp	=	$returntype[$i];
		$returnamt	=	$return[$i];
		$unitrem=$units_rem[$i];
		if($returnamt	== 0 || $returnamt == '')
		{
			continue;
		}
		if($returnamt>$unitrem){
			echo "Enterd Quantity is greater than units remaining";
			echo "<br>";
			continue;
			}else{
		$stock		=	$stockid[$i];
		$status		=	$returnstatus[$i];
		$data		=	array($stock,$returnamt,$storeid,$employeeid,$status,time(),$returntyp);
		$AdminDAO->insertrow("$dbname_detail.returns",$fields,$data);
		//adjusting stocks
		//$stockquery	=	"UPDATE $dbname_detail.stock SET unitsremaining=unitsremaining-$returnamt WHERE pkstockid='$stock'";
		//$AdminDAO->queryresult($stockquery);
		
	$fields_r		=	array('unitsremaining');
	$values_r		=	array($unitrem-$returnamt);
	$table		=	"$dbname_detail.stock";
  //  echo "<pre>";
	//print_r($values);
	$oldres		=	$AdminDAO->updaterow($table,$fields_r,$values_r,"pkstockid='$stock'");		
			}
	}
exit;
}// end post
?>