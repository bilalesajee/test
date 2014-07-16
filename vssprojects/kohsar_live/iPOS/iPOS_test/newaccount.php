<?php 
include_once("includes/security/adminsecurity.php");
global $AdminDAO;
if(sizeof($_POST)>0)
{
	$actitle	=	filter($_REQUEST['actitle']," ");
	$aclimit	=	$_REQUEST['limit'];
	if($aclimit == '' || $actitle == '')
	{
		echo"<li>Please enter account title and account limit.</li>";	
		exit;
	}
	$accheck	=	$AdminDAO->getrows("$dbname_detail.account","*"," title='$actitle'");
	if(count($accheck)>0)
	{
		echo"<li>This account <b><u> $actitle </u></b> already exists.</li>";	
		exit;
	}
	$date		=	time();
	$fields		=	array("title","creationdate","fkaddressbookid","accountlimit","status");
	$values		=	array($actitle,$date,$empid,$aclimit,'1');
	$AdminDAO->insertrow("$dbname_detail.account",$fields,$values);
	echo "Account added successfully.";
	exit;
}
?>