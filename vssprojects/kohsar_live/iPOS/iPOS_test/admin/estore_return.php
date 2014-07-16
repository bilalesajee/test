<?php
session_start();
$_SESSION = array(
    "storeid" => 3,
    "siteconfig" => 2,
    "countername" => -1,
    "siteconfig" => 2,
    "addressbookid" => 40,
    "name" => 'System Admin',
    "admin_section" => 'Admin logged in',
    "groupid" => 6,
    "groupname" => 'Administrator',);

include_once("../includes/security/adminsecurity.php");
global $AdminDAO;

if(sizeof($_REQUEST)>0)
{
	$returnamt=$_REQUEST['return_quantity'];
	$inid=$_REQUEST['Invceid'];
	$bid=$_REQUEST['barcodeid'];
	$sid=$_REQUEST['supid'];
	$stock=$_REQUEST['stid'];
	$storeid=$_REQUEST['srid'];
	$remids		=	$AdminDAO->getrows("$dbname_detail.stock","unitsremaining","fksupplierinvoiceid='$inid' and fkbarcodeid='$bid' and fksupplierid='$sid'");
	$unitrem	=	$remids[0]['unitsremaining'];
	$fields_r	=	array('unitsremaining');
	$values_r	=	array($unitrem+$returnamt);
	$table		=	"$dbname_detail.stock";
	$oldres		=	$AdminDAO->updaterow($table,$fields_r,$values_r,"fksupplierinvoiceid='$inid' and fkbarcodeid='$bid' and fksupplierid='$sid'");	
	
	 $fields	=	array("fkstockid","quantity","fkstoreid","returnstatus","returndate");
	 $data		=	array($stock,$returnamt,$storeid,'c',time());
	 //$AdminDAO->insertrow("$dbname_detail.returns",$fields,$data);	
	
		exit;
}// end post
?>